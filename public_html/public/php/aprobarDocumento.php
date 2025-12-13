<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once '../../private/conexion.php';
require_once '../vendor/autoload.php'; #LIBRERÍA SENDGRID
// Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer JH20250626
// require_once 'enviarCorreoFunciones.php';
require_once 'enviarCorreos.php';

// Documentos de residencias (los que ya no se piden) JH20250821
const DOCS_RESIDENCIAS = [6, 7];

// Productos exentos por ID (no requieren 6/7) JH20250821
const PRODUCTOS_EXENTOS = [12, 14, 15, 16, 17];

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");


$idDocumento = $_GET['idDocumento'];
$idEgresado = $_GET['idEgresado'];

// $stmt para seleccionar todos las columnas de la tabla de variables globales
$stmt = $conn->prepare("SELECT * FROM variables_globales WHERE Id_Variables_Globales = 1");
$stmt->execute();
$result = $stmt->get_result();
$variablesGlobales = $result->fetch_assoc();

// Variables globales de  Precio_Examen_Profesional_Variables_Globales
$precio_Examen_Profesional = $variablesGlobales['Precio_Examen_Profesional_Variables_Globales'];


$anexo_III_Aprobado = 1;
$documento_Aprobado = 1;
$anexo_III_Aprobado_Estatus = 6;
$anexo_III_Pendiente_Estatus = 5;
$formato_B_Aprobado_Egresado = 1;
$documentos_Entregados = 1;

//Fecha con formato (día/mes/año)
$fecha = date("d/m/Y");

// Inicializar una respuesta por defecto
$response = ['success' => false, 'message' => 'Error desconocido'];

try {

// Query para obtener los datos del egresado
$stmt = $conn->prepare("SELECT * FROM egresado JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario WHERE Num_Control = ?");
$stmt->bind_param('s', $idEgresado);
$stmt->execute();
$result = $stmt->get_result();
$egresadoData = $result->fetch_assoc();

// Obtener el ID del producto y verificar si es exento JH20250822
$productoId = (int)$egresadoData['Fk_Tipo_Titulacion_Egresado'];
$esExento = in_array($productoId, PRODUCTOS_EXENTOS, true);

//Query para obtener los datos del documento
$stmt = $conn->prepare("SELECT * FROM documentos_pendientes WHERE Id_Documentos_Pendientes = ?");
$stmt->bind_param('i', $idDocumento);
$stmt->execute();
$result = $stmt->get_result();
$documentoData = $result->fetch_assoc();

    // Realiza la conexión a la base de datos
    $stmt = $conn->prepare("UPDATE egresados_documentos SET Aceptado_Egresado_Documentos = ? WHERE Fk_Documentos_Pendientes2 = ? AND Fk_NumeroControl = ?");
    $stmt->bind_param('iis', $documento_Aprobado, $idDocumento, $idEgresado);
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Error en el update de egresados_documentos");
    }

    if (($idDocumento == 2 || $idDocumento == 10) && $egresadoData['FK_Estatus_Egresado'] == 5) {
        // Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer JH20250626
        // if (verificarLimiteCorreo($conn) >= 100) {
        //     echo json_encode(['success' => false, 'message' => 'Se ha alcanzado el límite de correos enviados por día.']);
        //     exit();
        // }
        $stmt_Estatus = $conn->prepare("UPDATE egresado 
        SET FK_Estatus_Egresado = ?, Anexo_III_Egresado = ? 
        WHERE Num_Control = ?
        AND FK_Estatus_Egresado = ?
        AND Formato_B_Aprobado_Egresado = ?");
        $stmt_Estatus->bind_param('iisii', $anexo_III_Aprobado_Estatus, 
        $anexo_III_Aprobado, 
        $idEgresado, 
        $anexo_III_Pendiente_Estatus, 
        $formato_B_Aprobado_Egresado);
        $stmt_Estatus->execute();

        $result_Estatus = $stmt_Estatus->get_result();

        $stmt_Estatus->close();
        
    } else if (($idDocumento == 2 || $idDocumento == 10) && $egresadoData['FK_Estatus_Egresado'] != 5){ //SC 08092025 Cambio de operador =! a != 
        // Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer JH20250626
        // if (verificarLimiteCorreo($conn) >= 100) {
        //     echo json_encode(['success' => false, 'message' => 'Se ha alcanzado el límite de correos enviados por día.']);
        //     exit();
        // }
        $stmt_Estatus = $conn->prepare("UPDATE egresado 
        SET Anexo_III_Egresado = ? 
        WHERE Num_Control = ?
        AND Formato_B_Aprobado_Egresado = ?");
        $stmt_Estatus->bind_param('isi', 
        $anexo_III_Aprobado, 
        $idEgresado, 
        $formato_B_Aprobado_Egresado);
        $stmt_Estatus->execute();

        $result_Estatus = $stmt_Estatus->get_result();

        $stmt_Estatus->close();
    }


    // Obtén el conteo de documentos requeridos para el tipo de titulación
    // $stmt_req = $conn->prepare("SELECT COUNT(*) as total_required FROM producto_titulacion_documentos_pendientes WHERE Fk_Producto_Titulacion_Documentos_Pendientes = ?");
    // $stmt_req->bind_param('i', $egresadoData['Fk_Tipo_Titulacion_Egresado']);
    // $stmt_req->execute();
    // $result_req = $stmt_req->get_result();
    // $data_req = $result_req->fetch_assoc();
    // $total_required = $data_req['total_required'];

    // Obtén el conteo de documentos aceptados del egresado
    // $stmt_acpt = $conn->prepare("SELECT COUNT(*) as total_accepted FROM egresados_documentos WHERE Aceptado_Egresado_Documentos = 1 AND Fk_NumeroControl = ?");
    // $stmt_acpt->bind_param('s', $idEgresado);
    // $stmt_acpt->execute();
    // $result_acpt = $stmt_acpt->get_result();
    // $data_acpt = $result_acpt->fetch_assoc();
    // $total_accepted = $data_acpt['total_accepted'];

    // --- Conteo de requeridos (excluyendo 6/7 si es exento) ---
    if ($esExento) {
        $stmt_req = $conn->prepare("
            SELECT COUNT(*) as total_required
            FROM producto_titulacion_documentos_pendientes
            WHERE Fk_Producto_Titulacion_Documentos_Pendientes = ?
            AND Fk_Documentos_Pendientes NOT IN (6,7)
        ");
    } else {
        $stmt_req = $conn->prepare("
            SELECT COUNT(*) as total_required
            FROM producto_titulacion_documentos_pendientes
            WHERE Fk_Producto_Titulacion_Documentos_Pendientes = ?
        ");
    }
    $stmt_req->bind_param('i', $egresadoData['Fk_Tipo_Titulacion_Egresado']);
    $stmt_req->execute();
    $result_req = $stmt_req->get_result();
    $data_req = $result_req->fetch_assoc();
    $total_required = (int)$data_req['total_required'];

    // --- Conteo de aceptados (excluyendo 6/7 si es exento) ---
    if ($esExento) {
        $stmt_acpt = $conn->prepare("
            SELECT COUNT(*) as total_accepted
            FROM egresados_documentos
            WHERE Aceptado_Egresado_Documentos = 1
            AND Fk_NumeroControl = ?
            AND Fk_Documentos_Pendientes2 NOT IN (6,7)
        ");
    } else {
        $stmt_acpt = $conn->prepare("
            SELECT COUNT(*) as total_accepted
            FROM egresados_documentos
            WHERE Aceptado_Egresado_Documentos = 1
            AND Fk_NumeroControl = ?
        ");
    }
    $stmt_acpt->bind_param('s', $idEgresado);
    $stmt_acpt->execute();
    $result_acpt = $stmt_acpt->get_result();
    $data_acpt = $result_acpt->fetch_assoc();
    $total_accepted = (int)$data_acpt['total_accepted'];

    //Compara los conteos
    if ($total_required == $total_accepted) {
        $stmt_update_egresado = $conn->prepare("UPDATE egresado SET Documentos_Entregados_Egresado = ? WHERE Num_Control = ?");
        $stmt_update_egresado->bind_param('is',$documentos_Entregados, $idEgresado);
        $stmt_update_egresado->execute();
    }


    if ($idDocumento == 2 || $idDocumento == 10) {
        $count = 0;

        do {
            $response = enviarCorreo(
                $conn,

                $egresadoData['Correo_Usuario'],

                $egresadoData['Nombres_Usuario'] . " " . $egresadoData['Apellidos_Usuario'],

                $documentoData["Descripcion_Documentos_Pendientes"] . ' aprobado.',

                'Buen día, su ' . $documentoData["Descripcion_Documentos_Pendientes"] . ' ha sido aprobado por un administrador, favor de acudir a Coordinación de Titulación a realizar el pago de su autorización de examen profesional que a la fecha de ' . $fecha . ' es de $' . $precio_Examen_Profesional . ' MXN, debe llevar su "Constancia de no inconveniencia" y su "Solicitud de acto de recepción profesional", además de la documentación que se le pide subir en la plataforma exceptuando la presentación y el trabajo final, favor de matenerse al pendiente mediante su correo registrado, si tiene documentos pendientes no olvide subirlos lo antes posible.',

                '<strong>Buen día, su ' . $documentoData["Descripcion_Documentos_Pendientes"] . ' ha sido aprobado por un administrador</strong>, favor de acudir a Coordinación de Titulación a realizar el pago de su autorización de examen profesional que a la fecha de ' . $fecha . ' es de $' . $precio_Examen_Profesional . ' MXN, debe llevar su <b>"Constancia de no inconveniencia"</b> y su <b>"Solicitud de acto de recepción profesional"</b>, además de la documentación que se le pide subir en la plataforma exceptuando la presentación y el trabajo final, favor de matenerse al pendiente mediante su correo registrado, si tiene documentos pendientes no olvide subirlos lo antes posible.'
            );

            if ($response->statusCode == 200) { // Cambio de metodo a propiedad $response->statusCode() == 202 JH20250626
                $count = 3;
                $response = ['success' => true];
            } else {
                $count++;
            }
        } while ($count < 3);
    } else {
        $response = ['success' => true];
    }
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
} finally {
    // Cerrar recursos de base de datos si están abiertos
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
    
    // Enviar la respuesta JSON
    echo json_encode($response);
}


