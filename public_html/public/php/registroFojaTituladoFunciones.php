<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

// Función para verificar duplicados (para el registro de fojas individuales)
function isDuplicate($conn, $query, $param, $errorMsg)
{
    $statement = $conn->prepare($query);
    $statement->bind_param("s", $param);
    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    $statement->close();

    if ($row['count'] > 0) {
        $_SESSION['message'] = $errorMsg;
        die(json_encode(['message' => $errorMsg]));
    }
}

// Función para actualizar libros individuales
function actualizarFojaTitulado($conn, $fkFormatoLibro, $fkFormatoFoja, $numControl)
{
    if (empty($fkFormatoLibro) || empty($fkFormatoFoja) || empty($numControl)) {
        echo json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
        exit;
    }

    // Actualiza la fila en la tabla de egresados donde Num_Control coincida
    $query = "UPDATE egresado SET Fk_Formato_Libro_Asignado_Egresado = ?, Fk_Formato_Foja_Asignado_Egresado = ? WHERE Num_Control = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $fkFormatoLibro, $fkFormatoFoja, $numControl);
    $stmt->execute();
    $stmt->close();

    return true;
}
?>
