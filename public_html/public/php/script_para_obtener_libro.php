<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

// Obtén el ID del libro desde la solicitud POST
$idLibro = isset($_POST['idLibro']) ? $_POST['idLibro'] : null;

if ($idLibro !== null) {
    // Utiliza un parámetro en la consulta SQL para evitar SQL injection
    $stmt = $conn->prepare("SELECT Descripcion_Libro FROM libro WHERE Id_Libro = ?");
    $stmt->bind_param("i", $idLibro); // "i" indica que es un parámetro entero
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $libroInfo = [
            'nombreL' => $row['Descripcion_Libro']
        ];

        echo json_encode($libroInfo);
    } else {
        // En caso de no encontrar el libro con el ID proporcionado
        echo json_encode(['error' => 'Libro no encontrado']);
    }

    $stmt->close();
} else {
    // En caso de que no se haya proporcionado un ID válido
    echo json_encode(['error' => 'ID de libro no válido']);
}

$conn->close();
?>
