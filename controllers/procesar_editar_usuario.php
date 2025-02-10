<?php
session_start();
require_once '../includes/conexion.php';
require_once '../includes/auth.php';

verificarSesion();

if ($_SESSION['id_municipio'] != 1) {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para realizar esta acción']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = filter_var($_POST['id_user'], FILTER_SANITIZE_NUMBER_INT);
    $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
    $apellido = filter_var($_POST['apellido'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $id_municipio = filter_var($_POST['id_municipio'], FILTER_SANITIZE_NUMBER_INT);

    try {
        $stmt = $pdo->prepare("UPDATE users SET nombre = ?, apellido = ?, email = ?, id_municipio = ? WHERE id_user = ?");
        $stmt->execute([$nombre, $apellido, $email, $id_municipio, $id_user]);

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