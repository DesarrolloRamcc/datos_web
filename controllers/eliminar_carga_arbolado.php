<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';

verificarSesion();

header('Content-Type: application/json');

try {
    if (!isset($_POST['id'])) {
        throw new Exception('ID no proporcionado');
    }

    $id = $_POST['id'];

    // Verificar que el registro existe y obtener la información de la imagen
    $stmt = $pdo->prepare("SELECT municipio, imagen FROM contadorarboles WHERE id = ?");
    $stmt->execute([$id]);
    $arbol = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$arbol) {
        throw new Exception('Registro no encontrado');
    }

    // Verificar que el usuario tiene acceso a este municipio
    verificarAccesoMunicipio($arbol['municipio']);

    // Iniciar transacción
    $pdo->beginTransaction();

    // Eliminar el registro de la base de datos
    $stmt = $pdo->prepare("DELETE FROM contadorarboles WHERE id = ?");
    if (!$stmt->execute([$id])) {
        throw new Exception('Error al eliminar el registro');
    }

    // Eliminar la imagen si existe
    if (!empty($arbol['imagen'])) {
        $rutaImagen = '../' . ltrim($arbol['imagen'], './');
        if (file_exists($rutaImagen)) {
            if (!unlink($rutaImagen)) {
                throw new Exception('Error al eliminar la imagen del servidor');
            }
        }
    }

    // Confirmar transacción
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Registro eliminado correctamente'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}