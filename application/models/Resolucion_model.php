<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Resolucion_model extends CI_Model {
 private function ordenar_por_grupo_anio($rows){ usort($rows,function($a,$b){ preg_match('/\\b(19|20)\\d{2}\\b/',(string)$a->grupo_descripcion,$ma); preg_match('/\\b(19|20)\\d{2}\\b/',(string)$b->grupo_descripcion,$mb); $ya=isset($ma[0])?(int)$ma[0]:0; $yb=isset($mb[0])?(int)$mb[0]:0; if($ya!==$yb) return $yb<=>$ya; $grupo=strcasecmp((string)$a->grupo_descripcion,(string)$b->grupo_descripcion); if($grupo!==0) return $grupo; $fecha=strcmp((string)$b->fecha_resolucion,(string)$a->fecha_resolucion); if($fecha!==0) return $fecha; return (int)$b->id <=> (int)$a->id; }); return $rows; }
 public function buscar($f=array(),$limite=NULL,$offset=0){
  $this->db->select('r.*, g.descripcion grupo_descripcion, g.consecutivo grupo_consecutivo')->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id')->where('r.activo',1);
  if(!empty($f['titulo'])) $this->db->like('r.titulo',$f['titulo']);
  if(!empty($f['fecha'])) $this->db->where('r.fecha_resolucion',$f['fecha']);
  if(!empty($f['grupo'])) $this->db->where('r.grupo_id',(int)$f['grupo']);
  $this->db->order_by('g.descripcion','ASC')->order_by('r.fecha_resolucion','DESC')->order_by('r.id','DESC');
  if($limite!==NULL) $this->db->limit((int)$limite,(int)$offset);
  return $this->ordenar_por_grupo_anio($this->db->get()->result());
 }
 public function contar_busqueda($f=array()){
  $this->db->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id')->where('r.activo',1);
  if(!empty($f['titulo'])) $this->db->like('r.titulo',$f['titulo']);
  if(!empty($f['fecha'])) $this->db->where('r.fecha_resolucion',$f['fecha']);
  if(!empty($f['grupo'])) $this->db->where('r.grupo_id',(int)$f['grupo']);
  return (int)$this->db->count_all_results();
 }
 public function anios_publicos(){
  return $this->db->select('YEAR(fecha_resolucion) anio')->distinct()->where('activo',1)->order_by('anio','DESC')->get('resoluciones')->result();
 }
 public function resumen_por_anio($anio){
  $rows=$this->db->select('r.*, g.descripcion grupo_descripcion, g.consecutivo grupo_consecutivo')->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id')->where('r.activo',1)->like('g.descripcion',(string)(int)$anio)->order_by('g.descripcion','ASC')->order_by('r.fecha_resolucion','DESC')->order_by('r.id','DESC')->get()->result();
  if(!$rows){
   $rows=$this->db->select('r.*, g.descripcion grupo_descripcion, g.consecutivo grupo_consecutivo')->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id')->where('r.activo',1)->where('YEAR(r.fecha_resolucion)',(int)$anio)->order_by('g.descripcion','ASC')->order_by('r.fecha_resolucion','DESC')->order_by('r.id','DESC')->get()->result();
  }
  $resumen=array();
  foreach($rows as $row){ if(!isset($resumen[$row->grupo_id])) $resumen[$row->grupo_id]=array(); if(count($resumen[$row->grupo_id])<4) $resumen[$row->grupo_id][]=$row; }
  uksort($resumen,function($a,$b) use ($rows){ $ga=''; $gb=''; foreach($rows as $row){ if((string)$row->grupo_id===(string)$a) $ga=(string)$row->grupo_descripcion; if((string)$row->grupo_id===(string)$b) $gb=(string)$row->grupo_descripcion; } return strcasecmp($ga,$gb); }); return $resumen;
 }
 public function todas_admin($usuarioId=NULL,$superadmin=FALSE,$grupoId=NULL){ if($usuarioId===NULL){ $usuarioId=$this->session->userdata('usuario_id'); $superadmin=(int)$this->session->userdata('es_admin')===2; } $this->db->select("r.*,g.descripcion grupo_descripcion,g.consecutivo grupo_consecutivo,(SELECT COUNT(*) FROM estadisticas_eventos e1 WHERE e1.tipo='descarga' AND e1.resolucion_id=r.id) descargas,(SELECT COUNT(*) FROM estadisticas_eventos e2 WHERE e2.tipo='descarga' AND e2.grupo_id=r.grupo_id) descargas_grupo",FALSE)->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id'); if(!$superadmin && $usuarioId!==NULL) $this->db->join('usuario_grupo ug','ug.grupo_id=r.grupo_id')->where('ug.usuario_id',(int)$usuarioId); if($grupoId) $this->db->where('r.grupo_id',(int)$grupoId); return $this->ordenar_por_grupo_anio($this->db->get()->result()); }
 public function enlaces_a_verificar(){ return $this->db->select('r.id,r.titulo,r.fecha_resolucion,r.archivo,r.url,r.activo,g.descripcion grupo_descripcion,g.consecutivo grupo_consecutivo')->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id')->order_by('r.id','DESC')->get()->result(); }
 public function una($id,$soloAdministrables=FALSE){ $this->db->select('r.*')->from('resoluciones r'); if($soloAdministrables && $this->session->userdata('usuario_id') && (int)$this->session->userdata('es_admin')!==2) $this->db->join('usuario_grupo ug','ug.grupo_id=r.grupo_id')->where('ug.usuario_id',(int)$this->session->userdata('usuario_id')); return $this->db->where('r.id',(int)$id)->get()->row(); }
 public function crear($d){ if((int)$this->session->userdata('es_admin')!==2 && !$this->db->where(array('usuario_id'=>(int)$this->session->userdata('usuario_id'),'grupo_id'=>(int)$d['grupo_id']))->count_all_results('usuario_grupo')) return FALSE; $d['creado_por']=$this->session->userdata('usuario_id'); $d['creado_en']=date('Y-m-d H:i:s'); return $this->db->insert('resoluciones',$d); }
 public function actualizar($id,$d){ if((int)$this->session->userdata('es_admin')!==2 && !$this->db->where(array('usuario_id'=>(int)$this->session->userdata('usuario_id'),'grupo_id'=>(int)$d['grupo_id']))->count_all_results('usuario_grupo')) return FALSE; $d['actualizado_en']=date('Y-m-d H:i:s'); return $this->db->where('id',(int)$id)->update('resoluciones',$d); }
}
