<?php
function iniciarSesion() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function verificarSesion() {
    iniciarSesion();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: InicioDeSesion");
        exit;
    }
}

function verificarSuperAdmin() {
    verificarSesion();
    if (!isset($_SESSION['super_admin']) || $_SESSION['super_admin'] != 1) {
        header("Location: acceso_denegado.php");
        exit;
    }
}

function obtenerIdMunicipioActual() {
    verificarSesion();
    return $_SESSION['id_municipio'] ?? null;
}

function esUsuarioAutenticado() {
    iniciarSesion();
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

function cerrarSesion() {
    iniciarSesion();
    $_SESSION = array();
    session_destroy();
    header("Location: InicioDeSesion");
    exit;
}

function verificarAccesoMunicipio($id_municipio_solicitado) {
    verificarSesion();
    $id_municipio_usuario = $_SESSION['id_municipio'] ?? null;
    $es_super_admin = $_SESSION['super_admin'] ?? false;

    if ($es_super_admin) {
        // Los superadmins tienen acceso a todos los municipios
        return;
    }

    if ($id_municipio_usuario === null) {
        // Si el usuario no tiene un municipio asociado, redirigir a una página de error
        header("Location: error.php?mensaje=No tienes un municipio asociado");
        exit;
    }

    if ($id_municipio_usuario != $id_municipio_solicitado) {
        // Si el municipio solicitado no coincide con el del usuario, redirigir al correcto
        $nombre_municipio = obtenerNombreMunicipio($id_municipio_usuario);
        header("Location: Arbolado_" . urlencode($nombre_municipio));
        exit;
    }
}

function tieneAccesoMunicipio($id_municipio) {
    iniciarSesion();
    $es_super_admin = $_SESSION['super_admin'] ?? false;
    $id_municipio_usuario = $_SESSION['id_municipio'] ?? null;

    return $es_super_admin || $id_municipio_usuario == $id_municipio;
}

// Función para obtener el nombre del municipio
function obtenerNombreMunicipio($id_municipio) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT name FROM municipios WHERE id = ?");
    $stmt->execute([$id_municipio]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado['name'] ?? 'Municipio Desconocido';
}

function esSuperAdmin() {
    iniciarSesion();
    return isset($_SESSION['super_admin']) && $_SESSION['super_admin'] == 1;
}