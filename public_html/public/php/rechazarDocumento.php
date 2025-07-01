<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once '../../private/conexion.php';
require_once '../vendor/autoload.php'; #LIBRERÍA SENDGRID
require_once 'enviarCorreos.php';

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$idDocumento = $_POST['idDocumento'];
$idEgresado = $_POST['idEgresado'];
$observaciones = $_POST['observaciones'];
$anexo_III_Rechazado = 0;

// Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer
// if (verificarLimiteCorreo($conn) >= 100) {
//     echo json_encode(['success' => false]);
//     exit();
// }

// Query para obtener los datos del egresado
$stmt = $conn->prepare("SELECT * FROM egresado JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario WHERE Num_Control = ?");
$stmt->bind_param('s', $idEgresado);
$stmt->execute();
$result = $stmt->get_result();
$egresadoData = $result->fetch_assoc();

//Query para obtener los datos del documento
$stmt = $conn->prepare("SELECT * FROM documentos_pendientes WHERE Id_Documentos_Pendientes = ?");
$stmt->bind_param('i', $idDocumento);
$stmt->execute();
$result = $stmt->get_result();
$documentoData = $result->fetch_assoc();

if ($idDocumento == 2 || $idDocumento == 10) {
    $stmt_Estatus = $conn->prepare("UPDATE egresado SET Anexo_III_Egresado = ? WHERE Num_Control = ?");
    $stmt_Estatus->bind_param('is', $anexo_III_Rechazado, $idEgresado);
    $stmt_Estatus->execute();

    $result_Estatus = $stmt_Estatus->get_result();

    $stmt_Estatus->close();
}

// Query para rechazar el documento (eliminar el registro)
$stmt = $conn->prepare("DELETE FROM egresados_documentos WHERE Fk_NumeroControl = ? AND Fk_Documentos_Pendientes2 = ?");
$stmt->bind_param('si', $idEgresado, $idDocumento);
$result = $stmt->execute();

if ($result) {
    $count = 0;

    do {
        $response = enviarCorreo(
            $conn,

            $egresadoData['Correo_Usuario'],

            $egresadoData['Nombres_Usuario'] . " " . $egresadoData['Apellidos_Usuario'],

            $documentoData["Descripcion_Documentos_Pendientes"] . ' rechazado.',

            'Su documento ' . $documentoData["Descripcion_Documentos_Pendientes"] . ' ha sido rechazado por un administrador con las observaciones: "'. $observaciones . '", favor de verificar dicho documento y volverlo a subir en la plataforma de http://login.tsoft.website/.',

            'Su documento <strong>' . $documentoData["Descripcion_Documentos_Pendientes"] . '</strong> ha sido rechazado por un administrador con las observaciones: "<strong>'. $observaciones . '</strong>", favor de verificar dicho documento y volverlo a subir en la plataforma de http://login.tsoft.website/.'
        );

        if ($response->statusCode == 200) {
            echo json_encode(['success' => true]);
            $count = 3;
        } else {
            $count++;
        }
    } while ($count < 3);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();