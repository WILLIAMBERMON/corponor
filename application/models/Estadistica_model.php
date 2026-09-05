<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Estadistica_model extends CI_Model {
 private function ordenar_grupos($rows){ usort($rows,function($a,$b){ $grupoA=isset($a->grupo_descripcion)?$a->grupo_descripcion:($a->descripcion??''); $grupoB=isset($b->grupo_descripcion)?$b->grupo_descripcion:($b->descripcion??''); preg_match('/\\b(19|20)\\d{2}\\b/',(string)$grupoA,$ma); preg_match('/\\b(19|20)\\d{2}\\b/',(string)$grupoB,$mb); $ya=isset($ma[0])?(int)$ma[0]:0; $yb=isset($mb[0])?(int)$mb[0]:0; if($ya!==$yb) return $yb<=>$ya; $grupo=strcasecmp((string)$grupoA,(string)$grupoB); if($grupo!==0) return $grupo; if(isset($a->fecha_resolucion,$b->fecha_resolucion)){ $fecha=strcmp((string)$b->fecha_resolucion,(string)$a->fecha_resolucion); if($fecha!==0) return $fecha; } return (int)($b->id??0) <=> (int)($a->id??0); }); return $rows; }
 public function registrar($tipo,$resolucion_id=NULL,$grupo_id=NULL){
  return $this->db->insert('estadisticas_eventos',array('tipo'=>$tipo,'resolucion_id'=>$resolucion_id,'grupo_id'=>$grupo_id,'ip'=>$this->input->ip_address(),'user_agent'=>substr((string)$this->input->user_agent(),0,500),'creado_en'=>date('Y-m-d H:i:s')));
 }
 private function base($f=array(),$grupoIds=NULL){
  $this->db->from('estadisticas_eventos e')->join('resoluciones r','r.id=e.resolucion_id','left')->join('grupos_resoluciones g','g.id=COALESCE(e.grupo_id,r.grupo_id)','left');
  if(!empty($f['desde'])) $this->db->where('r.fecha_resolucion >=',$f['desde']);
  if(!empty($f['hasta'])) $this->db->where('r.fecha_resolucion <=',$f['hasta']);
  if(!empty($f['grupo'])) $this->db->where('COALESCE(e.grupo_id,r.grupo_id)',(int)$f['grupo'],FALSE);
  if(is_array($grupoIds) && !$grupoIds) $this->db->where('1=',0,FALSE);
  elseif(is_array($grupoIds)) $this->db->where_in('COALESCE(e.grupo_id,r.grupo_id)',$grupoIds,FALSE);
 }
 public function totales($f=array(),$grupoIds=NULL){
  $visitas=(int)$this->db->where('tipo','visita')->count_all_results('estadisticas_eventos');
  $this->db->from('estadisticas_eventos e')->join('resoluciones r','r.id=e.resolucion_id')->where(array('e.tipo'=>'descarga','r.activo'=>1)); $this->aplicar_filtros_resolucion($f,$grupoIds); $descargas=(int)$this->db->count_all_results();
  return array('visitas'=>$visitas,'descargas'=>$descargas);
 }
 public function por_grupo($f=array(),$grupoIds=NULL){
  $this->db->from('estadisticas_eventos e')->join('resoluciones r','r.id=e.resolucion_id')->join('grupos_resoluciones g','g.id=r.grupo_id')->where(array('e.tipo'=>'descarga','r.activo'=>1)); $this->aplicar_filtros_resolucion($f,$grupoIds); return $this->ordenar_grupos($this->db->select('g.id,g.descripcion,0 visitas,COUNT(*) descargas',FALSE)->group_by(array('g.id','g.descripcion'))->get()->result());
 }
 public function por_resolucion($f=array(),$grupoIds=NULL,$limite=NULL,$offset=0,$incluirCeros=FALSE){
  $conteo="(SELECT COUNT(*) FROM estadisticas_eventos e WHERE e.resolucion_id=r.id AND e.tipo='descarga')"; $this->db->select("r.id,r.titulo,r.fecha_resolucion,g.descripcion grupo_descripcion,{$conteo} descargas",FALSE)->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id')->where('r.activo',1);
  $this->aplicar_filtros_resolucion($f,$grupoIds);
  if(!$incluirCeros) $this->db->where($conteo.' >',0,FALSE);
  if($limite!==NULL) $this->db->limit((int)$limite,(int)$offset); return $this->ordenar_grupos($this->db->get()->result());
 }
 public function contar_resoluciones($f=array(),$grupoIds=NULL,$incluirCeros=FALSE){
  $this->db->from('resoluciones r')->where('r.activo',1); $this->aplicar_filtros_resolucion($f,$grupoIds); if(!$incluirCeros) $this->db->where("EXISTS (SELECT 1 FROM estadisticas_eventos e WHERE e.resolucion_id=r.id AND e.tipo='descarga')",NULL,FALSE); return (int)$this->db->count_all_results();
 }
 private function aplicar_filtros_resolucion($f,$grupoIds=NULL){
  if(!empty($f['desde'])) $this->db->where('r.fecha_resolucion >=',$f['desde']);
  if(!empty($f['hasta'])) $this->db->where('r.fecha_resolucion <=',$f['hasta']);
  if(!empty($f['grupo'])) $this->db->where('r.grupo_id',(int)$f['grupo']);
  if(is_array($grupoIds) && !$grupoIds) $this->db->where('1=',0,FALSE); elseif(is_array($grupoIds)) $this->db->where_in('r.grupo_id',$grupoIds);
 }
 public function descargas_por_resoluciones($ids){
  if(!$ids) return array(); $rows=$this->db->select('resolucion_id,COUNT(*) total')->where('tipo','descarga')->where_in('resolucion_id',$ids)->group_by('resolucion_id')->get('estadisticas_eventos')->result(); $out=array(); foreach($rows as $r) $out[$r->resolucion_id]=(int)$r->total; return $out;
 }
}
