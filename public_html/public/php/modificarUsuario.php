<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once("modificarUsuarioFunciones.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Recuperar los datos enviados por el cliente
$data = json_decode(file_get_contents("php://input"));

// Sanitizar y validar los datos
$idUsuario = intval($data->idUsuario);
$fk_roles = intval($data->fk_roles);
$nombres = trim($data->nombres);
$apellidos = trim($data->apellidos);
$correo = trim($data->correo);

// Registrar el usuario
modificarUsuario($conn, $fk_roles, $nombres, $apellidos, $correo, $idUsuario);