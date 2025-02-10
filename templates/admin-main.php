<?php
require_once 'includes/auth.php';

verificarSesion();

if ($_SESSION['id_municipio'] != 1) {
    header("Location: acceso_denegado.php");
    exit;
}

// Obtener listado de usuarios
$sql = "SELECT u.id_user, u.nombre, u.apellido, u.email, u.id_municipio, m.nombre_muni 
        FROM users u 
        JOIN municipios m ON u.id_municipio = m.id_municipio";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Botón Agregar Usuario -->
<div class="container mt-4">
    <div class="d-flex justify-content-end mb-4">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario">
            <i class="bi bi-person-plus-fill me-2"></i>Agregar Usuario
        </button>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card">
        <div class="card-body">
            <table id="tablaUsuarios" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nombre y Apellido</th>
                        <th>Email</th>
                        <th>Municipio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre_muni']); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm me-2 btn-editar" data-id="<?php echo $usuario['id_user']; ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?php echo $usuario['id_user']; ?>" data-nombre="<?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>">
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
<!-- Modal Agregar Usuario -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarUsuarioLabel"><b>Agregar Usuario</b></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgregarUsuario">
                    <div class="modal-body">
                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <!-- Apellido -->
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <!-- Municipio -->
                        <div class="mb-3">
                            <label for="id_municipio" class="form-label">Municipio</label>
                            <select class="form-select" id="id_municipio" name="id_municipio" required>
                                <option value="">Seleccione un municipio</option>
                                <?php
                                // Obtener lista de municipios
                                $sqlMunicipios = "SELECT id_municipio, nombre_muni FROM municipios ORDER BY nombre_muni";
                                $stmtMunicipios = $pdo->query($sqlMunicipios);
                                while ($municipio = $stmtMunicipios->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($municipio['id_municipio']) . '">' .
                                        htmlspecialchars($municipio['nombre_muni']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
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
</div>
<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUsuarioLabel"><b>Editar Usuario</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarUsuario">
                <div class="modal-body">
                    <input type="hidden" id="edit_id_user" name="id_user">
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <!-- Apellido -->
                    <div class="mb-3">
                        <label for="edit_apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="edit_apellido" name="apellido" required>
                    </div>
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <!-- Municipio -->
                    <div class="mb-3">
                        <label for="edit_id_municipio" class="form-label">Municipio</label>
                        <select class="form-select" id="edit_id_municipio" name="id_municipio" required>
                            <?php
                            $sqlMunicipios = "SELECT id_municipio, nombre_muni FROM municipios ORDER BY nombre_muni";
                            $stmtMunicipios = $pdo->query($sqlMunicipios);
                            while ($municipio = $stmtMunicipios->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . htmlspecialchars($municipio['id_municipio']) . '">' .
                                    htmlspecialchars($municipio['nombre_muni']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para procesar el formulario -->
<script>
    document.getElementById('formAgregarUsuario').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Procesando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('controllers/procesar_agregar_usuario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Error del servidor');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Usuario agregado!',
                        text: 'El usuario ha sido cargado exitosamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Cerrar el modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgregarUsuario'));
                        modal.hide();

                        // Recargar la página
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Error al agregar el usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al procesar la solicitud'
                });
            });
    });
</script>

<!-- Script para eliminar y borrar usuarios -->
<script>
    // Función para cargar datos del usuario en el modal de edición
    function cargarDatosUsuario(id) {
        const usuario = <?php echo json_encode($usuarios); ?>.find(u => u.id_user == id);
        if (usuario) {
            document.getElementById('edit_id_user').value = usuario.id_user;
            document.getElementById('edit_nombre').value = usuario.nombre;
            document.getElementById('edit_apellido').value = usuario.apellido;
            document.getElementById('edit_email').value = usuario.email;
            document.getElementById('edit_id_municipio').value = usuario.id_municipio;
        }
    }

    // Event listener para botones de editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            cargarDatosUsuario(userId);
            const modalEditarUsuario = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
            modalEditarUsuario.show();
        });
    });

    // Event listener para el formulario de edición
    document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('controllers/procesar_editar_usuario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Usuario actualizado!',
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

    // Event listener para botones de eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userName = this.getAttribute('data-nombre');

            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Realmente deseas eliminar al usuario ${userName}?`,
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
                        text: `¿Estás completamente seguro de eliminar al usuario ${userName}?`,
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
                            formData.append('id_user', userId);

                            fetch('controllers/procesar_eliminar_usuario.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Usuario eliminado',
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

<!-- Script para inicializar DataTables -->
<script>
    $(document).ready(function() {
        $('#tablaUsuarios').DataTable({
            language: {
                url: 'cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json',
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
</script>

<!-- Estilos adicionales -->
<style>
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .bi {
        font-size: 1rem;
    }
</style>