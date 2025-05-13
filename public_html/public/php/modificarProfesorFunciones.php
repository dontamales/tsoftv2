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
function modificarProfesor($conn, $idProfesor, $nombresApellidos, $cedula, $grado)
{
    if ($_SESSION['user_role'] == !3) {
        $respuesta = [
            "message" => "Error: No tiene permiso para modificar profesores.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    if (empty($idProfesor) || empty($nombresApellidos) || empty($cedula) || empty($grado)) {
        $respuesta = [
            "message" => "Error: Todos los campos son obligatorios.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }


    $stmt2 = $conn->prepare("SELECT * FROM profesor WHERE Id_Profesor = ?");
    $stmt2->bind_param("i", $idProfesor);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $stmt2->close();

    if ($row['Nombre_Profesor'] != $nombresApellidos) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM profesor WHERE Nombre_Profesor = ?", $nombresApellidos, 'Error: Ya existe un profesor con ese nombre.');
    }
    
    if ($row['Cedula_Profesor'] != $cedula) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM profesor WHERE Cedula_Profesor = ?", $cedula, 'Error: Ya existe un profesor con esa cédula.');
    }
    

    // Preparar e insertar el usuario en la base de datos usando consultas preparadas
    $query = "UPDATE profesor 
    SET Nombre_Profesor = ?, 
    Cedula_Profesor = ?, 
    Grado_Academico_Profesor = ? 
    WHERE Id_Profesor = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $nombresApellidos, $cedula, $grado, $idProfesor);

    if ($stmt->execute()) {

        $respuesta = [
            "message" => "Profesor modificado con éxito.",
            "success" => isset($_SESSION['success']) ? $_SESSION['success'] : null
        ];

        echo json_encode($respuesta);
        die();

        $stmt->close();
    }
}
