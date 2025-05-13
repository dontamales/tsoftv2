<?php
$inicio = "../index.php"; #VARIABLES DE APOYO
$nivel = "Error con el rol";
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) { #VERIFICACIÓN DE USUARIO LOGUEADO
  header("Location: ../php/logout.php");
  exit;
} else {
  $id = $_SESSION['user_id']; #VARIABLES DE APOYO / ADEMÁS CON ESTO LE DAREMOS UN TOKEN AL SUPER ADMINISTRADOR MAESTRO
  $rol = $_SESSION['user_role']; #VARIABLES DE APOYO
  if ($_SESSION["user_role"] == 1) { #VERIFICACIÓN DE USUARIO SUSTENTANTE
    $inicio = "../views/userDashboard.php";
    $nivel = "Sustentante";
  }
  if ($_SESSION["user_role"] == 2 || $_SESSION["user_role"] == 3 || $_SESSION["user_role"] == 4 || $_SESSION["user_role"] == 5 || $_SESSION["user_role"] == 6) { #VERIFICACIÓN DE USUARIO ADMINISTRADOR
    $inicio = "../views/adminDashboard.php";

    switch ($rol) {
      case 2:
        $nivel = "Administrador";
        break;
      case 3:
        $nivel = "Super administrador";
        break;
      case 4:
        $nivel = "Secretario";
        break;
      case 5:
        $nivel = "Auxiliar";
        break;
        case 6: 
        $nivel = "Servicios escolares";
        break;
      default:
        $nivel = "Error con el rol";
        break;
    }
  }
}
?>