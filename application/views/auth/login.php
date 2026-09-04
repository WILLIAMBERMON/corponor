<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" type="image/png" href="<?= base_url('assets/images/corponor-favicon.png') ?>"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"><link href="<?= base_url('assets/css/corponor.css') ?>" rel="stylesheet"><title>Ingreso</title></head>
<body class="login-page-corponor"><div class="login-card">
    <div class="text-center mb-4"><img class="brand-logo-main" src="<?= base_url('assets/images/corponor-logo.png') ?>" alt="Logotipo institucional"><p class="text-muted">Gestion de Resoluciones</p></div>
    <?php if(validation_errors()): ?><div class="alert alert-danger"><?= validation_errors() ?></div><?php endif; ?>
    <?php if(!empty($error)): ?><div class="alert alert-danger"><?= html_escape($error) ?></div><?php endif; ?>
    <?php if($this->session->flashdata('success')): ?><div class="alert alert-success"><?= html_escape($this->session->flashdata('success')) ?></div><?php endif; ?>
    <?= form_open('login') ?><div class="mb-3"><label class="form-label">Documento</label><input class="form-control" name="documento" value="<?= set_value('documento') ?>" required autofocus></div><div class="mb-3"><label class="form-label">Clave</label><input type="password" class="form-control" name="clave" required></div><button class="btn btn-corponor w-100">Ingresar</button><?= form_close() ?>
    <div class="d-flex justify-content-between mt-3 small"><a href="<?= site_url('recuperar-clave') ?>">Olvido su clave?</a><a href="<?= site_url('resoluciones') ?>">Consulta publica</a></div>
</div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script></body></html>
