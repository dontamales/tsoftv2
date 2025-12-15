<?php
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
// Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer JH20250626
// require_once 'enviarCorreoFunciones.php'; // Asegúrate de que esta ruta sea la correcta
require_once 'enviarCorreos.php'; #Funciones para enviar correos

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$correoPara = $_POST['correoPara'];
$nombrePara = $_POST['nombrePara'];
$asunto = $_POST['asunto'];
$textoPlanoCorreo = $_POST['textoPlanoCorreo'];
$textoHtmlCorreo = $_POST['textoHtmlCorreo'];

$result = enviarCorreo($conn, $correoPara, $nombrePara, $asunto, $textoPlanoCorreo, $textoHtmlCorreo);
echo json_encode(['success' => $result]);

?>