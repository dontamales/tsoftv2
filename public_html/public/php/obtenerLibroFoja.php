<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$numControl = $_GET['numControl']; // Obtener el valor de numControl de la URL

$stmt = $conn->prepare("SELECT l.Id_Libro, l.Descripcion_Libro
FROM libro l
JOIN libro_documentos ld ON l.Id_Libro = ld.Fk_Libro_Libro_Documento
JOIN egresado e ON e.Fk_Tipo_Titulacion_Egresado = ld.Fk_Producto_Titulacion_Libro_Documento
WHERE e.Num_Control = ?");
$stmt->bind_param("s", $numControl); // Vincular el valor a la consulta preparada
$stmt->execute();
$result = $stmt->get_result();

$libro_foja = [];

while ($row = $result->fetch_assoc()) {
    $libro_foja[] = [
        'idL' => $row['Id_Libro'],
        'nombreL' => $row['Descripcion_Libro']
    ];
}

echo json_encode($libro_foja);

$stmt->close();
$conn->close();

?>

