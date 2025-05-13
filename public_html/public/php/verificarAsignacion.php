<?php
require_once 'sesion.php'; # VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; # VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); # VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

$data = json_decode(file_get_contents("php://input"));

// Sanitizar y validar los datos
$numControl = $data->numControl;

// Realizar una consulta para verificar si el usuario ya tiene asignados archivos FOJA
$query = "SELECT COUNT(*) AS count FROM egresado WHERE Num_Control = ? AND Fk_Formato_Libro_Asignado_Egresado IS NOT NULL AND Fk_Formato_Foja_Asignado_Egresado IS NOT NULL";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $numControl);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row['count'] > 0) {
    echo json_encode(['message' => 'El usuario ya tiene asignados archivos FOJA']);
} else {
    echo json_encode(['message' => 'Usuario no tiene asignados archivos FOJA']);
}
?>
