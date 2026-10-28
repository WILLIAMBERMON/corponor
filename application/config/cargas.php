<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Los valores se expresan en MB (1024 * 1024 bytes). Sin variable, manda PHP.
$bytes = static function ($valor) {
    $valor = trim((string) $valor);
    $numero = (float) $valor;
    switch (strtolower(substr($valor, -1))) {
        case 'g': $numero *= 1024;
        case 'm': $numero *= 1024;
        case 'k': $numero *= 1024;
    }
    return max(0, (int) $numero);
};
$config['upload_max'] = $bytes(ini_get('upload_max_filesize'));
$config['post_max'] = $bytes(ini_get('post_max_size'));
$config['limites'] = array();
foreach (array('archivo' => 'UPLOAD_PDF_MAX_MB', 'imagen' => 'UPLOAD_IMAGE_MAX_MB') as $campo => $variable) {
    $valor = getenv($variable);
    if ($valor === FALSE) {
        $valor = $_SERVER[$variable] ?? $_SERVER['REDIRECT_'.$variable] ?? FALSE;
    }
    $limite = is_numeric($valor) && (float) $valor > 0 && is_finite((float) $valor * 1048576)
        && (float) $valor * 1048576 < PHP_INT_MAX
        ? max(1, (int) ((float) $valor * 1048576)) : $config['upload_max'];
    if ($config['upload_max'] > 0) {
        $limite = $limite > 0 ? min($limite, $config['upload_max']) : $config['upload_max'];
    }
    $config['limites'][$campo] = $limite;
}
