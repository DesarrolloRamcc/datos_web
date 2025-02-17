<?php
require_once 'includes/auth.php';
verificarSesion();

$id_municipio = $_SESSION['id_municipio'] ?? null;
$nombre_municipio = '';

if ($id_municipio) {
    // Obtener el nombre del municipio
    $stmt = $pdo->prepare("SELECT name FROM municipios WHERE id = ?");
    $stmt->execute([$id_municipio]);
    $municipio = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre_municipio = $municipio['name'] ?? 'Municipio Desconocido';
}
?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-center display-4 mb-3">Bienvenido, <b><u><?php echo htmlspecialchars($nombre_municipio); ?></u></b></h1>
        </div>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-md-5 mb-5">
            <a href="Arbolado_<?php echo urlencode($nombre_municipio); ?>" class="btn btn-arbolado btn-lg w-100 py-5 d-flex flex-column align-items-center justify-content-center">
                <i class="bi bi-tree" style="font-size: 4rem;"></i>
                <span class="mt-3" style="font-size: 1.5rem;">Arbolado</span>
            </a>
        </div>
        <div class="col-md-5 mb-5">
            <button id="btnResiduos" class="btn btn-success btn-lg w-100 py-5 d-flex flex-column align-items-center justify-content-center">
                <i class="bi bi-recycle" style="font-size: 4rem;"></i>
                <span class="mt-3" style="font-size: 1.5rem;">Residuos</span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnResiduos = document.getElementById('btnResiduos');
    btnResiduos.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'En proceso...',
            text: 'Esta carga estará disponible proximamente.',
            icon: 'info',
            confirmButtonText: 'Entendido'
        });
    });
});
</script>