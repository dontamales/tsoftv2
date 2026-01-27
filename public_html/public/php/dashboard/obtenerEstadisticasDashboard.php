<?php
require_once __DIR__ . '/../sesion.php';
require_once __DIR__ . '/../../../private/conexion.php';

// Evitar que warnings/errores se impriman como HTML y forzar JSON
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../dashboard_errors.log');
header('Content-Type: application/json; charset=utf-8');

function _json_error($msg)
{
    echo json_encode(['error' => $msg]);
    if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli) $GLOBALS['conn']->close();
    exit;
}

$rol = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : null;
if ($rol === null) {
    error_log('[obtenerEstadisticasDashboard] Aviso: role_id no encontrado en $_SESSION');
}

$estadisticas = [];

// === TRÁMITES PENDIENTES ===
// Contar egresados con estatus diferente a "Titulado" (estatus 9)
$stmt = $conn->prepare("
SELECT COUNT(*) AS total
FROM egresado e
JOIN usuario u ON e.Fk_Usuario_Egresado = u.Id_Usuario
WHERE e.FK_Estatus_Egresado != 9
  AND (
        -- Semestre actual
        (
          YEAR(u.Fecha_Usuario) = YEAR(CURDATE())
          AND (
                (MONTH(CURDATE()) BETWEEN 1 AND 6 AND MONTH(u.Fecha_Usuario) BETWEEN 1 AND 6)
             OR (MONTH(CURDATE()) BETWEEN 7 AND 12 AND MONTH(u.Fecha_Usuario) BETWEEN 7 AND 12)
              )
        )
        -- Semestre anterior
        OR (
          (
            MONTH(CURDATE()) BETWEEN 1 AND 6
            AND YEAR(u.Fecha_Usuario) = YEAR(CURDATE()) - 1
            AND MONTH(u.Fecha_Usuario) BETWEEN 7 AND 12
          )
          OR (
            MONTH(CURDATE()) BETWEEN 7 AND 12
            AND YEAR(u.Fecha_Usuario) = YEAR(CURDATE())
            AND MONTH(u.Fecha_Usuario) BETWEEN 1 AND 6
          )
        )
      );
");
$stmt->execute();
$res = null;
if (method_exists($stmt, 'get_result')) {
    $res = $stmt->get_result();
}
if ($res !== null && $res !== false) {
    $result = $res->fetch_assoc();
    $estadisticas['tramitesPendientes'] = $result['total'] ?? 0;
} else {
    $total = null;
    if ($stmt->bind_result($total)) {
        $stmt->fetch();
        $estadisticas['tramitesPendientes'] = $total ?? 0;
    } else {
        $estadisticas['tramitesPendientes'] = 0;
    }
}
$stmt->close();

// Calcular tendencia (comparar con ayer)
$stmt = $conn->prepare("
    SELECT COUNT(*) as total_ayer
    FROM egresado e
    JOIN usuario u ON u.Id_Usuario = e.Fk_Usuario_Egresado
    WHERE e.FK_Estatus_Egresado != 9
    AND DATE(u.Fecha_Usuario) < CURDATE()
");
$stmt->execute();
$totalAyer = 0;
$res = null;
if (method_exists($stmt, 'get_result')) {
    $res = $stmt->get_result();
}
if ($res !== null && $res !== false) {
    $result = $res->fetch_assoc();
    $totalAyer = $result['total_ayer'] ?? 0;
} else {
    $total_ayer = null;
    if ($stmt->bind_result($total_ayer)) {
        $stmt->fetch();
        $totalAyer = $total_ayer ?? 0;
    }
}
$diferencia = $estadisticas['tramitesPendientes'] - $totalAyer;
$estadisticas['tramitesPendientesTendencia'] = $diferencia > 0 ? "+$diferencia desde ayer" : "Sin cambios";
$stmt->close();

// === DOCUMENTOS PENDIENTES ===
// Contar documentos que no han sido aprobados
$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM egresados_documentos
    WHERE Aceptado_Egresado_Documentos = 0
");
$stmt->execute();
$res = null;
if (method_exists($stmt, 'get_result')) {
    $res = $stmt->get_result();
}
if ($res !== null && $res !== false) {
    $result = $res->fetch_assoc();
    $estadisticas['documentosPendientes'] = $result['total'] ?? 0;
} else {
    $total = null;
    if ($stmt->bind_result($total)) {
        $stmt->fetch();
        $estadisticas['documentosPendientes'] = $total ?? 0;
    } else {
        $estadisticas['documentosPendientes'] = 0;
    }
}
$stmt->close();

// Documentos subidos hoy
$stmt = $conn->prepare("
    SELECT COUNT(*) as total_hoy
    FROM egresados_documentos
    WHERE DATE(Fecha_Documento_Subido_Egresado_Documentos) = CURDATE()
");
$stmt->execute();
$totalHoy = 0;
$res = null;
if (method_exists($stmt, 'get_result')) {
    $res = $stmt->get_result();
}
if ($res !== null && $res !== false) {
    $result = $res->fetch_assoc();
    $totalHoy = $result['total_hoy'] ?? 0;
} else {
    $total_hoy = null;
    if ($stmt->bind_result($total_hoy)) {
        $stmt->fetch();
        $totalHoy = $total_hoy ?? 0;
    }
}
$estadisticas['documentosHoy'] = $totalHoy > 0 ? "$totalHoy nuevos hoy" : "Sin documentos hoy";
$stmt->close();

// === CEREMONIAS PROGRAMADAS ===
// Contar ceremonias futuras
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT DATE(Fecha_Hora_Ceremonia_Egresado)) as total,
           MIN(Fecha_Hora_Ceremonia_Egresado) as proxima
    FROM egresado
    WHERE Fecha_Hora_Ceremonia_Egresado >= NOW()
");
$stmt->execute();
$estadisticas['ceremoniasProgramadas'] = 0;
$proximaCeremonia = null;
$res = null;
if (method_exists($stmt, 'get_result')) {
    $res = $stmt->get_result();
}
if ($res !== null && $res !== false) {
    $result = $res->fetch_assoc();
    $estadisticas['ceremoniasProgramadas'] = $result['total'] ?? 0;
    $proximaCeremonia = $result['proxima'] ?? null;
} else {
    $total = null;
    $proxima = null;
    if ($stmt->bind_result($total, $proxima)) {
        $stmt->fetch();
        $estadisticas['ceremoniasProgramadas'] = $total ?? 0;
        $proximaCeremonia = $proxima ?? null;
    }
}

if ($proximaCeremonia) {
    $fecha = new DateTime($proximaCeremonia);
    $estadisticas['proximaCeremonia'] = 'Próxima: ' . $fecha->format('d M');
} else {
    $estadisticas['proximaCeremonia'] = 'Sin ceremonias';
}
$stmt->close();

// === EFICIENCIA TERMINAL (para todos) ===
$stmt = $conn->prepare("
    SELECT Promedio_Eficiencia_Terminal
    FROM reporte_eficiencia_terminal
    ORDER BY Fecha_Creacion_Eficiencia_Terminal DESC
    LIMIT 1
");
$stmt->execute();
// Intentar obtener resultado usando get_result(); si falla, usar bind_result() como fallback
$eficRow = null;
$res = null;
if (method_exists($stmt, 'get_result')) {
    $res = $stmt->get_result();
}

if ($res !== null && $res !== false) {
    $eficRow = $res->fetch_assoc();
} else {
    // Fallback para entornos sin mysqlnd
    $promedio = null;
    if ($stmt->bind_result($promedio)) {
        $stmt->fetch();
        if ($promedio !== null) {
            $eficRow = ['Promedio_Eficiencia_Terminal' => $promedio];
        }
    }
}

if ($eficRow && array_key_exists('Promedio_Eficiencia_Terminal', $eficRow)) {
    $estadisticas['eficienciaTerminal'] = number_format((float)$eficRow['Promedio_Eficiencia_Terminal'], 2) . '%';
} else {
    // Registrar detalles para diagnóstico
    $err = '';
    if ($stmt->errno) $err .= "Stmt errno: {$stmt->errno}. ";
    if ($stmt->error) $err .= "Stmt error: {$stmt->error}. ";
    if ($conn->error) $err .= "Conn error: {$conn->error}. ";
    if ($err) error_log("[eficienciaTerminal] " . $err);
    $estadisticas['eficienciaTerminal'] = 'N/A';
}
$stmt->close();

// === SOLO PARA ROL 3 (Super Admin) ===
// Titulados este semestre
$stmt = $conn->prepare("
        SELECT COUNT(*) as total
        FROM egresado
        WHERE FK_Estatus_Egresado = 9
        AND YEAR(Fecha_Hora_Ceremonia_Egresado) = YEAR(CURDATE())
        AND MONTH(Fecha_Hora_Ceremonia_Egresado) BETWEEN 
            IF(MONTH(CURDATE()) <= 6, 1, 7) 
            AND IF(MONTH(CURDATE()) <= 6, 6, 12)
    ");
$stmt->execute();
$res = null;
if (method_exists($stmt, 'get_result')) {
    $res = $stmt->get_result();
}
if ($res !== null && $res !== false) {
    $result = $res->fetch_assoc();
    $estadisticas['tituladosSemestre'] = $result['total'] ?? 0;
} else {
    $total = null;
    if ($stmt->bind_result($total)) {
        $stmt->fetch();
        $estadisticas['tituladosSemestre'] = $total ?? 0;
    } else {
        $estadisticas['tituladosSemestre'] = 0;
    }
}
$stmt->close();

echo json_encode($estadisticas);
$conn->close();
