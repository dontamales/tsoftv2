<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

try {
    $stmt = $conn->prepare("SELECT c.Id_Carrera, 
    c.Fk_Departamento_Carrera, 
    d.Nombre_Departamento, 
    c.Fk_Jefe_Carrera, 
    p.Nombre_Profesor AS Nombre_Jefe_Carrera, 
    c.Nombre_Carrera, 
    c.Iniciales_Carrera, 
    c.Tipo_Carrera 
    FROM carrera c
    LEFT JOIN departamento d ON c.Fk_Departamento_Carrera = d.Id_Departamento
    LEFT JOIN profesor p ON c.Fk_Jefe_Carrera = p.Id_Profesor
    ORDER BY FIELD(c.Tipo_Carrera, 'Licenciatura', 'Maestría', 'Doctorado')");
    if (!$stmt) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $carrera = array();
    while ($row = $result->fetch_assoc()) {
        $carrera[] = $row;
    }

    // Establecemos la cabecera JSON para indicar que la respuesta es un JSON
    header('Content-Type: application/json');
    echo json_encode($carrera);

    $conn->close();
} catch (Exception $e) {
    // También es buena práctica devolver un JSON en caso de error
    header('Content-Type: application/json');
    echo json_encode(["error" => $e->getMessage()]);
}
