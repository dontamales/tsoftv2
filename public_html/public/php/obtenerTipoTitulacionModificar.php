<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT Id_Titulacion, Tipo_Producto_Titulacion FROM producto_titulacion");
$stmt->execute();
$result = $stmt->get_result();

$planes_estudio = [];

while ($row = $result->fetch_assoc()) {
    $planes_estudio[] = [
        'id' => $row["Id_Titulacion"],
        'nombre' => $row["Tipo_Producto_Titulacion"]
    ];
}

echo json_encode($planes_estudio);

$stmt->close();
$conn->close();

?>
