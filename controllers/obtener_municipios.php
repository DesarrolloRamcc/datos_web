<?php
header('Content-Type: application/json');
require_once '../includes/conexion.php';

try {
    $stmt = $pdo->prepare("SELECT id, name FROM municipios ORDER BY name");
    $stmt->execute();
    $municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($municipios);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener municipios: ' . $e->getMessage()]);
}