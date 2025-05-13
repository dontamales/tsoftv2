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

// Función para registrar carreras individuales
function registrarCarrera($conn, $departamento, $jefe, $nombreCarrera, $iniciales, $tipo)
{
    if (empty($departamento) || empty($jefe) || empty($nombreCarrera) || empty($iniciales) || empty($tipo)) {
        echo json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
        exit;
    }

    // Verificar duplicados
    isDuplicate($conn, "SELECT COUNT(*) AS count FROM carrera WHERE Nombre_Carrera = ?", $nombreCarrera, 'Error: Ya existe una carrera con ese nombre.');

    isDuplicate($conn, "SELECT COUNT(*) AS count FROM carrera WHERE Iniciales_Carrera = ?", $iniciales, 'Error: Ya existe una carrera con esas iniciales.');

    // Preparar e insertar el profesor en la base de datos usando consultas preparadas
    $query = "INSERT INTO carrera (Fk_Departamento_Carrera, Fk_Jefe_Carrera, Nombre_Carrera	, Iniciales_Carrera, Tipo_Carrera) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisss", $departamento, $jefe, $nombreCarrera, $iniciales, $tipo);
    $stmt->execute();
    $stmt->close();

    return true;
}