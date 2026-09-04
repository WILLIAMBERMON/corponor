<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Grupo_model extends CI_Model {
 public function todos(){ return $this->db->order_by('consecutivo','DESC')->get('grupos_resoluciones')->result(); }
 public function crear($descripcion){
   $this->db->trans_start();
   $max=$this->db->select_max('consecutivo','m')->get('grupos_resoluciones')->row(); $consecutivo=((int)$max->m)+1;
   $this->db->insert('grupos_resoluciones',array('consecutivo'=>$consecutivo,'descripcion'=>$descripcion,'creado_por'=>$this->session->userdata('usuario_id'),'creado_en'=>date('Y-m-d H:i:s')));
   $this->db->trans_complete(); return $this->db->trans_status();
 }
}
