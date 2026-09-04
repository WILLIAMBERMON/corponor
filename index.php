<?php
define('ENVIRONMENT', $_SERVER['CI_ENV'] ?? 'development');
switch (ENVIRONMENT) {
    case 'development': error_reporting(-1); ini_set('display_errors', 1); break;
    default: ini_set('display_errors', 0); if (version_compare(PHP_VERSION, '5.3', '>=')) error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED); else error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
}
$system_path = __DIR__ . '/vendor/codeigniter/framework/system';
$application_folder = __DIR__ . '/application';
$view_folder = '';
if (!is_dir($system_path)) { http_response_code(500); exit('Falta CodeIgniter. Ejecute: composer install'); }
$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_path);
define('FCPATH', __DIR__.DIRECTORY_SEPARATOR);
define('SYSDIR', basename(BASEPATH));
define('APPPATH', realpath($application_folder).DIRECTORY_SEPARATOR);
define('VIEWPATH', APPPATH.'views'.DIRECTORY_SEPARATOR);
require_once BASEPATH.'core/CodeIgniter.php';
