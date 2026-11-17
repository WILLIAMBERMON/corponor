<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Revision_enlaces_model extends CI_Model {
 public function ultima(){ return $this->db->order_by('id','DESC')->limit(1)->get('revision_enlaces')->row(); }
 public function activa(){ return $this->db->where('estado','en_curso')->order_by('id','DESC')->limit(1)->get('revision_enlaces')->row(); }
 public function una($id){ return $this->db->get_where('revision_enlaces',array('id'=>$id))->row(); }
 public function cursor_inicial(){ $r=$this->db->select_max('id')->get('resoluciones')->row(); return (int)$r->id+1; }
 public function cursor_acuerdos(){ $r=$this->db->select_max('id')->get('acuerdos')->row(); return (int)$r->id+1; }
 public function pasar_acuerdos($id){ $this->db->where('id',$id)->where('estado','en_curso')->update('revision_enlaces',array('fase'=>'acuerdos','cursor_id'=>$this->cursor_acuerdos())); }
 public function siguiente_acuerdo($antes){ return $this->db->select('a.id,a.titulo,a.fecha_acuerdo AS fecha_resolucion,a.archivo,a.url,a.activo,g.descripcion AS grupo_descripcion')->from('acuerdos a')->join('grupos_acuerdos g','g.id=a.grupo_id')->where('a.id <',(int)$antes)->order_by('a.id','DESC')->limit(1)->get()->result(); }
 public function iniciar($usuario){ $this->db->insert('revision_enlaces',array('usuario_id'=>$usuario,'estado'=>'en_curso','iniciada_en'=>date('Y-m-d H:i:s'),'cursor_id'=>$this->cursor_inicial())); return (int)$this->db->insert_id(); }
 public function finalizar($id){ $this->db->where('id',$id)->update('revision_enlaces',array('estado'=>'finalizada','finalizada_en'=>date('Y-m-d H:i:s'),'cursor_id'=>0)); }
 public function registrar($id,$r,$resultado,$tipo_doc){
  $this->db->trans_begin();
  $revision=$this->db->query('SELECT id FROM revision_enlaces WHERE id=? AND estado=? FOR UPDATE',array($id,'en_curso'))->row();
  if(!$revision){ $this->db->trans_rollback(); return false; }
  if($resultado['problema']) $this->db->insert('revision_enlaces_novedades',array('revision_id'=>$id,'resolucion_id'=>$tipo_doc==='resolucion'?(int)$r->id:NULL,'acuerdo_id'=>$tipo_doc==='acuerdo'?(int)$r->id:NULL,'tipo_documento'=>$tipo_doc,'titulo'=>$r->titulo,'grupo'=>$r->grupo_descripcion,'tipo'=>$resultado['tipo'],'estado'=>$resultado['estado'],'detalle'=>$resultado['detalle'],'archivo_original'=>$r->archivo,'url_original'=>$r->url));
  $this->db->set('cursor_id',(int)$r->id)->set('revisados','revisados+1',false);
  if($resultado['problema']) $this->db->set('novedades','novedades+1',false);
  $this->db->where('id',$id)->update('revision_enlaces');
  $this->db->trans_commit(); return true;
 }
 public function pendientes($id){
  // Un origen corregido, documento eliminado o despublicado deja de ser una novedad pendiente.
  return $this->db->select('n.*')->from('revision_enlaces_novedades n')
   ->join('resoluciones r',"n.tipo_documento='resolucion' AND r.id=n.resolucion_id AND r.activo=1 AND (r.archivo <=> n.archivo_original) AND (r.url <=> n.url_original)",'left',false)
   ->join('acuerdos a',"n.tipo_documento='acuerdo' AND a.id=n.acuerdo_id AND a.activo=1 AND (a.archivo <=> n.archivo_original) AND (a.url <=> n.url_original)",'left',false)
   ->where('n.revision_id',(int)$id)
   ->group_start()->where("(n.tipo_documento='resolucion' AND r.id IS NOT NULL)",NULL,FALSE)
   ->or_where("(n.tipo_documento='acuerdo' AND a.id IS NOT NULL)",NULL,FALSE)->group_end()
   ->order_by('n.id','ASC')->get()->result();
 }
}
