<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once '../../private/conexion.php';

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$anexo_III_Egresado = 1;
$formato_B_Aprobado_Egresado = 1;
$sinodales_Asignados_Estatus = 7;
$fecha_Ceremonia_Estatus = 8;
$null_date = '0000-00-00 00:00:00';


try {
    $stmt = $conn->prepare("SELECT
    e.Num_Control,
    u.Nombres_Usuario,
    u.Apellidos_Usuario,
    c.Nombre_Carrera,
    p.Nombre_Proyecto,
    e.Fecha_Hora_Ceremonia_Egresado
FROM egresado e
JOIN usuario u ON e.Fk_Usuario_Egresado = u.ID_Usuario
LEFT JOIN carrera c ON e.Fk_Carrera_Egresado = c.ID_Carrera
LEFT JOIN proyecto p ON e.Fk_Proyecto_Egresado = p.ID_Proyecto
WHERE e.Anexo_III_Egresado = ?
    AND (e.FK_Estatus_Egresado =  ? OR e.FK_Estatus_Egresado = ?)
    AND e.Formato_B_Aprobado_Egresado = ?
    AND (e.Fecha_Hora_Ceremonia_Egresado >= NOW() 
    OR  e.Fecha_Hora_Ceremonia_Egresado IS NULL 
    OR  e.Fecha_Hora_Ceremonia_Egresado = ?);");
    $stmt->bind_param("iiiis", $anexo_III_Egresado, $sinodales_Asignados_Estatus, $fecha_Ceremonia_Estatus, $formato_B_Aprobado_Egresado, $null_date);


    if (!$stmt) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $egresados = array();
    while ($row = $result->fetch_assoc()) {
        $egresados[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($egresados);

    $conn->close();
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(["error" => $e->getMessage()]);
}
