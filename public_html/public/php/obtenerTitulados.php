<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$titulado_Estatus = 9;

$sql = "SELECT * FROM egresado 
JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
JOIN carrera ON egresado.Fk_Carrera_Egresado = carrera.Id_Carrera
WHERE egresado.FK_Estatus_Egresado = ?
ORDER BY egresado.Fecha_Hora_Ceremonia_Egresado DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $titulado_Estatus);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);

$conn->close();
?>