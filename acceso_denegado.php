<?php
// Iniciar sesión (por si acaso necesitamos verificar el estado de la sesión en el futuro)
session_start();

// Redirigir al usuario a la página de inicio de sesión
header("Location: PanelAdministrador");
exit();