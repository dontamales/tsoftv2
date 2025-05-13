<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN


// Limpiamos todas las variables de sesión
$_SESSION = array();

// Si la sesión contenía cookies, eliminamos las cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_regenerate_id(true);

// Destruimos la sesión
session_destroy();

// Redirijimos al login
header("Location: ../index.php");
exit;
?>