<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <h1 class="h4 mb-0"><?= html_escape($resolucion->titulo) ?></h1>
    <a class="btn btn-corponor" href="<?= site_url('resoluciones/descargar/'.$resolucion->token_publico) ?>">Descargar PDF</a>
</div>
<iframe src="<?= site_url('resoluciones/archivo/'.$resolucion->token_publico) ?>" title="Visor de la resolución PDF" referrerpolicy="no-referrer" style="width:100%;height:80vh;border:1px solid #dee2e6;"></iframe>
<p class="text-muted mt-2">Si su navegador no muestra el documento, utilice el botón Descargar PDF.</p>
