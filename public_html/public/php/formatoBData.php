<?php
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([1, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once("../../private/conexion.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$usuario_actual = $_SESSION['user_id'];

// Obtener datos de usuario
$stmt = $conn->prepare("SELECT * 
    FROM usuario 
    JOIN egresado ON usuario.Id_Usuario = egresado.Fk_Usuario_Egresado 
    LEFT JOIN direccion ON egresado.Fk_Direccion_Egresado = direccion.Id_Direccion 
    LEFT JOIN genero ON egresado.Fk_Sexo_Egresado = genero.Id_Sexo_Genero 
    LEFT JOIN proyecto ON egresado.Fk_Proyecto_Egresado = proyecto.Id_Proyecto 
    LEFT JOIN profesor ON egresado.Fk_Asesor_Interno_Egresado = profesor.Id_Profesor
    JOIN carrera ON egresado.Fk_Carrera_Egresado = carrera.Id_Carrera 
    LEFT JOIN producto_titulacion ON egresado.Fk_Tipo_Titulacion_Egresado = producto_titulacion.Id_Titulacion
    WHERE usuario.Id_Usuario = ?
");
$stmt->bind_param("i", $usuario_actual);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$stmt->close();
?>