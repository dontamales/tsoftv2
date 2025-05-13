<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN

function require_roles($required_roles)
{
  // Si no se ha definido el rol del usuario o el rol del usuario no está en los roles requeridos
  if (!isset($_SESSION["user_role"]) || !in_array($_SESSION["user_role"], $required_roles)) {
    header('Location: ../404.php');
    exit;
  }
}
