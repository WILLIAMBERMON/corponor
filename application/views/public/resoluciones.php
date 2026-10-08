<section class="hero-resolutions mb-4">
    <span class="eyebrow">Consulta pública</span>
    <h1>Resoluciones</h1>
    <p>Consulte los actos publicados por año y grupo.</p>
</section>
<div class="filter-box mb-4">
    <?= form_open('resoluciones',array('method'=>'get')) ?>
    <div class="row g-3 align-items-end">
        <div class="col-lg-6"><label class="form-label">Título</label><input name="titulo" class="form-control" value="<?= html_escape($f['titulo']??'') ?>" placeholder="Buscar por palabras del título"></div>
        <div class="col-lg-6"><label class="form-label">Fecha</label><input type="date" name="fecha" class="form-control" value="<?= html_escape($f['fecha']??'') ?>"></div>
        </div>
    <div class="row g-3 align-items-start mt-0">
        <div class="col-lg-7"><label class="form-label" for="grupos">Grupos</label><select id="grupos" data-placeholder="Todos los grupos" name="grupo[]" class="form-select resolution-select2" multiple><?php foreach($grupos as $g): ?><option value="<?= (int)$g->id ?>" <?= in_array((int)$g->id,$f['grupo'],TRUE)?'selected':'' ?>><?= html_escape($g->descripcion) ?></option><?php endforeach; ?></select></div>
        <div class="col-lg-4"><label class="form-label" for="vigencias">Vigencias</label><select id="vigencias" data-placeholder="Todas las vigencias" name="vigencia[]" class="form-select resolution-select2" multiple><?php foreach($anios as $item): ?><option value="<?= (int)$item->anio ?>" <?= in_array((int)$item->anio,$f['vigencia'],TRUE)?'selected':'' ?>><?= (int)$item->anio ?></option><?php endforeach; ?></select></div>
        <div class="col-lg-1 d-grid"><span class="form-label d-none d-lg-block" aria-hidden="true">&nbsp;</span><button class="btn btn-corponor resolution-search-button" title="Buscar" aria-label="Buscar resoluciones"><i class="bi bi-search"></i></button></div>
    </div>
    <p class="text-muted mt-2 mb-0">La vigencia corresponde al año de la fecha de resolución. Sin selección se incluyen todos. Puede buscar y seleccionar varias opciones en cada filtro. <a href="<?= site_url('resoluciones') ?>">Limpiar filtros</a></p>
    <?= form_close() ?>
</div>
<?php if(!$detalle): ?>
<nav class="year-tabs mb-4" aria-label="Años de resoluciones"><?php foreach($anios as $item): ?><a class="year-tab <?= ((int)$item->anio===$anio)?'active':'' ?>" href="<?= site_url('resoluciones?anio='.(int)$item->anio) ?>"><?= (int)$item->anio ?></a><?php endforeach; ?></nav>
<?php if(!$resumen): ?><div class="alert alert-light border">No se encontraron resoluciones para el año seleccionado.</div><?php endif; ?>
<?php foreach($resumen as $grupo): $primera=$grupo[0]; ?>
    <div class="group-preview"><div class="group-heading mt-4"><h2><?= html_escape($primera->grupo_descripcion) ?></h2></div>
    <div class="row g-3 resolution-grid"><?php foreach($grupo as $r): ?><article class="resolution-card col-md-3 col-sm-6"><div class="row g-0 h-100"><?php if($r->imagen): ?><div class="col-12"><img src="<?= base_url($r->imagen) ?>" class="resolution-image" alt="Vista previa"></div><?php endif; ?><div class="col-12"><div class="p-4"><div class="resolution-date"><i class="bi bi-calendar3"></i> <?= date('d/m/Y',strtotime($r->fecha_resolucion)) ?> <span class="download-count"><i class="bi bi-download"></i> <?= (int)$r->descargas ?></span></div><h3><?= html_escape($r->titulo) ?></h3><div class="d-flex align-items-center gap-2"><a class="btn btn-outline-primary btn-sm" href="<?= site_url('resoluciones/descargar/'.(int)$r->id) ?>" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf"></i> Ver resolución</a><a class="btn btn-outline-primary btn-sm" href="<?= $r->archivo ? site_url('resoluciones/archivo/'.(int)$r->id) : site_url('resoluciones/descargar/'.(int)$r->id) ?>" download title="Descargar archivo" aria-label="Descargar archivo"><i class="bi bi-download" aria-hidden="true"></i> Descargar</a></div></div></div></div></article><?php endforeach; ?></div>
    <div class="group-more"><a class="btn btn-corponor btn-sm" href="<?= site_url('resoluciones?grupo='.(int)$primera->grupo_id) ?>">Ver todas las resoluciones del grupo <i class="bi bi-arrow-right"></i></a></div></div>
<?php endforeach; ?>
<?php else: ?>
    <div class="results-heading"><h2>Resultados de la consulta</h2><a href="<?= site_url('resoluciones') ?>">Ver resumen por año</a></div>
    <?php if(!$resoluciones): ?><div class="alert alert-light border">No se encontraron resoluciones con los filtros seleccionados.</div><?php endif; ?>
    <?php $actual=null; foreach($resoluciones as $r): ?><?php if($actual!==$r->grupo_id): $actual=$r->grupo_id; if($actual!==$resoluciones[0]->grupo_id): ?></div><?php endif; ?><div class="group-heading mt-4"><h2><?= html_escape($r->grupo_descripcion) ?></h2></div><div class="row g-3 resolution-grid"><?php endif; ?><article class="resolution-card col-md-3 col-sm-6"><div class="p-4"><div class="resolution-date"><i class="bi bi-calendar3"></i> <?= date('d/m/Y',strtotime($r->fecha_resolucion)) ?> <span class="download-count"><i class="bi bi-download"></i> <?= (int)$r->descargas ?></span></div><h3><?= html_escape($r->titulo) ?></h3><div class="d-flex align-items-center gap-2"><a class="btn btn-outline-primary btn-sm" href="<?= site_url('resoluciones/descargar/'.(int)$r->id) ?>" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf"></i> Ver resolución</a><a class="btn btn-outline-primary btn-sm" href="<?= $r->archivo ? site_url('resoluciones/archivo/'.(int)$r->id) : site_url('resoluciones/descargar/'.(int)$r->id) ?>" download title="Descargar archivo" aria-label="Descargar archivo"><i class="bi bi-download" aria-hidden="true"></i> Descargar</a></div></div></article><?php endforeach; ?><?php if($resoluciones): ?></div><?php endif; ?>
    <?php if($total>$por_pagina): $paginas=(int)ceil($total/$por_pagina); $parametros=array_filter($f,function($valor){ return $valor!==NULL && $valor!==''; }); ?>
    <nav class="public-pagination" aria-label="Paginación de resoluciones"><span>Mostrando página <?= (int)$pagina ?> de <?= $paginas ?></span><div><?php for($i=1;$i<=$paginas;$i++): $url=site_url('resoluciones').'?'.http_build_query(array_merge($parametros,array('pagina'=>$i))); ?><a class="<?= $i===$pagina?'active':'' ?>" href="<?= html_escape($url) ?>"><?= $i ?></a><?php endfor; ?></div></nav>
    <?php endif; ?>
<?php endif; ?>
