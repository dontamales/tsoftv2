<?php
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1); // La cookie de sesión solo se enviará a través de conexiones HTTP
    ini_set('session.cookie_secure', 1); // Solo se puede acceder a la cookie de sesión a través de conexiones HTTPS
    ini_set('session.use_only_cookies', 1); // El identificador de sesión solo se puede pasar a través de cookies
    ini_set('session.cookie_lifetime', 0); // La cookie de sesión expirará cuando el navegador se cierre
    session_start();
}
?>
