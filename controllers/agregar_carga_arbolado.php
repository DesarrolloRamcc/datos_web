<?php
require_once '../includes/conexion.php';
require_once '../includes/auth.php';

verificarSesion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $cantidad = $_POST['cantidad'];
    $especie = $_POST['especie'];
    $publicoprivado = $_POST['publicoprivado'];
    $quienLoPlanto = $_POST['quienLoPlanto'];
    $descripcion = $_POST['descripcion'];
    $municipio = $_POST['municipio'];

    // Validar que el municipio del usuario coincida con el enviado
    if ($municipio != $_SESSION['id_municipio']) {
        echo json_encode(['success' => false, 'message' => 'Error de autenticación']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO contadorarboles (date, cantidad, especie, publicoprivado, quienLoPlanto, descripcion, municipio) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date, $cantidad, $especie, $publicoprivado, $quienLoPlanto, $descripcion, $municipio]);

        $id_arbol = $pdo->lastInsertId();

        // Manejo de la imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['imagen']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                throw new Exception('Formato de imagen no permitido');
            }

            $new_filename = 'carga_arbol_' . $id_arbol . '.' . $ext;
            $upload_path = '../uploads/arbolado/' . $new_filename;
            $db_path = './uploads/arbolado/' . $new_filename; // Ruta para guardar en la base de datos

            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                throw new Exception('Error al subir la imagen');
            }

            // Actualizar el registro con la ruta completa de la imagen
            $sql = "UPDATE contadorarboles SET imagen = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$db_path, $id_arbol]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Carga de árboles registrada con éxito']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}