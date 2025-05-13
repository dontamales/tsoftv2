<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php"; #CONEXIÓN A LA BASE DE DATOS
require_once '../vendor/autoload.php'; #LIBRERÍA SENDGRID

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

function incrementarConteoCorreo($conn)
{

    // Obtener la fecha actual
    $fecha_actual = date("Y-m-d"); // Formato: YYYY-MM-DD
    $mes_actual = date("m"); // Formato: MM
    $ano_actual = date("Y"); // Formato: YYYY

    // Definir la variable $periodo en función del mes actual
    if ($mes_actual >= 1 && $mes_actual <= 6) {
        $periodo = "ENERO-JUNIO";
        $fecha_Inicio_Periodo = $ano_actual . "-01-01";
        $fecha_Cierre_Periodo = $ano_actual . "-06-30";
        $periodo_Completo = $periodo . " " . $ano_actual;
    } else {
        $periodo = "AGOSTO-DICIEMBRE";
        $fecha_Inicio_Periodo = $ano_actual . "-08-01";
        $fecha_Cierre_Periodo = $ano_actual . "-12-31";
        $periodo_Completo = $periodo . " " . $ano_actual;
    }

    try {
        $today = date("Y-m-d");
        $query = "INSERT INTO correos_enviados (fecha, conteo)
                  VALUES (?, 1)
                  ON DUPLICATE KEY UPDATE conteo = conteo + 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $today);
        if ($stmt->execute() === TRUE) {
            return true;
        } else {
            error_log("Error: " . $stmt->error, 3, "../assets/archivos/" . $periodo_Completo . "/logs/error/error al enviar correo.log.log");  // registrar el error
            return false;
        }
    } catch (Exception $e) {

        error_log("Error: " . $e->getMessage(), 3, "../assets/archivos/" . $periodo_Completo . "/logs/error/error al enviar correo.log.log");  // registrar el error
        return false;
    }
}

function verificarLimiteCorreo($conn)
{

    // Obtener la fecha actual
    $fecha_actual = date("Y-m-d"); // Formato: YYYY-MM-DD
    $mes_actual = date("m"); // Formato: MM
    $ano_actual = date("Y"); // Formato: YYYY

    // Definir la variable $periodo en función del mes actual
    if ($mes_actual >= 1 && $mes_actual <= 6) {
        $periodo = "ENERO-JUNIO";
        $fecha_Inicio_Periodo = $ano_actual . "-01-01";
        $fecha_Cierre_Periodo = $ano_actual . "-06-30";
        $periodo_Completo = $periodo . " " . $ano_actual;
    } else {
        $periodo = "AGOSTO-DICIEMBRE";
        $fecha_Inicio_Periodo = $ano_actual . "-08-01";
        $fecha_Cierre_Periodo = $ano_actual . "-12-31";
        $periodo_Completo = $periodo . " " . $ano_actual;
    }

    try {
        $today = date("Y-m-d");

        // Utilizar una sentencia preparada para evitar SQL Injection
        $stmt = $conn->prepare("SELECT conteo FROM correos_enviados WHERE fecha = ?");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $conteo = $row['conteo'];
        } else {
            $conteo = 0;
        }

        // Liberar el resultado y cerrar la sentencia
        $result->free();
        $stmt->close();

        return $conteo; // Retornamos el conteo actual de correos

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


function enviarCorreo($conn, $correoPara, $nombrePara, $asunto, $textoPlanoCorreo, $textoHtmlCorreo)
{

    // Obtener la fecha actual
    $fecha_actual = date("Y-m-d"); // Formato: YYYY-MM-DD
    $mes_actual = date("m"); // Formato: MM
    $ano_actual = date("Y"); // Formato: YYYY

    // Definir la variable $periodo en función del mes actual
    if ($mes_actual >= 1 && $mes_actual <= 6) {
        $periodo = "ENERO-JUNIO";
        $fecha_Inicio_Periodo = $ano_actual . "-01-01";
        $fecha_Cierre_Periodo = $ano_actual . "-06-30";
        $periodo_Completo = $periodo . " " . $ano_actual;
    } else {
        $periodo = "AGOSTO-DICIEMBRE";
        $fecha_Inicio_Periodo = $ano_actual . "-08-01";
        $fecha_Cierre_Periodo = $ano_actual . "-12-31";
        $periodo_Completo = $periodo . " " . $ano_actual;
    }

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../private');
    $dotenv->load();

    $apiKey = $_ENV['SENDGRID_API_KEY'];

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom("coordinacion_titulacion@cdjuarez.tecnm.mx", "T-Soft (Coordinación de Titulación)");
    $email->setSubject($asunto);
    $email->addTo($correoPara, $nombrePara);
    $email->addContent("text/plain", $textoPlanoCorreo);
    $email->addContent("text/html", $textoHtmlCorreo);

    $sendgrid = new \SendGrid($apiKey);
    try {
        if (!incrementarConteoCorreo($conn)) {
            throw new Exception("Error al actualizar el contador de correos electrónicos.");
        }
        $response = $sendgrid->send($email);
        return $response;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage(), 3, "../assets/archivos/" . $periodo_Completo . "/logs/error/error al enviar correo.log.log");  // registrar el error
        return false;
    }
}

function enviarCorreoArchivo($conn, $correoPara, $nombrePara, $asunto, $textoPlanoCorreo, $textoHtmlCorreo, $archivoAdjunto, $nombreArchivoAdjunto)
{

    // Obtener la fecha actual
    $fecha_actual = date("Y-m-d"); // Formato: YYYY-MM-DD
    $mes_actual = date("m"); // Formato: MM
    $ano_actual = date("Y"); // Formato: YYYY

    // Definir la variable $periodo en función del mes actual
    if ($mes_actual >= 1 && $mes_actual <= 6) {
        $periodo = "ENERO-JUNIO";
        $fecha_Inicio_Periodo = $ano_actual . "-01-01";
        $fecha_Cierre_Periodo = $ano_actual . "-06-30";
        $periodo_Completo = $periodo . " " . $ano_actual;
    } else {
        $periodo = "AGOSTO-DICIEMBRE";
        $fecha_Inicio_Periodo = $ano_actual . "-08-01";
        $fecha_Cierre_Periodo = $ano_actual . "-12-31";
        $periodo_Completo = $periodo . " " . $ano_actual;
    }

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../private');
    $dotenv->load();

    $apiKey = $_ENV['SENDGRID_API_KEY'];

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom("coordinacion_titulacion@cdjuarez.tecnm.mx", "T-Soft (Coordinación de Titulación)");
    $email->setSubject($asunto);
    $email->addTo($correoPara, $nombrePara);
    $email->addContent("text/plain", $textoPlanoCorreo);
    $email->addContent("text/html", $textoHtmlCorreo);

    $file_encoded = base64_encode(file_get_contents($archivoAdjunto));
    $email->addAttachment(
        $file_encoded,
        "application/text",
        $nombreArchivoAdjunto,
        "attachment"
    );

    $sendgrid = new \SendGrid($apiKey);
    try {
        if (!incrementarConteoCorreo($conn)) {
            throw new Exception("Error al actualizar el contador de correos electrónicos.");
        }
        $response = $sendgrid->send($email);
        return $response;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage(), 3, "../assets/archivos/" . $periodo_Completo . "/logs/error/error al enviar correo.log.log");  // registrar el error
        return false;
    }
}
