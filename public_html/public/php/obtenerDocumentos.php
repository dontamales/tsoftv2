<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT Id_Documentos_Pendientes, Descripcion_Documentos_Pendientes FROM documentos_pendientes");
$stmt->execute();
$result = $stmt->get_result();

$profesores = [];

while ($row = $result->fetch_assoc()) {
    $profesores[] = [
        'id' => $row['Id_Documentos_Pendientes'],
        'nombre' => $row['Descripcion_Documentos_Pendientes']
    ];
}

echo json_encode($profesores);

$stmt->close();
$conn->close();
