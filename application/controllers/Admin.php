<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends MY_Controller {
 public function __construct(){ parent::__construct(); $this->require_login(); $this->load->model(array('Usuario_model','Grupo_model','Resolucion_model')); }
 public function dashboard(){ $this->render('admin/dashboard',array('title'=>'Panel principal')); }
 public function usuarios(){ $this->require_admin(); $this->render('admin/usuarios/index',array('title'=>'Usuarios','usuarios'=>$this->Usuario_model->todos())); }
 public function usuario_crear(){ $this->guardar_usuario(); }
 public function usuario_editar($id){ $this->guardar_usuario((int)$id); }
 private function guardar_usuario($id=NULL){
  $this->require_admin(); $usuario=$id?$this->Usuario_model->una($id):NULL;
  if($id && !$usuario) show_404();
  $super=$this->Usuario_model->es_superadmin();
  if($usuario && (int)$usuario->es_admin===2 && !$super) show_error('Solo el superadministrador puede editar esta cuenta.',403);
  if($this->input->method()==='post'){
   $this->form_validation->set_rules('nombres','Nombres','required|trim|max_length[150]');
   $this->form_validation->set_rules('documento','Documento','required|trim|max_length[30]');
   $this->form_validation->set_rules('email','Correo','required|trim|valid_email|max_length[190]');
   if(!$usuario) $this->form_validation->set_rules('clave','Clave','required|min_length[8]');
   if($this->form_validation->run()){
    $data=array('nombres'=>$this->input->post('nombres',TRUE),'documento'=>$this->input->post('documento',TRUE),'email'=>$this->input->post('email',TRUE));
    $duplicado=FALSE;
    foreach(array('documento','email') as $campo){ $this->db->where($campo,$data[$campo]); if($id) $this->db->where('id !=',$id); if($this->db->count_all_results('usuarios')) $duplicado=TRUE; }
    $rol=(string)$this->input->post('es_admin');
    if(!in_array($rol,array('0','1','2'),TRUE) || (!$super && $rol==='2')) show_error('Rol no autorizado.',403);
    if($usuario && (int)$usuario->id===(int)$this->session->userdata('usuario_id') && (int)$usuario->es_admin!==(int)$rol) $error='No puede cambiar su propio rol.';
    elseif($duplicado) $error='El documento o el correo ya pertenece a otro usuario.';
    else {
     $data['es_admin']=(int)$rol;
     if($usuario){ $data['actualizado_en']=date('Y-m-d H:i:s'); $ok=$this->db->where('id',$id)->update('usuarios',$data); }
     else { $data['clave']=$this->input->post('clave'); $data['activo']=1; $ok=$this->Usuario_model->crear($data); }
     if($ok){ $this->session->set_flashdata('success','Usuario guardado.'); redirect('admin/usuarios'); }
     $error='No fue posible guardar el usuario.';
    }
   }
  }
  $this->render('admin/usuarios/form',array('title'=>$usuario?'Editar usuario':'Crear usuario','usuario'=>$usuario,'super'=>$super,'error'=>$error??NULL));
 }
 public function usuario_clave($id){
  $id=(int)$id;
  if((int)$this->session->userdata('es_admin')<1 && $id!==(int)$this->session->userdata('usuario_id')) show_error('Solo puede restablecer su propia clave.',403);
  $usuario=$this->Usuario_model->una($id); if(!$usuario) show_404();
  if($this->input->method()==='post'){
   $this->form_validation->set_rules('clave','Nueva clave','required|min_length[8]');
   $this->form_validation->set_rules('clave2','Confirmación','required|matches[clave]');
   if($this->form_validation->run()){
    $this->db->trans_start();
    $this->Usuario_model->cambiar_clave($id,$this->input->post('clave'));
    $this->db->delete('password_reset_tokens',array('usuario_id'=>$id));
    $this->db->trans_complete();
    if(!$this->db->trans_status()) show_error('No fue posible actualizar la clave.',500);
    $this->session->set_flashdata('success','Clave actualizada.'); redirect((int)$this->session->userdata('es_admin')>=1?'admin/usuarios':'admin');
   }
  }
  $this->render('admin/usuarios/clave',array('title'=>'Restablecer clave','usuario'=>$usuario));
 }
 private function exigir_post(){ if($this->input->method()!=='post') show_error('Método no permitido.',405); }
 private function exigir_grupo($id){ if(!$this->Grupo_model->puede_administrar($id,$this->session->userdata('usuario_id'),$this->Usuario_model->es_superadmin())) show_error('Grupo no autorizado.',403); }
 public function resolucion_eliminar($id){
  $this->exigir_post(); $r=$this->Resolucion_model->una((int)$id); if(!$r) show_404(); $this->exigir_grupo($r->grupo_id);
  if(!$this->db->delete('resoluciones',array('id'=>$r->id))) show_error('No fue posible eliminar la resolución.',500);
  $this->session->set_flashdata('success','Resolución eliminada.'); redirect('admin/resoluciones');
 }
 public function grupo_eliminar($id){
  $this->exigir_post(); $this->exigir_grupo((int)$id);
  if(!$this->db->get_where('grupos_resoluciones',array('id'=>(int)$id))->row()) show_404();
  $this->db->query('DELETE FROM grupos_resoluciones WHERE id = ? AND NOT EXISTS (SELECT 1 FROM resoluciones WHERE grupo_id = ?)',array((int)$id,(int)$id));
  $eliminado=$this->db->affected_rows();
  $this->session->set_flashdata($eliminado?'success':'error',$eliminado?'Grupo eliminado.':'No se puede eliminar un grupo que contiene resoluciones.'); redirect('admin/grupos');
 }
 public function usuario_grupos($id){ $this->require_admin(); if(!$this->Usuario_model->es_superadmin()) show_error('Solo el superadministrador puede asignar grupos.',403); $usuario=$this->Usuario_model->una($id); if(!$usuario) show_404(); $grupos=$this->Grupo_model->todos(NULL,TRUE); if($this->input->method()==='post'){ $this->Usuario_model->asignar_grupos($usuario->id,$this->input->post('grupos')); $this->session->set_flashdata('success','Grupos asignados.'); redirect('admin/usuarios'); } $asignados=array_map(function($g){return (int)$g->id;},$this->Usuario_model->grupos_asignados($usuario->id)); $this->render('admin/usuarios/grupos',array('title'=>'Asignar grupos','usuario'=>$usuario,'grupos'=>$grupos,'asignados'=>$asignados)); }
 public function grupos(){ $this->render('admin/grupos/index',array('title'=>'Grupos de resoluciones','grupos'=>$this->Grupo_model->todos())); }
 public function grupo_crear(){ if(!$this->Usuario_model->es_superadmin()) show_error('Solo el superadministrador puede crear grupos.',403); if($this->input->method()==='post'){ $this->form_validation->set_rules('descripcion','Descripción','required|max_length[255]'); if($this->form_validation->run()){ $this->Grupo_model->crear($this->input->post('descripcion',TRUE)); $this->session->set_flashdata('success','Grupo creado.'); redirect('admin/grupos'); }} $this->render('admin/grupos/form',array('title'=>'Crear grupo')); }
 public function resoluciones(){ $super=$this->Usuario_model->es_superadmin(); $grupo=(int)$this->input->get('grupo',TRUE); $this->render('admin/resoluciones/index',array('title'=>'Administrar resoluciones','resoluciones'=>$this->Resolucion_model->todas_admin(NULL,$super,$grupo),'grupos'=>$this->Grupo_model->todos($this->session->userdata('usuario_id'),$super),'grupo_seleccionado'=>$grupo)); }
 public function enlaces(){
  $this->require_admin();
  $total=(int)$this->db->count_all_results('resoluciones');
  $max=$this->db->select_max('id')->get('resoluciones')->row();
  $this->render('admin/enlaces',array('title'=>'Novedades de enlaces','total'=>$total,'antes'=>(int)$max->id+1));
 }
 public function enlaces_lote(){
  $this->require_admin(); $antes=$this->input->get('antes');
  if(!is_scalar($antes) || !ctype_digit((string)$antes) || (int)$antes<1) show_error('Cursor inválido.',400);
  $registros=$this->Resolucion_model->enlaces_a_verificar((int)$antes);
  $datos=array('fin'=>!$registros,'antes'=>(int)$antes,'novedad'=>NULL);
  if($registros){
   $r=$registros[0]; $resultado=$this->verificar_enlace($r); $datos['antes']=(int)$r->id;
   if($resultado['problema']) $datos['novedad']=array('resolucion'=>$r,'resultado'=>$resultado);
  }
  $this->output->set_content_type('application/json')->set_header('Cache-Control: no-store')->set_output(json_encode($datos));
 }
 private function verificar_enlace($r){
  if($r->archivo){ $ruta=FCPATH.ltrim(str_replace('/',DIRECTORY_SEPARATOR,$r->archivo),DIRECTORY_SEPARATOR); if(!is_file($ruta)) return array('problema'=>true,'tipo'=>'Archivo local','estado'=>'No encontrado','detalle'=>$r->archivo); return array('problema'=>false,'tipo'=>'Archivo local','estado'=>'OK','detalle'=>'Archivo disponible'); }
  if(!$r->url) return array('problema'=>true,'tipo'=>'Sin origen','estado'=>'Sin enlace','detalle'=>'Debe cargar un PDF o registrar una URL.');
  if(!function_exists('curl_init')) return array('problema'=>true,'tipo'=>'URL','estado'=>'No verificable','detalle'=>'La extensión cURL de PHP no está disponible.');
  $ch=curl_init($r->url); curl_setopt_array($ch,array(CURLOPT_NOBODY=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_MAXREDIRS=>5,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_TIMEOUT=>12,CURLOPT_RETURNTRANSFER=>true,CURLOPT_USERAGENT=>'Corponor Resoluciones Link Checker/1.0',CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2)); curl_exec($ch); $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); $error=curl_error($ch); curl_close($ch);
  if($code>=200 && $code<400) return array('problema'=>false,'tipo'=>'URL','estado'=>'HTTP '.$code,'detalle'=>'Enlace disponible'); return array('problema'=>true,'tipo'=>'URL','estado'=>$code?'HTTP '.$code:'Error de conexión','detalle'=>$error?:'El servidor no respondió correctamente.');
 }
 private function upload($field,$dir,$types,$max=10240){ if(empty($_FILES[$field]['name'])) return NULL; $config=array('upload_path'=>FCPATH.$dir,'allowed_types'=>$types,'max_size'=>$max,'encrypt_name'=>TRUE,'remove_spaces'=>TRUE); $this->load->library('upload'); $this->upload->initialize($config); if(!$this->upload->do_upload($field)) throw new RuntimeException(strip_tags($this->upload->display_errors())); return $dir.'/'.$this->upload->data('file_name'); }
 public function resolucion_crear(){ $grupos=$this->Grupo_model->todos(); if($this->input->method()==='post'){ $this->validar_resolucion(); if($this->form_validation->run()){ try { $archivo=$this->upload('archivo','uploads/resoluciones','pdf'); $imagen=$this->upload('imagen','uploads/imagenes','jpg|jpeg|png|webp',4096); if(!$archivo && !$this->input->post('url')) throw new RuntimeException('Debe cargar un archivo PDF o indicar una URL.'); $this->Resolucion_model->crear(array('grupo_id'=>(int)$this->input->post('grupo_id'),'titulo'=>$this->input->post('titulo',TRUE),'fecha_resolucion'=>$this->input->post('fecha_resolucion',TRUE),'archivo'=>$archivo,'url'=>$this->input->post('url',TRUE) ?: NULL,'imagen'=>$imagen,'activo'=>1)); $this->session->set_flashdata('success','Resolución creada.'); redirect('admin/resoluciones'); } catch(Exception $e){ $data['error']=$e->getMessage(); } }} $this->render('admin/resoluciones/form',array_merge($data??array(),array('title'=>'Crear resolución','grupos'=>$grupos,'resolucion'=>NULL))); }
 public function resolucion_editar($id){ $r=$this->Resolucion_model->una((int)$id); if(!$r) show_404(); $this->exigir_grupo($r->grupo_id); $grupos=$this->Grupo_model->todos(); if($this->input->method()==='post'){ $this->validar_resolucion(); if($this->form_validation->run()){ try { $archivo=$this->upload('archivo','uploads/resoluciones','pdf'); $imagen=$this->upload('imagen','uploads/imagenes','jpg|jpeg|png|webp',4096); $d=array('grupo_id'=>(int)$this->input->post('grupo_id'),'titulo'=>$this->input->post('titulo',TRUE),'fecha_resolucion'=>$this->input->post('fecha_resolucion',TRUE),'url'=>$this->input->post('url',TRUE) ?: NULL,'activo'=>$this->input->post('activo')?1:0); if($archivo) $d['archivo']=$archivo; if($imagen) $d['imagen']=$imagen; if(!$archivo && !$r->archivo && !$d['url']) throw new RuntimeException('Debe existir archivo PDF o URL.'); $this->Resolucion_model->actualizar($r->id,$d); $this->session->set_flashdata('success','Resolución actualizada.'); redirect('admin/resoluciones'); }catch(Exception $e){$data['error']=$e->getMessage();}}} $this->render('admin/resoluciones/form',array_merge($data??array(),array('title'=>'Editar resolución','grupos'=>$grupos,'resolucion'=>$r))); }
 public function fecha_resolucion_valida($valor){
  $zona=new DateTimeZone('America/Bogota');
  $fecha=is_string($valor)?DateTimeImmutable::createFromFormat('!Y-m-d',$valor,$zona):FALSE;
  if(!$fecha || $fecha->format('Y-m-d')!==$valor){
   $this->form_validation->set_message('fecha_resolucion_valida','El campo {field} debe contener una fecha válida.'); return FALSE;
  }
  $limite=(new DateTimeImmutable('today',$zona))->modify('+30 days');
  if($fecha>$limite){
   $this->form_validation->set_message('fecha_resolucion_valida','El campo {field} no puede ser posterior al '.$limite->format('d/m/Y').' (30 días desde hoy).'); return FALSE;
  }
  return TRUE;
 }
 private function validar_resolucion(){ $this->exigir_grupo((int)$this->input->post('grupo_id')); $this->form_validation->set_rules('grupo_id','Grupo','required|integer'); $this->form_validation->set_rules('titulo','Título','required|max_length[255]'); $this->form_validation->set_rules('fecha_resolucion','Fecha','required|callback_fecha_resolucion_valida'); if($this->input->post('url')) $this->form_validation->set_rules('url','URL','valid_url|max_length[1000]'); }
}
