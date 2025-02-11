<?php
require_once 'includes/auth.php';

verificarSesion();
verificarSuperAdmin();

// Obtener listado de municipios
$sql = "SELECT m.id_municipio, m.nombre_muni, p.nombre_provincia 
        FROM municipios m 
        JOIN provincias p ON m.id_provincia = p.id_provincia";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Botón Agregar Municipio -->
<div class="container mt-4 mb-5">
    <h1 class="text-center"><b>Municipios</b></h1>
    <div class="d-flex justify-content-end mb-4">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalAgregarMunicipio">
            <i class="bi bi-plus-circle me-2"></i>Agregar Municipio
        </button>
    </div>

    <!-- Tabla de Municipios -->
    <div class="card">
        <div class="card-body">
            <table id="tablaMunicipios" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nombre del Municipio</th>
                        <th>Provincia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($municipios as $municipio): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($municipio['nombre_muni']); ?></td>
                            <td><?php echo htmlspecialchars($municipio['nombre_provincia']); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?php echo $municipio['id_municipio']; ?>" data-nombre="<?php echo htmlspecialchars($municipio['nombre_muni']); ?>">
                                    <i class="bi bi-trash-fill"></i>
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

<!-- Modal Agregar Municipio -->
<div class="modal fade" id="modalAgregarMunicipio" tabindex="-1" aria-labelledby="modalAgregarMunicipioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarMunicipioLabel"><b>Agregar Municipio</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAgregarMunicipio">
                <div class="modal-body">
                    <!-- Nombre del Municipio -->
                    <div class="mb-3">
                        <label for="nombre_muni" class="form-label">Nombre del Municipio</label>
                        <input type="text" class="form-control" id="nombre_muni" name="nombre_muni" required>
                    </div>

                    <!-- Provincia -->
                    <div class="mb-3">
                        <label for="id_provincia" class="form-label">Provincia</label>
                        <select class="form-select" id="id_provincia" name="id_provincia" required>
                            <option value="">Seleccione una provincia</option>
                            <?php
                            $sqlProvincias = "SELECT id_provincia, nombre_provincia FROM provincias ORDER BY nombre_provincia";
                            $stmtProvincias = $pdo->query($sqlProvincias);
                            while ($provincia = $stmtProvincias->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . htmlspecialchars($provincia['id_provincia']) . '">' . 
                                     htmlspecialchars($provincia['nombre_provincia']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Inicializar DataTables
$(document).ready(function() {
    $('#tablaMunicipios').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        responsive: true,
        columnDefs: [{
            targets: -1,
            orderable: false,
            searchable: false
        }],
        order: [[0, 'asc']]
    });
});

// Agregar Municipio
document.getElementById('formAgregarMunicipio').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('controllers/procesar_agregar_municipio.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Municipio agregado!',
                text: 'El municipio ha sido agregado exitosamente',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Ocurrió un error al agregar el municipio'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar la solicitud'
        });
    });
});

// Eliminar Municipio
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function() {
        const municipioId = this.getAttribute('data-id');
        const municipioNombre = this.getAttribute('data-nombre');

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Realmente deseas eliminar el municipio ${municipioNombre}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Segunda confirmación
                Swal.fire({
                    title: 'Confirma nuevamente',
                    text: `¿Estás completamente seguro de eliminar el municipio ${municipioNombre}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar definitivamente',
                    cancelButtonText: 'Cancelar'
                }).then((secondResult) => {
                    if (secondResult.isConfirmed) {
                        // Proceder con la eliminación
                        const formData = new FormData();
                        formData.append('id_municipio', municipioId);

                        fetch('controllers/procesar_eliminar_municipio.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Municipio eliminado',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al procesar la solicitud'
                            });
                        });
                    }
                });
            }
        });
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