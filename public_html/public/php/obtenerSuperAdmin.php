<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT Id_Usuario, Nombres_Usuario FROM usuario WHERE Fk_Roles_Usuario = 3");
$stmt->execute();
$result = $stmt->get_result();

$usuarios = [];

while ($row = $result->fetch_assoc()) {
    $usuarios[] = [
        'id' => $row["Id_Usuario"],
        'nombre' => $row["Nombres_Usuario"]
    ];
}

echo json_encode($usuarios);

$stmt->close();
$conn->close();

?>
