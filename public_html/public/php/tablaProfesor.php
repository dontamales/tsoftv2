<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Obtener los profesores de la base de datos
try {
    $stmt = $conn->prepare("SELECT Id_Profesor, Nombre_Profesor, Cedula_Profesor, Grado_Academico_Profesor FROM profesor ORDER BY Nombre_Profesor ASC");
    if (!$stmt) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear un array con los datos de los profesores
    $profesor = array();
    while ($row = $result->fetch_assoc()) {
        $profesor[] = $row;
    }

    echo json_encode($profesor);

    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>