<?php
require_once 'includes/auth.php';

verificarSesion();

$id_municipio = obtenerIdMunicipioActual();
$nombre_municipio = $_SESSION['nombre_municipio'] ?? 'Municipio';
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-center display-4 mb-3">Bienvenido, <b><u><?php echo htmlspecialchars($nombre_municipio); ?></u></b></h1>
        </div>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-md-5 mb-5">
            <a href="arbolado.php?id_municipio=<?php echo $id_municipio; ?>" class="btn btn-arbolado btn-lg w-100 py-5 d-flex flex-column align-items-center justify-content-center">
                <i class="bi bi-tree" style="font-size: 4rem;"></i>
                <span class="mt-3" style="font-size: 1.5rem;">Arbolado</span>
            </a>
        </div>
        <div class="col-md-5 mb-5">
            <a href="residuos.php?id_municipio=<?php echo $id_municipio; ?>" class="btn btn-success btn-lg w-100 py-5 d-flex flex-column align-items-center justify-content-center">
                <i class="bi bi-recycle" style="font-size: 4rem;"></i>
                <span class="mt-3" style="font-size: 1.5rem;">Residuos</span>
            </a>
        </div>
    </div>
</div>