<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Página no encontrada</title>
<style>
body { background:#f4f7f8; margin:40px; font:15px/1.5 Arial,sans-serif; color:#20384a; }
#container { max-width:680px; margin:10vh auto; padding:28px; background:#fff; border-top:4px solid #a8c63d; border-radius:8px; box-shadow:0 4px 18px rgba(20,55,75,.12); }
h1 { margin-top:0; font-size:24px; }
a { color:#2868a9; }
</style>
</head>
<body>
<div id="container">
<h1><?php echo isset($heading) ? $heading : 'Página no encontrada'; ?></h1>
<p><?php echo isset($message) ? $message : 'La página solicitada no existe.'; ?></p>
</div>
</body>
</html>
