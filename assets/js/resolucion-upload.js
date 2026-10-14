(function () {
    'use strict';
    const form = document.getElementById('resolucion-upload');
    if (!form) return;
    const status = document.getElementById('upload-status');
    const bar = document.getElementById('upload-progress');
    const message = document.getElementById('upload-message');
    let busy = false;
    function progress(value) {
        bar.style.width = value + '%'; bar.textContent = value + '%';
        bar.setAttribute('aria-valuenow', value);
    }
    function unlock() {
        busy = false;
        form.querySelectorAll('button').forEach(button => { button.disabled = false; });
    }
    window.addEventListener('beforeunload', function (event) {
        if (busy) { event.preventDefault(); event.returnValue = ''; }
    });
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (busy || !form.reportValidity()) return;
        busy = true; status.hidden = false; progress(0);
        message.className = 'mt-2 text-muted'; message.textContent = 'Comprobando sesión y límites del servidor...';
        form.querySelectorAll('button').forEach(button => { button.disabled = true; });
        try {
            const response = await fetch(form.dataset.config, {credentials: 'same-origin', cache: 'no-store', signal: AbortSignal.timeout(20000)});
            if (!response.ok || response.redirected) throw new Error('La sesión no está disponible. Inicie sesión en otra pestaña y vuelva a intentar.');
            const config = await response.json();
            if (!config.csrf_name || !config.csrf_hash) throw new Error('No se pudo validar la sesión.');
            const data = new FormData(form);
            data.set(config.csrf_name, config.csrf_hash);
            let size = 0;
            for (const [field, value] of data.entries()) {
                if (value instanceof File) {
                    const max = field === 'imagen' ? 4 : 16;
                    if (value.size > max * 1024 * 1024) throw new Error('El archivo ' + value.name + ' supera el límite de ' + max + ' MB.');
                    if (config.upload_max > 0 && value.size > config.upload_max) throw new Error('PHP permite solo ' + (config.upload_max / 1048576).toFixed(1) + ' MB por archivo. Ajuste upload_max_filesize en el hosting.');
                    size += value.size;
                } else size += new Blob([value]).size;
            }
            if (config.post_max > 0 && size + 65536 > config.post_max) throw new Error('El formulario supera el límite de PHP. Aumente post_max_size en el hosting (recomendado: 24M).');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.timeout = 600000;
            let lastActivity = Date.now();
            const monitor = window.setInterval(function () {
                if (Date.now() - lastActivity > 20000) message.textContent = 'Sin avance durante ' + Math.floor((Date.now() - lastActivity) / 1000) + ' segundos. Esperando al servidor; no vuelva a enviar todavía.';
            }, 5000);
            xhr.upload.onprogress = function (e) {
                lastActivity = Date.now();
                if (e.lengthComputable) {
                    const value = Math.round(e.loaded * 100 / e.total); progress(value);
                    message.textContent = value >= 100 ? 'Archivo enviado. El servidor está validando y guardando la resolución...' : 'Cargando archivos: ' + value + '%';
                }
            };
            xhr.onload = function () {
                let result;
                try { result = JSON.parse(xhr.responseText); } catch (e) { result = null; }
                if (xhr.status >= 200 && xhr.status < 300 && result && result.ok === true) {
                    busy = false; window.location.assign(result.redirect); return;
                }
                message.className = 'mt-2 text-danger';
                if (xhr.status === 403) message.textContent = 'El servidor rechazó la solicitud de seguridad (403). Los datos se conservan. Reintente para renovar el token; si persiste, revise la sesión, las cookies y los límites PHP del hosting.';
                else if (xhr.status === 413) message.textContent = 'El servidor rechazó el tamaño de la carga (413). Revise los límites de PHP y del servidor web.';
                else message.textContent = (result && result.error) || 'No se pudo confirmar el guardado. Compruebe la sesión y revise el listado en otra pestaña antes de reenviar para evitar duplicados.';
            };
            xhr.onerror = xhr.ontimeout = function () {
                message.className = 'mt-2 text-danger';
                message.textContent = 'Se perdió la conexión o se agotó el tiempo de espera. Los datos se conservan. Revise el listado en otra pestaña antes de reenviar: el servidor podría haber guardado la resolución.';
            };
            xhr.onloadend = function () { clearInterval(monitor); unlock(); };
            message.textContent = 'Iniciando carga...';
            xhr.send(data);
        } catch (error) {
            message.className = 'mt-2 text-danger'; message.textContent = error.message; unlock();
        }
    });
})();
