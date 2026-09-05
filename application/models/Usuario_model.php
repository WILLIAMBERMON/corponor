<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Usuario_model extends CI_Model {
 protected $table='usuarios';
 public function por_documento($documento){ return $this->db->get_where($this->table,array('documento'=>$documento,'activo'=>1))->row(); }
 public function por_email($email){ return $this->db->get_where($this->table,array('email'=>$email,'activo'=>1))->row(); }
 public function todos(){ return $this->db->order_by('nombres')->get($this->table)->result(); }
 public function una($id){ return $this->db->where('id',(int)$id)->get($this->table)->row(); }
 public function crear($data){ $data['clave']=password_hash($data['clave'],PASSWORD_DEFAULT); $data['creado_en']=date('Y-m-d H:i:s'); return $this->db->insert($this->table,$data); }
 public function cambiar_clave($id,$clave){ return $this->db->where('id',$id)->update($this->table,array('clave'=>password_hash($clave,PASSWORD_DEFAULT),'actualizado_en'=>date('Y-m-d H:i:s'))); }
 public function es_superadmin(){ return (string)$this->session->userdata('documento')==='1000000000'; }
 public function grupos_asignados($usuarioId){ $grupos=$this->db->select('g.*')->from('grupos_resoluciones g')->join('usuario_grupo ug','ug.grupo_id=g.id')->where('ug.usuario_id',(int)$usuarioId)->get()->result(); usort($grupos,function($a,$b){ preg_match('/\\b(19|20)\\d{2}\\b/',(string)$a->descripcion,$ma); preg_match('/\\b(19|20)\\d{2}\\b/',(string)$b->descripcion,$mb); $ya=isset($ma[0])?(int)$ma[0]:0; $yb=isset($mb[0])?(int)$mb[0]:0; if($ya!==$yb) return $yb<=>$ya; return strcasecmp((string)$a->descripcion,(string)$b->descripcion); }); return $grupos; }
 public function asignar_grupos($usuarioId,$grupoIds){ $this->db->trans_start(); $this->db->delete('usuario_grupo',array('usuario_id'=>(int)$usuarioId)); foreach(array_unique(array_map('intval',(array)$grupoIds)) as $grupoId) if($grupoId>0) $this->db->insert('usuario_grupo',array('usuario_id'=>(int)$usuarioId,'grupo_id'=>$grupoId)); $this->db->trans_complete(); return $this->db->trans_status(); }
}
