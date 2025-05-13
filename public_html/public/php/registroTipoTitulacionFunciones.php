<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Función para verificar duplicados (para el registro de tipos de titulación individuales)
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

// Función para registrar tipos de titulación individuales
function registrarTipoTitulacion($conn, $nombreTipoTitulacion)
{
    if (empty($nombreTipoTitulacion)) {
        echo json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM producto_titulacion");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['Tipo_Producto_Titulacion'] == $nombreTipoTitulacion) {
            echo json_encode(['message' => 'Error: Ya existe un tipo de titulación con ese nombre.']);
            exit;
        }
    }

    // Preparar e insertar el tipo de titulación en la base de datos usando consultas preparadas
    $query = "INSERT INTO producto_titulacion (Tipo_Producto_Titulacion) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nombreTipoTitulacion);
    $stmt->execute();
    $stmt->close();

    return true;
}

// Función para registrar tipos de titulación individuales
function registrarTipoTitulacionPlanEstudio($conn, $nombreTipoTitulacion, $planEstudio)
{
    if (empty($nombreTipoTitulacion) || empty($planEstudio)) {
        echo json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM producto_titulacion WHERE Tipo_Producto_Titulacion = ?");
    $stmt->bind_param("s", $nombreTipoTitulacion);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Preparar e insertar el tipo de titulación en la base de datos usando consultas preparadas
    $query = "INSERT INTO producto_titulacion_planes (Fk_Producto_Titulacion, Fk_PlanEstudio) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $row['Id_Titulacion'], $planEstudio);
    $stmt->execute();
    $stmt->close();

    return true;
}

// Función para registrar tipos de titulación individuales
function registrarTipoTitulacionDocumentos($conn, $nombreTipoTitulacion, $documentos)
{
    if (empty($nombreTipoTitulacion) || empty($documentos)) {
        echo json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
        return false;
    }

    $stmt = $conn->prepare("SELECT * FROM producto_titulacion WHERE Tipo_Producto_Titulacion = ?");
    $stmt->bind_param("s", $nombreTipoTitulacion);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    foreach ($documentos as $documento_id) {
        // Verificar si el registro ya existe
        $checkStmt = $conn->prepare("SELECT * FROM producto_titulacion_documentos_pendientes WHERE Fk_Producto_Titulacion_Documentos_Pendientes = ? AND Fk_Documentos_Pendientes = ?");
        $checkStmt->bind_param("ii", $row['Id_Titulacion'], $documento_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkStmt->close();

        if ($checkResult->num_rows == 0) {
            $query = "INSERT INTO producto_titulacion_documentos_pendientes (Fk_Producto_Titulacion_Documentos_Pendientes, Fk_Documentos_Pendientes) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $row['Id_Titulacion'], $documento_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    return true;
}
