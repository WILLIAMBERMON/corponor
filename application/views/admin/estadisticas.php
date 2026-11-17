<?php
$esAcuerdo=$modulo==='acuerdos';$tipo=$esAcuerdo?'acuerdos':'resoluciones';$singular=$esAcuerdo?'Acuerdo':'Resolución';
$params=array_filter($f,function($value){return $value!==NULL && $value!=='' && $value!==0;});$params['modulo']=$modulo;
?>
<div class="d-flex gap-2 mb-3" role="group" aria-label="Tipo de documento">
 <a class="btn <?= !$esAcuerdo?'btn-corponor':'btn-outline-primary' ?>" href="<?= site_url('admin/estadisticas') ?>">Resoluciones</a>
 <a class="btn <?= $esAcuerdo?'btn-corponor':'btn-outline-primary' ?>" href="<?= site_url('admin/estadisticas?modulo=acuerdos') ?>">Acuerdos</a>
</div>
<div class="card mb-4"><div class="card-body"><form method="get" action="<?= site_url('admin/estadisticas') ?>" class="row g-3 align-items-end">
 <input type="hidden" name="modulo" value="<?= $modulo ?>">
 <div class="col-md-3"><label class="form-label">Desde fecha de <?= $singular ?></label><input type="date" name="desde" class="form-control" value="<?= html_escape($f['desde']) ?>"></div>
 <div class="col-md-3"><label class="form-label">Hasta fecha de <?= $singular ?></label><input type="date" name="hasta" class="form-control" value="<?= html_escape($f['hasta']) ?>"></div>
 <div class="col-md-2"><label class="form-label">Vigencia</label><input type="number" name="vigencia" class="form-control" min="1900" max="2100" value="<?= $f['vigencia']?:'' ?>" placeholder="Año"></div>
 <div class="col-md-3"><label class="form-label">Grupo</label><select name="grupo" class="form-select"><option value="">Todos los grupos</option><?php foreach($grupos as $g): ?><option value="<?= (int)$g->id ?>" <?= (int)$f['grupo']===(int)$g->id?'selected':'' ?>><?= html_escape($g->descripcion) ?></option><?php endforeach; ?></select></div>
 <div class="col-md-1 d-grid"><button class="btn btn-corponor" aria-label="Filtrar"><i class="bi bi-search"></i></button></div>
</form></div></div>
<div class="row g-4 mb-4">
 <div class="col-md-<?= $esAcuerdo?4:6 ?>"><div class="card card-corponor"><div class="card-body"><div class="text-muted">Visitas a la página pública</div><div class="display-5 fw-bold"><?= (int)$totales['visitas'] ?></div></div></div></div>
 <?php if($esAcuerdo): ?><div class="col-md-4"><div class="card card-corponor"><div class="card-body"><div class="text-muted">Visualizaciones de PDF</div><div class="display-5 fw-bold"><?= (int)$totales['visualizaciones'] ?></div></div></div></div><?php endif; ?>
 <div class="col-md-<?= $esAcuerdo?4:6 ?>"><div class="card card-corponor"><div class="card-body"><div class="text-muted">Descargas de <?= $tipo ?></div><div class="display-5 fw-bold"><?= (int)$totales['descargas'] ?></div></div></div></div>
</div>
<div class="row g-4 mb-4"><div class="col-lg-6"><div class="card"><div class="card-header fw-semibold">Descargas por grupo</div><div class="card-body"><canvas id="graficoGrupos" height="220"></canvas></div></div></div><div class="col-lg-6"><div class="card"><div class="card-header fw-semibold">Distribución de descargas</div><div class="card-body"><canvas id="graficoDescargas" height="220"></canvas></div></div></div></div>
<div class="d-flex justify-content-between align-items-center mb-3"><h5 class="mb-0">Informe de <?= $tipo ?></h5><div><a class="btn btn-outline-primary me-2" href="<?= html_escape(site_url('admin/estadisticas').'?'.http_build_query(array_merge($params,array('detalle'=>1,'ceros'=>$incluir_ceros?1:0)))) ?>"><i class="bi bi-list-ul"></i> Ver detalle</a><a class="btn btn-success" href="<?= html_escape(site_url('admin/estadisticas/exportar').'?'.http_build_query($params)) ?>"><i class="bi bi-file-earmark-spreadsheet"></i> Descargar CSV</a></div></div>
<?php if($detalle): ?><div class="card mb-3"><div class="card-body py-2"><form method="get" action="<?= site_url('admin/estadisticas') ?>"><input type="hidden" name="detalle" value="1"><?php foreach($params as $key=>$value): ?><input type="hidden" name="<?= html_escape($key) ?>" value="<?= html_escape($value) ?>"><?php endforeach; ?><label class="form-check-label"><input class="form-check-input me-1" type="checkbox" name="ceros" value="1" <?= $incluir_ceros?'checked':'' ?> onchange="this.form.submit()"> Ver <?= $tipo ?> con 0 descargas</label></form></div></div>
<div class="card"><div class="card-body table-responsive"><table id="estadisticasTable" class="table table-hover align-middle datatable"><thead><tr><th><?= $singular ?></th><th>Fecha</th><th>Grupo</th><?php if($esAcuerdo): ?><th>Visualizaciones</th><?php endif; ?><th>Descargas</th></tr></thead><tbody><?php foreach($por_documento as $r): ?><tr><td><?= html_escape($r->titulo) ?></td><td><?= html_escape($esAcuerdo?$r->fecha_acuerdo:$r->fecha_resolucion) ?></td><td><?= html_escape($r->grupo_descripcion) ?></td><?php if($esAcuerdo): ?><td><?= (int)$r->visualizaciones ?></td><?php endif; ?><td><strong><?= (int)$r->descargas ?></strong></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php else: ?><div class="alert alert-light border">El detalle está oculto. Presione <strong>Ver detalle</strong> para cargar los documentos.</div><?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script><script>
<?php $labels=array();$descargas=array();foreach($por_grupo as $g){$labels[]=$g->descripcion;$descargas[]=(int)$g->descargas;} ?>
const labels=<?= json_encode($labels,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
const descargas=<?= json_encode($descargas) ?>;
new Chart(document.getElementById('graficoGrupos'),{type:'bar',data:{labels:labels,datasets:[{label:'Descargas',data:descargas,backgroundColor:'#a8c63d'}]},options:{responsive:true,scales:{x:{ticks:{autoSkip:false}}}}});
new Chart(document.getElementById('graficoDescargas'),{type:'doughnut',data:{labels:labels,datasets:[{data:descargas,backgroundColor:['#2868a9','#a8c63d','#78c9ed','#123e69','#789521']}]},options:{responsive:true}});
</script>
