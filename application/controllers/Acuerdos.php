<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Acuerdos extends MY_Controller {
 public function __construct(){parent::__construct();$this->load->model(array('Acuerdo_model','Usuario_model','Estadistica_acuerdo_model'));}
 private function exigir($permiso){$this->require_login();if(!$this->Acuerdo_model->permiso($permiso)) show_error('No autorizado',403);}
 private function grupo($id){if(!$this->Acuerdo_model->grupo_permitido($id)) show_error('Grupo no autorizado',403);}
 public function index(){
  $anios=$this->Acuerdo_model->anios_publicos();$permitidos=array_map(function($r){return (int)$r->anio;},$anios);
  $solicitado=(int)$this->input->get('anio',TRUE);
  $anio=in_array($solicitado,$permitidos,TRUE)?$solicitado:($permitidos?$permitidos[0]:NULL);
  $titulo=trim((string)$this->input->get('titulo',TRUE));
  $f=array('titulo'=>$titulo,'anio'=>$anio);
  $rows=$anio!==NULL?$this->Acuerdo_model->publicos($f):array();
  $ids=array();foreach($rows as $a){$ids[]=(int)$a->id;$a->token_publico=$this->Acuerdo_model->token($a);}
  $descargas=$this->Estadistica_acuerdo_model->descargas_por_acuerdos($ids);
  $visualizaciones=$this->Estadistica_acuerdo_model->visualizaciones_por_acuerdos($ids);
  foreach($rows as $a){$a->descargas=(int)($descargas[$a->id]??0);$a->visualizaciones=(int)($visualizaciones[$a->id]??0);}
  if($this->input->method()==='get')$this->Estadistica_acuerdo_model->registrar('visita');
  $this->render('public/acuerdos',array('title'=>'Acuerdos','acuerdos'=>$rows,'anios'=>$anios,'anio'=>$anio,'f'=>$f),'public');
 }
 public function ver($token=NULL){
  $a=$this->Acuerdo_model->por_token($token);
  if(!$a)show_404();
  // No registrar aqui: el visor solicita /acuerdos/archivo y esa peticion
  // registra una unica visualizacion, sumada al contador publico de descargas.
  $this->render('public/acuerdo',array('title'=>$a->titulo,'acuerdo'=>$a),'public');
 }
 public function descargar($token=NULL){$this->pdf($token,TRUE);}
 public function archivo($token=NULL){$this->pdf($token,FALSE);}
 private function pdf($token,$download){
  $a=$this->Acuerdo_model->por_token($token);if(!$a)show_404();
  if(!in_array($this->input->method(),array('get','head'),TRUE))show_error('Método no permitido',405);
  if(session_status()===PHP_SESSION_ACTIVE) session_write_close();
  $this->load->library('Documento_pdf');
  try{$fp=$this->documento_pdf->abrir($a);}catch(Exception $e){show_error('No fue posible obtener el PDF.',502);return;}
  $stat=fstat($fp);
  if($this->input->method()==='get')$this->Estadistica_acuerdo_model->registrar($download?'descarga':'visualizacion',(int)$a->id,(int)$a->grupo_id);
  header('Content-Type: application/pdf');header('Content-Disposition: '.($download?'attachment':'inline').'; filename="acuerdo.pdf"');
  header('Content-Length: '.$stat['size']);header('X-Content-Type-Options: nosniff');header('Referrer-Policy: no-referrer');header('Cache-Control: private, no-store');header('Accept-Ranges: none');
  if($this->input->method()!=='head')fpassthru($fp);fclose($fp);exit;
 }
 public function admin(){ $this->exigir('gestionar_acuerdos');$this->render('admin/acuerdos/index',array('title'=>'Acuerdos','acuerdos'=>$this->Acuerdo_model->todos_admin())); }
 public function grupos(){ $this->exigir('administrar_grupos');$this->render('admin/acuerdos/grupos',array('title'=>'Grupos de acuerdos','grupos'=>$this->Acuerdo_model->grupos())); }
 public function grupo_crear(){
  $this->exigir('administrar_grupos');if($this->input->method()!=='post')show_error('Método no permitido',405);
  $descripcion=trim((string)$this->input->post('descripcion',TRUE));if($descripcion===''||mb_strlen($descripcion)>255)show_error('Descripción inválida',422);
  if($this->db->where('descripcion',$descripcion)->count_all_results('grupos_acuerdos'))show_error('Ya existe un grupo con esa descripción',409);
  $this->db->trans_begin();$max=$this->db->select_max('consecutivo','n')->get('grupos_acuerdos')->row();
  $this->db->insert('grupos_acuerdos',array('consecutivo'=>(int)$max->n+1,'descripcion'=>$descripcion,'creado_por'=>$this->session->userdata('usuario_id'),'creado_en'=>date('Y-m-d H:i:s')));
  if($this->db->trans_status() && (int)$this->session->userdata('es_admin')!==2){
   $nuevo=(int)$this->db->insert_id();
   $this->db->insert('usuario_grupo_acuerdo',array('usuario_id'=>(int)$this->session->userdata('usuario_id'),'grupo_id'=>$nuevo));
  }
  if(!$this->db->trans_status()){$this->db->trans_rollback();show_error('No se pudo crear el grupo',500);}else $this->db->trans_commit();redirect('admin/grupos-acuerdos');
 }
 public function grupo_editar($id){
  $this->exigir('administrar_grupos');
  $id=(int)$id;
  $g=$this->db->get_where('grupos_acuerdos',array('id'=>$id))->row();
  if(!$g)show_404();
  $this->grupo($id);
  $error=NULL;
  if($this->input->method()==='post'){
   $descripcion=trim((string)$this->input->post('descripcion',TRUE));
   if($descripcion===''||mb_strlen($descripcion)>255)$error='Descripción inválida';
   elseif($this->db->where('id !=',$id)->where('descripcion',$descripcion)->count_all_results('grupos_acuerdos'))$error='Ya existe un grupo con esa descripción';
   else {
    $ok=$this->db->where('id',$id)->update('grupos_acuerdos',array('descripcion'=>$descripcion,'actualizado_en'=>date('Y-m-d H:i:s')));
    if($ok)redirect('admin/grupos-acuerdos');
    $error='No se pudo actualizar el grupo';
   }
  }
  $this->render('admin/acuerdos/grupo_form',array('title'=>'Editar grupo de acuerdos','grupo'=>$g,'error'=>$error));
 }
 public function grupo_eliminar($id){
  $this->exigir('administrar_grupos');
  if($this->input->method()!=='post')show_error('Método no permitido',405);
  $id=(int)$id;
  $g=$this->db->get_where('grupos_acuerdos',array('id'=>$id))->row();
  if(!$g)show_404();
  $this->grupo($id);
  if($this->db->where('grupo_id',$id)->count_all_results('acuerdos')>0){
   $this->session->set_flashdata('grupo_error','No se puede eliminar el grupo porque contiene acuerdos. Traslade o elimine primero sus acuerdos.');
   redirect('admin/grupos-acuerdos');return;
  }
  $this->db->trans_begin();
  $this->db->delete('usuario_grupo_acuerdo',array('grupo_id'=>$id));
  $this->db->delete('grupos_acuerdos',array('id'=>$id));
  if(!$this->db->trans_status()){$this->db->trans_rollback();show_error('No se pudo eliminar el grupo',500);return;}
  $this->db->trans_commit();
  $this->session->set_flashdata('grupo_ok','Grupo eliminado correctamente');
  redirect('admin/grupos-acuerdos');
 }
 public function crear(){ $this->guardar(NULL); }
 public function editar($id){$this->guardar((int)$id);}
 private function guardar($id){
  $this->exigir('gestionar_acuerdos');$a=$id?$this->Acuerdo_model->una($id):NULL;if($id&&!$a)show_404();if($a)$this->grupo($a->grupo_id);
  $error=NULL;
  if($this->input->method()==='post'){
   $gid=(int)$this->input->post('grupo_id');$this->grupo($gid);
   $titulo=trim((string)$this->input->post('titulo',TRUE));$fecha=(string)$this->input->post('fecha_acuerdo',TRUE);$url=trim((string)$this->input->post('url',TRUE));
   $d=DateTimeImmutable::createFromFormat('!Y-m-d',$fecha);
   if(!$gid||!$this->db->get_where('grupos_acuerdos',array('id'=>$gid))->row())$error='Grupo inválido';
   elseif($titulo===''||mb_strlen($titulo)>255)$error='Título inválido';
   elseif(!$d||$d->format('Y-m-d')!==$fecha)$error='Fecha inválida';
   elseif($url!==''&&(strlen($url)>1000||!filter_var($url,FILTER_VALIDATE_URL)||!in_array(strtolower(parse_url($url,PHP_URL_SCHEME)),array('https','http'),TRUE)))$error='URL inválida';
   $archivo=$a?$a->archivo:NULL;
   if(!$error&&!empty($_FILES['archivo']['name'])){
    $dir=FCPATH.'uploads/acuerdos/';if(!is_dir($dir))mkdir($dir,0755,TRUE);
    $this->load->library('upload');$this->upload->initialize(array('upload_path'=>$dir,'allowed_types'=>'pdf','encrypt_name'=>TRUE,'max_size'=>20480));
    if(!$this->upload->do_upload('archivo'))$error=strip_tags($this->upload->display_errors());else {
     $subido=$this->upload->data('full_path');$fp=fopen($subido,'rb');$cabecera=$fp?fread($fp,5):'';if($fp)fclose($fp);
     if($cabecera!=='%PDF-'){@unlink($subido);$error='El archivo no es un PDF válido.';}
     else $archivo='uploads/acuerdos/'.$this->upload->data('file_name');
    }
   }
   if(!$archivo&&$url==='')$error='Debe indicar un PDF o una URL';
   if(!$error){
    $data=array('grupo_id'=>$gid,'titulo'=>$titulo,'fecha_acuerdo'=>$fecha,'url'=>$url?:NULL,'archivo'=>$archivo,'activo'=>$this->input->post('activo')?1:0,'actualizado_en'=>date('Y-m-d H:i:s'));
    if($a){$ok=$this->db->where('id',$id)->update('acuerdos',$data);}else{$data['creado_por']=(int)$this->session->userdata('usuario_id');$data['creado_en']=date('Y-m-d H:i:s');$ok=$this->db->insert('acuerdos',$data);}
    if($ok)redirect('admin/acuerdos');$error='No fue posible guardar';
   }
  }
  $this->render('admin/acuerdos/form',array('title'=>$a?'Editar acuerdo':'Nuevo acuerdo','acuerdo'=>$a,'grupos'=>$this->Acuerdo_model->grupos(),'error'=>$error));
 }
 public function permisos($id){
  $this->require_login();if(!$this->Usuario_model->es_superadmin())show_error('Solo superadministrador',403);
  $u=$this->Usuario_model->una((int)$id);if(!$u)show_404();
  if($this->input->method()==='post'){
   $grupos=(array)$this->input->post('grupos');$ids=array_values(array_unique(array_filter(array_map('intval',$grupos))));
   $valid=array();if($ids){$rows=$this->db->where_in('id',$ids)->get('grupos_acuerdos')->result();foreach($rows as $g)$valid[]=(int)$g->id;}
   if(count($ids)!==count($valid))show_error('Grupo no válido',422);
   $this->db->trans_begin();$this->db->delete('usuario_grupo_acuerdo',array('usuario_id'=>$u->id));
   foreach($valid as $gid)$this->db->insert('usuario_grupo_acuerdo',array('usuario_id'=>$u->id,'grupo_id'=>$gid));
   $this->db->replace('usuario_permiso_acuerdo',array('usuario_id'=>$u->id,'administrar_grupos'=>$this->input->post('administrar_grupos')?1:0,'gestionar_acuerdos'=>$this->input->post('gestionar_acuerdos')?1:0));
   if(!$this->db->trans_status()){$this->db->trans_rollback();show_error('Error al guardar',500);}else $this->db->trans_commit();redirect('admin/usuarios');
  }
  $p=$this->db->get_where('usuario_permiso_acuerdo',array('usuario_id'=>$u->id))->row();$assigned=$this->db->select('grupo_id')->get_where('usuario_grupo_acuerdo',array('usuario_id'=>$u->id))->result();
  $this->render('admin/acuerdos/permisos',array('title'=>'Permisos de acuerdos','usuario'=>$u,'permiso'=>$p,'grupos'=>$this->Acuerdo_model->grupos(TRUE),'asignados'=>array_map(function($x){return (int)$x->grupo_id;},$assigned)));
 }
}
