<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once("registroTipoTitulacionFunciones.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$data = json_decode(file_get_contents("php://input"));

// Sanitizar y validar los datos
$nombreTipoTitulacion = trim($data->nombreTipoTitulacion);
$documentos = $data->documentos;
$planEstudio = intval($data->planEstudio);

// Registrar el tipo de titulación
$resultado = registrarTipoTitulacion($conn, $nombreTipoTitulacion);
$resultado2 = registrarTipoTitulacionPlanEstudio($conn, $nombreTipoTitulacion, $planEstudio);
$resultado3 = registrarTipoTitulacionDocumentos($conn, $nombreTipoTitulacion, $documentos);

if ($resultado == true) {
    if ($resultado2 == true) {
        if ($resultado3 == true) {
            echo json_encode(['message' => 'Registro exitoso']);
        } else {
            echo json_encode(['message' => 'Registro de tipo de titulación exitoso, pero hubo un error al registrar los documentos']);
        }
    } else {
        echo json_encode(['message' => 'Registro de tipo de titulación exitoso, pero hubo un error al registrar el plan de estudio']);
    }
} else {
    echo json_encode(['message' => 'Error en el registro']);
}