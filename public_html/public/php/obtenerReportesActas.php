<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

header('Content-Type: application/json');

$sql = "
SELECT 
    r.Id_Reporte_Actas_Libros,
    r.Fecha_Creacion_Reporte,
    r.Anio,
    r.Periodo,
    r.Direccion_Archivo,
    l.Descripcion_Libro AS Nombre_Libro
FROM 
    reporte_actas_libros r
JOIN 
    libro l ON r.Id_Libro = l.Id_Libro
ORDER BY 
    r.Fecha_Creacion_Reporte DESC
";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
