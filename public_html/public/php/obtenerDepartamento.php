<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT Id_Departamento, Nombre_Departamento FROM departamento");
$stmt->execute();
$result = $stmt->get_result();

$departamentos = [];

while ($row = $result->fetch_assoc()) {
    $departamentos[] = [
        'id' => $row['Id_Departamento'],
        'nombre' => $row['Nombre_Departamento']
    ];
}

echo json_encode($departamentos);

$stmt->close();
$conn->close();
