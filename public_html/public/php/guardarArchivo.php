<?php
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([1]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once '../../private/conexion.php';
require_once '../vendor/autoload.php';
require_once("formatoBData.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

use Ramsey\Uuid\Uuid;

// Genera el token
$uuid = Uuid::uuid4();
$token = $uuid->toString();

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

$directorio_subida = '../assets/archivos/' . $periodo_Completo . '/' . 'sustentantes/' . $usuario['Num_Control'] . '/' . 'ANEXO III y documentos/';

$archivos = [
    'fileUpload1' => 'Identificación oficial mexicana vigente de ',
    'fileUpload2' => 'Anexo III de ',
    'fileUpload3' => 'Asignacion de sinodales de ',
    'fileUpload4' => 'Trabajo final en PDF de ',
    'fileUpload5' => 'Presentación PowerPoint del proyecto de ',
    'fileUpload6' => 'Carta de acreditación de residencias de ',
    'fileUpload7' => 'Constancia de liberación de residencias de ',
    'fileUpload8' => 'Kardex de calificación del EGEL de ',
    'fileUpload9' => 'Certificado CENEVAL de ',
    'fileUpload10' => 'Oficio de liberación (equivalente al anexo III) de ',
    'fileUpload11' => 'Kardex de calificación (promedio igual o mayor a 90) de ',
    'fileUpload12' => 'Kardex de calificación de maestría (promedio igual o mayor a 80) de ',
    'fileUpload13' => 'Carta de la universidad de '
];

$asignaciones = [
    'fileUpload1' => 1,
    'fileUpload2' => 2,
    'fileUpload3' => 3,
    'fileUpload4' => 4,
    'fileUpload5' => 5,
    'fileUpload6' => 6,
    'fileUpload7' => 7,
    'fileUpload8' => 8,
    'fileUpload9' => 9,
    'fileUpload10' => 10,
    'fileUpload11' => 11,
    'fileUpload12' => 12,
    'fileUpload13' => 13,
];

function direccionesParaCuadros($inputId, $nombreArchivo, $usuarioId, $directorio_subida)
{
    global $alerta;

    // Verificar si la carpeta del usuario existe, si no, se crea
    if (!file_exists($directorio_subida)) {
        mkdir($directorio_subida, 0777, true);
    }

    // Nombre de archivo predeterminado
    $nombre_archivo_predeterminado = $nombreArchivo . $usuarioId;

    // Obtenemos la extensión del archivo original
    $extension_archivo = pathinfo($_FILES[$inputId]['name'], PATHINFO_EXTENSION);

    // Generamos el nombre completo del archivo con la extensión
    $nombre_archivo = $nombre_archivo_predeterminado . '.' . $extension_archivo;
    $ruta_archivo = $directorio_subida . $nombre_archivo;

        // Mover el archivo a la ruta especificada
        if (move_uploaded_file($_FILES[$inputId]['tmp_name'], $ruta_archivo)) {
            $alerta = "Archivo subido correctamente.";
        } else {
            $alerta = "Error al subir el archivo.";
        }

    return $ruta_archivo;  // Devuelve la dirección del archivo
}

function guardarTokenAnexoIII($conn, $egresadoId, $documentoId, $direccion_archivo, $token)
{
    $pendientes = 0;
    $stmt = $conn->prepare("INSERT INTO egresados_documentos (Fk_NumeroControl, Fk_Documentos_Pendientes2, Direccion_Archivo_Egresados_Documentos, Token_Egresado_Documentos, Fecha_Documento_Subido_Egresado_Documentos, Aceptado_Egresado_Documentos) VALUES (?, ?, ?, ?, NOW(), ?) ON DUPLICATE KEY UPDATE Direccion_Archivo_Egresados_Documentos = ?, Token_Egresado_Documentos = ?");
    $stmt->bind_param("sississ", $egresadoId, $documentoId, $direccion_archivo, $token, $pendientes, $direccion_archivo, $token);
    $stmt->execute();

    // Aquí es donde se duplica el archivo para el documentoId 6 o 7
    if ($documentoId == 6 || $documentoId == 7) {
        $documentoIdOtro = ($documentoId == 6) ? 7 : 6;
        $stmt->bind_param("sississ", $egresadoId, $documentoIdOtro, $direccion_archivo, $token, $pendientes, $direccion_archivo, $token);
        $stmt->execute();
    }

    $stmt->close();
}


function verificarDocumentoSubidoEgresado($conn, $usuarioId, $documentoId)
{

    $stmt = $conn->prepare("SELECT * FROM egresados_documentos WHERE Fk_NumeroControl = ? AND Fk_Documentos_Pendientes2 = ?");
    $stmt->bind_param("si", $usuarioId, $documentoId);
    $stmt->execute();

    $result = $stmt->get_result();
    $registroExiste = $result->num_rows > 0;

    $stmt->close();

    return $registroExiste; // Devuelve TRUE si el documento ya existe, y FALSE si no.
}

foreach ($archivos as $inputId => $nombreArchivo) {
    if ($_FILES[$inputId]['error'] === 0) {
        $indice = $asignaciones[$inputId];
        if (verificarDocumentoSubidoEgresado($conn, $usuario['Num_Control'], $indice)) {
            $_SESSION['alerta'] = "El archivo ya existe en la ruta especificada, no se puede subir más de una vez cada archivo hasta que sea revisado por un coordinador.";
            header('Location: ../views/cargarDocumentos.php');
            exit;
        }

        $ruta_archivo = direccionesParaCuadros($inputId, $nombreArchivo, $usuario['Num_Control'], $directorio_subida);
        
        if ($alerta !== "El archivo ya existe en la ruta especificada, no se puede subir más de una vez cada archivo hasta que sea revisado por un coordinador." && $alerta !== "Error al subir el archivo.") {
            // Subir el documento para el $indice actual
            guardarTokenAnexoIII($conn, $usuario['Num_Control'], $indice, $ruta_archivo, $token);

            // Si el documento es 6 o 7, subirlo también para el otro
            if ($indice == 6 || $indice == 7) {
                $indice_gemelo = ($indice == 6) ? 7 : 6;
                guardarTokenAnexoIII($conn, $usuario['Num_Control'], $indice_gemelo, $ruta_archivo, $token);
            }

            $_SESSION['alerta'] = $alerta;
            header('Location: ../views/cargarDocumentos.php');
            exit;
        }
        
        $_SESSION['alerta'] = $alerta;
        header('Location: ../views/cargarDocumentos.php');
        exit;
    }
}


// Si llegas a este punto, significa que no se cargó ningún archivo
$_SESSION['alerta'] = "Error al subir el archivo.";
header('Location: ../views/cargarDocumentos.php');
exit;
