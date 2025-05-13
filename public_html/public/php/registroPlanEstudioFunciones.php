<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Función para verificar duplicados (para el registro de planes de estudio individuales)
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

// Función para registrar planes de estudio individuales
function registrarPlanEstudio($conn, $periodoGeneracion, $descripcionPlanAnio)
{
    if (empty($periodoGeneracion) || empty($descripcionPlanAnio)) {
        echo json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
        exit;
    }

    // Verificar duplicados
    isDuplicate($conn, "SELECT COUNT(*) AS count FROM planes_estudio WHERE Periodo_Generacion_Plan_Estudio = ?", $periodoGeneracion, 'Error: Ya existe un plan de estudio con ese periodo de generación.');

    isDuplicate($conn, "SELECT COUNT(*) AS count FROM planes_estudio WHERE Descripcion_Del_Plan_De_Año_Plan_Estudio = ?", $descripcionPlanAnio, 'Error: Ya existe un plan de estudio con esa descripción.');

    // Preparar e insertar el plan de estudio en la base de datos usando consultas preparadas
    $query = "INSERT INTO planes_estudio (Periodo_Generacion_Plan_Estudio, Descripcion_Del_Plan_De_Año_Plan_Estudio) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $periodoGeneracion, $descripcionPlanAnio);
    $stmt->execute();
    $stmt->close();
}
