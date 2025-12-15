<?php
require_once 'sesion.php';
require_once 'auth.php';
require_roles([2, 3]);
require_once '../../private/conexion.php';

date_default_timezone_set('America/Denver');
$conn->query("SET time_zone='-06:00'");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seleccionados = json_decode($_POST['seleccionados']);

    // Estatus para los casos donde se elimina la fecha (ajusta si lo necesitas)
    $estatus_sin_fecha = 7; // Sinodales asignados, pero sin ceremonia

    // Requisitos para desasignar
    $estatus_actuales_permitidos = [7, 8];
    $anexo_III_Egresado = 1;
    $formato_B_Aprobado_Egresado = 1;

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            UPDATE egresado 
            SET Fecha_Hora_Ceremonia_Egresado = NULL, FK_Estatus_Egresado = ?
            WHERE Num_Control = ?
            AND FK_Estatus_Egresado IN (?, ?)
            AND Anexo_III_Egresado = ?
            AND Formato_B_Aprobado_Egresado = ?
        ");

        foreach ($seleccionados as $numControl) {
            $stmt->bind_param(
                "iiiiii",
                $estatus_sin_fecha,
                $numControl,
                $estatus_actuales_permitidos[0],
                $estatus_actuales_permitidos[1],
                $anexo_III_Egresado,
                $formato_B_Aprobado_Egresado
            );
            $stmt->execute();
        }

        $conn->commit();
        echo json_encode(["message" => "Fechas desasignadas correctamente."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["error" => $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
}
