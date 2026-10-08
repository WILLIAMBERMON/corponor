<div class="d-flex justify-content-between align-items-center mb-3"><p class="mb-0 text-muted">La revisión no modifica los registros. Mantenga esta página abierta hasta finalizar.</p><a class="btn btn-outline-primary" href="<?= site_url('admin/enlaces') ?>">Volver a revisar</a></div>
<div id="revision-estado" class="alert alert-info" role="status" aria-live="polite">Preparando revisión de <?= (int)$total ?> resoluciones...</div>
<button id="revision-reintentar" class="btn btn-outline-primary mb-3" hidden>Reintentar desde el último registro</button>
<noscript><div class="alert alert-warning">Active JavaScript para revisar los enlaces.</div></noscript>
<div class="card"><div class="card-body table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Resolución</th><th>Grupo</th><th>Origen</th><th>Estado</th><th>Detalle</th><th></th></tr></thead><tbody id="revision-resultados"></tbody></table></div></div>
<script>
(function () {
    const estado = document.getElementById('revision-estado');
    const filas = document.getElementById('revision-resultados');
    const reintentar = document.getElementById('revision-reintentar');
    const endpoint = <?= json_encode(site_url('admin/enlaces/lote'),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;
    const editar = <?= json_encode(site_url('admin/resoluciones/editar/'),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;
    let antes = <?= (int)$antes ?>, revisados = 0, novedades = 0;
    function agregar(item) {
        const r = item.resolucion, v = item.resultado;
        const fila = document.createElement('tr');
        [r.titulo + ' (' + r.fecha_resolucion + ')', r.grupo_descripcion, v.tipo, v.estado, v.detalle].forEach(function (texto) {
            const celda = document.createElement('td'); celda.textContent = texto; fila.appendChild(celda);
        });
        const celda = document.createElement('td'), enlace = document.createElement('a');
        enlace.href = editar.replace(/\/$/, '') + '/' + Number(r.id); enlace.textContent = 'Corregir';
        enlace.className = 'btn btn-sm btn-outline-primary'; enlace.target = '_blank'; enlace.rel = 'noopener';
        celda.appendChild(enlace); fila.appendChild(celda); filas.appendChild(fila);
    }
    async function revisar() {
        reintentar.hidden = true; estado.className = 'alert alert-info';
        try {
            while (true) {
                estado.textContent = 'Revisadas: ' + revisados + '. Novedades: ' + novedades + '. Revisando siguiente enlace...';
                const respuesta = await fetch(endpoint + '?antes=' + antes, {credentials: 'same-origin', cache: 'no-store'});
                if (!respuesta.ok || respuesta.redirected) throw new Error('Respuesta inválida');
                const datos = await respuesta.json();
                if (typeof datos.fin !== 'boolean') throw new Error('Respuesta inválida');
                if (datos.fin) {
                    estado.className = novedades ? 'alert alert-warning' : 'alert alert-success';
                    estado.textContent = 'Revisión finalizada. Se revisaron ' + revisados + ' resoluciones. Novedades: ' + novedades + '.';
                    break;
                }
                if (!Number.isInteger(datos.antes) || datos.antes >= antes || datos.antes < 1) throw new Error('Cursor inválido');
                if (datos.novedad) { agregar(datos.novedad); novedades++; }
                antes = datos.antes; revisados++;
            }
        } catch (error) {
            estado.className = 'alert alert-warning';
            estado.textContent = 'Revisión interrumpida tras ' + revisados + ' resoluciones. Se conservan los resultados. Compruebe su conexión y que la sesión siga activa antes de reintentar.';
            reintentar.hidden = false;
        }
    }
    reintentar.addEventListener('click', revisar);
    revisar();
})();
</script>