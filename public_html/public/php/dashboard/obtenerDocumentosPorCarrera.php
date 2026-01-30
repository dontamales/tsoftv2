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

// Obtener documentos pendientes agrupados por carrera
$stmt = $conn->prepare("
    SELECT 
        c.Nombre_Carrera as carrera,
        COUNT(DISTINCT ed.Fk_NumeroControl) as pendientes
    FROM egresados_documentos ed
    JOIN egresado e ON e.Num_Control = ed.Fk_NumeroControl
    JOIN carrera c ON c.Id_Carrera = e.Fk_Carrera_Egresado
    WHERE ed.Aceptado_Egresado_Documentos = 0
    GROUP BY c.Id_Carrera, c.Nombre_Carrera
    ORDER BY pendientes DESC
    LIMIT 5
");

$stmt->execute();
$result = $stmt->get_result();

$documentos = [];
while ($row = $result->fetch_assoc()) {
    $documentos[] = $row;
}

echo json_encode($documentos);
$stmt->close();
$conn->close();
