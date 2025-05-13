<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$busqueda = $_GET['q'];
$estatus = $_GET['estatus'];

$stmt = $conn->prepare("SELECT Num_Control, Fk_Proyecto_Egresado, Fk_Carrera_Egresado, Fk_Usuario_Egresado, Fk_Tipo_Titulacion_Egresado, Nombres_Usuario, Apellidos_Usuario, Correo_Usuario, Nombre_Proyecto, Nombre_Carrera, Tipo_Producto_Titulacion
FROM egresado 
JOIN proyecto ON egresado.Fk_Proyecto_Egresado = proyecto.Id_Proyecto 
JOIN carrera ON egresado.Fk_Carrera_Egresado = carrera.Id_Carrera
JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
JOIN producto_titulacion ON egresado.Fk_Tipo_Titulacion_Egresado = producto_titulacion.Id_Titulacion
WHERE FK_Estatus_Egresado = ? AND Num_Control LIKE ?");
$busquedaParam = '%' . $busqueda . '%';
$stmt->bind_param('is', $estatus, $busquedaParam);
$stmt->execute();

$result = $stmt->get_result();
$egresado = array();

while ($fila = $result->fetch_assoc()) {
    $egresado[] = $fila;
}

echo json_encode($egresado);

$stmt->close();
$conn->close();
?>
