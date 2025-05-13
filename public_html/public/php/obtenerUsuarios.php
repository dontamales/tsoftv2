<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');


$stmt = $conn->prepare("SELECT * FROM usuario 
JOIN roles ON usuario.Fk_Roles_Usuario = roles.Id_Roles
ORDER BY Rol ASC");
$stmt->execute();
$result = $stmt->get_result();

$egresados = [];

while ($row = $result->fetch_assoc()) {
    $egresados[] = [
        'id' => $row['Id_Usuario'],
        'nombre' => $row['Nombres_Usuario'] . " " . $row['Apellidos_Usuario'],
        'rol' => $row['Rol'],
    ];
}

echo json_encode($egresados);

$stmt->close();
$conn->close();

?>