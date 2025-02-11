<?php
// procesar_editar_usuario.php
session_start();
require_once '../includes/conexion.php';
require_once '../includes/auth.php';

verificarSesion();
verificarSuperAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = filter_var($_POST['id_user'], FILTER_SANITIZE_NUMBER_INT);
    $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
    $apellido = filter_var($_POST['apellido'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $id_municipio = !empty($_POST['id_municipio']) ? filter_var($_POST['id_municipio'], FILTER_SANITIZE_NUMBER_INT) : null;
    $super_admin = isset($_POST['super_admin']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE users SET nombre = ?, apellido = ?, email = ?, id_municipio = ?, super_admin = ? WHERE id_user = ?");
        $stmt->execute([$nombre, $apellido, $email, $id_municipio, $super_admin, $id_user]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se realizaron cambios en el usuario']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}