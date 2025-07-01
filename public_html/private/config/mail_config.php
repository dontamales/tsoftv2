<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../public/vendor/autoload.php'; // Cargar la librería PHPMailer


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function crearMailer(): PHPMailer {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USERNAME'];
    $mail->Password   = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $_ENV['MAIL_PORT'];
    $mail->setFrom( $_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME'] );
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    return $mail;
}
