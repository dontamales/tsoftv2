<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once("../../private/conexion.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$anexo_III_Aprobado = 1;
$formato_B_Aprobado = 1;
$titulado_Estatus = 9;

// Obtener datos de usuario
$stmt = $conn->prepare("SELECT * FROM egresado 
JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario 
LEFT JOIN asignacion_sinodales ON egresado.Fk_Proyecto_Egresado = asignacion_sinodales.Fk_Proyecto_Sinodales
WHERE egresado.Fk_Proyecto_Egresado IS NOT NULL 
AND egresado.Anexo_III_Egresado = ?
AND egresado.Formato_B_Aprobado_Egresado = ?
AND egresado.FK_Estatus_Egresado != ?");
$stmt->bind_param("iii", $anexo_III_Aprobado, $formato_B_Aprobado, $titulado_Estatus);
$stmt->execute();
$result = $stmt->get_result();

$egresado = [];

while ($row = $result->fetch_assoc()) {
    $egresado[] = [
        'id' => $row["Fk_Proyecto_Egresado"],
        'nombre' => $row["Num_Control"] . " - " . $row["Nombres_Usuario"] . " " . $row["Apellidos_Usuario"],
    ];
}

echo json_encode($egresado);

$stmt->close();
$conn->close();

?>