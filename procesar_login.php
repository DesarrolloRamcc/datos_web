<?php
session_start();
require_once 'includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $sql = "SELECT u.id_user, u.email, u.password, u.nombre, u.apellido, u.id_municipio, u.super_admin, m.name as nombre_muni 
            FROM users u 
            LEFT JOIN municipios m ON u.id_municipio = m.id 
            WHERE u.email = ?";
    
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (password_verify($password, $usuario['password'])) {
                // Iniciar sesión y guardar datos del usuario
                $_SESSION['loggedin'] = true;
                $_SESSION['id_user'] = $usuario['id_user'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['apellido'] = $usuario['apellido'];
                $_SESSION['id_municipio'] = $usuario['id_municipio'];
                $_SESSION['nombre_municipio'] = $usuario['nombre_muni'];
                $_SESSION['super_admin'] = $usuario['super_admin'];

                // Redirigir según si es super_admin o no
                if ($usuario['super_admin'] == 1) {
                    $redirect = "admin.php";
                } else {
                    $redirect = "PanelAdministrador";
                }

                // Preparar respuesta JSON
                $response = [
                    'success' => true,
                    'redirect' => $redirect
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'La contraseña ingresada es incorrecta'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'No existe una cuenta con ese correo electrónico'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Error en el servidor'
        ];
    }

    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // Si no es una solicitud POST, redirigir a la página de inicio de sesión
    header('Location: InicioDeSesion');
    exit;
}