<div class="card"><div class="card-body">
<?php if(validation_errors()): ?><div class="alert alert-danger"><?= validation_errors() ?></div><?php endif; ?>
<?= form_open($usuario?'admin/usuarios/editar/'.(int)$usuario->id:'admin/usuarios/crear') ?>
<div class="row g-3">
<?php foreach(array('nombres'=>'Nombres','documento'=>'Documento','email'=>'Email') as $campo=>$etiqueta): ?>
<div class="col-md-6"><label class="form-label" for="<?= $campo ?>"><?= $etiqueta ?></label><input id="<?= $campo ?>" type="<?= $campo==='email'?'email':'text' ?>" name="<?= $campo ?>" class="form-control" maxlength="<?= $campo==='documento'?30:($campo==='nombres'?150:190) ?>" value="<?= set_value($campo,$usuario?$usuario->$campo:'') ?>" required></div>
<?php endforeach; ?>
<?php if(!$usuario): ?><div class="col-md-6"><label class="form-label">Clave</label><input type="password" name="clave" class="form-control" autocomplete="new-password" minlength="8" required></div><?php endif; ?>
<div class="col-md-6"><label class="form-label" for="es_admin">Rol</label><select class="form-select" id="es_admin" name="es_admin">
<?php foreach(array(0=>'Usuario',1=>'Administrador',2=>'Superadministrador') as $rol=>$nombre): if($rol===2 && !$super) continue; ?>
<option value="<?= $rol ?>" <?= set_select('es_admin',(string)$rol,(int)($usuario->es_admin??0)===$rol) ?>><?= $nombre ?></option>
<?php endforeach; ?></select></div></div>
<button class="btn btn-corponor mt-4">Guardar usuario</button> <a class="btn btn-secondary mt-4" href="<?= site_url('admin/usuarios') ?>">Cancelar</a>
<?= form_close() ?></div></div>
