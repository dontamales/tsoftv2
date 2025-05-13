<?php
require_once 'sesion.php';
require_once 'auth.php';
require_roles([2, 3, 4, 5, 6]);
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$idUsuario = $_POST['id'];

$stmt = $conn->prepare("SELECT 
    usuario.Id_Usuario, usuario.Fk_Roles_Usuario, usuario.Nombres_Usuario, usuario.Apellidos_Usuario, usuario.Correo_Usuario, usuario.Fecha_Usuario,
    egresado.*, 
    anexo_i_ii.*, 
    carrera.*,
    direccion.*, 
    egresados_documentos.*, 
    documentos_pendientes.*, 
    genero.*, 
    estatus.*,
    planes_estudio.*, 
    producto_titulacion.*, 
    producto_titulacion_documentos_pendientes.*, 
    profesor.Nombre_Profesor AS Nombre_Asesor, 
    proyecto.*, 
    asignacion_sinodales.*,
    c1.Nombre_Carrera AS Nombre_Carrera_1, 
    c2.Nombre_Carrera AS Nombre_Carrera_2, 
    s1.Nombre_Profesor AS Nombre_Sinodal_1, 
    s2.Nombre_Profesor AS Nombre_Sinodal_2, 
    s3.Nombre_Profesor AS Nombre_Sinodal_3, 
    s4.Nombre_Profesor AS Nombre_Sinodal_4

FROM usuario 
JOIN egresado ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario 
LEFT JOIN anexo_i_ii ON anexo_i_ii.Fk_Egresado_Anexo_I_II = egresado.Num_Control 
JOIN carrera ON carrera.Id_Carrera = egresado.Fk_Carrera_Egresado 
LEFT JOIN direccion ON direccion.Id_Direccion = egresado.Fk_Direccion_Egresado 
LEFT JOIN egresados_documentos ON egresados_documentos.Fk_NumeroControl = egresado.Num_Control 
LEFT JOIN documentos_pendientes ON documentos_pendientes.Id_Documentos_Pendientes = egresados_documentos.Fk_Documentos_Pendientes2 
LEFT JOIN genero ON genero.Id_Sexo_Genero = egresado.Fk_Sexo_Egresado 
LEFT JOIN estatus ON egresado.FK_Estatus_Egresado = estatus.Id_Estatus
LEFT JOIN planes_estudio ON planes_estudio.Id_PlanEstudio = egresado.Fk_Plan_Estudio_Egresado 
LEFT JOIN producto_titulacion ON producto_titulacion.Id_Titulacion = egresado.Fk_Tipo_Titulacion_Egresado 
LEFT JOIN producto_titulacion_documentos_pendientes ON producto_titulacion_documentos_pendientes.Fk_Producto_Titulacion_Documentos_Pendientes = producto_titulacion.Id_Titulacion 
LEFT JOIN profesor ON profesor.Id_Profesor = egresado.Fk_Asesor_Interno_Egresado 
LEFT JOIN proyecto ON proyecto.Id_Proyecto = egresado.Fk_Proyecto_Egresado 
LEFT JOIN asignacion_sinodales ON asignacion_sinodales.Fk_Proyecto_Sinodales = proyecto.Id_Proyecto
LEFT JOIN carrera c1 ON egresado.Fk_Carrera_Equipo_Egresado1 = c1.Id_Carrera
LEFT JOIN carrera c2 ON egresado.Fk_Carrera_Equipo_Egresado2 = c2.Id_Carrera 
LEFT JOIN profesor s1 ON s1.Id_Profesor = asignacion_sinodales.Fk_Sinodal_1 
LEFT JOIN profesor s2 ON s2.Id_Profesor = asignacion_sinodales.Fk_Sinodal_2 
LEFT JOIN profesor s3 ON s3.Id_Profesor = asignacion_sinodales.Fk_Sinodal_3 
LEFT JOIN profesor s4 ON s4.Id_Profesor = asignacion_sinodales.Fk_Sinodal_4 
WHERE usuario.Id_Usuario = ?");

$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

$datos = $result->fetch_assoc();

if ($datos['Numero_Equipo_Egresados'] == 1) {
    $datos['NumeroControl_Equipo_Egresado1'] = '';
    $datos['Nombre_Equipo_Egresado1'] = '';
    $datos['Nombre_Carrera_Equipo_Egresado1'] = '';
    $datos['Nombre_Carrera_1'] = '';
    $datos['NumeroControl_Equipo_Egresado2'] = '';
    $datos['Nombre_Equipo_Egresado2'] = '';
    $datos['Nombre_Carrera_Equipo_Egresado2'] = '';
    $datos['Nombre_Carrera_2'] = '';
    
}  else if ($datos['Numero_Equipo_Egresados'] == 2) {
    $datos['NumeroControl_Equipo_Egresado2'] = '';
    $datos['Nombre_Equipo_Egresado2'] = '';
    $datos['Nombre_Carrera_Equipo_Egresado2'] = '';
    $datos['Nombre_Carrera_2'] = ''; 

}

echo json_encode($datos);

$stmt->close();
$conn->close();
