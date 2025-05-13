<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([1, 2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

// Verificar si se ha pasado un parámetro de plan de estudio
if (isset($_GET['Id_PlanEstudio'])) {
    $Id_PlanEstudio = $_GET['Id_PlanEstudio'];

    // Preparar una consulta SQL que filtre los tipos de titulación basándose en el plan de estudio
    // NOTA: Asegúrate de que el nombre de la tabla y el nombre de las columnas son correctos.
    $stmt = $conn->prepare("SELECT producto_titulacion.Id_Titulacion, producto_titulacion.Tipo_Producto_Titulacion FROM producto_titulacion INNER JOIN producto_titulacion_planes ON producto_titulacion_planes.Fk_Producto_Titulacion = producto_titulacion.Id_Titulacion INNER JOIN 
    planes_estudio ON planes_estudio.Id_PlanEstudio = producto_titulacion_planes.Fk_PlanEstudio WHERE planes_estudio.Id_PlanEstudio = ?");
    $stmt->bind_param("i", $Id_PlanEstudio);
} else {
    // Si no se pasó un plan de estudio, seleccionar todos los tipos de titulación
    $stmt = $conn->prepare("SELECT Id_Titulacion, Tipo_Producto_Titulacion FROM producto_titulacion");
}

$stmt->execute();
$result = $stmt->get_result();

$carreras = [];

while ($row = $result->fetch_assoc()) {
    $carreras[] = [
        'id' => $row["Id_Titulacion"],
        'nombre' => $row["Tipo_Producto_Titulacion"]
    ];
}


echo json_encode($carreras);

$stmt->close();
$conn->close();