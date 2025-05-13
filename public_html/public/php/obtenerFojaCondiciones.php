<?php
require_once 'sesion.php'; // Verificación de sesión
require_once 'auth.php'; // Verificación de usuario administrador
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

// Verifica si se recibió el parámetro libro
if (isset($_GET['libro'])) {
    $libro = $_GET['libro'];

    // Consulta SQL para obtener los datos de formato_foja filtrados por el libro seleccionado
    $query = "SELECT ff.Id_Formato_Foja, l.Descripcion_Libro AS Fk_Libro_Formato_Foja, ff.Nombre_Formato_Foja, ff.Periodo_Formato_Foja, ff.Anio_Formato_Foja, ff.Direccion_Archivo_Formato_Foja
    FROM formato_foja ff
    INNER JOIN libro l ON ff.Fk_Libro_Formato_Foja = l.Id_Libro
    WHERE ff.Fk_Libro_Formato_Foja = ? 
    AND NOT EXISTS (SELECT 1 FROM egresado e WHERE e.Fk_Formato_Foja_Asignado_Egresado = ff.Id_Formato_Foja)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $libro);
    $stmt->execute();
    $result = $stmt->get_result();

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
} else {
    // Manejo de error si no se proporciona el parámetro 'libro'
    $formatoFojaData = [];
    // Puedes definir una respuesta de error aquí si lo deseas.
}

// Devuelve los datos en formato JSON
echo json_encode($formatoFojaData);

$stmt->close();
$conn->close();
?>
