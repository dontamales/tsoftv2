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
function modificarCarrera($conn, $idCarrera, $nombreCarrera, $departamentoCarrera, $jefeCarrera, $inicialesCarrera, $tipoCarrera)
{
    if ($_SESSION['user_role'] == !3) {
        $respuesta = [
            "message" => "Error: No tiene permiso para modificar carreras.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    if (empty($idCarrera) || empty($nombreCarrera) || empty($departamentoCarrera) || empty($jefeCarrera) || empty($inicialesCarrera) || empty($tipoCarrera)) {
        $respuesta = [
            "message" => "Error: Todos los campos son obligatorios.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    $stmt2 = $conn->prepare("SELECT * FROM carrera WHERE Id_Carrera = ?");
    $stmt2->bind_param("i", $idCarrera);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $stmt2->close();

    if ($row['Nombre_Carrera'] != $nombreCarrera) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM carrera WHERE Nombre_Carrera = ?", $nombreCarrera, 'Error: Ya existe una carrera con ese nombre.');
    }

    if ($row['Iniciales_Carrera'] != $nombreCarrera) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM carrera WHERE Iniciales_Carrera = ?", $inicialesCarrera, 'Error: Ya existe una carrera con esas iniciales.');
    }


    // Preparar e insertar el usuario en la base de datos usando consultas preparadas
    $query = "UPDATE carrera 
    SET Nombre_Carrera = ?, 
    Fk_Departamento_Carrera = ?, 
    Fk_Jefe_Carrera = ?,
    Iniciales_Carrera = ?,
    Tipo_Carrera = ?
    WHERE Id_Carrera = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siissi", $nombreCarrera, $departamentoCarrera, $jefeCarrera, $inicialesCarrera, $tipoCarrera, $idCarrera);

    if ($stmt->execute()) {

        $respuesta = [
            "message" => "Carrera modificada con éxito.",
            "success" => isset($_SESSION['success']) ? $_SESSION['success'] : null
        ];

        echo json_encode($respuesta);
        die();

        $stmt->close();
    }
}
