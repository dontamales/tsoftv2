<?php
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once("../../private/conexion.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Verifica la solicitud AJAX y el ID del registro
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepara y ejecuta la consulta de actualización
    $stmt = $conn->prepare("UPDATE egresado SET FK_Estatus_Egresado = 5 WHERE Num_Control = ?");
    $stmt->bind_param('s', $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false]);
}
