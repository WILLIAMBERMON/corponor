<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="card"><div class="card-body">
 <?php if($error): ?><div class="alert alert-danger"><?= html_escape($error) ?></div><?php endif; ?>
 <?= form_open('admin/grupos-acuerdos/editar/'.(int)$grupo->id) ?>
 <label class="form-label" for="descripcionGrupo">Descripción del grupo</label>
 <textarea class="form-control" id="descripcionGrupo" name="descripcion" rows="3" maxlength="255" required><?= html_escape($this->input->method()==='post' ? $this->input->post('descripcion',TRUE) : $grupo->descripcion) ?></textarea>
 <div class="form-text">Consecutivo: GA-<?= str_pad((int)$grupo->consecutivo,4,'0',STR_PAD_LEFT) ?></div>
 <button class="btn btn-corponor mt-3" type="submit">Guardar cambios</button>
 <a class="btn btn-outline-secondary mt-3" href="<?= site_url('admin/grupos-acuerdos') ?>">Cancelar</a>
 <?= form_close() ?>
</div></div>
