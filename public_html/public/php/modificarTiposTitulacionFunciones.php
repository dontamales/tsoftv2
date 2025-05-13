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
function modificarTipoTitulacion($conn, $idTipoTitulacion, $nombreTipoTitulacion, $documentos, $planEstudio)
{
    if ($_SESSION['user_role'] == !3) {
        $respuesta = [
            "message" => "Error: No tiene permiso para modificar los tipos de titulación.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    if (empty($idTipoTitulacion) || empty($nombreTipoTitulacion) || empty($documentos) || empty($planEstudio)) {
        $respuesta = [
            "message" => "Error: Todos los campos son obligatorios.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    $stmt2 = $conn->prepare("SELECT * FROM producto_titulacion WHERE Id_Titulacion = ?");
    $stmt2->bind_param("i", $idTipoTitulacion);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $stmt2->close();

    if ($row['Tipo_Producto_Titulacion'] != $nombreTipoTitulacion) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM producto_titulacion WHERE Tipo_Producto_Titulacion = ?", $nombreTipoTitulacion, 'Error: Ya existe un tipo de titulación con ese nombre.');
    }

    // Actualizamos producto_titulacion
    $query = "UPDATE producto_titulacion 
    SET Tipo_Producto_Titulacion = ?
    WHERE Id_Titulacion = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $nombreTipoTitulacion, $idTipoTitulacion);
    $stmt->execute();
    $stmt->close();

    // Actualizamos producto_titulacion_planes
    $query2 = "UPDATE producto_titulacion_planes 
    SET Fk_PlanEstudio = ?
    WHERE Fk_Producto_Titulacion = ?";
    $stmt3 = $conn->prepare($query2);
    $stmt3->bind_param("ii", $planEstudio, $idTipoTitulacion);
    $stmt3->execute();
    $stmt3->close();

    // Eliminamos todas las relaciones existentes de producto_titulacion_documentos_pendientes
    $query_delete = "DELETE FROM producto_titulacion_documentos_pendientes 
    WHERE Fk_Producto_Titulacion_Documentos_Pendientes = ?";
    $stmt_delete = $conn->prepare($query_delete);
    $stmt_delete->bind_param("i", $idTipoTitulacion);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Insertamos las nuevas relaciones en producto_titulacion_documentos_pendientes
    $query_insert = "INSERT INTO producto_titulacion_documentos_pendientes (Fk_Documentos_Pendientes, Fk_Producto_Titulacion_Documentos_Pendientes) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($query_insert);

    foreach ($documentos as $documento_id) {
        $stmt_insert->bind_param("ii", $documento_id, $idTipoTitulacion);
        $stmt_insert->execute();
    }
    $stmt_insert->close();



    $respuesta = [
        "message" => "Tipo de titulación modificada con éxito.",
        "success" => isset($_SESSION['success']) ? $_SESSION['success'] : null
    ];

    echo json_encode($respuesta);
    die();
}
