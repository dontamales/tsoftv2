<?php
require_once 'sesion.php'; // VERIFICACIÓN DE SESIÓN
require_once 'auth.php';   // VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); // VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Consulta para obtener los reportes de cohorte anual
$sql = "SELECT * FROM reporte_cohorte_anual ORDER BY Fecha_Creacion_Reporte_Cohorte_Anual DESC";
$result = $conn->query($sql);
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
