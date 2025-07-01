<?php
require_once 'sesion.php';
require_once 'auth.php';
require_roles([2, 3, 4, 5]);

require_once "../../private/conexion.php";
require_once '../../private/config/mail_config.php'; // Ruta de la configuración de PHPMailer

date_default_timezone_set('America/Denver');
$conn->query("SET time_zone='-06:00'");

function periodoActual()
{
    $mes = date("m");
    $anio = date("Y");
    return ($mes <= 6 ? "ENERO-JUNIO $anio" : "AGOSTO-DICIEMBRE $anio");
}

function incrementarConteoCorreo($conn)
{
    $today = date("Y-m-d");
    $query = "INSERT INTO correos_enviados (fecha, conteo)
              VALUES (?, 1)
              ON DUPLICATE KEY UPDATE conteo = conteo + 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $today);
    return $stmt->execute();
}

function enviarCorreo($conn, $correoPara, $nombrePara, $asunto, $textoPlanoCorreo, $textoHtmlCorreo)
{
    $periodo = periodoActual();
    $mail = crearMailer(); // ← desde mailer_config.php

    try {
        $mail->isHTML(true);
        $mail->addAddress($correoPara, $nombrePara);
        $mail->Subject = $asunto;
        $mail->Body    = $textoHtmlCorreo;
        $mail->AltBody = $textoPlanoCorreo;

        if (!incrementarConteoCorreo($conn)) {
            return (object)[
                'statusCode' => 500,
                'message' => 'No se pudo registrar el envío en la base de datos.'
            ];
        }

        if ($mail->send()) {
            return (object)[
                'statusCode' => 200,
                'message' => 'Correo enviado correctamente.'
            ];
        } else {
            return (object)[
                'statusCode' => 500,
                'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo
            ];
        }
    } catch (Exception $e) {
        return (object)[
            'statusCode' => 500,
            'message' => 'Excepción al enviar correo: ' . $e->getMessage()
        ];
    }
}


function enviarCorreoArchivo($conn, $correoPara, $nombrePara, $asunto, $textoPlanoCorreo, $textoHtmlCorreo, $archivoAdjunto, $nombreArchivoAdjunto)
{
    $periodo = periodoActual();
    $mail = crearMailer(); // PHPMailer preconfigurado

    try {
        $mail->addAddress($correoPara, $nombrePara);
        $mail->Subject = $asunto;
        $mail->Body    = $textoHtmlCorreo;
        $mail->AltBody = $textoPlanoCorreo;

        $mail->addAttachment($archivoAdjunto, $nombreArchivoAdjunto);

        if (!incrementarConteoCorreo($conn)) {
            return (object)[
                'statusCode' => 500,
                'message' => 'No se pudo registrar el envío en la base de datos.'
            ];
        }

        if ($mail->send()) {
            return (object)[
                'statusCode' => 200,
                'message' => 'Correo con archivo enviado correctamente.'
            ];
        } else {
            return (object)[
                'statusCode' => 500,
                'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo
            ];
        }
    } catch (Exception $e) {
        error_log("Error: {$mail->ErrorInfo}", 3, "../assets/archivos/$periodo/logs/error/error al enviar correo.log.log");
        return (object)[
            'statusCode' => 500,
            'message' => 'Excepción al enviar correo con archivo: ' . $e->getMessage()
        ];
    }
}
