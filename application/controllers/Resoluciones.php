<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Resoluciones extends MY_Controller {
 public function __construct(){ parent::__construct(); $this->load->model(array('Resolucion_model','Grupo_model','Estadistica_model')); }
 public function index(){
  $f=array('titulo'=>$this->input->get('titulo',TRUE),'fecha'=>$this->input->get('fecha',TRUE),'grupo'=>$this->selecciones('grupo'),'vigencia'=>$this->selecciones('vigencia'));
  $anios=$this->Resolucion_model->anios_publicos(); $anioSolicitado=(int)$this->input->get('anio',TRUE); $anio=$anioSolicitado ?: (!empty($anios)?(int)$anios[0]->anio:(int)date('Y'));
  $detalle=!empty($f['titulo']) || !empty($f['fecha']) || !empty($f['grupo']) || !empty($f['vigencia']); $pagina=max(1,(int)$this->input->get('pagina',TRUE)); $porPagina=10; $total=$detalle?$this->Resolucion_model->contar_busqueda($f):0;
  if(!$detalle) $this->Estadistica_model->registrar('visita');
  $resoluciones=$detalle?$this->Resolucion_model->buscar($f,$porPagina,($pagina-1)*$porPagina):array(); $resumen=$detalle?array():$this->Resolucion_model->resumen_por_anio($anio); $filas=$detalle?$resoluciones:array(); foreach($resumen as $grupo) $filas=array_merge($filas,$grupo); $ids=array(); foreach($filas as $fila) $ids[]=(int)$fila->id; $descargas=$this->Estadistica_model->descargas_por_resoluciones($ids); foreach($filas as $fila) $fila->descargas=(int)($descargas[$fila->id]??0);
  $data=array('title'=>'Resoluciones','resoluciones'=>$resoluciones,'resumen'=>$resumen,'grupos'=>$this->Grupo_model->todos(),'f'=>$f,'anios'=>$anios,'anio'=>$anio,'detalle'=>$detalle,'pagina'=>$pagina,'por_pagina'=>$porPagina,'total'=>$total);
  $this->render('public/resoluciones',$data,'public');
 }
 private function selecciones($campo){
  $resultado=array();
  foreach((array)$this->input->get($campo,TRUE) as $valor) if(is_scalar($valor) && ctype_digit((string)$valor) && (int)$valor>0) $resultado[]=(int)$valor;
  return array_values(array_unique($resultado));
 }
 public function descargar($id){ $r=$this->Resolucion_model->una((int)$id); if(!$r || !$r->activo) show_404(); $this->Estadistica_model->registrar('descarga',(int)$r->id,(int)$r->grupo_id); $destino=$r->archivo?base_url($r->archivo):$r->url; redirect($destino); }
 public function archivo($id){
  $r=$this->Resolucion_model->una((int)$id); if(!$r || !$r->activo || !$r->archivo) show_404();
  $base=realpath(FCPATH.'uploads/resoluciones'); $ruta=realpath(FCPATH.$r->archivo);
  if(!$base || !$ruta || strpos($ruta,$base.DIRECTORY_SEPARATOR)!==0 || !is_file($ruta) || !is_readable($ruta)) show_404();
  $this->Estadistica_model->registrar('descarga',(int)$r->id,(int)$r->grupo_id);
  $this->load->helper('download'); force_download($ruta,NULL,TRUE);
 }
}
