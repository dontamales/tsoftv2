<?php
require_once 'sesion.php';
require_once '../../private/conexion.php';

// Documentos de residencias (los que ya no se piden) JH20251022
const DOCS_RESIDENCIAS = [6, 7];
const PRODUCTOS_EXENTOS = [12, 14, 15, 16, 17];
const FECHA_EXENCION_RESIDENCIAS = '2025-08-15';

$usuarioId = $_SESSION['user_id'];

// Obtener Num_Control, tipo de titulación y fecha de usuario
$stmt = $conn->prepare("SELECT e.Num_Control, e.Fk_Tipo_Titulacion_Egresado, u.Fecha_Usuario
                        FROM egresado e
                        JOIN usuario u ON u.Id_Usuario = e.Fk_Usuario_Egresado 
                        WHERE e.Fk_Usuario_Egresado = ?");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$numControl = $res['Num_Control'];
$tipoTitulacion = $res['Fk_Tipo_Titulacion_Egresado'];
$fechaUsuario = $res['Fecha_Usuario'];

// Determinar si aplica la exención
$esExento = in_array($tipoTitulacion, PRODUCTOS_EXENTOS, true);
$fechaExento = $fechaUsuario ? (strtotime($fechaUsuario) > strtotime(FECHA_EXENCION_RESIDENCIAS)) : false;
$excluirResidencias = $esExento && $fechaExento;
$stmt->close();

// Traer todos los documentos esperados para ese tipo
$stmt = $conn->prepare("
  SELECT dp.Id_Documentos_Pendientes,
         dp.Descripcion_Documentos_Pendientes,
         ed.Aceptado_Egresado_Documentos,
         ed.Fecha_Documento_Subido_Egresado_Documentos
  FROM producto_titulacion_documentos_pendientes ptdp
  JOIN documentos_pendientes dp 
    ON dp.Id_Documentos_Pendientes = ptdp.Fk_Documentos_Pendientes
  LEFT JOIN egresados_documentos ed
    ON ed.Fk_Documentos_Pendientes2 = dp.Id_Documentos_Pendientes
   AND ed.Fk_NumeroControl = ?
  WHERE ptdp.Fk_Producto_Titulacion_Documentos_Pendientes = ?
");
$stmt->bind_param("si", $numControl, $tipoTitulacion);
$stmt->execute();
$result = $stmt->get_result();

$documentos = [];
while ($row = $result->fetch_assoc()) {
    // Si aplica la exención, omitir documentos 6 y 7
    if ($excluirResidencias && in_array((int)$row['Id_Documentos_Pendientes'], DOCS_RESIDENCIAS, true)) {
        continue;
    }
    $documentos[] = $row;
}

echo json_encode($documentos);
$stmt->close();
$conn->close();
?>