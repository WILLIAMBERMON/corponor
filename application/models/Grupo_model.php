<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Grupo_model extends CI_Model {
 public function todos($usuarioId=NULL,$superadmin=FALSE){ if($usuarioId===NULL){ $usuarioId=$this->session->userdata('usuario_id'); $superadmin=(string)$this->session->userdata('documento')==='1000000000'; } if(!$superadmin && $usuarioId!==NULL) $this->db->select('g.*')->from('grupos_resoluciones g')->join('usuario_grupo ug','ug.grupo_id=g.id')->where('ug.usuario_id',(int)$usuarioId); else $this->db->from('grupos_resoluciones g'); $grupos=$this->db->get()->result(); usort($grupos,function($a,$b){ preg_match('/\\b(19|20)\\d{2}\\b/',(string)$a->descripcion,$ma); preg_match('/\\b(19|20)\\d{2}\\b/',(string)$b->descripcion,$mb); $ya=isset($ma[0])?(int)$ma[0]:0; $yb=isset($mb[0])?(int)$mb[0]:0; if($ya!==$yb) return $yb<=>$ya; return strcasecmp((string)$a->descripcion,(string)$b->descripcion); }); return $grupos; }
 public function puede_administrar($grupoId,$usuarioId,$superadmin=FALSE){ return $superadmin || (bool)$this->db->where(array('usuario_id'=>(int)$usuarioId,'grupo_id'=>(int)$grupoId))->count_all_results('usuario_grupo'); }
 public function crear($descripcion){
   if((string)$this->session->userdata('documento')!=='1000000000') return FALSE;
   $this->db->trans_start();
   $max=$this->db->select_max('consecutivo','m')->get('grupos_resoluciones')->row(); $consecutivo=((int)$max->m)+1;
   $this->db->insert('grupos_resoluciones',array('consecutivo'=>$consecutivo,'descripcion'=>$descripcion,'creado_por'=>$this->session->userdata('usuario_id'),'creado_en'=>date('Y-m-d H:i:s')));
   $this->db->trans_complete(); return $this->db->trans_status();
 }
}
