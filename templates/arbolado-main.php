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

    <!-- Tabla de Árboles (visible en pantallas grandes) -->
    <div class="card d-none d-md-block">
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
    <div class="d-md-none">
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
    @media (max-width: 767.98px) {
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

<!-- Script para inicializar DataTables -->
<script>
    $(document).ready(function() {
        var table = $('#tablaArboles').DataTable({
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

        // Aplicar la búsqueda y ordenamiento de DataTables a las tarjetas en móviles
        $('#tablaArboles_filter input').on('keyup', function() {
            var searchTerm = $(this).val();
            filterCards(searchTerm);
        });

        table.on('order.dt', function() {
            var order = table.order();
            sortCards(order);
        });

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
    });
</script>

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