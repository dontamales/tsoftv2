<?php
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once("../../private/conexion.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$formato_B_Aprobado_Egresado = 1;

/**
 * Actualiza el estatus y observaciones para "Formato B" de un usuario dado.
 *
 * @param int $idUsuario El ID del usuario a actualizar.
 * @param int $nuevoEstatus El nuevo estatus para "Formato B".
 * @param string $observaciones Las observaciones para añadir al registro del usuario.
 * @return array Resultado de la operación.
 */
function actualizarEstatusObservacionesFormatoB($idUsuario, $nuevoEstatus, $observaciones)
{
    global $conn;

    // Define constantes para los estatus de "Revision Pendiente" y "Formato B Desaprobado"
    $estatus_Revision_Pendiente = 2;
    $estatus_Formato_B_Desaprobado = 3;

    // Define la variable global para el estatus de "Formato B Aprobado Egresado"
    global $formato_B_Aprobado_Egresado;

    // Consulta de la base de datos para verificar si el usuario tiene un estatus de "Revision Pendiente" o "Formato B Desaprobado"
    $stmt = $conn->prepare("SELECT * FROM egresado 
    WHERE Fk_Usuario_Egresado = ? 
    AND (FK_Estatus_Egresado = ?
    OR FK_Estatus_Egresado = ?);");
    $stmt->bind_param("iii", $idUsuario, $estatus_Revision_Pendiente, $estatus_Formato_B_Desaprobado);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si el usuario tiene un estatus de "Revision Pendiente" o "Formato B Desaprobado"
    if ($result->num_rows > 0) {
        // Actualiza el estatus de "Formato B Aprobado Egresado" según el nuevo estatus
        if ($nuevoEstatus == 3) {
            $formato_B_Aprobado_Egresado = 0;
        } else if ($nuevoEstatus == 4) {
            $formato_B_Aprobado_Egresado = 1;
        }

        // Actualiza el estatus y observaciones del usuario en la base de datos
        $stmt = $conn->prepare("UPDATE egresado 
            SET Fecha_Aprobado_Formato_B_Egresado = NOW(),
            Observaciones_Formato_B_Egresado = ?,
            Formato_B_Aprobado_Egresado = ?,
            FK_Estatus_Egresado = ?
            WHERE Fk_Usuario_Egresado = ?;");
        $stmt->bind_param("siii", $observaciones, $formato_B_Aprobado_Egresado, $nuevoEstatus, $idUsuario);
        $stmt->execute();

        // Verifica si la consulta fue exitosa y devuelve un resultado
        if ($stmt->affected_rows > 0) {
            return ['success' => true, 'user_id' => $idUsuario, 'estatus' => $nuevoEstatus, 'formato_B' => $formato_B_Aprobado_Egresado];
        } else {
            return ['success' => false, 'user_id' => $idUsuario, 'estatus' => $nuevoEstatus, 'formato_B' => $formato_B_Aprobado_Egresado];
        }
        $stmt->close();
    } else {
        // Si el usuario no tiene un estatus de "Revision Pendiente" o "Formato B Desaprobado", actualiza su estatus de "Formato B Aprobado Egresado" y observaciones
        $stmt = $conn->prepare("UPDATE egresado 
        SET Fecha_Aprobado_Formato_B_Egresado = NOW(),
        Observaciones_Formato_B_Egresado = ?,
        Formato_B_Aprobado_Egresado = ?
        WHERE Fk_Usuario_Egresado = ?;");
        $stmt->bind_param("sii", $observaciones, $formato_B_Aprobado_Egresado, $idUsuario);
        $stmt->execute();

        // Verifica si la consulta fue exitosa y devuelve un resultado
        if ($stmt->affected_rows > 0) {
            return ['success' => true, 'user_id' => $idUsuario, 'formato_B' => $formato_B_Aprobado_Egresado];
        } else {
            return ['success' => false, 'user_id' => $idUsuario, 'formato_B' => $formato_B_Aprobado_Egresado];
        }
    }
}




/**
 * Actualiza el estatus de Formato B para un usuario.
 * 
 * @param mysqli $conn Conexión a la base de datos.
 * @param int $idUsuario ID del usuario.
 * @param int $nuevoEstatus Nuevo estatus.
 * @return array Resultado de la operación.
 */
function actualizarEstatusFormatoB($conn, $idUsuario, $nuevoEstatus)
{
    logMessage("Intentando actualizar estatus para número de control: $idUsuario");

    // Constantes
    $estatus_Anexos_I_II_Pendientes = 4;
    $formato_B_Aprobado_Egresado = 1;
    $formato_B_Enviado_Egresado = 2;

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Consulta de busqueda de usuario
        $result = consultarEgresado($conn, $idUsuario, $formato_B_Aprobado_Egresado, $formato_B_Enviado_Egresado, $estatus_Anexos_I_II_Pendientes);

        if ($result->num_rows > 0) {
            // Consulta de actualización de estatus e información
            actualizarEgresado($conn, $idUsuario, $nuevoEstatus, $formato_B_Aprobado_Egresado);
        } else {
            // Consulta de actualización de información
            actualizarEgresadoFormatoB($conn, $idUsuario, $formato_B_Aprobado_Egresado);
        }

        // Confirmar transacción
        $conn->commit();

        logMessage("Estatus actualizado exitosamente para número de control: $idUsuario");

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        logMessage("Error en la transacción para número de control: $idUsuario, Error: " . $e->getMessage());
        throw $e;  // Lanzar la excepción para un manejo de errores más extenso
    }
}

/**
 * Consulta el estatus de Formato B para un usuario.
 * 
 * @param mysqli $conn Conexión a la base de datos.
 * @param int $idUsuario ID del usuario.
 * @param int $formato_B_Aprobado_Egresado Estatus de Formato B aprobado.
 * @param int $estatus_Anexos_I_II_Pendientes Estatus de anexos I y II pendientes.
 * @return mysqli_result Resultado de la consulta.
 */
function consultarEgresado($conn, $idUsuario, $formato_B_Aprobado_Egresado, $formato_B_Enviado_Egresado, $estatus_Anexos_I_II_Pendientes)
{
    $stmt = $conn->prepare("SELECT * FROM egresado 
        WHERE Num_Control = ? 
        AND (Formato_B_Aprobado_Egresado = ? OR Formato_B_Aprobado_Egresado = ?)
        AND FK_Estatus_Egresado = ?;");
    $stmt->bind_param("siii", $idUsuario, $formato_B_Aprobado_Egresado, $formato_B_Enviado_Egresado, $estatus_Anexos_I_II_Pendientes);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Actualiza el estatus de Formato B para un usuario.
 * 
 * @param mysqli $conn Conexión a la base de datos.
 * @param int $idUsuario ID del usuario.
 * @param int $nuevoEstatus Nuevo estatus.
 * @param int $formato_B_Aprobado_Egresado Estatus de Formato B aprobado.
 */
function actualizarEgresado($conn, $idUsuario, $nuevoEstatus, $formato_B_Aprobado_Egresado)
{
    $stmt = $conn->prepare("UPDATE egresado 
            SET FK_Estatus_Egresado = ?, 
            Fecha_Aprobado_Formato_B_Egresado = NOW(),
            Formato_B_Aprobado_Egresado = ? 
            WHERE Num_Control = ?;");
    $stmt->bind_param("iis", $nuevoEstatus, $formato_B_Aprobado_Egresado, $idUsuario);
    $stmt->execute();
    $stmt->close();
}

/**
 * Actualiza el estatus de Formato B para un usuario.
 * 
 * @param mysqli $conn Conexión a la base de datos.
 * @param int $idUsuario ID del usuario.
 * @param int $formato_B_Aprobado_Egresado Estatus de Formato B aprobado.
 */
function actualizarEgresadoFormatoB($conn, $idUsuario, $formato_B_Aprobado_Egresado)
{
    $stmt = $conn->prepare("UPDATE egresado 
            SET Fecha_Aprobado_Formato_B_Egresado = NOW(),
            Formato_B_Aprobado_Egresado = ? 
            WHERE Num_Control = ?;");
    $stmt->bind_param("is", $formato_B_Aprobado_Egresado, $idUsuario);
    $stmt->execute();
    $stmt->close();
}

function logMessage($menssage){
    // Registrar el duplicado en un archivo .log
    $logEntry = date('Y-m-d H:i:s') . " - " . $menssage . "\n";
    $directorio = '../assets/archivos/logs/';
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
      }
    file_put_contents($directorio . date("Y.m.d") . ' actualizarEstatus.log', $logEntry, FILE_APPEND);
}