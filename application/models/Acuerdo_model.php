<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Acuerdo_model extends CI_Model {
 public function permiso($clave){
  if((int)$this->session->userdata('es_admin')===2) return TRUE;
  if(!in_array($clave,array('administrar_grupos','gestionar_acuerdos'),TRUE)) return FALSE;
  $p=$this->db->get_where('usuario_permiso_acuerdo',array('usuario_id'=>(int)$this->session->userdata('usuario_id')))->row();
  return $p && (int)$p->$clave===1;
 }
 public function grupos($todos=FALSE){
  $this->db->select('g.*')->from('grupos_acuerdos g');
  if(!$todos && (int)$this->session->userdata('es_admin')!==2) $this->db->join('usuario_grupo_acuerdo ug','ug.grupo_id=g.id')->where('ug.usuario_id',(int)$this->session->userdata('usuario_id'));
  return $this->db->order_by('g.descripcion','ASC')->get()->result();
 }
 public function grupo_permitido($id){
  if((int)$this->session->userdata('es_admin')===2) return TRUE;
  return (bool)$this->db->where('usuario_id',(int)$this->session->userdata('usuario_id'))->where('grupo_id',(int)$id)->count_all_results('usuario_grupo_acuerdo');
 }
 public function una($id){return $this->db->get_where('acuerdos',array('id'=>(int)$id))->row();}
 public function todos_admin(){
  $this->db->select('a.*,g.descripcion grupo_descripcion')->from('acuerdos a')->join('grupos_acuerdos g','g.id=a.grupo_id');
  if((int)$this->session->userdata('es_admin')!==2) $this->db->join('usuario_grupo_acuerdo ug','ug.grupo_id=a.grupo_id')->where('ug.usuario_id',(int)$this->session->userdata('usuario_id'));
  return $this->db->order_by('a.fecha_acuerdo','DESC')->get()->result();
 }
 public function anios_publicos(){
  return $this->db->select('YEAR(fecha_acuerdo) anio',FALSE)->from('acuerdos')->where('activo',1)->group_by('YEAR(fecha_acuerdo)')->order_by('anio','DESC')->get()->result();
 }
 public function publicos($f=array(),$limit=500){
  $this->db->select('a.*,g.descripcion grupo_descripcion')->from('acuerdos a')->join('grupos_acuerdos g','g.id=a.grupo_id')->where('a.activo',1);
  if(!empty($f['titulo'])) $this->db->like('a.titulo',substr((string)$f['titulo'],0,150));
  if(!empty($f['anio']) && preg_match('/^(19|20)\d{2}$/',(string)$f['anio'])){
   $this->db->where('a.fecha_acuerdo >=',$f['anio'].'-01-01')->where('a.fecha_acuerdo <=',$f['anio'].'-12-31');
  }
  return $this->db->order_by('a.fecha_acuerdo','DESC')->order_by('a.id','DESC')->limit($limit)->get()->result();
 }
 public function por_token($token){
  if(!is_string($token)||!preg_match('/\A[a-f0-9]{64}\z/',$token)) return NULL;
  return $this->db->get_where('acuerdos',array('token_publico'=>$token,'activo'=>1))->row();
 }
 public function token($a){
  if($a->token_publico) return $a->token_publico;
  $t=bin2hex(random_bytes(32));
  $this->db->where('id',(int)$a->id)->where('token_publico IS NULL',NULL,FALSE)->update('acuerdos',array('token_publico'=>$t));
  $row=$this->una($a->id);return $row->token_publico;
 }
}
