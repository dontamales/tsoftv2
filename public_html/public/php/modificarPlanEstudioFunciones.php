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
function modificarPlanEstudio($conn, $idPlanEstudio, $periodoPlanEstudio, $descripcionPlanEstudio)
{
    if ($_SESSION['user_role'] == !3) {
        $respuesta = [
            "message" => "Error: No tiene permiso para modificar carreras.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    if (empty($idPlanEstudio) || empty($periodoPlanEstudio) || empty($descripcionPlanEstudio)) {
        $respuesta = [
            "message" => "Error: Todos los campos son obligatorios.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    $stmt2 = $conn->prepare("SELECT * FROM planes_estudio WHERE Id_PlanEstudio = ?");
    $stmt2->bind_param("i", $idPlanEstudio);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $stmt2->close();

    if ($row['Periodo_Generacion_Plan_Estudio'] != $periodoPlanEstudio) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM planes_estudio WHERE Periodo_Generacion_Plan_Estudio = ?", $periodoPlanEstudio, 'Error: Ya existe un plan de estudio con ese período.');
    }

    if ($row['Descripcion_Del_Plan_De_Año_Plan_Estudio'] != $descripcionPlanEstudio) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM planes_estudio WHERE Descripcion_Del_Plan_De_Año_Plan_Estudio = ?", $descripcionPlanEstudio, 'Error: Ya existe un plan de estudio con esa descripción.');
    }

    // Preparar e insertar el usuario en la base de datos usando consultas preparadas
    $query = "UPDATE planes_estudio 
    SET Periodo_Generacion_Plan_Estudio = ?,
    Descripcion_Del_Plan_De_Año_Plan_Estudio = ? 
    WHERE Id_PlanEstudio = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $periodoPlanEstudio, $descripcionPlanEstudio, $idPlanEstudio);

    if ($stmt->execute()) {

        $respuesta = [
            "message" => "Plan de estudio modificado con éxito.",
            "success" => isset($_SESSION['success']) ? $_SESSION['success'] : null
        ];

        echo json_encode($respuesta);
        die();

        $stmt->close();
    }
}
