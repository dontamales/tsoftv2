<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

try {
    $stmt = $conn->prepare("SELECT Id_Documentos_Pendientes, Descripcion_Documentos_Pendientes FROM documentos_pendientes");
    if (!$stmt) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $documentos = array();
    while ($row = $result->fetch_assoc()) {
        $documentos[] = $row;
    }

    // Establecemos la cabecera JSON para indicar que la respuesta es un JSON
    header('Content-Type: application/json');
    echo json_encode($documentos);

    $conn->close();
} catch (Exception $e) {
    // También es buena práctica devolver un JSON en caso de error
    header('Content-Type: application/json');
    echo json_encode(["error" => $e->getMessage()]);
}
?>
