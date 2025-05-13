<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once("registroPlanEstudioFunciones.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$data = json_decode(file_get_contents("php://input"));

// Sanitizar y validar los datos
$periodoGeneracion = trim($data->periodoGeneracion);
$descripcionPlanAnio = trim($data->descripcionPlanAnio);

// Registrar el plan de estudio
$resultado = registrarPlanEstudio($conn, $periodoGeneracion, $descripcionPlanAnio);

if ($resultado === true) {
    echo json_encode(['message' => 'Registro exitoso']);
} else {
    echo json_encode(['message' => 'Error en el registro']);
}
