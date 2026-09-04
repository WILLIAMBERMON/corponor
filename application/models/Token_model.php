<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Token_model extends CI_Model {
 public function crear($usuario_id,$token){
  $this->db->where('usuario_id',$usuario_id)->delete('password_reset_tokens');
  return $this->db->insert('password_reset_tokens',array('usuario_id'=>$usuario_id,'token_hash'=>hash('sha256',$token),'expira_en'=>date('Y-m-d H:i:s',time()+3600),'creado_en'=>date('Y-m-d H:i:s')));
 }
 public function valido($token){ return $this->db->where('token_hash',hash('sha256',$token))->where('usado_en IS NULL',NULL,FALSE)->where('expira_en >=',date('Y-m-d H:i:s'))->get('password_reset_tokens')->row(); }
 public function usar($id){ return $this->db->where('id',$id)->update('password_reset_tokens',array('usado_en'=>date('Y-m-d H:i:s'))); }
}
