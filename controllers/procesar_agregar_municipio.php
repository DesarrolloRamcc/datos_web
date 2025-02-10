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
    $nombre_muni = filter_var($_POST['nombre_muni'], FILTER_SANITIZE_STRING);
    $id_provincia = filter_var($_POST['id_provincia'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Verificar si el municipio ya existe
        $stmt = $pdo->prepare("SELECT id_municipio FROM municipios WHERE nombre_muni = ? AND id_provincia = ?");
        $stmt->execute([$nombre_muni, $id_provincia]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'El municipio ya existe en esta provincia']);
            exit;
        }

        // Insertar el nuevo municipio
        $sql = "INSERT INTO municipios (nombre_muni, id_provincia) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre_muni, $id_provincia]);

        echo json_encode(['success' => true, 'message' => 'Municipio agregado correctamente']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al agregar el municipio: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}