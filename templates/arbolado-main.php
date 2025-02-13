<?php
require_once 'includes/auth.php';
require_once 'includes/conexion.php';

verificarSesion();

$id_municipio = $_GET['id_municipio'] ?? null;

if (!$id_municipio) {
    die("Error: No se proporcionó un ID de municipio válido.");
}

// Obtener el nombre del municipio
$stmt = $pdo->prepare("SELECT name FROM municipios WHERE id = ?");
$stmt->execute([$id_municipio]);
$municipio = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre_municipio = $municipio['name'] ?? 'Municipio Desconocido';

// Obtener listado de árboles
$sql = "SELECT id, date, cantidad, descripcion FROM contadorarboles WHERE municipio = ? ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_municipio]);
$arboles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4 mb-5">
    <h1 class="text-center"><b>Arbolado de <?php echo htmlspecialchars($nombre_municipio); ?></b></h1>
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalAgregarArbol">
            <i class="bi bi-plus-circle me-2"></i>Agregar Árbol
        </button>
    </div>

    <!-- Tabla de Árboles -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaArboles" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cantidad</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($arboles as $arbol): ?>
                            <tr>
                                <td data-label="Fecha"><?php echo date('d-m-y', strtotime($arbol['date'])); ?></td>
                                <td data-label="Cantidad"><?php echo htmlspecialchars($arbol['cantidad']); ?></td>
                                <td data-label="Descripción"><?php echo htmlspecialchars(substr($arbol['descripcion'], 0, 50)) . (strlen($arbol['descripcion']) > 50 ? '...' : ''); ?></td>
                                <td data-label="Acciones">
                                    <button class="btn btn-warning btn-sm me-1 btn-editar" data-id="<?php echo $arbol['id']; ?>">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?php echo $arbol['id']; ?>">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script para inicializar DataTables -->
<script>
    $(document).ready(function() {
        $('#tablaArboles').DataTable({
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
                [0, 'desc']
            ],
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Todos"]
            ]
        });
    });
</script>

<!-- Estilos adicionales para responsive -->
<style>
    @media screen and (max-width: 767px) {
        #tablaArboles thead {
            display: none;
        }

        #tablaArboles,
        #tablaArboles tbody,
        #tablaArboles tr,
        #tablaArboles td {
            display: block;
            width: 100%;
        }

        #tablaArboles tr {
            margin-bottom: 15px;
        }

        #tablaArboles td {
            text-align: right;
            padding-left: 50%;
            position: relative;
        }

        #tablaArboles td:before {
            content: attr(data-label);
            position: absolute;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
        }
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .bi {
        font-size: 1rem;
    }
</style>

<?php include 'templates/nueva_carga_arbolado.php'; ?>

<!-- AGREGAR CARGA -->
<script>
    document.getElementById('formAgregarArbol').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch('controllers/agregar_carga_arbolado.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
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
    });

    // Validación de imagen
    document.getElementById('imagen').addEventListener('change', function(e) {
        if (this.files.length > 1) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Solo se permite subir una imagen'
            });
            this.value = '';
        }
    });
</script>