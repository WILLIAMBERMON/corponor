<div class="card"><div class="card-body">
<p>Restablecer la clave de <strong><?= html_escape($usuario->nombres) ?></strong>.</p>
<?php if(validation_errors()): ?><div class="alert alert-danger"><?= validation_errors() ?></div><?php endif; ?>
<?= form_open('admin/usuarios/clave/'.(int)$usuario->id) ?>
<div class="mb-3"><label class="form-label">Nueva clave</label><input type="password" class="form-control" name="clave" minlength="8" autocomplete="new-password" required></div>
<div class="mb-3"><label class="form-label">Confirmar clave</label><input type="password" class="form-control" name="clave2" minlength="8" autocomplete="new-password" required></div>
<button class="btn btn-corponor">Guardar clave</button>
<?= form_close() ?></div></div>
