<?php
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once("../../private/conexion.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$estatus_Revision_Pendiente = 2;
$estatus_Formato_B_Desaprobado = 3;
$envio_Anexos_I_II = 4;
$envio_Anexo_III = 5;
$sinodales_Pendientes = 6;
$formato_B_Aprobado = 2;

$stmt = $conn->prepare("SELECT COUNT(*) 
AS total 
FROM egresado 
WHERE (egresado.Fk_Estatus_Egresado = ? 
    OR egresado.Fk_Estatus_Egresado = ? 
    OR   egresado.Fk_Estatus_Egresado = ? 
    OR   egresado.Fk_Estatus_Egresado = ? 
    OR   egresado.Fk_Estatus_Egresado = ?) 
    AND egresado.Formato_B_Aprobado_Egresado = ?
");
$stmt->bind_param("iiiiii", $estatus_Revision_Pendiente, $estatus_Formato_B_Desaprobado, $envio_Anexos_I_II, $envio_Anexo_III, $sinodales_Pendientes, $formato_B_Aprobado);
$stmt->execute();
$countResult = $stmt->get_result();
$count = $countResult->fetch_assoc();
echo json_encode($count);
$stmt->close();
?>