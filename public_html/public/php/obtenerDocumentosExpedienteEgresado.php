<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Documentos de residencias (los que ya no se piden) JH20251022
const DOCS_RESIDENCIAS = [6, 7];
const PRODUCTOS_EXENTOS = [12, 14, 15, 16, 17];
const FECHA_EXENCION_RESIDENCIAS = '2025-08-15';


// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json; charset=utf-8');  // deja solo JSON
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');         // guarda errores en archivo

// header('Content-Type: application/json');
// header('Content-Type: application/x-www-form-urlencoded');


$idUsuario = $_POST['id'];

$stmt_por_revisar = $conn->prepare("SELECT *
FROM egresado 
JOIN proyecto ON egresado.Fk_Proyecto_Egresado = proyecto.Id_Proyecto 
JOIN carrera ON egresado.Fk_Carrera_Egresado = carrera.Id_Carrera
JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
JOIN producto_titulacion ON egresado.Fk_Tipo_Titulacion_Egresado = producto_titulacion.Id_Titulacion
LEFT JOIN egresados_documentos ON egresado.Num_Control = egresados_documentos.Fk_NumeroControl
LEFT JOIN documentos_pendientes ON egresados_documentos.Fk_Documentos_Pendientes2  = documentos_pendientes.Id_Documentos_Pendientes
WHERE usuario.Id_Usuario = ?");
$stmt_por_revisar->bind_param('i', $idUsuario);
$stmt_por_revisar->execute();

$result = $stmt_por_revisar->get_result();

$egresados = array();

$stmt_pendientes = $conn->prepare("SELECT dp.Id_Documentos_Pendientes, dp.Descripcion_Documentos_Pendientes
    FROM producto_titulacion_documentos_pendientes ptdp
    JOIN documentos_pendientes dp ON dp.Id_Documentos_Pendientes = ptdp.Fk_Documentos_Pendientes
    WHERE ptdp.Fk_Producto_Titulacion_Documentos_Pendientes = ?
");

$stmt_aceptados = $conn->prepare("SELECT *
FROM egresados_documentos 
JOIN documentos_pendientes ON egresados_documentos.Fk_Documentos_Pendientes2  = documentos_pendientes.Id_Documentos_Pendientes
JOIN egresado ON egresado.Num_Control = egresados_documentos.Fk_NumeroControl
JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
WHERE egresados_documentos.Aceptado_Egresado_Documentos = 1
AND usuario.Id_Usuario = ?");

$stmt_totales_entregados = $conn->prepare("SELECT *
FROM egresado 
JOIN proyecto ON egresado.Fk_Proyecto_Egresado = proyecto.Id_Proyecto 
JOIN carrera ON egresado.Fk_Carrera_Egresado = carrera.Id_Carrera
JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
JOIN producto_titulacion ON egresado.Fk_Tipo_Titulacion_Egresado = producto_titulacion.Id_Titulacion
JOIN egresados_documentos ON egresado.Num_Control = egresados_documentos.Fk_NumeroControl
JOIN documentos_pendientes ON egresados_documentos.Fk_Documentos_Pendientes2  = documentos_pendientes.Id_Documentos_Pendientes
WHERE usuario.Id_Usuario = ?");

while ($fila = $result->fetch_assoc()) {
    $numControl = $fila['Num_Control'];
    
    if (!isset($egresados[$numControl])) {
        $productoId = (int)$fila['Id_Titulacion'];
        $fechaUsuario = $fila['Fecha_Usuario'];
    $esExento = in_array($productoId, PRODUCTOS_EXENTOS, true);
    $fechaExento = $fechaUsuario ? (strtotime($fechaUsuario) > strtotime(FECHA_EXENCION_RESIDENCIAS)) : false;
        $excluirResidencias = $esExento && $fechaExento;
        $egresados[$numControl] = array(
            'Num_Control' => $fila['Num_Control'],
            'Nombres_Usuario' => $fila['Nombres_Usuario'],
            'Apellidos_Usuario' => $fila['Apellidos_Usuario'],
            'Correo_Usuario' => $fila['Correo_Usuario'],
            'Nombre_Proyecto' => $fila['Nombre_Proyecto'],
            'Nombre_Carrera' => $fila['Nombre_Carrera'],
            'Id_Tipo_Producto_Titulacion' => (int)($fila['Id_Titulacion'] ?? 0),
            'Tipo_Producto_Titulacion' => $fila['Tipo_Producto_Titulacion'],
            // flag para indicar si se deben excluir los documentos de residencias (6,7)
            'ExcluirResidencias' => $excluirResidencias,
            'DocumentosPorRevisar' => array(),
            'DocumentosPendientes' => array(),
        );
    }

    $egresados[$numControl]['DocumentosPorRevisar'][] = array(
        'Num_Control' => $fila['Num_Control'],
        'Id_Documentos_Pendientes' => $fila['Id_Documentos_Pendientes'],
        'Descripcion_Documentos_Pendientes' => $fila['Descripcion_Documentos_Pendientes'],
        'Direccion_Archivo_Egresados_Documentos' => $fila['Direccion_Archivo_Egresados_Documentos'],
        'Fecha_Documento_Subido_Egresado_Documentos' => $fila['Fecha_Documento_Subido_Egresado_Documentos'],
        'Aceptado_Egresado_Documentos' => $fila['Aceptado_Egresado_Documentos'],
    );
}

foreach ($egresados as $numControl => &$egresado) {
    // Documentos Aprobados
    $stmt_aceptados->bind_param('i', $idUsuario);
    $stmt_aceptados->execute();
    $result_aceptados = $stmt_aceptados->get_result();

    $documentosAprobados = array();
    while ($fila_aceptados = $result_aceptados->fetch_assoc()) {
        // Asegurarnos de tomar solo los documentos que pertenecen a este Num_Control
        if (isset($fila_aceptados['Fk_NumeroControl']) && $fila_aceptados['Fk_NumeroControl'] != $numControl) {
            continue;
        }
        $idDoc = (int)$fila_aceptados['Id_Documentos_Pendientes'];
        // Omitir documentos de residencias si aplica la exención
        if ($egresado['ExcluirResidencias'] && in_array($idDoc, DOCS_RESIDENCIAS, true)) {
            continue;
        }
        $documentosAprobados[] = $fila_aceptados['Descripcion_Documentos_Pendientes'];
    }

    $egresado['DocumentosAprobados'] = $documentosAprobados;

    // Documentos Totales
    $stmt_totales_entregados->bind_param('i', $idUsuario);
    $stmt_totales_entregados->execute();
    $result_totales_entregados = $stmt_totales_entregados->get_result();

    $documentosTotales = array();
    while ($fila_totales_entregados = $result_totales_entregados->fetch_assoc()) {
        // Filtrar por Num_Control para este egresado
        if (isset($fila_totales_entregados['Fk_NumeroControl']) && $fila_totales_entregados['Fk_NumeroControl'] != $numControl) {
            continue;
        }
        $idDocTotal = (int)$fila_totales_entregados['Id_Documentos_Pendientes'];
        // Omitir documentos de residencias si aplica la exención
        if ($egresado['ExcluirResidencias'] && in_array($idDocTotal, DOCS_RESIDENCIAS, true)) {
            continue;
        }
        $documentosTotales[] = $fila_totales_entregados; // Aquí puedes especificar los campos que necesitas
    }

    $egresado['DocumentosTotales'] = $documentosTotales;
    
    //Documentos Pendientes basados en Id_Tipo_Producto_Titulacion no en el string JH20250921
    $tipo_titulacion_id = (int)$egresado['Id_Tipo_Producto_Titulacion'];
    if ($tipo_titulacion_id === 0) {
        $egresado['DocumentosPendientes'] = [];
    } else {
        $stmt_pendientes->bind_param('i', $tipo_titulacion_id);
        $stmt_pendientes->execute();
        $result_pendientes = $stmt_pendientes->get_result();

        $documentosPendientes = array();
        while ($fila_pendientes = $result_pendientes->fetch_assoc()) {
            $idPend = (int)$fila_pendientes['Id_Documentos_Pendientes'];
            // Omitir documentos de residencias si aplica la exención
            if ($egresado['ExcluirResidencias'] && in_array($idPend, DOCS_RESIDENCIAS, true)) {
                continue;
            }
            $documentosPendientes[] = $fila_pendientes['Descripcion_Documentos_Pendientes'];
        }

        $diferencias = array_values(array_diff(
            $documentosPendientes,
            array_column($egresado['DocumentosTotales'], 'Descripcion_Documentos_Pendientes')
        ));
        $egresado['DocumentosPendientes'] = $diferencias;
    }

    /* Esta parte ya no es necesaria porque se usa el Id_Usuario para todo JH20250921
    //Documentos Pendientes
    $tipo_titulacion = $egresado['Tipo_Producto_Titulacion'];
    $stmt_pendientes->bind_param('i', $tipo_titulacion);
    $stmt_pendientes->execute();
    $result_pendientes = $stmt_pendientes->get_result();

    $documentosPendientes = array();
    while ($fila_pendientes = $result_pendientes->fetch_assoc()) {
        $documentosPendientes[] = $fila_pendientes['Descripcion_Documentos_Pendientes'];
    }

    $diferencias = array_values(array_diff($documentosPendientes, array_column($egresado['DocumentosTotales'], 'Descripcion_Documentos_Pendientes')));

    $egresado['DocumentosPendientes'] = $diferencias;
    */

    // Filtrar los documentos que aún no han sido aceptados
    $documentosPorRevisarFiltrados = array_values(array_filter($egresado['DocumentosPorRevisar'], function ($documento) use ($egresado) {
        // Filtrar solo los no aceptados
        if ($documento['Aceptado_Egresado_Documentos'] != 0) {
            return false;
        }
        // Omitir documentos de residencias si aplica la exención
        if ($egresado['ExcluirResidencias'] && in_array((int)$documento['Id_Documentos_Pendientes'], DOCS_RESIDENCIAS, true)) {
            return false;
        }
        return true;
    }));

    // Si todos los documentos han sido aceptados y no hay documentos pendientes, quitar el egresado de los resultados

        $egresado['DocumentosPorRevisar'] = $documentosPorRevisarFiltrados;
}

echo json_encode(array_values($egresados));

$stmt_pendientes->close();
$stmt_aceptados->close();
$stmt_por_revisar->close();
$stmt_totales_entregados->close();
$conn->close();
