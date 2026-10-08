<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= html_escape($title) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/corponor-favicon.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/corponor.css?v=20260904') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/resoluciones-select2.css?v=20261008-2') ?>">
</head>
<body class="public-body">
<header class="public-wp-header">
    <div class="public-wp-upper">
        <div class="container public-wp-upper-inner">
            <div class="public-wp-logos">
                <a href="https://corponor.gov.co/web/" target="_blank" rel="noopener"><img class="public-wp-main-logo" src="<?= base_url('assets/images/corponor-logo.png') ?>" alt="Sitio institucional"></a>
                <a href="https://www.gov.co" target="_blank" rel="noopener"><img src="<?= base_url('assets/images/govco.png') ?>" alt="GOV.CO"></a>
                <img src="<?= base_url('assets/images/pais-col.png') ?>" alt="Colombia">
                <a href="https://corponor.gov.co/web/index.php/pagos-en-linea/" target="_blank" rel="noopener"><img class="public-wp-pse" src="<?= base_url('assets/images/pse.jpeg') ?>" alt="Pagos en linea"></a>
            </div>
            <div class="public-wp-tools"><a href="https://www.facebook.com/" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a><a href="https://twitter.com/" target="_blank" rel="noopener" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a><a href="mailto:corponor@corponor.gov.co" aria-label="Correo"><i class="bi bi-envelope"></i></a></div>
        </div>
    </div>
    <div class="public-wp-menu">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark p-0">
                <button class="navbar-toggler ms-auto my-3" type="button" data-bs-toggle="collapse" data-bs-target="#public-wp-navigation" aria-controls="public-wp-navigation" aria-expanded="false" aria-label="Abrir menu"><span class="navbar-toggler-icon"></span></button>
                <div id="public-wp-navigation" class="collapse navbar-collapse">
                    <ul class="navbar-nav mx-auto public-wp-navigation">
                        <li class="nav-item"><a class="nav-link active" href="<?= site_url('resoluciones') ?>">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://corponor.gov.co/web/index.php/my-account/" target="_blank" rel="noopener">Transparencia y acceso a la informacion</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://corponor.gov.co/web/index.php/descripcion-general/" target="_blank" rel="noopener">Participa</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://corponor.gov.co/web/index.php/informes/" target="_blank" rel="noopener">Concurso publico</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://corponor.gov.co/web/index.php/historico-de-noticias/" target="_blank" rel="noopener">Comunicaciones</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://corponor.gov.co/web/index.php/atencion-al-ciudadano/" target="_blank" rel="noopener">Atencion y servicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://corponor.gov.co/web/index.php/notificaciones/" target="_blank" rel="noopener">Notificaciones</a></li>
                        <li class="nav-item"><a class="nav-link" href="https://corponor.gov.co/juegos/webjuegos/" target="_blank" rel="noopener">CORPOKIDS</a></li>
                        <li class="nav-item"><a class="nav-link public-wp-admin-link" href="<?= site_url('login') ?>"><i class="bi bi-lock me-1"></i> Administracion</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</header>
<main class="container-fluid py-5 public-main"><?php $this->load->view($content_view); ?></main>
<footer class="public-wp-footer">
    <section class="public-wp-find">
        <div class="container"><h2>Encuentranos</h2><div class="public-wp-maps"><iframe src="https://www.google.com.co/maps/embed?pb=!1m18!1m12!1m3!1d830.8265201573755!2d-72.49235927360303!3d7.88468556525218!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e6645a0c3fb8fdf%3A0xdc8e143f9ea41724!2sCORPONOR!5e0!3m2!1ses!2sco!4v1626362053157!5m2!1ses!2sco" width="600" height="450" style="border:0;" title="Ubicacion institucional" allowfullscreen="" loading="lazy"></iframe><iframe src="https://www.google.com.co/maps/embed?pb=!4v1626362194436!6m8!1m7!1shLUfCWefADZwuIkbxXYLsg!2m2!1d7.88471001551477!2d-72.49255038347158!3f4.101228945710261!4f-3.6194817548023224!5f0.7820865974627469" width="600" height="450" style="border:0;" title="Vista de la sede" allowfullscreen="" loading="lazy"></iframe></div></div>
    </section>
    <section class="public-wp-contact"><div class="container"><div class="public-wp-footer-brands"><a href="https://www.gov.co" target="_blank" rel="noopener"><img src="<?= base_url('assets/images/govco.png') ?>" alt="GOV.CO"></a><img src="<?= base_url('assets/images/pais-col.png') ?>" alt="Colombia"><a href="https://corponor.gov.co/web/index.php/pagos-en-linea/" target="_blank" rel="noopener"><img class="public-wp-pse" src="<?= base_url('assets/images/pse.jpeg') ?>" alt="Pagos en linea"></a></div><p class="public-wp-address"><strong>Corporacion Autonoma Regional de la Frontera Nororiental</strong><br>Direccion: Calle 13 No. 3E - 278. Barrio Caobos - Cucuta, Norte de Santander<br>Telefono conmutador: <a href="tel:+5760748868">(+57) (60) (7) 5748868</a><br>Linea gratuita nacional: 01-8000-75200 / 01-8000-975201<br>Ventanilla Unica para Radicacion: <a href="https://siep-corponor.com/siepdocpqr9/pqr_corponor_/" target="_blank" rel="noopener">Siep-Corponor</a><br>Linea Anticorrupcion: <a href="mailto:corponorjuntosporlatransparencia@corponor.gov.co">corponorjuntosporlatransparencia@corponor.gov.co</a><br>Horario de atencion: Lunes a viernes de 7:15 a.m. a 12:00 m. y de 2:15 p.m. a 6:00 p.m.<br>Notificaciones judiciales: <a href="mailto:procesosjudiciales@corponor.gov.co">procesosjudiciales@corponor.gov.co</a> / <a href="mailto:procesosjudicialescorponor@corponor.gov.co">procesosjudicialescorponor@corponor.gov.co</a></p><div class="public-wp-footer-links"><a href="https://corponor.gov.co/web/index.php/transparencia/politicas-4/" target="_blank" rel="noopener">Politicas</a><a href="https://corponor.gov.co/web/index.php/mapa-del-sitio" target="_blank" rel="noopener">Mapa de sitio</a><a href="https://mail.corponor.gov.co:2096/" target="_blank" rel="noopener">Correo institucional</a><a href="https://siep-corponor.com/siepdoc/login.php" target="_blank" rel="noopener">SIEPDOC</a></div><div class="public-wp-credits">Desarrollado por <a href="https://labco.com.co/" target="_blank" rel="noopener">Labco Centro de Innovacion</a> · Copyright <?= date('Y') ?> · <a href="mailto:williambermon@gmail.com">Ing. William Bermon</a></div></div></section>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
<script>
if (window.jQuery && jQuery.fn.select2) {
    jQuery('.resolution-select2').each(function () {
        jQuery(this).select2({
            width: '100%',
            placeholder: jQuery(this).data('placeholder'),
            allowClear: true,
            closeOnSelect: false,
            language: 'es'
        });
    });
}
</script>
</body>
</html>
