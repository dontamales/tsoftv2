<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Función para verificar duplicados 
function isDuplicate($conn, $query, $param, $param2, $errorMsg)
{
    $statement = $conn->prepare($query);
    $statement->bind_param("si", $param, $param2);
    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    $statement->close();

    if ($row['count'] > 0) {
        $_SESSION['message'] = $errorMsg;
        die(json_encode(['message' => $errorMsg]));
    }
}

// Función para registrar departamentos
function modificarDepartamento($conn, $idDepartamento, $nombreDepartamento, $jefeDepartamento, $correoJefatura, $correoProyecto)
{
    if ($_SESSION['user_role'] == !3) {
        $respuesta = [
            "message" => "Error: No tiene permiso para modificar departamentos.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    if (empty($idDepartamento) || empty($nombreDepartamento) || empty($jefeDepartamento) || empty($correoJefatura)) {
        $respuesta = [
            "message" => "Error: Todos los campos son obligatorios.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    $stmt2 = $conn->prepare("SELECT * FROM departamento WHERE Id_Departamento = ?");
    $stmt2->bind_param("i", $idDepartamento);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $stmt2->close();

    if ($row['Nombre_Departamento'] != $nombreDepartamento) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM departamento WHERE Nombre_Departamento = ? AND Id_Departamento != ?", $nombreDepartamento, $idDepartamento, 'Error: Ya existe un departamento con ese nombre.');
    }

    // Preparar e insertar el usuario en la base de datos usando consultas preparadas
    $query = "UPDATE departamento 
    SET Nombre_Departamento = ?, 
    Fk_Jefe_Departamento = ?, 
    Correo_Jefatura_Departamento = ?,
    Correo_Proyecto_Departamento = ?
    WHERE Id_Departamento = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sissi", $nombreDepartamento, $jefeDepartamento, $correoJefatura, $correoProyecto, $idDepartamento);

    if ($stmt->execute()) {

        $respuesta = [
            "message" => "Departamento modificado con éxito.",
            "success" => isset($_SESSION['success']) ? $_SESSION['success'] : null
        ];

        echo json_encode($respuesta);
        die();

        $stmt->close();
    }
}
