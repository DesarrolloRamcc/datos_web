<?php
require_once 'includes/auth.php';
require_once 'includes/conexion.php';

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

// Contador 1: Municipios con cargas de árboles
$sqlMunicipiosConCargas = "SELECT COUNT(DISTINCT municipio) as total FROM contadorarboles";
$stmtMunicipiosConCargas = $pdo->query($sqlMunicipiosConCargas);
$municipiosConCargas = $stmtMunicipiosConCargas->fetchColumn();

// Contador 2: Cantidad total de árboles
$sqlTotalArboles = "SELECT SUM(cantidad) as total FROM contadorarboles";
$stmtTotalArboles = $pdo->query($sqlTotalArboles);
$totalArboles = $stmtTotalArboles->fetchColumn() ?: 0;

// Contador 3: Cantidad total de cargas
$sqlTotalCargas = "SELECT COUNT(*) as total FROM contadorarboles";
$stmtTotalCargas = $pdo->query($sqlTotalCargas);
$totalCargas = $stmtTotalCargas->fetchColumn();

// LISTA DE MUNICIPIOS CON ACCIONES Y TOTAL DE ÁRBOLES
$sqlMunicipiosConAcciones = "SELECT m.name, SUM(c.cantidad) as total_arboles
                             FROM municipios m 
                             JOIN contadorarboles c ON m.id = c.municipio 
                             GROUP BY m.id, m.name
                             ORDER BY m.name ASC";
$stmtMunicipiosConAcciones = $pdo->query($sqlMunicipiosConAcciones);
$municipiosConAcciones = $stmtMunicipiosConAcciones->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4 mb-5">
    <h1 class="text-center mb-4"><b>Municipios</b></h1>

    <!-- Contadores -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
            <div class="card h-100 bg-light cursor-pointer" id="municipiosConAccionesCard">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted mb-2">Municipios con acciones</h5>
                    <p class="card-text display-6 fw-bold text-success mb-0"><?php echo number_format($municipiosConCargas); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
            <div class="card h-100 bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted mb-2">Total árboles</h5>
                    <p class="card-text display-6 fw-bold text-success mb-0"><?php echo number_format($totalArboles); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted mb-2">Total de acciones</h5>
                    <p class="card-text display-6 fw-bold text-success mb-0"><?php echo number_format($totalCargas); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Municipios -->
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

<!-- Modal for Municipalities with Actions -->
<div class="modal fade" id="municipiosConAccionesModal" tabindex="-1" aria-labelledby="municipiosConAccionesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="municipiosConAccionesModalLabel">Municipios con Acciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchMunicipios" class="form-control mb-3" placeholder="Buscar municipio...">
                <ul id="listaMunicipios" class="list-group">
                    <?php foreach ($municipiosConAcciones as $municipio): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($municipio['name']); ?>
                            <span class="badge rounded-pill" style="background: #285152;">Árboles: <?php echo number_format($municipio['total_arboles']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
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
            window.location.href = `detalle_municipio.php?id=${municipioId}`;
        });
    });

    // Event listener for the "Municipios con acciones" card
    document.getElementById('municipiosConAccionesCard').addEventListener('click', function() {
        var myModal = new bootstrap.Modal(document.getElementById('municipiosConAccionesModal'));
        myModal.show();
    });

    // Search functionality for the modal
    document.getElementById('searchMunicipios').addEventListener('keyup', function() {
        var filter = this.value.toLowerCase();
        var listItems = document.getElementById('listaMunicipios').getElementsByTagName('li');

        for (var i = 0; i < listItems.length; i++) {
            var textValue = listItems[i].textContent || listItems[i].innerText;
            if (textValue.toLowerCase().indexOf(filter) > -1) {
                listItems[i].style.display = "";
            } else {
                listItems[i].style.display = "none";
            }
        }
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

    .card {
        transition: transform 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .cursor-pointer{
        cursor: pointer;
    }
</style>