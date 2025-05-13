<?php
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once("../../private/conexion.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

if (!isset($_POST['user_id'])) {
    echo json_encode(array("error" => "Se requiere un user_id."));
    exit;
}

$idEstudiante =  $_POST['user_id'];

$estatus_Revision_Pendiente = 2;
$estatus_Formato_B_Desaprobado = 3;
$envio_Anexos_I_II = 4;
$envio_Anexo_III = 5;
$sinodales_Pendientes = 6;
$formato_B_Aprobado = 2;

// Obtener datos de un usuario específico
$stmt = $conn->prepare("SELECT egresado.*,
    usuario.Id_Usuario, usuario.Fk_Roles_Usuario, usuario.Nombres_Usuario, usuario.Apellidos_Usuario, usuario.Correo_Usuario, usuario.Fecha_Usuario,
    carrera.*,
    estatus.*,
    direccion.*,
    genero.*,
    proyecto.*,
    profesor.*,
    planes_estudio.*,
    producto_titulacion.*,
    c1.Nombre_Carrera AS Nombre_Carrera_1, 
    c2.Nombre_Carrera AS Nombre_Carrera_2 
    FROM egresado
    JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
    JOIN carrera ON egresado.Fk_Carrera_Egresado = carrera.Id_Carrera
    JOIN estatus ON egresado.FK_Estatus_Egresado = estatus.Id_Estatus
    JOIN direccion ON egresado.Fk_Direccion_Egresado = direccion.Id_Direccion 
    JOIN genero ON egresado.Fk_Sexo_Egresado = genero.Id_Sexo_Genero 
    JOIN proyecto ON egresado.Fk_Proyecto_Egresado = proyecto.Id_Proyecto 
    LEFT JOIN profesor ON egresado.Fk_Asesor_Interno_Egresado = profesor.Id_Profesor
    JOIN planes_estudio ON egresado.Fk_Plan_Estudio_Egresado = planes_estudio.Id_PlanEstudio
    JOIN producto_titulacion ON egresado.Fk_Tipo_Titulacion_Egresado = producto_titulacion.Id_Titulacion
    LEFT JOIN carrera c1 ON egresado.Fk_Carrera_Equipo_Egresado1 = c1.Id_Carrera
    LEFT JOIN carrera c2 ON egresado.Fk_Carrera_Equipo_Egresado2 = c2.Id_Carrera
    WHERE egresado.Fk_Usuario_Egresado = ?
    AND (egresado.FK_Estatus_Egresado = ? 
    OR   egresado.FK_Estatus_Egresado = ? 
    OR   egresado.FK_Estatus_Egresado = ? 
    OR   egresado.FK_Estatus_Egresado = ? 
    OR   egresado.FK_Estatus_Egresado = ?)
    AND egresado.Formato_B_Aprobado_Egresado = ?
");
$stmt->bind_param("iiiiiii", $idEstudiante, $estatus_Revision_Pendiente, $estatus_Formato_B_Desaprobado, $envio_Anexos_I_II, $envio_Anexo_III, $sinodales_Pendientes, $formato_B_Aprobado);
$stmt->execute();
$result = $stmt->get_result();

$datosEstudiante = $result->fetch_assoc();

$stmt->close();

if ($datosEstudiante['Numero_Equipo_Egresados'] == 1) {
    $datosEstudiante['NumeroControl_Equipo_Egresado1'] = '';
    $datosEstudiante['Nombre_Equipo_Egresado1'] = '';
    $datosEstudiante['Nombre_Carrera_Equipo_Egresado1'] = '';
    $datosEstudiante['Nombre_Carrera_1'] = '';
    $datosEstudiante['NumeroControl_Equipo_Egresado2'] = '';
    $datosEstudiante['Nombre_Equipo_Egresado2'] = '';
    $datosEstudiante['Nombre_Carrera_Equipo_Egresado2'] = '';
    $datosEstudiante['Nombre_Carrera_2'] = '';
    
}  else if ($datosEstudiante['Numero_Equipo_Egresados'] == 2) {
    $datosEstudiante['NumeroControl_Equipo_Egresado2'] = '';
    $datosEstudiante['Nombre_Equipo_Egresado2'] = '';
    $datosEstudiante['Nombre_Carrera_Equipo_Egresado2'] = '';
    $datosEstudiante['Nombre_Carrera_2'] = ''; 

}

echo json_encode($datosEstudiante);

?>