<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Revision_enlaces_model extends CI_Model {
 public function ultima(){ return $this->db->order_by('id','DESC')->limit(1)->get('revision_enlaces')->row(); }
 public function activa(){ return $this->db->where('estado','en_curso')->order_by('id','DESC')->limit(1)->get('revision_enlaces')->row(); }
 public function una($id){ return $this->db->get_where('revision_enlaces',array('id'=>$id))->row(); }
 public function cursor_inicial(){ $r=$this->db->select_max('id')->get('resoluciones')->row(); return (int)$r->id+1; }
 public function iniciar($usuario){ $this->db->insert('revision_enlaces',array('usuario_id'=>$usuario,'estado'=>'en_curso','iniciada_en'=>date('Y-m-d H:i:s'),'cursor_id'=>$this->cursor_inicial())); return (int)$this->db->insert_id(); }
 public function finalizar($id){ $this->db->where('id',$id)->update('revision_enlaces',array('estado'=>'finalizada','finalizada_en'=>date('Y-m-d H:i:s'),'cursor_id'=>0)); }
 public function registrar($id,$r,$resultado){
  $this->db->trans_begin();
  $revision=$this->db->query('SELECT id FROM revision_enlaces WHERE id=? AND estado=? FOR UPDATE',array($id,'en_curso'))->row();
  if(!$revision){ $this->db->trans_rollback(); return false; }
  if($resultado['problema']) $this->db->insert('revision_enlaces_novedades',array('revision_id'=>$id,'resolucion_id'=>$r->id,'titulo'=>$r->titulo,'grupo'=>$r->grupo_descripcion,'tipo'=>$resultado['tipo'],'estado'=>$resultado['estado'],'detalle'=>$resultado['detalle'],'archivo_original'=>$r->archivo,'url_original'=>$r->url));
  $this->db->set('cursor_id',(int)$r->id)->set('revisados','revisados+1',false);
  if($resultado['problema']) $this->db->set('novedades','novedades+1',false);
  $this->db->where('id',$id)->update('revision_enlaces');
  $this->db->trans_commit(); return true;
 }
 public function pendientes($id){
  // Se omiten las novedades corregidas desde el formulario: cambia el origen, se elimina o desactiva la resolución.
  return $this->db->select('n.*,r.id AS vigente_id')->from('revision_enlaces_novedades n')
   ->join('resoluciones r','r.id=n.resolucion_id AND r.activo=1 AND (r.archivo <=> n.archivo_original) AND (r.url <=> n.url_original)','inner',false)
   ->where('n.revision_id',$id)->order_by('n.id','ASC')->get()->result();
 }
}
