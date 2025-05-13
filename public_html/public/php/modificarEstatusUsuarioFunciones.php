<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

function validateForm($usuarioEstatus)
{
    if (!$usuarioEstatus) {
        return "Seleccione un estatus para modificar.";
    }

    return "";
}


function modificarEstatusUsuario($conn, $usuarioEstatus, $usuarioId,){

    $stmt2 = $conn->prepare("SELECT * FROM usuario WHERE Id_Usuario = ?");
    $stmt2->bind_param("i", $usuarioId);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $stmt2->close();

    // Preparar e insertar el usuario en la base de datos usando consultas preparadas
    $query = "UPDATE egresado 
    SET FK_Estatus_Egresado = ? 
    WHERE Fk_Usuario_Egresado = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $usuarioEstatus, $usuarioId);

    if ($stmt->execute()) {

        $_SESSION['success'] = "Estatus de usuario modificado con éxito.";

        $respuesta = [
            "message" => "Usuario modificado con éxito.",
            "success" => isset($_SESSION['success']) ? $_SESSION['success'] : null
        ];

        echo json_encode($respuesta);
        die();

        $stmt->close();
    }
}