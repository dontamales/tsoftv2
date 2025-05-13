<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Obtener datos de la solicitud AJAX
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se recibieron datos válidos
if ($data && is_array($data)) {
    try {
        // Preparar la consulta para insertar en la tabla libro_documentos
        $stmt = $conn->prepare("INSERT INTO libro_documentos (Fk_Libro_Libro_Documento, Fk_Producto_Titulacion_Libro_Documento) VALUES (?, ?)");

        // Array para almacenar datos omitidos
        $datosOmitidos = [];

        // Recorrer los resultados y ejecutar la inserción si no existe un registro con los mismos valores
        foreach ($data as $resultado) {
            $libro = $resultado['libro'];
            $titulaciones = $resultado['titulaciones'];

            foreach ($titulaciones as $titulacion) {
                // Verificar si ya existe un registro con los mismos valores
                $checkStmt = $conn->prepare("SELECT COUNT(*) FROM libro_documentos WHERE Fk_Libro_Libro_Documento = ? AND Fk_Producto_Titulacion_Libro_Documento = ?");
                $checkStmt->bind_param("ii", $libro, $titulacion);
                $checkStmt->execute();
                $checkStmt->bind_result($count);
                $checkStmt->fetch();
                $checkStmt->close();

                // Si no hay registros existentes, realizar la inserción
                if ($count == 0) {
                    // Bind de los parámetros y ejecución de la consulta de inserción
                    $stmt->bind_param("ii", $libro, $titulacion);
                    $stmt->execute();
                } else {
                    // Almacenar los datos omitidos
                    $datosOmitidos[] = ["libro" => $libro, "titulacion" => $titulacion];
                }
            }
        }

        // Cerrar la conexión
        $stmt->close();
        $conn->close();

        // Enviar una respuesta exitosa con datos omitidos
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "datosOmitidos" => $datosOmitidos]);
    } catch (Exception $e) {
        // Enviar una respuesta de error
        header('Content-Type: application/json');
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    // Enviar una respuesta de error si no se recibieron datos válidos
    header('Content-Type: application/json');
    echo json_encode(["error" => "Datos no válidos"]);
}
?>
