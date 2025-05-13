<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

// Consulta SQL para obtener los egresados en estatus 9 (Titulado)
$query = "SELECT e.Num_Control, u.Nombres_Usuario, u.Apellidos_Usuario 
FROM egresado e
INNER JOIN usuario u ON e.Fk_Usuario_Egresado = u.Id_Usuario
WHERE e.FK_Estatus_Egresado = 9 
AND e.Fk_Formato_Libro_Asignado_Egresado IS NULL 
AND e.Fk_Formato_Foja_Asignado_Egresado IS NULL";
$result = $conn->query($query);

// Verifica si la consulta se realizó con éxito
if ($result) {
    // Crear un array para almacenar los egresados
    $egresados = array();

    // Recorre los resultados y almacena los datos en el array
    while ($row = $result->fetch_assoc()) {
        $egresados[$row['Num_Control']] = $row['Nombres_Usuario'] . ' ' . $row['Apellidos_Usuario'];
    }
}
?>