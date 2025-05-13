<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Función para verificar duplicados (para el registro de usuarios individuales)
function isDuplicate($conn, $query, $param, $errorMsg)
{
    $statement = $conn->prepare($query);
    $statement->bind_param("s", $param);
    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    $statement->close();

    if ($row['count'] > 0) {
        $_SESSION['message'] = $errorMsg;
        die(json_encode(['message' => $errorMsg]));
    }
}

// Función para registrar usuarios individuales
function modificarDocumento($conn, $idDocumento, $nombreDocumento)
{
    if ($_SESSION['user_role'] == !3) {
        $respuesta = [
            "message" => "Error: No tiene permiso para modificar los documentos.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    if (empty($idDocumento) || empty($nombreDocumento)) {
        $respuesta = [
            "message" => "Error: Todos los campos son obligatorios.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    $stmt2 = $conn->prepare("SELECT * FROM documentos_pendientes WHERE Id_Documentos_Pendientes = ?");
    $stmt2->bind_param("i", $idDocumento);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $stmt2->close();

    if ($row['Descripcion_Documentos_Pendientes'] != $nombreDocumento) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM documentos_pendientes WHERE Descripcion_Documentos_Pendientes = ?", $nombreDocumento, 'Error: Ya existe un documento con ese nombre.');
    }

    // Preparar e insertar el usuario en la base de datos usando consultas preparadas
    $query = "UPDATE documentos_pendientes 
    SET Descripcion_Documentos_Pendientes = ? 
    WHERE Id_Documentos_Pendientes = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $nombreDocumento, $idDocumento);

    if ($stmt->execute()) {

        $respuesta = [
            "message" => "Documento modificado con éxito.",
            "success" => isset($_SESSION['success']) ? $_SESSION['success'] : null
        ];

        echo json_encode($respuesta);
        die();

        $stmt->close();
    }
}
