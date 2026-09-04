<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" type="image/png" href="<?= base_url('assets/images/corponor-favicon.png') ?>"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"><link href="<?= base_url('assets/css/corponor.css') ?>" rel="stylesheet"><title>Recuperar clave</title></head>
<body class="login-page-corponor"><div class="login-card">
    <div class="text-center mb-4"><img class="brand-logo-main" src="<?= base_url('assets/images/corponor-logo.png') ?>" alt="Logotipo institucional"><p class="text-muted">Gestion de Resoluciones</p></div><h2 class="h5">Restablecer clave</h2><p class="text-muted small">Ingrese el correo asociado al usuario.</p>
    <?php if(validation_errors()): ?><div class="alert alert-danger"><?= validation_errors() ?></div><?php endif; ?><?php if($this->session->flashdata('success')): ?><div class="alert alert-success"><?= html_escape($this->session->flashdata('success')) ?></div><?php endif; ?>
    <?= form_open('recuperar-clave') ?><div class="mb-3"><label class="form-label">Correo</label><input type="email" name="email" class="form-control" required></div><button class="btn btn-corponor w-100">Enviar enlace</button><?= form_close() ?><a class="d-block text-center mt-3 small" href="<?= site_url('login') ?>">Volver al ingreso</a>
</div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script></body></html>
