<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$fecha = date("Y-m-d");

$stmt = $conn->prepare("SELECT id, fecha, conteo FROM correos_enviados WHERE fecha = ?");
$stmt->bind_param("s", $fecha);
$stmt->execute();
$result = $stmt->get_result();
$conteo = $result->fetch_assoc();

$stmt->close();

$cuenta = $conteo['conteo'] ?? 0;

echo json_encode(array("correosRestantes" => 100 - $cuenta));
?>