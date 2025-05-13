<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once("modificarPlanEstudioFunciones.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Recuperar los datos enviados por el cliente
$data = json_decode(file_get_contents("php://input"));

// Sanitizar y validar los datos
$idPlanEstudio = intval($data->idPlanEstudio);
$periodoPlanEstudio = trim($data->periodoPlanEstudio);
$descripcionPlanEstudio = trim($data->descripcionPlanEstudio);


// Registrar el usuario
modificarPlanEstudio($conn, $idPlanEstudio, $periodoPlanEstudio, $descripcionPlanEstudio);