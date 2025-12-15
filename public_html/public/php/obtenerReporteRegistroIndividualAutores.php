<?php
require_once 'sesion.php'; // VERIFICACIÓN DE SESIÓN
require_once 'auth.php';   // VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]);        // VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');
// Zona horaria de MySQL para esta sesión
$conn->query("SET time_zone='-06:00'");

// Consulta para obtener los reportes de registro individual de autores
$sql = "SELECT * 
        FROM reporte_registro_individual_autores 
        ORDER BY Fecha_Creacion_Reporte_Registro_Individual_Autores DESC";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
