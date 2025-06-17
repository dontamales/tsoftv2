<?php
require_once 'sesion.php';
require_once 'auth.php';
require_roles([3]);
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');
$conn->query("SET time_zone='-06:00'");

// Leer el JSON que envía el cliente (numeroFoja e idLibro)
$input = json_decode(file_get_contents('php://input'), true);

if (
    isset($input['numeroFoja']) && is_numeric($input['numeroFoja']) &&
    isset($input['idLibro'])    && is_numeric($input['idLibro'])
) {
    $numeroFoja = intval($input['numeroFoja']);
    $idLibro    = intval($input['idLibro']);

    try {
        // 1) Obtener el Id_Formato_Foja correspondiente
        $sqlGetFoja = "
            SELECT Id_Formato_Foja
              FROM formato_foja
             WHERE Id_Formato_Foja = ?
               AND Fk_Libro_Formato_Foja = ?
            LIMIT 1
        ";
        $stmtGet = $conn->prepare($sqlGetFoja);
        $stmtGet->bind_param('ii', $numeroFoja, $idLibro);
        $stmtGet->execute();
        $res = $stmtGet->get_result();

        if ($row = $res->fetch_assoc()) {
            $idFormatoFoja = intval($row['Id_Formato_Foja']);
            $stmtGet->close();

            // 2) Actualizar en egresado para “desasignar” la foja hallada
            $sqlUpdate = "
                UPDATE egresado
                    SET Fk_Formato_Foja_Asignado_Egresado = NULL,
                    Fk_Formato_Libro_Asignado_Egresado = NULL
                WHERE Fk_Formato_Foja_Asignado_Egresado = ?
                    AND Fk_Formato_Libro_Asignado_Egresado  = ?
            ";
            $stmtUpd = $conn->prepare($sqlUpdate);
            $stmtUpd->bind_param('ii', $idFormatoFoja, $idLibro);

            if ($stmtUpd->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Foja desasignada satisfactoriamente.'
                ]);
            } else {
                // Error al ejecutar el UPDATE
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al desasignar la foja: ' . $stmtUpd->error
                ]);
            }
            $stmtUpd->close();
        } else {
            // No existe esa foja para ese libro
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró una foja con número ' . $numeroFoja
                    . ' en el libro ID ' . $idLibro . '.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Excepción: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan parámetros o no son válidos.'
    ]);
}

$conn->close();
