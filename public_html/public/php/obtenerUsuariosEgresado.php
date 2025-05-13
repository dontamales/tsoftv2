<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$idRol = 1;

$stmt = $conn->prepare("SELECT Id_Usuario, Nombres_Usuario, Apellidos_Usuario, Num_Control 
FROM usuario
JOIN egresado 
ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario 
WHERE usuario.Fk_Roles_Usuario = ?
ORDER BY Num_Control DESC");
$stmt->bind_param("i", $idRol);
$stmt->execute();
$result = $stmt->get_result();

$egresados = [];

while ($row = $result->fetch_assoc()) {
    $egresados[] = [
        'id' => $row['Id_Usuario'],
        'nombre' => $row['Nombres_Usuario'] . " " . $row['Apellidos_Usuario'],
        'numControl' => $row['Num_Control']
    ];
}

echo json_encode($egresados);

$stmt->close();
$conn->close();