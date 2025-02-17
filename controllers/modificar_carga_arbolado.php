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
    $date = $_POST['date'];
    $cantidad = $_POST['cantidad'];
    $especie = $_POST['especie'];
    $publicoprivado = $_POST['publicoprivado'];
    $quienLoPlanto = $_POST['quienLoPlanto'];
    $descripcion = $_POST['descripcion'];
    $municipio = $_POST['municipio'];

    // Verificar que el registro existe y pertenece al municipio correcto
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

    // Preparar la consulta base
    $sql = "UPDATE contadorarboles SET date = ?, cantidad = ?, especie = ?, publicoprivado = ?, quienLoPlanto = ?, descripcion = ?";
    $params = [$date, $cantidad, $especie, $publicoprivado, $quienLoPlanto, $descripcion];

    // Manejar la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0) {
        // Validar el tipo de archivo
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            throw new Exception('Formato de imagen no permitido. Use JPG, JPEG o PNG.');
        }

        // Generar nombre único con timestamp
        $timestamp = time();
        $new_filename = 'carga_arbol_' . $id . '_' . $timestamp . '.' . $ext;
        $upload_path = '../uploads/arbolado/' . $new_filename;
        $db_path = './uploads/arbolado/' . $new_filename;

        // Crear directorio si no existe
        if (!file_exists('../uploads/arbolado/')) {
            mkdir('../uploads/arbolado/', 0777, true);
        }

        // Eliminar imagen anterior si existe
        if (!empty($arbol['imagen'])) {
            $rutaImagenAnterior = '../' . ltrim($arbol['imagen'], './');
            if (file_exists($rutaImagenAnterior)) {
                unlink($rutaImagenAnterior);
            }
        }

        // Subir nueva imagen
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
            throw new Exception('Error al subir la imagen');
        }

        // Agregar imagen a la consulta SQL
        $sql .= ", imagen = ?";
        $params[] = $db_path;
    }

    // Completar la consulta
    $sql .= " WHERE id = ?";
    $params[] = $id;

    // Ejecutar la actualización
    $stmt = $pdo->prepare($sql);
    if (!$stmt->execute($params)) {
        throw new Exception('Error al actualizar el registro');
    }

    // Confirmar transacción
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Registro actualizado correctamente'
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