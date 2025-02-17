<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';

verificarSesion();

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID no proporcionado');
    }

    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM contadorarboles WHERE id = ?");
    $stmt->execute([$id]);
    $arbol = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$arbol) {
        throw new Exception('Registro no encontrado');
    }

    // Verificar que el usuario tiene acceso a este municipio
    verificarAccesoMunicipio($arbol['municipio']);

    echo json_encode([
        'success' => true,
        'arbol' => $arbol
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}