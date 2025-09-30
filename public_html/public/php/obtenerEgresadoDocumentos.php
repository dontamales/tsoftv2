<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

// Documentos de residencias (los que ya no se piden) JH20250821
const DOCS_RESIDENCIAS = [6, 7];

// Productos exentos por ID (no requieren 6/7) JH20250821
const PRODUCTOS_EXENTOS = [12, 14, 15, 16, 17];

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$documentos_entregados = 0;
$formato_B_Aprobado = 1;
$estatus_Anexo_III_Pendiente = 5;
$estatus_Fecha_Ceremonia_Asignada = 8;

$stmt_por_revisar = $conn->prepare("SELECT *
FROM egresado 
JOIN proyecto ON egresado.Fk_Proyecto_Egresado = proyecto.Id_Proyecto 
JOIN carrera ON egresado.Fk_Carrera_Egresado = carrera.Id_Carrera
JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
JOIN producto_titulacion ON egresado.Fk_Tipo_Titulacion_Egresado = producto_titulacion.Id_Titulacion
JOIN egresados_documentos ON egresado.Num_Control = egresados_documentos.Fk_NumeroControl
JOIN documentos_pendientes ON egresados_documentos.Fk_Documentos_Pendientes2  = documentos_pendientes.Id_Documentos_Pendientes
WHERE egresado.Formato_B_Aprobado_Egresado = ?
AND egresado.FK_Estatus_Egresado >= ? 
AND egresado.FK_Estatus_Egresado <= ?
ORDER BY egresados_documentos.Fecha_Documento_Subido_Egresado_Documentos ASC");
$stmt_por_revisar->bind_param('iii', $formato_B_Aprobado, $estatus_Anexo_III_Pendiente, $estatus_Fecha_Ceremonia_Asignada);
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
WHERE egresados_documentos.Aceptado_Egresado_Documentos = 1
AND egresados_documentos.Fk_NumeroControl = ?");

$stmt_totales_entregados = $conn->prepare("SELECT *
FROM egresado 
JOIN proyecto ON egresado.Fk_Proyecto_Egresado = proyecto.Id_Proyecto 
JOIN carrera ON egresado.Fk_Carrera_Egresado = carrera.Id_Carrera
JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
JOIN producto_titulacion ON egresado.Fk_Tipo_Titulacion_Egresado = producto_titulacion.Id_Titulacion
JOIN egresados_documentos ON egresado.Num_Control = egresados_documentos.Fk_NumeroControl
JOIN documentos_pendientes ON egresados_documentos.Fk_Documentos_Pendientes2  = documentos_pendientes.Id_Documentos_Pendientes
WHERE egresado.Formato_B_Aprobado_Egresado = ?
AND egresado.FK_Estatus_Egresado >= ? 
AND egresado.FK_Estatus_Egresado <= ?
AND egresados_documentos.Fk_NumeroControl = ? ORDER BY egresados_documentos.Aceptado_Egresado_Documentos ASC");

while ($fila = $result->fetch_assoc()) {
    $numControl = $fila['Num_Control'];
    $productoId = (int)$fila['Fk_Tipo_Titulacion_Egresado'];
    $esExento = in_array($productoId, PRODUCTOS_EXENTOS, true);
    if (!isset($egresados[$numControl])) {
        $egresados[$numControl] = array(
            'Num_Control' => $fila['Num_Control'],
            'Nombres_Usuario' => $fila['Nombres_Usuario'],
            'Apellidos_Usuario' => $fila['Apellidos_Usuario'],
            'Correo_Usuario' => $fila['Correo_Usuario'],
            'Nombre_Proyecto' => $fila['Nombre_Proyecto'],
            'Nombre_Carrera' => $fila['Nombre_Carrera'],
            'Tipo_Producto_Titulacion' => $fila['Tipo_Producto_Titulacion'],
            'EsExento' => $esExento, // GUARDAR FLAG
            'DocumentosPorRevisar' => array(),
            'DocumentosPendientes' => array(),
        );
    } else {
        // Asegura coherencia del flag si llegan múltiples filas
        $egresados[$numControl]['EsExento'] = $esExento;
    }

    // $egresados[$numControl]['DocumentosPorRevisar'][] = array(
    //     'Num_Control' => $fila['Num_Control'],
    //     'Id_Documentos_Pendientes' => $fila['Id_Documentos_Pendientes'],
    //     'Descripcion_Documentos_Pendientes' => $fila['Descripcion_Documentos_Pendientes'],
    //     'Direccion_Archivo_Egresados_Documentos' => $fila['Direccion_Archivo_Egresados_Documentos'],
    //     'Fecha_Documento_Subido_Egresado_Documentos' => $fila['Fecha_Documento_Subido_Egresado_Documentos'],
    //     'Aceptado_Egresado_Documentos' => $fila['Aceptado_Egresado_Documentos'],
    // );
    
    // Filtra 6/7 si es exento JH20250821
    if (!($esExento && in_array((int)$fila['Id_Documentos_Pendientes'], DOCS_RESIDENCIAS, true))) {
        $egresados[$numControl]['DocumentosPorRevisar'][] = array(
            'Num_Control' => $fila['Num_Control'],
            'Id_Documentos_Pendientes' => $fila['Id_Documentos_Pendientes'],
            'Descripcion_Documentos_Pendientes' => $fila['Descripcion_Documentos_Pendientes'],
            'Direccion_Archivo_Egresados_Documentos' => $fila['Direccion_Archivo_Egresados_Documentos'],
            'Fecha_Documento_Subido_Egresado_Documentos' => $fila['Fecha_Documento_Subido_Egresado_Documentos'],
            'Aceptado_Egresado_Documentos' => $fila['Aceptado_Egresado_Documentos'],
        );
    }
}

foreach ($egresados as $numControl => &$egresado) {
    // Documentos Aprobados
    $stmt_aceptados->bind_param('s', $numControl);
    $stmt_aceptados->execute();
    $result_aceptados = $stmt_aceptados->get_result();

    $documentosAprobados = array();
    while ($fila_aceptados = $result_aceptados->fetch_assoc()) {
        $documentosAprobados[] = $fila_aceptados['Descripcion_Documentos_Pendientes'];
    }

    $egresado['DocumentosAprobados'] = $documentosAprobados;

    // Documentos Totales
    $stmt_totales_entregados->bind_param('iiis', $formato_B_Aprobado, $estatus_Anexo_III_Pendiente, $estatus_Fecha_Ceremonia_Asignada, $numControl);
    $stmt_totales_entregados->execute();
    $result_totales_entregados = $stmt_totales_entregados->get_result();

    $documentosTotales = array();
    while ($fila_totales_entregados = $result_totales_entregados->fetch_assoc()) {
        $documentosTotales[] = $fila_totales_entregados; // Aquí puedes especificar los campos que necesitas
    }

    $egresado['DocumentosTotales'] = $documentosTotales;

    //Documentos Pendientes
    $tipo_titulacion = $egresado['Tipo_Producto_Titulacion'];
    $stmt_pendientes->bind_param('i', $tipo_titulacion);
    $stmt_pendientes->execute();
    $result_pendientes = $stmt_pendientes->get_result();

    $documentosPendientes = array();
    $esExentoEgresado = !empty($egresado['EsExento']); // <<--- usar flag por egresado
    
    while ($fila_pendientes = $result_pendientes->fetch_assoc()) {
        if (!( !empty($egresado['EsExento']) && in_array((int)$fila_pendientes['Id_Documentos_Pendientes'], DOCS_RESIDENCIAS, true))) {
            $documentosPendientes[] = $fila_pendientes['Descripcion_Documentos_Pendientes'];
        }
    }

    $diferencias = array_values(array_diff($documentosPendientes, array_column($egresado['DocumentosTotales'], 'Descripcion_Documentos_Pendientes')));

    $egresado['DocumentosPendientes'] = $diferencias;

    // Filtrar los documentos que aún no han sido aceptados
    $documentosPorRevisarFiltrados = array_values(array_filter($egresado['DocumentosPorRevisar'], function ($documento) {
        return $documento['Aceptado_Egresado_Documentos'] == 0;
    }));

    // Si todos los documentos han sido aceptados y no hay documentos pendientes, quitar el egresado de los resultados
    if (empty($documentosPorRevisarFiltrados) && empty($egresado['DocumentosPendientes'])) {
        unset($egresados[$numControl]);
    } else {
        $egresado['DocumentosPorRevisar'] = $documentosPorRevisarFiltrados;
    }
    //var_dump($egresado['DocumentosPorRevisar']);
}

echo json_encode(array_values($egresados));

$stmt_pendientes->close();
$stmt_aceptados->close();
$stmt_por_revisar->close();
$stmt_totales_entregados->close();
$conn->close();