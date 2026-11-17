<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <h1 class="h4 mb-0"><?= html_escape($acuerdo->titulo) ?></h1>
    <a class="btn btn-corponor" href="<?= site_url('acuerdos/descargar/'.$acuerdo->token_publico) ?>">Descargar PDF</a>
</div>
<iframe src="<?= html_escape(site_url('acuerdos/archivo/'.$acuerdo->token_publico)) ?>" title="Visor del acuerdo PDF" referrerpolicy="no-referrer" style="width:100%;height:80vh;border:1px solid #dee2e6;"></iframe>
<p class="text-muted mt-2">Si su navegador no muestra el documento, utilice el botón Descargar PDF.</p>
