<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once("modificarLibroFunciones.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Recuperar los datos enviados por el cliente
$data = json_decode(file_get_contents("php://input"));

// Sanitizar y validar los datos
$idLibro = intval($data->idLibro);
$nombreLibro = trim($data->nombreLibro);


// Registrar el usuario
modificarLibro($conn, $idLibro, $nombreLibro);