<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-end mb-3">
 <button class="btn btn-corponor" type="button" data-bs-toggle="collapse" data-bs-target="#nuevoGrupoAcuerdo" aria-expanded="false" aria-controls="nuevoGrupoAcuerdo"><i class="bi bi-plus-lg"></i> Crear grupo</button>
</div>
<?php if($this->session->flashdata('grupo_error')): ?><div class="alert alert-danger"><?= html_escape($this->session->flashdata('grupo_error')) ?></div><?php endif; ?>
<?php if($this->session->flashdata('grupo_ok')): ?><div class="alert alert-success"><?= html_escape($this->session->flashdata('grupo_ok')) ?></div><?php endif; ?>
<div class="collapse mb-3" id="nuevoGrupoAcuerdo"><div class="card"><div class="card-body">
 <h2 class="h5">Crear grupo de acuerdos</h2>
 <?= form_open('admin/grupos-acuerdos/crear') ?>
 <label class="form-label" for="descripcionGrupo">Descripción</label>
 <textarea class="form-control" id="descripcionGrupo" name="descripcion" rows="2" maxlength="255" required></textarea>
 <div class="form-text">El consecutivo se asigna automáticamente.</div>
 <button class="btn btn-corponor mt-3" type="submit">Guardar grupo</button>
 <?= form_close() ?>
</div></div></div>
<div class="card"><div class="card-body table-responsive">
 <table id="gruposAcuerdosTable" class="table table-hover datatable">
 <thead><tr><th>Consecutivo</th><th>Descripción</th><th>Fecha creación</th><th>Acciones</th></tr></thead>
 <tbody>
 <?php foreach($grupos as $g): ?>
 <tr>
 <td><span class="badge bg-corponor-blue">GA-<?= str_pad((int)$g->consecutivo,4,'0',STR_PAD_LEFT) ?></span></td>
 <td><?= html_escape($g->descripcion) ?></td>
 <td><?= html_escape($g->creado_en) ?></td>
 <td>
 <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/acuerdos/crear').'?grupo_id='.(int)$g->id ?>">Agregar acuerdos</a>
 <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('admin/grupos-acuerdos/editar/'.(int)$g->id) ?>">Editar</a>
 <?= form_open('admin/grupos-acuerdos/eliminar/'.(int)$g->id,array('class'=>'d-inline','onsubmit'=>"return confirm('¿Eliminar este grupo? Solo se permite si no contiene acuerdos.');")) ?>
 <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i> Eliminar</button>
 <?= form_close() ?>
 </td>
 </tr>
 <?php endforeach; ?>
 </tbody></table>
</div></div>
