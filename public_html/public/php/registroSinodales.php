<?php
require_once 'sesion.php'; 
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once("registroSinodalesFunciones.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$data = json_decode(file_get_contents("php://input"));

$sinodal1 = intval($data->sinodal1);
$rolSinodal1 = intval($data->rolSinodal1);
$sinodal2 = intval($data->sinodal2);
$rolSinodal2 = intval($data->rolSinodal2);
$sinodal3 = intval($data->sinodal3);
$rolSinodal3 = intval($data->rolSinodal3);
$sinodal4 = intval($data->sinodal4);
$rolSinodal4 = intval($data->rolSinodal4);
$egresadoProyecto = intval($data->egresadoProyecto);

$resultado = registrarSinodal($conn, $sinodal1, $rolSinodal1, $sinodal2, $rolSinodal2, $sinodal3, $rolSinodal3, $sinodal4, $rolSinodal4, $egresadoProyecto);

if ($resultado === 'Actualización de asignaión de sinodales exitosa' || $resultado === 'Asignación de sinodales exitoso') {
    echo $resultado;
} else {
    echo $resultado;
}
