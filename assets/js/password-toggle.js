document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="password"]').forEach(function (input) {
        var wrapper = document.createElement('div');
        wrapper.className = 'input-group';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-outline-secondary';
        button.setAttribute('aria-label', 'Mostrar clave');
        button.setAttribute('aria-pressed', 'false');
        button.innerHTML = '<i class="bi bi-eye" aria-hidden="true"></i>';
        button.addEventListener('click', function () {
            var visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-label', visible ? 'Ocultar clave' : 'Mostrar clave');
            button.setAttribute('aria-pressed', String(visible));
            button.firstChild.className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
        wrapper.appendChild(button);
    });
});
