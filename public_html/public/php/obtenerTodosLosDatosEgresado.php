<?php
require_once 'sesion.php';
require_once 'auth.php';
require_roles([2, 3, 5, 6]);
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

if (isset($_GET['numControl'])) {
    $numControl = $_GET['numControl'];
    
    $stmt = $conn->prepare("SELECT * FROM egresado INNER JOIN producto_titulacion ON egresado.Fk_Tipo_Titulacion_Egresado = producto_titulacion.Id_Titulacion WHERE Num_Control = ?");
    $stmt->bind_param("s", $numControl);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(array()); // Enviar un objeto JSON vacío si no se encontraron datos
    }

    $stmt->close();
} else {
    echo json_encode(array()); // Enviar un objeto JSON vacío si no se proporciona el número de control
}

$conn->close();
?>