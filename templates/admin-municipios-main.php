<?php
require_once 'includes/auth.php';

verificarSesion();
verificarSuperAdmin();

// Obtener listado de municipios con información adicional
$sql = "SELECT m.id, m.name, m.name_governor, p.name AS nombre_provincia
        FROM municipios m 
        JOIN provincias p ON m.province_id = p.id
        ORDER BY m.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Tabla de Municipios -->
<div class="container mt-4 mb-5">
    <h1 class="text-center mb-5"><b>Municipios</b></h1>

    <div class="card">
        <div class="card-body">
            <table id="tablaMunicipios" class="table table-striped table-hover table-responsive">
                <thead>
                    <tr>
                        <th>Nombre del Municipio</th>
                        <th>Provincia</th>
                        <th>Intendente</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($municipios as $municipio): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($municipio['name']); ?></td>
                            <td><?php echo htmlspecialchars($municipio['nombre_provincia']); ?></td>
                            <td><?php echo htmlspecialchars($municipio['name_governor']); ?></td>
                            <td>
                                <button class="btn btn-outline-info btn-sm btn-detalle" data-id="<?php echo $municipio['id']; ?>">
                                    <i class="bi bi-eye-fill"></i> Ver Detalle
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-danger btn-lg">Cerrar Sesión</a>
    </div>
</div>

<script>
    // Inicializar DataTables
    $(document).ready(function() {
        $('#tablaMunicipios').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true,
            columnDefs: [{
                targets: -1,
                orderable: false,
                searchable: false
            }],
            order: [
                [0, 'asc']
            ]
        });
    });

    // Event listener para botones de detalle
    document.querySelectorAll('.btn-detalle').forEach(btn => {
        btn.addEventListener('click', function() {
            const municipioId = this.getAttribute('data-id');
            // Redirigir a la página de detalle del municipio
            window.location.href = `detalle_municipio.php?id=${municipioId}`;
        });
    });
</script>

<style>
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .bi {
        font-size: 1rem;
    }
</style>