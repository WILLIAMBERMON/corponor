<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Auth extends MY_Controller {
 public function __construct(){ parent::__construct(); $this->load->model(array('Usuario_model','Token_model')); }
 public function login(){
  if($this->session->userdata('usuario_id')) redirect('admin');
  if($this->input->method()==='post'){
   $this->form_validation->set_rules('documento','Documento','required|trim|max_length[30]'); $this->form_validation->set_rules('clave','Clave','required');
   if($this->form_validation->run()){
    $u=$this->Usuario_model->por_documento($this->input->post('documento',TRUE));
    if($u && password_verify($this->input->post('clave'),$u->clave)){
      $this->session->sess_regenerate(TRUE); $this->session->set_userdata(array('usuario_id'=>$u->id,'nombres'=>$u->nombres,'documento'=>$u->documento,'es_admin'=>(int)$u->es_admin)); redirect('admin');
    } $data['error']='Documento o clave incorrectos.';
   }
  } $this->load->view('auth/login',$data??array());
 }
 public function logout(){ $this->session->sess_destroy(); redirect('login'); }
 public function forgot(){
  if($this->input->method()==='post'){
   $this->form_validation->set_rules('email','Correo','required|valid_email');
   if($this->form_validation->run()){
    $u=$this->Usuario_model->por_email($this->input->post('email',TRUE));
    if($u){ $token=bin2hex(random_bytes(32)); $this->Token_model->crear($u->id,$token); $url=site_url('restablecer-clave/'.$token);
      $this->email->from(getenv('MAIL_FROM') ?: 'no-reply@corponor.gov.co','CORPONOR - Resoluciones'); $this->email->to($u->email); $this->email->subject('Restablecimiento de clave'); $this->email->message('<p>Hola '.html_escape($u->nombres).',</p><p>Para restablecer su clave use este enlace (válido por 1 hora):</p><p><a href="'.$url.'">'.$url.'</a></p>'); $this->email->send(); }
    $this->session->set_flashdata('success','Si el correo está registrado, recibirá un enlace de restablecimiento.'); redirect('recuperar-clave');
   }
  } $this->load->view('auth/forgot');
 }
 public function reset($token){
  $r=$this->Token_model->valido($token); if(!$r) show_error('El enlace es inválido o expiró.',400);
  if($this->input->method()==='post'){
   $this->form_validation->set_rules('clave','Nueva clave','required|min_length[8]'); $this->form_validation->set_rules('clave2','Confirmación','required|matches[clave]');
   if($this->form_validation->run()){ $this->Usuario_model->cambiar_clave($r->usuario_id,$this->input->post('clave')); $this->Token_model->usar($r->id); $this->session->set_flashdata('success','Clave actualizada. Ya puede iniciar sesión.'); redirect('login'); }
  } $this->load->view('auth/reset',array('token'=>$token));
 }
}
