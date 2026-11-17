<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= html_escape($title ?? 'Panel') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/corponor-favicon.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc7/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/corponor.css') ?>">
    <style>
    /* Resalta únicamente la opción activa del sidebar. */
    .app-sidebar .sidebar-menu > .nav-item > .nav-link.corponor-current {
        background: #315878 !important;
        color: #fff !important;
        border-left: 4px solid #a7cb39;
        border-radius: 7px;
        font-weight: 700;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
    }
    .app-sidebar .sidebar-menu > .nav-item > .nav-link.corponor-current .nav-icon { color: #c8ec67 !important; }
    .app-sidebar .sidebar-menu > .nav-item > .nav-link.corponor-current:hover { background: #3c698d !important; }
    .app-sidebar .sidebar-menu > .nav-item > .nav-link.corponor-current { display: flex; align-items: center; }
    .app-sidebar .sidebar-menu > .nav-item > .nav-link.corponor-current::after {
        content: "\2713";
        color: #c8ec67;
        font-weight: 800;
        font-size: 1rem;
        margin-left: auto;
        padding-left: .5rem;
        line-height: 1;
    }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
    <nav class="app-header navbar navbar-expand bg-white shadow-sm">
        <div class="container-fluid">
            <a class="nav-link" data-lte-toggle="sidebar" href="#"><i class="bi bi-list"></i></a>
            <span class="navbar-text ms-2 fw-semibold">Gestión documental CORPONOR</span>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><span class="nav-link"><?= html_escape($this->session->userdata('nombres')) ?></span></li>
                <li><a class="nav-link" href="<?= site_url('admin/usuarios/clave/'.(int)$this->session->userdata('usuario_id')) ?>">Mi clave</a></li><li><a class="nav-link" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Salir</a></li>
            </ul>
        </div>
    </nav>
    <aside class="app-sidebar bg-corponor-dark shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
            <a href="<?= site_url('admin') ?>" class="brand-link text-decoration-none">
                <img class="brand-logo brand-logo-sidebar" src="<?= base_url('assets/images/corponor-logo-blanco.png') ?>" alt="Logotipo institucional">
            </a>
        </div>
        <div class="sidebar-wrapper"><nav class="mt-2"><ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
            <?php
            // Coincidencia exacta o por subruta; evita activar «Grupos» en «Grupos de acuerdos».
            $sidebar_path = trim((string) uri_string(), '/');
            $sidebar_active = static function ($route) use ($sidebar_path) {
                return $route === 'admin' ? ($sidebar_path === 'admin' || $sidebar_path === '') : ($sidebar_path === $route || strpos($sidebar_path, $route . '/') === 0);
            };
            $sidebar_class = static function ($route) use ($sidebar_active) {
                return $sidebar_active($route) ? ' corponor-current active' : '';
            };
            ?>
            <li class="nav-item"><a href="<?= site_url('admin') ?>" class="nav-link<?= $sidebar_class('admin') ?>"><i class="nav-icon bi bi-speedometer2"></i><p>Panel</p></a></li>
            <?php if((int)$this->session->userdata('es_admin')>=1): ?><li class="nav-item"><a href="<?= site_url('admin/usuarios') ?>" class="nav-link<?= $sidebar_class('admin/usuarios') ?>"><i class="nav-icon bi bi-people"></i><p>Usuarios</p></a></li><?php endif; ?>
            <?php $CI =& get_instance(); $CI->load->model('Acuerdo_model'); ?>
            <li class="nav-header text-uppercase small fw-semibold mt-3">RESOLUCIONES</li>
            <li class="nav-item"><a href="<?= site_url('admin/grupos') ?>" class="nav-link<?= $sidebar_class('admin/grupos') ?>"><i class="nav-icon bi bi-folder2-open"></i><p>Grupos</p></a></li>
            <li class="nav-item"><a href="<?= site_url('admin/resoluciones') ?>" class="nav-link<?= $sidebar_class('admin/resoluciones') ?>"><i class="nav-icon bi bi-file-earmark-text"></i><p>Resoluciones</p></a></li>
            <li class="nav-item"><a href="<?= site_url('resoluciones') ?>" class="nav-link" target="_blank" rel="noopener"><i class="nav-icon bi bi-globe"></i><p>Vista pública</p></a></li>
            <?php if($CI->Acuerdo_model->permiso('administrar_grupos') || $CI->Acuerdo_model->permiso('gestionar_acuerdos')): ?>
            <li class="nav-header text-uppercase small fw-semibold mt-3">ACUERDOS</li>
            <?php if($CI->Acuerdo_model->permiso('administrar_grupos')): ?><li class="nav-item"><a href="<?= site_url('admin/grupos-acuerdos') ?>" class="nav-link<?= $sidebar_class('admin/grupos-acuerdos') ?>"><i class="nav-icon bi bi-folder"></i><p>Grupos de acuerdos</p></a></li><?php endif; ?>
            <?php if($CI->Acuerdo_model->permiso('gestionar_acuerdos')): ?><li class="nav-item"><a href="<?= site_url('admin/acuerdos') ?>" class="nav-link<?= $sidebar_class('admin/acuerdos') ?>"><i class="nav-icon bi bi-file-earmark-check"></i><p>Acuerdos</p></a></li><?php endif; ?>
            <li class="nav-item"><a href="<?= site_url('acuerdos') ?>" class="nav-link" target="_blank" rel="noopener"><i class="nav-icon bi bi-globe"></i><p>Vista pública</p></a></li>
            <?php endif; ?>
            <li class="nav-header text-uppercase small fw-semibold mt-3">HERRAMIENTAS</li>
            <?php if((int)$this->session->userdata('es_admin')>=1): ?><li class="nav-item"><a href="<?= site_url('admin/enlaces') ?>" class="nav-link<?= $sidebar_class('admin/enlaces') ?>"><i class="nav-icon bi bi-link-45deg"></i><p>Novedades de enlaces</p></a></li><?php endif; ?>
            <li class="nav-item"><a href="<?= site_url('admin/estadisticas') ?>" class="nav-link<?= $sidebar_class('admin/estadisticas') ?>"><i class="nav-icon bi bi-bar-chart-line"></i><p>Estadísticas</p></a></li>
        </ul></nav></div>
    </aside>
    <main class="app-main">
        <div class="app-content-header"><div class="container-fluid"><h3 class="mb-0"><?= html_escape($title ?? '') ?></h3></div></div>
        <div class="app-content"><div class="container-fluid">
            <?php if($this->session->flashdata('success')): ?><div class="alert alert-success"><?= html_escape($this->session->flashdata('success')) ?></div><?php endif; ?>
            <?php $error=$error??$this->session->flashdata('error'); if(!empty($error)): ?><div class="alert alert-danger"><?= html_escape($error) ?></div><?php endif; ?>
            <?php $this->load->view($content_view); ?>
        </div></div>
    </main>
    <footer class="app-footer">Gestor documental CORPONOR <span class="float-end d-none d-sm-inline">Diseñado por: <a href="https://labco.com.co/" target="_blank" rel="noopener">Labco.com.co</a><br>Copyright: <a href="mailto:williambermon@gmail.com">Ing. William Bermon</a></span></footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc7/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && $.fn.DataTable) {
        $('.datatable').each(function () {
            const options = { pageLength: 10, lengthMenu: [10, 25, 50, 100], order: [], language: { search: 'Buscar:', lengthMenu: 'Mostrar _MENU_', info: 'Mostrando _START_ a _END_ de _TOTAL_', infoEmpty: 'Sin registros', zeroRecords: 'No se encontraron registros', paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' } } };
            if (this.id === 'resolucionesTable') { options.rowGroup = { dataSrc: 2 }; options.columnDefs = [{ targets: 3, visible: false }]; options.order = []; }
            $(this).DataTable(options);
        });
        $('.select2').select2({ width: '100%', placeholder: 'Buscar grupo', allowClear: true });
    }
});
</script>
<script src="<?= base_url('assets/js/password-toggle.js') ?>"></script></body>
</html>
