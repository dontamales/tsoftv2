<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

function allUppercase($name)
{
    return strtoupper($name);
}

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

// Función para verificar duplicados (para la subida de archivos Excel)
function isDuplicateExcel($conn, $query, $param)
{
    $statement = $conn->prepare($query);
    $statement->bind_param("s", $param);
    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    $statement->close();

    if ($row['count'] > 0) {
        // Registrar el duplicado en un archivo .log
        $logEntry = date('Y-m-d H:i:s') . " - Duplicado detectado: " . $param . "\n";
        $directorio = '../assets/archivos/logs/lista de profesores/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
          }
        file_put_contents($directorio . date("Y.m.d") . ' duplicados.log', $logEntry, FILE_APPEND);

        // Devolver true si se encuentra un duplicado
        return true;
    } else {
        // Devolver false si no se encuentra un duplicado
        return false;
    }
}

// Función para registrar profesor individuales
function registrarProfesor($conn, $nombreCompleto, $cedula, $grado)
{
    if (empty($nombreCompleto) || empty($cedula) || empty($grado)) {
        echo json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
        exit;
    }

    // Preparar e insertar el profesor en la base de datos usando consultas preparadas
    $query = "INSERT INTO profesor (Nombre_Profesor, Cedula_Profesor, Grado_Academico_Profesor) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $nombreCompleto, $cedula, $grado);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function registrarProfesorExcel($conn, $nombreCompleto, $cedula, $grado)
{

    // Formatear los datos de fecha
    $fecha = date("Y-m-d");
    // Preparar e insertar el usuario en la base de datos usando consultas preparadas
    $query = "INSERT INTO profesor (Nombre_Profesor, Cedula_Profesor, Grado_Academico_Profesor) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $nombreCompleto, $cedula, $grado);


    if ($stmt->execute()) {
        return ['message' => 'Profesor registrado exitosamente.', 'status' => true];
    } else {
        return ['message' => 'Error al registrar el profesor: ' . $stmt->error];
    }
    $stmt->close();
}
