<?php defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Controller extends CI_Controller {
    public function __construct(){ parent::__construct(); date_default_timezone_set('America/Bogota'); }
    protected function render($view,$data=array(),$layout='admin'){
        if($view==='admin/resoluciones/form' && $this->input->is_ajax_request() && $this->input->method()==='post'){
            $mensaje=trim(strip_tags(validation_errors('',' ')));
            $this->output->set_status_header(422)->set_content_type('application/json')->set_output(json_encode(array(
                'ok'=>FALSE,'error'=>$data['error']??($mensaje ?: 'Revise los datos del formulario.'),
                'csrf_name'=>$this->security->get_csrf_token_name(),'csrf_hash'=>$this->security->get_csrf_hash()
            )));
            return;
        }
        $data['content_view']=$view;
        $this->load->view('layouts/'.$layout,$data);
    }
    protected function require_login(){
        if(!$this->session->userdata('usuario_id')) redirect('login');
        $this->load->model('Usuario_model');
        $u=$this->Usuario_model->una($this->session->userdata('usuario_id'));
        if(!$u || !$u->activo){ $this->session->sess_destroy(); redirect('login'); }
        $this->session->set_userdata(array('nombres'=>$u->nombres,'documento'=>$u->documento,'es_admin'=>(int)$u->es_admin));
    }
    protected function require_admin(){ $this->require_login(); if((int)$this->session->userdata('es_admin')<1) show_error('Acceso no autorizado',403); }
}
