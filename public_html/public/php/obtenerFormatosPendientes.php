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

// Obtener datos de usuario
$stmt = $conn->prepare("SELECT * 
    FROM egresado
    JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
    JOIN carrera carrera1 ON egresado.Fk_Carrera_Egresado = carrera1.Id_Carrera
    JOIN estatus ON egresado.Fk_Estatus_Egresado = estatus.Id_Estatus
    JOIN direccion ON egresado.Fk_Direccion_Egresado = direccion.Id_Direccion 
    JOIN genero ON egresado.Fk_Sexo_Egresado = genero.Id_Sexo_Genero 
    JOIN proyecto ON egresado.Fk_Proyecto_Egresado = proyecto.Id_Proyecto 
    LEFT JOIN profesor ON egresado.Fk_Asesor_Interno_Egresado = profesor.Id_Profesor
    JOIN planes_estudio ON egresado.Fk_Plan_Estudio_Egresado = planes_estudio.Id_PlanEstudio
    JOIN producto_titulacion ON egresado.Fk_Tipo_Titulacion_Egresado = producto_titulacion.Id_Titulacion
    LEFT JOIN carrera carrera2 ON egresado.Fk_Carrera_Equipo_Egresado1 = carrera2.Id_Carrera
    LEFT JOIN carrera carrera3 ON egresado.Fk_Carrera_Equipo_Egresado2 = carrera3.Id_Carrera
    WHERE (egresado.Fk_Estatus_Egresado = ? 
    OR egresado.Fk_Estatus_Egresado = ? 
    OR   egresado.Fk_Estatus_Egresado = ? 
    OR   egresado.Fk_Estatus_Egresado = ? 
    OR   egresado.Fk_Estatus_Egresado = ?) 
    AND egresado.Formato_B_Aprobado_Egresado = ?
    ORDER BY egresado.Fecha_Envio_Formato_B_Egresado ASC
    LIMIT 10
");
$stmt->bind_param("iiiiii", $estatus_Revision_Pendiente, $estatus_Formato_B_Desaprobado, $envio_Anexos_I_II, $envio_Anexo_III, $sinodales_Pendientes, $formato_B_Aprobado);
$stmt->execute();
$result = $stmt->get_result();

$datosEstudiante = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close(); 

echo json_encode($datosEstudiante);
?>