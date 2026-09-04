<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Resolucion_model extends CI_Model {
 public function buscar($f=array(),$limite=NULL,$offset=0){
  $this->db->select('r.*, g.descripcion grupo_descripcion, g.consecutivo grupo_consecutivo')->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id')->where('r.activo',1);
  if(!empty($f['titulo'])) $this->db->like('r.titulo',$f['titulo']);
  if(!empty($f['fecha'])) $this->db->where('r.fecha_resolucion',$f['fecha']);
  if(!empty($f['grupo'])) $this->db->where('r.grupo_id',(int)$f['grupo']);
  $this->db->order_by('g.descripcion','ASC')->order_by('r.fecha_resolucion','DESC')->order_by('r.id','DESC');
  if($limite!==NULL) $this->db->limit((int)$limite,(int)$offset);
  return $this->db->get()->result();
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
  return $resumen;
 }
 public function todas_admin(){ return $this->db->select('r.*,g.descripcion grupo_descripcion,g.consecutivo grupo_consecutivo')->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id')->order_by('r.id','DESC')->get()->result(); }
 public function enlaces_a_verificar(){ return $this->db->select('r.id,r.titulo,r.fecha_resolucion,r.archivo,r.url,r.activo,g.descripcion grupo_descripcion,g.consecutivo grupo_consecutivo')->from('resoluciones r')->join('grupos_resoluciones g','g.id=r.grupo_id')->order_by('r.id','DESC')->get()->result(); }
 public function una($id){ return $this->db->get_where('resoluciones',array('id'=>$id))->row(); }
 public function crear($d){ $d['creado_por']=$this->session->userdata('usuario_id'); $d['creado_en']=date('Y-m-d H:i:s'); return $this->db->insert('resoluciones',$d); }
 public function actualizar($id,$d){ $d['actualizado_en']=date('Y-m-d H:i:s'); return $this->db->where('id',$id)->update('resoluciones',$d); }
}
