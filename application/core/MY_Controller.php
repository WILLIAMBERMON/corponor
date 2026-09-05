<?php defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Controller extends CI_Controller {
    public function __construct(){ parent::__construct(); date_default_timezone_set('America/Bogota'); }
    protected function render($view,$data=array(),$layout='admin'){
        $data['content_view']=$view;
        $this->load->view('layouts/'.$layout,$data);
    }
    protected function require_login(){ if(!$this->session->userdata('usuario_id')) redirect('login'); }
    protected function require_admin(){ $this->require_login(); if((string)$this->session->userdata('documento')!=='1000000000') show_error('Acceso no autorizado',403); }
}
