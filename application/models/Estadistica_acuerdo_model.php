<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Estadistica_acuerdo_model extends CI_Model {
 public function registrar($tipo,$acuerdo_id=NULL,$grupo_id=NULL){
  if(!in_array($tipo,array('visita','visualizacion','descarga'),TRUE)) return FALSE;
  return $this->db->insert('estadisticas_acuerdos_eventos',array('tipo'=>$tipo,'acuerdo_id'=>$acuerdo_id,'grupo_id'=>$grupo_id,'ip'=>$this->input->ip_address(),'user_agent'=>substr((string)$this->input->user_agent(),0,500),'creado_en'=>date('Y-m-d H:i:s')));
 }
 public function descargas_por_acuerdos($ids){
  if(!$ids)return array(); $rows=$this->db->select('acuerdo_id,COUNT(*) total',FALSE)->where('tipo','descarga')->where_in('acuerdo_id',$ids)->group_by('acuerdo_id')->get('estadisticas_acuerdos_eventos')->result();
  $out=array();foreach($rows as $r)$out[$r->acuerdo_id]=(int)$r->total;return $out;
 }
 public function visualizaciones_por_acuerdos($ids){
  if(!$ids)return array();
  $rows=$this->db->select('acuerdo_id,COUNT(*) total',FALSE)->where('tipo','visualizacion')->where_in('acuerdo_id',$ids)->group_by('acuerdo_id')->get('estadisticas_acuerdos_eventos')->result();
  $out=array();foreach($rows as $r)$out[$r->acuerdo_id]=(int)$r->total;return $out;
 }
 private function filtrar($f,$grupos,$alias='a'){
  if(!empty($f['desde']))$this->db->where($alias.'.fecha_acuerdo >=',$f['desde']);
  if(!empty($f['hasta']))$this->db->where($alias.'.fecha_acuerdo <=',$f['hasta']);
  if(!empty($f['grupo']))$this->db->where($alias.'.grupo_id',(int)$f['grupo']);
  if(is_array($grupos)) { if(!$grupos)$this->db->where('1=0',NULL,FALSE);else $this->db->where_in($alias.'.grupo_id',$grupos); }
 }
 public function totales($f,$grupos){
  $this->db->from('estadisticas_acuerdos_eventos e')->join('acuerdos a','a.id=e.acuerdo_id')->where('a.activo',1);$this->filtrar($f,$grupos);
  $rows=$this->db->select('e.tipo,COUNT(*) total',FALSE)->group_by('e.tipo')->get()->result();$out=array('visitas'=>0,'visualizaciones'=>0,'descargas'=>0);
  foreach($rows as $r){if($r->tipo==='visualizacion')$out['visualizaciones']=(int)$r->total;if($r->tipo==='descarga')$out['descargas']=(int)$r->total;}
  // Visitas a la página pública: independientes de la fecha del documento.
  $out['visitas']=(int)$this->db->where('tipo','visita')->count_all_results('estadisticas_acuerdos_eventos');return $out;
 }
 public function por_grupo($f,$grupos){
  $this->db->select("g.id,g.descripcion,SUM(CASE WHEN e.tipo='descarga' THEN 1 ELSE 0 END) descargas,SUM(CASE WHEN e.tipo='visualizacion' THEN 1 ELSE 0 END) visualizaciones",FALSE)->from('acuerdos a')->join('grupos_acuerdos g','g.id=a.grupo_id')->join('estadisticas_acuerdos_eventos e','e.acuerdo_id=a.id','left')->where('a.activo',1);$this->filtrar($f,$grupos);
  return $this->db->group_by(array('g.id','g.descripcion'))->order_by('g.descripcion','ASC')->get()->result();
 }
 public function por_acuerdo($f,$grupos,$incluirCeros){
  $this->db->select("a.id,a.titulo,a.fecha_acuerdo,g.descripcion grupo_descripcion,SUM(CASE WHEN e.tipo='descarga' THEN 1 ELSE 0 END) descargas,SUM(CASE WHEN e.tipo='visualizacion' THEN 1 ELSE 0 END) visualizaciones",FALSE)->from('acuerdos a')->join('grupos_acuerdos g','g.id=a.grupo_id')->join('estadisticas_acuerdos_eventos e','e.acuerdo_id=a.id','left')->where('a.activo',1);$this->filtrar($f,$grupos);
  $this->db->group_by(array('a.id','a.titulo','a.fecha_acuerdo','g.descripcion'));
  if(!$incluirCeros)$this->db->having('descargas > 0',NULL,FALSE);
  return $this->db->order_by('a.fecha_acuerdo','DESC')->limit(5000)->get()->result();
 }
}
