<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Usuario_model extends CI_Model {
 protected $table='usuarios';
 public function por_documento($documento){ return $this->db->get_where($this->table,array('documento'=>$documento,'activo'=>1))->row(); }
 public function por_email($email){ return $this->db->get_where($this->table,array('email'=>$email,'activo'=>1))->row(); }
 public function todos(){ return $this->db->order_by('nombres')->get($this->table)->result(); }
 public function crear($data){ $data['clave']=password_hash($data['clave'],PASSWORD_DEFAULT); $data['creado_en']=date('Y-m-d H:i:s'); return $this->db->insert($this->table,$data); }
 public function cambiar_clave($id,$clave){ return $this->db->where('id',$id)->update($this->table,array('clave'=>password_hash($clave,PASSWORD_DEFAULT),'actualizado_en'=>date('Y-m-d H:i:s'))); }
}
