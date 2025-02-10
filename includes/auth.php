<?php
function iniciarSesion() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function verificarSesion() {
    iniciarSesion();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: login.php");
        exit;
    }
}

function verificarAccesoMunicipio($id_municipio_pagina) {
    verificarSesion();
    if ($_SESSION['id_municipio'] != $id_municipio_pagina) {
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
    header("Location: login.php");
    exit;
}