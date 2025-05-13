<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([1, 2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$busqueda = $_GET['q'];

$stmt = $conn->prepare("SELECT Id_Profesor, Nombre_Profesor FROM profesor WHERE Nombre_Profesor LIKE ? ORDER BY Nombre_Profesor");
$busquedaParam = '%' . $busqueda . '%';
$stmt->bind_param('s', $busquedaParam);
$stmt->execute();

$result = $stmt->get_result();
$profesores = array();

while ($row = $result->fetch_assoc()) {
    $profesores[] = array(
        'id' => $row['Id_Profesor'],
        'nombre' => $row['Nombre_Profesor']
    );
}

echo json_encode($profesores);

$stmt->close();
$conn->close();
?>