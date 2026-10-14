<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Documento_pdf {
 public function abrir($r){
  if($r->archivo){
   $base=realpath(FCPATH.'uploads/resoluciones'); $ruta=realpath(FCPATH.$r->archivo);
   if(!$base || !$ruta || strpos($ruta,$base.DIRECTORY_SEPARATOR)!==0 || !is_file($ruta) || !is_readable($ruta)) throw new RuntimeException('Archivo no disponible.');
   $fp=fopen($ruta,'rb');
  } else {
   $fp=$this->remoto($r->url);
  }
  if(!$fp) throw new RuntimeException('Archivo no disponible.');
  $inicio=fread($fp,1024); rewind($fp);
  if(strpos($inicio,'%PDF-')===FALSE){ fclose($fp); throw new RuntimeException('El origen no contiene un PDF válido.'); }
  return $fp;
 }
 private function remoto($url){
  $ci=get_instance(); $ci->config->load('documentos');
  $hosts=$ci->config->item('documentos_hosts'); $max=(int)$ci->config->item('documentos_max_bytes');
  if(!function_exists('curl_init')) throw new RuntimeException('Servicio no disponible.');
  $partes=parse_url($url);
  if(!$partes || !in_array(strtolower($partes['scheme']??''),array('http','https'),TRUE) || isset($partes['user']) || isset($partes['pass'])) throw new RuntimeException('Origen no permitido.');
  $host=strtolower($partes['host']??''); $puerto=$partes['port']??($partes['scheme']==='https'?443:80);
  if(!in_array($host,$hosts,TRUE) || $puerto!==($partes['scheme']==='https'?443:80)) throw new RuntimeException('Origen no permitido.');
  $ips=gethostbynamel($host);
  if(!$ips) throw new RuntimeException('Origen no disponible.');
  foreach($ips as $ip) if(!filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE)) throw new RuntimeException('Origen no permitido.');
  $fp=tmpfile(); if(!$fp) throw new RuntimeException('Servicio no disponible.');
  $ch=curl_init($url); $bytes=0;
  curl_setopt_array($ch,array(
   CURLOPT_FOLLOWLOCATION=>FALSE, CURLOPT_CONNECTTIMEOUT=>5, CURLOPT_TIMEOUT=>20,
   CURLOPT_PROTOCOLS=>CURLPROTO_HTTP|CURLPROTO_HTTPS, CURLOPT_PROXY=>'',
   CURLOPT_RESOLVE=>array($host.':'.$puerto.':'.$ips[0]),
   CURLOPT_SSL_VERIFYPEER=>TRUE, CURLOPT_SSL_VERIFYHOST=>2,
   CURLOPT_USERAGENT=>'Corponor Documentos/1.0',
   CURLOPT_WRITEFUNCTION=>function($ch,$contenido) use ($fp,$max,&$bytes){
    $bytes+=strlen($contenido); if($bytes>$max) return 0;
    return fwrite($fp,$contenido);
   }
  ));
  $ok=curl_exec($ch); $estado=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
  // No se reenvían redirecciones ni cabeceras del servidor de origen al cliente.
  if(!$ok || $estado!==200){ fclose($fp); throw new RuntimeException('No fue posible obtener el PDF.'); }
  rewind($fp); return $fp;
 }
}
