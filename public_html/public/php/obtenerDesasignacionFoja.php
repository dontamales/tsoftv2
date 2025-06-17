<?php
require_once 'sesion.php';
require_once 'auth.php';
require_once "../../private/conexion.php";


if (isset($_GET['libroId'])) {
    $libroId = intval($_GET['libroId']);

    $resFojas = $conn->query("
        SELECT e.Num_Control, e.Fk_Formato_Foja_Asignado_Egresado,f.Nombre_Formato_Foja
        FROM egresado e
        INNER JOIN formato_foja f ON e.Fk_Formato_Foja_Asignado_Egresado = f.Id_Formato_Foja
        WHERE f.Fk_Libro_Formato_Foja = $libroId
        ORDER BY f.Numero_Formato_Foja ASC
    ");

    $fojas = [];
    while ($fila = $resFojas->fetch_assoc()) {
        $fojas[] = $fila;
    }
    $resFojas->free();

    echo json_encode($fojas);
}
