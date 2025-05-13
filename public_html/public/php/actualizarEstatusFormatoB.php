<?php
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once("../../private/conexion.php");
require_once("actualizarEstatusFuncionesFormatoB.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");


$idUsuario = $_POST['user_id'];
$nuevoEstatus = $_POST['status'];
$observaciones = $_POST['observaciones'];

$response = actualizarEstatusObservacionesFormatoB($idUsuario, $nuevoEstatus, $observaciones);

echo json_encode($response);
?>