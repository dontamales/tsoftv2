<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Obtener los usuarios de la base de datos
try {
    $stmt = $conn->prepare("SELECT Id_Usuario, Fk_Roles_Usuario, Nombres_Usuario, Apellidos_Usuario, Correo_Usuario, Fecha_Usuario, FK_Estatus_Egresado, Num_Control FROM usuario
    LEFT JOIN egresado ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario
    JOIN roles ON roles.Id_Roles = usuario.Fk_Roles_Usuario
    ORDER BY Fk_Roles_Usuario DESC, Fecha_Usuario DESC");
    if (!$stmt) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear un array con los datos de los usuarios
    $usuarios = array();
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }

    echo json_encode($usuarios);

    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>