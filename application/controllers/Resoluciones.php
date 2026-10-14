<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Resoluciones extends MY_Controller {
 public function __construct(){ parent::__construct(); $this->load->model(array('Resolucion_model','Grupo_model','Estadistica_model')); }
 public function index(){
  $f=array('titulo'=>$this->input->get('titulo',TRUE),'fecha'=>$this->input->get('fecha',TRUE),'grupo'=>$this->selecciones('grupo'),'vigencia'=>$this->selecciones('vigencia'),'territorial'=>$this->selecciones_territoriales());
  $anios=$this->Resolucion_model->anios_publicos(); $anioSolicitado=(int)$this->input->get('anio',TRUE); $anio=$anioSolicitado ?: (!empty($anios)?(int)$anios[0]->anio:(int)date('Y'));
  $detalle=!empty($f['titulo']) || !empty($f['fecha']) || !empty($f['grupo']) || !empty($f['vigencia']) || !empty($f['territorial']); $pagina=max(1,(int)$this->input->get('pagina',TRUE)); $porPagina=10; $total=$detalle?$this->Resolucion_model->contar_busqueda($f):0;
  if(!$detalle) $this->Estadistica_model->registrar('visita');
  $resoluciones=$detalle?$this->Resolucion_model->buscar($f,$porPagina,($pagina-1)*$porPagina):array(); $resumen=$detalle?array():$this->Resolucion_model->resumen_por_anio($anio); $filas=$detalle?$resoluciones:array(); foreach($resumen as $grupo) $filas=array_merge($filas,$grupo); $ids=array(); foreach($filas as $fila) $ids[]=(int)$fila->id; $descargas=$this->Estadistica_model->descargas_por_resoluciones($ids); foreach($filas as $fila){ $fila->descargas=(int)($descargas[$fila->id]??0); $fila->token_publico=$this->Resolucion_model->token_publico($fila); }
  $data=array('title'=>'Resoluciones','resoluciones'=>$resoluciones,'resumen'=>$resumen,'territoriales'=>$this->Resolucion_model->territoriales(),'f'=>$f,'anios'=>$anios,'anio'=>$anio,'detalle'=>$detalle,'pagina'=>$pagina,'por_pagina'=>$porPagina,'total'=>$total);
  $this->render('public/resoluciones',$data,'public');
 }
 private function selecciones_territoriales(){
  $permitidas=$this->Resolucion_model->territoriales(); $resultado=array();
  foreach((array)$this->input->get('territorial',TRUE) as $valor) if(is_string($valor) && isset($permitidas[$valor])) $resultado[]=$valor;
  return array_values(array_unique($resultado));
 }
 private function selecciones($campo){
  $resultado=array();
  foreach((array)$this->input->get($campo,TRUE) as $valor) if(is_scalar($valor) && ctype_digit((string)$valor) && (int)$valor>0) $resultado[]=(int)$valor;
  return array_values(array_unique($resultado));
 }
 public function ver($token=NULL){
  $r=$this->Resolucion_model->por_token($token); if(!$r) show_404();
  $this->render('public/documento',array('title'=>$r->titulo,'resolucion'=>$r),'public');
 }
 public function descargar($token=NULL){ $this->entregar_pdf($token,TRUE); }
 public function archivo($token=NULL){ $this->entregar_pdf($token,FALSE); }
 private function entregar_pdf($token,$descargar){
  $r=$this->Resolucion_model->por_token($token); if(!$r) show_404();
  if(!in_array($this->input->method(),array('get','head'),TRUE)) show_error('Método no permitido.',405);
  if(session_status()===PHP_SESSION_ACTIVE) session_write_close();
  $this->load->library('Documento_pdf');
  try { $fp=$this->documento_pdf->abrir($r); }
  catch(Exception $e){ show_error('No fue posible obtener el PDF. Intente más tarde o comuníquese con la entidad.',502); return; }
  $stat=fstat($fp);
  if($this->input->method()==='get') $this->Estadistica_model->registrar('descarga',(int)$r->id,(int)$r->grupo_id);
  header('Content-Type: application/pdf');
  header('Content-Disposition: '.($descargar?'attachment':'inline').'; filename="resolucion.pdf"');
  header('Content-Length: '.$stat['size']);
  header('X-Content-Type-Options: nosniff');
  header('Referrer-Policy: no-referrer');
  header('Cache-Control: private, no-store');
  header('Accept-Ranges: none');
  if($this->input->method()!=='head') fpassthru($fp);
  fclose($fp); exit;
 }
}