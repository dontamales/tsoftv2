<?php
require_once 'sesion.php';
require_once 'auth.php';
require_roles([3]);
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

if (isset($_POST['ids'])) {
    $ids = $_POST['ids'];

    // Comprobar que todos los valores en $ids son enteros
    foreach ($ids as $key => $id) {
        $ids[$key] = intval($id);  // Convertir a entero
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sqlLibro = "DELETE FROM libro 
                   WHERE Id_Libro IN ($placeholders)";


    $typesLibro = str_repeat('i', count($ids));
    $paramsLibro = array_merge($ids);

    $stmtLibro = $conn->prepare($sqlLibro);
    $stmtLibro->bind_param($typesLibro, ...$paramsLibro);



    if ($stmtLibro->execute()) {
        echo json_encode(["success" => true, "message" => "Libro(s) eliminado(s) correctamente."]);
        $stmtLibro->close();  // Cerrar prepared statement
        die();
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar los libros."]);
        $stmtLibro->close();  // Cerrar prepared statement
        die();
    }
} else {
    echo json_encode(["success" => false, "message" => "No se recibieron los datos necesarios."]);
}
