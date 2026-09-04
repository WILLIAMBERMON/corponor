<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" type="image/png" href="<?= base_url('assets/images/corponor-favicon.png') ?>"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"><link href="<?= base_url('assets/css/corponor.css') ?>" rel="stylesheet"><title>Actualizar clave</title></head>
<body class="login-page-corponor"><div class="login-card">
    <div class="text-center mb-4"><img class="brand-logo-main" src="<?= base_url('assets/images/corponor-logo.png') ?>" alt="Logotipo institucional"><p class="text-muted">Gestion de Resoluciones</p></div><h2 class="h5">Definir nueva clave</h2>
    <?php if(validation_errors()): ?><div class="alert alert-danger"><?= validation_errors() ?></div><?php endif; ?>
    <?= form_open('restablecer-clave/'.$token) ?><div class="mb-3"><label class="form-label">Nueva clave</label><input type="password" name="clave" class="form-control" minlength="8" required></div><div class="mb-3"><label class="form-label">Confirmar clave</label><input type="password" name="clave2" class="form-control" minlength="8" required></div><button class="btn btn-corponor w-100">Actualizar clave</button><?= form_close() ?>
</div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script></body></html>
