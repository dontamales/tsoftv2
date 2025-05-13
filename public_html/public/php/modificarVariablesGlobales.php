<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

if (isset($_POST['modificarVariablesHorario']) && isset($_POST['modificarVariablesPrecioExamenPro']) && isset($_POST['modificarVariablesCorreoTitulacion']) && isset($_POST['modificarVariablesTelefonoTitulacion']) && isset($_POST['modificarVariablesUbicacionTitulacion'])) {
     $horario = $_POST['modificarVariablesHorario'];
     $precioExamenPro = $_POST['modificarVariablesPrecioExamenPro'];
     $correoTitulacion = $_POST['modificarVariablesCorreoTitulacion'];
     $telefonoTitulacion = $_POST['modificarVariablesTelefonoTitulacion'];
     $ubicacionTitulacion = $_POST['modificarVariablesUbicacionTitulacion'];
     $id = 1;

     $stmt = $conn->prepare("UPDATE variables_globales SET Horario_Atencion_Variables_Globales = ?, Precio_Examen_Profesional_Variables_Globales = ?, Correo_Coordinacion_Titulacion_Variables_Globales = ?, Telefono_Coordinacion_Titulacion_Variables_Globales = ?, Ubicacion_Coordinacion_Titulacion_Variables_Globales = ? WHERE Id_Variables_Globales = ?");
     $stmt->bind_param("sssssi", $horario, $precioExamenPro, $correoTitulacion, $telefonoTitulacion, $ubicacionTitulacion, $id);

     if ($stmt->execute()) {
          $_SESSION['success'] = "Variables globales actualizadas correctamente.";
     } else {
          $_SESSION['error'] = "No se ha podido actualizar las variables globales.";
     }
}

// Redirigir de vuelta a gestionPassword
header("Location: ../views/gestionVariablesGlobales.php");
exit();


$stmt->close();
$conn->close();
