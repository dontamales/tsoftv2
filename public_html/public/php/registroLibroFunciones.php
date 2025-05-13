<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Función para verificar duplicados (para el registro de libro individuales)
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

// Función para registrar libros individuales
function registrarLibro($conn, $descripcion)
{
    if (empty($descripcion)) {
        echo json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
        exit;
    }

    // Verificar duplicados
    isDuplicate($conn, "SELECT COUNT(*) AS count FROM libro WHERE Descripcion_Libro = ?", $descripcion, 'Error: Ya existe un libro con esa descripción.');

    // Preparar e insertar el libro en la base de datos usando consultas preparadas
    $query = "INSERT INTO libro (Descripcion_Libro) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $descripcion);
    $stmt->execute();
    $stmt->close();

    return true;
}

