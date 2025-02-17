<?php
//INCLUDE HEAD
include_once 'includes/head.php';
require_once 'includes/auth.php';

verificarSesion();

$id_municipio = $_GET['id'] ?? null;

if (!$id_municipio) {
    die("Error: No se proporcionó un ID de municipio válido.");
}

// Verificar si el usuario es superadmin o tiene acceso al municipio
if (!esSuperAdmin() && !tieneAccesoMunicipio($id_municipio)) {
    die("Error: No tiene permiso para acceder a este municipio.");
}

?>

<body class="index-page">

<?php
//INCLUYO HEADER
include_once 'includes/header.php';
?>

<main class="main">
    <?php
    include_once 'templates/detalle-municipio-main.php';
    ?>
</main>

<!-- FOOTER Y SCRIPTS -->
<?php
include_once 'includes/footer.php';
include_once 'includes/scripts.php';
?>

</body>
</html>