<?php defined('BASEPATH') OR exit('No direct script access allowed');
$config['protocol'] = getenv('MAIL_PROTOCOL') ?: 'smtp';
$config['smtp_host'] = getenv('MAIL_HOST') ?: 'smtp.example.com';
$config['smtp_user'] = getenv('MAIL_USER') ?: '';
$config['smtp_pass'] = getenv('MAIL_PASS') ?: '';
$config['smtp_port'] = (int)(getenv('MAIL_PORT') ?: 587);
$config['smtp_crypto'] = getenv('MAIL_CRYPTO') ?: 'tls';
$config['mailtype'] = 'html'; $config['charset']='utf-8'; $config['newline']="\r\n"; $config['crlf']="\r\n";
