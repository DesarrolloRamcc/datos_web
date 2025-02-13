<?php
//INCLUDE HEAD
require_once 'includes/head.php';
require_once 'includes/auth.php';

verificarSesion();

$nombre_municipio = $_GET['nombre_municipio'] ?? '';
$nombre_municipio = urldecode($nombre_municipio);

$stmt = $pdo->prepare("SELECT id FROM municipios WHERE name = ?");
$stmt->execute([$nombre_municipio]);
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
$id_municipio = $resultado['id'] ?? null;

if (!$id_municipio) {
    die("Error: No se encontró un municipio válido.");
}

verificarAccesoMunicipio($id_municipio);
?>

<body class="index-page">

<?php
//INCLUYO HEADER
include_once 'includes/header.php';
?>

<main class="main">
    <?php
    include_once 'templates/arbolado-main.php';
    ?>
</main>

<!-- FOOTER Y SCRIPTS -->
<?php
include_once 'includes/footer.php';
include_once 'includes/scripts.php';
?>

</body>
</html>