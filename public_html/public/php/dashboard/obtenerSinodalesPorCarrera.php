<?php
require_once __DIR__ . '/../sesion.php';
require_once __DIR__ . '/../../../private/conexion.php';

// Evitar que warnings/errores se impriman como HTML y forzar JSON
ini_set('display_errors', 0);
ini_set('log_errors', 1);
header('Content-Type: application/json; charset=utf-8');

// Obtener asignación de sinodales agrupados por carrera
// Solo contamos egresados que tienen Anexo III aprobado (estatus >= 6)
// y que todavía no están titulados (estatus < 9)
// Estos son los que realmente necesitan sinodales
$stmt = $conn->prepare("
    SELECT 
        c.Nombre_Carrera as carrera,
        COUNT(DISTINCT e.Num_Control) as total,
        COUNT(DISTINCT CASE WHEN e.Fk_Sinodales_Asignados_Egresado IS NOT NULL THEN e.Num_Control END) as asignados
    FROM egresado e
    JOIN carrera c ON c.Id_Carrera = e.Fk_Carrera_Egresado
    WHERE e.FK_Estatus_Egresado >= 6
    AND e.FK_Estatus_Egresado < 9
    AND e.Anexo_III_Egresado = 1
    GROUP BY c.Id_Carrera, c.Nombre_Carrera
    HAVING total > 0
    ORDER BY asignados ASC
    LIMIT 5
");

$stmt->execute();
$res = null;
if (method_exists($stmt, 'get_result')) {
    $res = $stmt->get_result();
}

$sinodales = [];

if ($res !== null && $res !== false) {
    while ($row = $res->fetch_assoc()) {
        $asignados = (int)$row['asignados'];
        $total = (int)$row['total'];
        $porcentaje = $total > 0 ? round(($asignados / $total) * 100) : 0;

        // Determinar color del badge
        if ($porcentaje == 0) {
            $badge_class = 'bg-danger';
        } elseif ($porcentaje < 60) {
            $badge_class = 'bg-warning text-dark';
        } else {
            $badge_class = 'bg-success';
        }

        $sinodales[] = [
            'carrera' => $row['carrera'],
            'asignados' => $asignados,
            'total' => $total,
            'badge_class' => $badge_class
        ];
    }
} else {
    // Fallback para mysqli sin mysqlnd
    $carrera = null;
    $total = null;
    $asignados = null;

    if ($stmt->bind_result($carrera, $total, $asignados)) {
        while ($stmt->fetch()) {
            $asignados_val = (int)$asignados;
            $total_val = (int)$total;
            $porcentaje = $total_val > 0 ? round(($asignados_val / $total_val) * 100) : 0;

            if ($porcentaje == 0) {
                $badge_class = 'bg-danger';
            } elseif ($porcentaje < 60) {
                $badge_class = 'bg-warning text-dark';
            } else {
                $badge_class = 'bg-success';
            }

            $sinodales[] = [
                'carrera' => $carrera,
                'asignados' => $asignados_val,
                'total' => $total_val,
                'badge_class' => $badge_class
            ];
        }
    }
}

echo json_encode($sinodales);
$stmt->close();
$conn->close();
