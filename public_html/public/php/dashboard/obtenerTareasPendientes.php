<?php
require_once __DIR__ . '/../sesion.php';
require_once __DIR__ . '/../../../private/conexion.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../dashboard_errors.log');
header('Content-Type: application/json; charset=utf-8');

function _json_error($msg) {
    echo json_encode(['error' => $msg]);
    if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli) $GLOBALS['conn']->close();
    exit;
}

$tareas = [];

// 1. Formatos B pendientes de revisión
$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM egresado
    WHERE FK_Estatus_Egresado = 2
");
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result['total'] > 0) {
    $tareas[] = [
        'badge' => 'urgent',
        'badge_text' => 'Urgente',
        'descripcion' => 'Revisar formatos B pendientes',
        'url' => 'formatosPendientes.php'
    ];
}
$stmt->close();

// 2. Sinodales sin asignar
$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM egresado
    WHERE FK_Estatus_Egresado >= 5
    AND FK_Estatus_Egresado < 8
    AND Fk_Sinodales_Asignados_Egresado IS NULL
");
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result['total'] > 0) {
    $tareas[] = [
        'badge' => 'today',
        'badge_text' => 'Hoy',
        'descripcion' => 'Asignar sinodales para ceremonia',
        'url' => 'gestionSinodal.php'
    ];
}
$stmt->close();

// 3. Egresados listos para actualizar a titulados
$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM egresado
    WHERE FK_Estatus_Egresado = 8
    AND Fecha_Hora_Ceremonia_Egresado < NOW()
");
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result['total'] > 0) {
    $tareas[] = [
        'badge' => 'new',
        'badge_text' => 'Nuevo',
        'descripcion' => 'Actualizar estatus de titulados',
        'url' => 'actualizarTitulados.php'
    ];
}
$stmt->close();

// 4. Documentos pendientes de revisión
$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM egresados_documentos
    WHERE Aceptado_Egresado_Documentos = 0
    AND Fecha_Documento_Subido_Egresado_Documentos IS NOT NULL
");
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result['total'] > 0) {
    $tareas[] = [
        'badge' => 'pending',
        'badge_text' => 'Pendiente',
        'descripcion' => 'Revisar documentos subidos',
        'url' => 'gestionDocumentos.php'
    ];
}
$stmt->close();

// 5. Anexos I y II pendientes de envío
$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM egresado
    WHERE FK_Estatus_Egresado = 4
");
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result['total'] > 0) {
    $tareas[] = [
        'badge' => 'late',
        'badge_text' => 'Atrasado',
        'descripcion' => 'Envío de anexos pendientes',
        'url' => 'envioAnexosFallidoEgresado.php'
    ];
}
$stmt->close();

echo json_encode($tareas);
$conn->close();
