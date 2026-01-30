<?php
require_once __DIR__ . '/../sesion.php';
require_once __DIR__ . '/../../../private/conexion.php';

// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../dashboard_errors.log');
header('Content-Type: application/json; charset=utf-8');

function _json_error($msg) {
    echo json_encode(['error' => $msg]);
    if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli) $GLOBALS['conn']->close();
    exit;
}

// Obtener próximas ceremonias
$stmt = $conn->prepare("
    SELECT 
        DATE(e.Fecha_Hora_Ceremonia_Egresado) as fecha,
        GROUP_CONCAT(DISTINCT c.Iniciales_Carrera ORDER BY c.Iniciales_Carrera SEPARATOR ', ') as carreras,
        COUNT(DISTINCT e.Num_Control) as total_egresados
    FROM egresado e
    JOIN carrera c ON c.Id_Carrera = e.Fk_Carrera_Egresado
    WHERE e.Fecha_Hora_Ceremonia_Egresado >= NOW()
    GROUP BY DATE(e.Fecha_Hora_Ceremonia_Egresado)
    ORDER BY e.Fecha_Hora_Ceremonia_Egresado ASC
    LIMIT 5
");

$stmt->execute();
$result = $stmt->get_result();

$eventos = [];
while ($row = $result->fetch_assoc()) {
    $fecha = new DateTime($row['fecha']);

    // Formato en español
    $meses = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

    $eventos[] = [
        'dia' => $fecha->format('d'),
        'mes' => $meses[(int)$fecha->format('n')],
        'dia_semana' => $dias[(int)$fecha->format('w')],
        'fecha_completa' => $dias[(int)$fecha->format('w')] . ', ' . $fecha->format('d') . ' de ' . $meses[(int)$fecha->format('n')] . ' de ' . $fecha->format('Y'),
        'carreras' => $row['carreras'],
        'total_egresados' => $row['total_egresados']
    ];
}

echo json_encode($eventos);
$stmt->close();
$conn->close();
