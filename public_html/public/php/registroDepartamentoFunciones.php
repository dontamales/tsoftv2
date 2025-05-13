<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Función para verificar duplicados (para el registro de departamentos individuales)
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

// Función para registrar departamentos individuales
function registrarDepartamento($conn, $nombreDepartamento, $jefeDepartamento, $correoJefatura, $correoProyecto)
{
    if (empty($nombreDepartamento) ||  empty($jefeDepartamento) || empty($correoJefatura) || empty($correoProyecto)) {
        echo json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
        exit;
    }

    // Verificar duplicados
    isDuplicate($conn, "SELECT COUNT(*) AS count FROM departamento WHERE Nombre_Departamento = ?", $nombreDepartamento, 'Error: Ya existe un departamento con ese nombre.');

    // Preparar e insertar el departamento en la base de datos usando consultas preparadas
    $query = "INSERT INTO departamento (Nombre_Departamento, Fk_Jefe_Departamento, Correo_Jefatura_Departamento, Correo_Proyecto_Departamento) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siss", $nombreDepartamento, $jefeDepartamento, $correoJefatura, $correoProyecto);
    $stmt->execute();
    $stmt->close();

    return true;
}