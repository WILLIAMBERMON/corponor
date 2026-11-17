<section class="hero-resolutions mb-4"><span class="eyebrow">Consulta pública</span><h1>Acuerdos</h1><p>Consulte los acuerdos publicados por vigencia.</p></section>
<div class="filter-box mb-4">
 <?= form_open('acuerdos',array('method'=>'get')) ?>
 <div class="row g-3 align-items-end">
  <div class="col-lg-8"><label class="form-label" for="acuerdo-titulo">Título</label><input id="acuerdo-titulo" name="titulo" class="form-control" maxlength="150" placeholder="Buscar por palabras del título" value="<?= html_escape($f['titulo']) ?>"></div>
  <div class="col-lg-3"><label class="form-label" for="acuerdo-vigencia">Vigencia</label><select id="acuerdo-vigencia" name="anio" class="form-select"><?php foreach($anios as $item): ?><option value="<?= (int)$item->anio ?>" <?= (int)$item->anio===$anio?'selected':'' ?>><?= (int)$item->anio ?></option><?php endforeach; ?></select></div>
  <div class="col-lg-1 d-grid"><button class="btn btn-corponor" aria-label="Buscar acuerdos"><i class="bi bi-search"></i></button></div>
 </div>
 <p class="text-muted mt-2 mb-0">Las vigencias se presentan de la más reciente a la más antigua. <a href="<?= site_url('acuerdos') ?>">Limpiar filtros</a></p>
 <?= form_close() ?>
</div>
<nav class="year-tabs mb-4" aria-label="Vigencias de acuerdos"><?php foreach($anios as $item): ?><a class="year-tab <?= (int)$item->anio===$anio?'active':'' ?>" href="<?= html_escape(site_url('acuerdos').'?'.http_build_query(array('anio'=>(int)$item->anio,'titulo'=>$f['titulo']))) ?>"><?= (int)$item->anio ?></a><?php endforeach; ?></nav>
<?php if(!$acuerdos): ?><div class="alert alert-light border">No se encontraron acuerdos publicados para la vigencia seleccionada.</div><?php endif; ?>
<?php $grupos=array(); foreach($acuerdos as $a) $grupos[(int)$a->grupo_id][]=$a; ?>
<?php foreach($grupos as $items): $primero=$items[0]; ?>
<section class="group-preview">
 <div class="group-heading mt-4"><h2><?= html_escape($primero->grupo_descripcion) ?></h2></div>
 <div class="row g-3 resolution-grid">
 <?php foreach($items as $a): ?>
  <article class="resolution-card col-md-3 col-sm-6"><div class="row g-0 h-100"><div class="col-12"><div class="p-4">
   <div class="resolution-date"><span class="download-count" title="Visualizaciones y descargas"><i class="bi bi-download"></i> <?= (int)$a->descargas + (int)$a->visualizaciones ?></span></div>
   <h3><?= html_escape($a->titulo) ?></h3>
   <div class="d-flex flex-wrap align-items-center gap-2">
    <a class="btn btn-outline-primary btn-sm" href="<?= site_url('acuerdos/ver/'.$a->token_publico) ?>"><i class="bi bi-file-earmark-pdf"></i> Ver acuerdo</a>
    <a class="btn btn-outline-primary btn-sm" href="<?= site_url('acuerdos/descargar/'.$a->token_publico) ?>"><i class="bi bi-download"></i> Descargar</a>
   </div>
  </div></div></div></article>
 <?php endforeach; ?>
 </div>
</section>
<?php endforeach; ?>
