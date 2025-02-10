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

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
        $stmt->execute([$id_user]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el usuario']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}