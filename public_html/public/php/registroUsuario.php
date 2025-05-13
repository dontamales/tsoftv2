<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once("registroUsuarioFunciones.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Recuperar los datos enviados por el cliente
$data = json_decode(file_get_contents("php://input"));

// Sanitizar y validar los datos
$fk_roles = intval($data->fk_roles);
$nombres = trim($data->nombres);
$apellidos = trim($data->apellidos);
$correo = trim($data->correo);
$numero_control = trim($data->numero_control);
$carrera = intval($data->carrera);
$promedio = floatval($data->promedio);
$telefono = trim($data->telefono);

//$password = 12345678;
$password = generarPasswordAleatoria(8);
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

isDuplicate($conn, "SELECT COUNT(*) AS count FROM usuario WHERE Correo_Usuario = ?", $correo, 'Error: Ya existe un usuario con ese correo electrónico.');

if ($fk_roles === 1) {
    isDuplicate($conn, "SELECT COUNT(*) AS count FROM egresado WHERE Num_Control = ?", $numero_control, 'Error: Ya existe un egresado con ese número de control.');
}

// Registrar el usuario
registrarUsuario($conn, $fk_roles, $nombres, $apellidos, $correo, $hashed_password, $numero_control, $carrera, $promedio, $telefono, $password);