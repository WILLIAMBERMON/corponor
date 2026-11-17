<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="card">
  <div class="card-body">
    <p class="mb-3">Usuario: <strong><?= html_escape($usuario->nombres) ?></strong> (<?= html_escape($usuario->documento) ?>)</p>
    <?= form_open('admin/usuarios/permisos-acuerdos/'.(int)$usuario->id) ?>
    <h2 class="h6 mb-3">Permisos de administración</h2>
    <div class="row g-3 mb-4">
      <div class="col-md-6 col-lg-4">
        <label class="border rounded p-3 d-block h-100">
          <input type="checkbox" name="administrar_grupos" value="1" <?= $permiso && $permiso->administrar_grupos ? 'checked' : '' ?>>
          Administrar grupos de acuerdos
        </label>
      </div>
      <div class="col-md-6 col-lg-4">
        <label class="border rounded p-3 d-block h-100">
          <input type="checkbox" name="gestionar_acuerdos" value="1" <?= $permiso && $permiso->gestionar_acuerdos ? 'checked' : '' ?>>
          Gestionar acuerdos
        </label>
      </div>
    </div>
    <hr>
    <h2 class="h6 my-3">Grupos de acuerdos asignados</h2>
    <div class="row g-3">
      <?php foreach ($grupos as $g): ?>
        <div class="col-md-6 col-lg-4">
          <label class="border rounded p-3 d-block h-100">
            <input type="checkbox" name="grupos[]" value="<?= (int)$g->id ?>" <?= in_array((int)$g->id, $asignados, TRUE) ? 'checked' : '' ?>>
            <?= html_escape($g->descripcion) ?>
          </label>
        </div>
      <?php endforeach; ?>
    </div>
    <button type="submit" class="btn btn-corponor mt-4">Guardar permisos</button>
    <?= form_close() ?>
  </div>
</div>
