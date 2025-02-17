<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';

verificarSesion();

header('Content-Type: application/json');

try {
    if (!isset($_POST['id']) || !isset($_POST['id_municipio'])) {
        throw new Exception('ID o ID de municipio no proporcionado');
    }

    $id = $_POST['id'];
    $id_municipio = $_POST['id_municipio'];

    // Verificar si el usuario es superadmin o tiene acceso al municipio
    if (!esSuperAdmin() && !tieneAccesoMunicipio($id_municipio)) {
        throw new Exception('No tiene permiso para eliminar este arbolado');
    }

    // Verificar que el registro existe y obtener la información de la imagen
    $stmt = $pdo->prepare("SELECT municipio, imagen FROM contadorarboles WHERE id = ?");
    $stmt->execute([$id]);
    $arbol = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$arbol || $arbol['municipio'] != $id_municipio) {
        throw new Exception('Registro no encontrado o no pertenece al municipio especificado');
    }

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