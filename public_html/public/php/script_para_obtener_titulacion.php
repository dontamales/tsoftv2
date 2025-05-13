<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

// Obtén el ID de la titulación desde la solicitud POST
$idTitulacion = isset($_POST['idTitulacion']) ? $_POST['idTitulacion'] : null;

if ($idTitulacion !== null) {
    // Utiliza un parámetro en la consulta SQL para evitar SQL injection
    $stmt = $conn->prepare("SELECT Tipo_Producto_Titulacion FROM producto_titulacion WHERE Id_Titulacion = ?");
    $stmt->bind_param("i", $idTitulacion); // "i" indica que es un parámetro entero
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $titulacionInfo = [
            'nombreL' => $row['Tipo_Producto_Titulacion']
        ];

        echo json_encode($titulacionInfo);
    } else {
        // En caso de no encontrar la titulación con el ID proporcionado
        echo json_encode(['error' => 'Titulación no encontrada']);
    }

    $stmt->close();
} else {
    // En caso de que no se haya proporcionado un ID válido
    echo json_encode(['error' => 'ID de titulación no válido']);
}

$conn->close();
?>
