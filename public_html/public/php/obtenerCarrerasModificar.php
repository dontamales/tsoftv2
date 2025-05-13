<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT Id_Carrera, Nombre_Carrera FROM carrera");

$stmt->execute();
$result = $stmt->get_result();

$carreras = [];

while ($row = $result->fetch_assoc()) {
    $carreras[] = [
        'id' => $row["Id_Carrera"],
        'nombre' => $row["Nombre_Carrera"]
    ];
}

echo json_encode($carreras);

$stmt->close();
$conn->close();

?>