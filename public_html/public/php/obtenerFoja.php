<?php
require_once 'sesion.php'; // Verificación de sesión
require_once 'auth.php'; // Verificación de usuario administrador
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

// Consulta SQL para obtener los datos de la tabla formato_foja
$query = "SELECT ff.Id_Formato_Foja, l.Descripcion_Libro AS Fk_Libro_Formato_Foja, ff.Nombre_Formato_Foja, ff.Periodo_Formato_Foja, ff.Anio_Formato_Foja, ff.Direccion_Archivo_Formato_Foja
FROM formato_foja ff
INNER JOIN libro l ON ff.Fk_Libro_Formato_Foja = l.Id_Libro;
";
$result = $conn->query($query);

// Verifica si la consulta se realizó con éxito
if ($result) {
    // Crear un array para almacenar los datos de formato_foja
    $formatoFojaData = array();

    // Recorre los resultados y almacena los datos en el array
    while ($row = $result->fetch_assoc()) {
        $formatoFojaData[] = [
            'ID' => $row['Id_Formato_Foja'],
            'Libro' => $row['Fk_Libro_Formato_Foja'],
            'Nombre Foja' => $row['Nombre_Formato_Foja'],
            'Periodo' => $row['Periodo_Formato_Foja'],
            'Año' => $row['Anio_Formato_Foja'],
            'Dirección' => $row['Direccion_Archivo_Formato_Foja']
        ];
    }
}
?>
