<?php
require_once 'includes/conexion.php';
require_once 'includes/auth.php';

verificarSesion();
verificarSuperAdmin();

$id_municipio = $_GET['id'] ?? null;

if (!$id_municipio) {
    die("Error: No se proporcionó un ID de municipio válido.");
}

// Verificar si el usuario es superadmin o tiene acceso al municipio
if (!esSuperAdmin() && !tieneAccesoMunicipio($id_municipio)) {
    die("Error: No tiene permiso para acceder a este municipio.");
}

// Obtener el nombre del municipio
$stmt = $pdo->prepare("SELECT name FROM municipios WHERE id = ?");
$stmt->execute([$id_municipio]);
$municipio = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre_municipio = $municipio['name'] ?? 'Municipio Desconocido';

// Obtener el total de registros
$sqlRegistros = "SELECT COUNT(*) as total_registros FROM contadorarboles WHERE municipio = ?";
$stmtRegistros = $pdo->prepare($sqlRegistros);
$stmtRegistros->execute([$id_municipio]);
$totalRegistros = $stmtRegistros->fetch(PDO::FETCH_ASSOC)['total_registros'];

// Obtener la suma total de árboles
$sqlTotal = "SELECT SUM(cantidad) as total_arboles FROM contadorarboles WHERE municipio = ?";
$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute([$id_municipio]);
$totalArboles = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_arboles'] ?? 0;

// Obtener listado de árboles
$sql = "SELECT id, date, cantidad, descripcion FROM contadorarboles WHERE municipio = ? ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_municipio]);
$arboles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <h1 class="text-center m-4"><b>Arbolado de <?php echo htmlspecialchars($nombre_municipio); ?></b></h1>
    <!-- Nuevos contadores -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
            <div class="card h-100 bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted mb-2">Acciones</h5>
                    <p class="card-text display-6 fw-bold text-success mb-0"><?php echo number_format($totalRegistros); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted mb-2">Árboles</h5>
                    <p class="card-text display-6 fw-bold text-success mb-0"><?php echo number_format($totalArboles); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between mb-3">
        <?php if (esSuperAdmin()): ?>
            <a href="admin-municipios.php">
                <button type="button" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Volver al panel de municipios
                </button>
            </a>
        <?php else: ?>
            <a href="PanelAdministrador">
                <button type="button" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Volver al panel
                </button>
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalAgregarArbol">
            <i class="bi bi-plus-circle me-2"></i>Agregar Árbol
        </button>
    </div>

    <!-- Tabla de Árboles (visible en pantallas grandes) -->
    <div class="card d-none d-md-block overflow-hidden mb-5">
        <div class="card-body">
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
                            <td><?php echo date('d-m-y', strtotime($arbol['date'])); ?></td>
                            <td><?php echo htmlspecialchars($arbol['cantidad']); ?></td>
                            <td><?php echo htmlspecialchars(substr($arbol['descripcion'], 0, 80)) . (strlen($arbol['descripcion']) > 80 ? '...' : ''); ?></td>
                            <td>
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

    <!-- Tarjetas de Árboles (visibles en pantallas pequeñas) -->
    <div class="d-md-none mb-5">
        <div id="tarjetasArboles" class="row row-cols-1 g-4">
            <?php foreach ($arboles as $arbol): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Fecha: <?php echo date('d-m-y', strtotime($arbol['date'])); ?></h5>
                            <p class="card-text">Cantidad: <?php echo htmlspecialchars($arbol['cantidad']); ?></p>
                            <p class="card-text">Descripción: <?php echo htmlspecialchars(substr($arbol['descripcion'], 0, 80)) . (strlen($arbol['descripcion']) > 80 ? '...' : ''); ?></p>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-warning btn-sm me-1 btn-editar" data-id="<?php echo $arbol['id']; ?>">
                                <i class="bi bi-pencil-fill"></i> Editar
                            </button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?php echo $arbol['id']; ?>">
                                <i class="bi bi-trash-fill"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
    /* Estilos actualizados */
    .container-fluid {
        max-width: 1400px;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 0;
    }

    /* Ajustes para DataTables */
    .dataTables_wrapper {
        width: 100%;
        overflow: hidden;
    }

    /* Ajustes para móviles */
    @media (max-width: 767.98px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .card-body {
            padding: 0.5rem;
        }

        .card-title {
            font-size: 1rem;
        }

        .card-text {
            font-size: 0.875rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>

<script>
    // Script para inicializar DataTables y manejar las acciones de los botones
    $(document).ready(function() {
        var table = $('#tablaArboles').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true,
            scrollX: false,
            autoWidth: false,
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

        // Funciones para filtrar y ordenar tarjetas en móviles
        function filterCards(searchTerm) {
            $('#tarjetasArboles .col').each(function() {
                var cardText = $(this).text().toLowerCase();
                $(this).toggle(cardText.indexOf(searchTerm.toLowerCase()) !== -1);
            });
        }

        function sortCards(order) {
            var cards = $('#tarjetasArboles .col').get();
            cards.sort(function(a, b) {
                var aData = $(a).find('.card-title').text();
                var bData = $(b).find('.card-title').text();
                if (order[0][1] === 'desc') {
                    return aData < bData ? 1 : -1;
                } else {
                    return aData > bData ? 1 : -1;
                }
            });
            $('#tarjetasArboles').append(cards);
        }

        $('#tablaArboles_filter input').on('keyup', function() {
            filterCards($(this).val());
        });

        table.on('order.dt', function() {
            sortCards(table.order());
        });

        // Manejar clic en botón editar
        $('.btn-editar').click(function() {
            const id = $(this).data('id');

            fetch(`controllers/obtener_carga_arbolado.php?id=${id}&id_municipio=<?php echo $id_municipio; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit_id').value = data.arbol.id;
                        document.getElementById('edit_date').value = data.arbol.date;
                        document.getElementById('edit_cantidad').value = data.arbol.cantidad;
                        document.getElementById('edit_especie').value = data.arbol.especie;
                        document.getElementById('edit_publicoprivado').value = data.arbol.publicoprivado;
                        document.getElementById('edit_quienLoPlanto').value = data.arbol.quienLoPlanto;
                        document.getElementById('edit_descripcion').value = data.arbol.descripcion;

                        const imagenActualDiv = document.getElementById('imagenActual');
                        if (data.arbol.imagen) {
                            imagenActualDiv.innerHTML = `
                        <p>Imagen actual:</p>
                        <img src="${data.arbol.imagen}" class="img-thumbnail" style="max-width: 200px">
                    `;
                        } else {
                            imagenActualDiv.innerHTML = '<p>No hay imagen actual</p>';
                        }

                        const modal = new bootstrap.Modal(document.getElementById('modalEditarArbol'));
                        modal.show();
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
                        text: 'Error al cargar los datos'
                    });
                });
        });

        // Manejar clic en botón eliminar
        $('.btn-eliminar').click(function() {
            var id = $(this).data('id');
            // Aquí iría el código para confirmar y eliminar el árbol
        });
    });
</script>

<?php
include 'templates/nueva_carga_arbolado.php';
include 'templates/editar_carga_arbolado.php';
?>

<!-- Scripts para manejar la adición, edición y eliminación de árboles -->
<script>
    // Código para manejar la adición de nuevos árboles
    document.getElementById('formAgregarArbol').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('id_municipio', <?php echo $id_municipio; ?>);

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

    // Código para manejar la edición de árboles
    document.getElementById('formEditarArbol').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('id_municipio', <?php echo $id_municipio; ?>);

        fetch('controllers/modificar_carga_arbolado.php', {
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

    // Código para manejar la eliminación de árboles
    function eliminarCargaArbolado(id) {
        fetch('controllers/eliminar_carga_arbolado.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + id + '&id_municipio=<?php echo $id_municipio; ?>'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
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

    // Inicializar los botones de eliminar con confirmación
    document.querySelectorAll('.btn-eliminar').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarCargaArbolado(id);
                }
            });
        });
    });
</script>