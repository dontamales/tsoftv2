<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT Id_Libro, Descripcion_Libro FROM libro");
$stmt->execute();
$result = $stmt->get_result();

$libro_foja = []; // Cambio de nombre de variable para evitar conflicto

while ($row = $result->fetch_assoc()) {
    $libro_foja[] = [
        'idL' => $row['Id_Libro'],
        'nombreL' => $row['Descripcion_Libro']
    ];
}

echo json_encode($libro_foja);

$stmt->close();
$conn->close();
?>

