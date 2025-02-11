<?php
header('Content-Type: application/json');
require_once '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Obtener y validar los datos
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $email = $_POST['email'];
        $id_municipio = $_POST['id_municipio'] ?: null;
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $super_admin = isset($_POST['super_admin']) ? 1 : 0;

        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id_user FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
            exit;
        }

        // Insertar el nuevo usuario
        $sql = "INSERT INTO users (nombre, apellido, email, password, id_municipio, super_admin) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $email, $password, $id_municipio, $super_admin]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al crear el usuario: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}