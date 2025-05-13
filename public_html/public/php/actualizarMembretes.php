<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

function obtenerExtension($archivo_tmp)
{
    $tipo_mime = mime_content_type($archivo_tmp);
    switch ($tipo_mime) {
        case 'image/jpeg':
        case 'image/jpg':
            return '.jpg';
        case 'image/png':
            return '.png';
        default:
            return false; // Tipo no admitido
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        (isset($_FILES['encabezado_Membrete']) &&
            $_FILES['encabezado_Membrete']['error'] === UPLOAD_ERR_OK)

        && (isset($_FILES['pie_Membrete']) &&
            $_FILES['pie_Membrete']['error'] === UPLOAD_ERR_OK)

        && (isset($_FILES['firma_Membrete']) &&
            $_FILES['firma_Membrete']['error'] === UPLOAD_ERR_OK)
    ) {

        $encabezado_Membrete = $_FILES['encabezado_Membrete'];
        $pie_Membrete = $_FILES['pie_Membrete'];
        $firma_Membrete = $_FILES['firma_Membrete'];

        // Verificar que sea una imagen
        $checkEncabezado = getimagesize($encabezado_Membrete["tmp_name"]);
        $checkPie = getimagesize($pie_Membrete["tmp_name"]);
        $checkFirma = getimagesize($firma_Membrete["tmp_name"]);

        $extEncabezado = obtenerExtension($encabezado_Membrete["tmp_name"]);
        $extPie = obtenerExtension($pie_Membrete["tmp_name"]);
        $extFirma = obtenerExtension($firma_Membrete["tmp_name"]);

        if (!$extEncabezado || !$extPie || !$extFirma) {
            $_SESSION['error'] = "Uno o más archivos no tienen un formato admitido.";
            header("Location: ../views/membretesDocumentos.php");
            exit;
        }

        if ($checkEncabezado !== false && $checkPie !== false && $checkFirma !== false) {
            // Es una imagen
        } else {
            $_SESSION['error'] = "Uno o más archivos no son imágenes.";
            header("Location: ../views/membretesDocumentos.php");
            exit;
        }

        $id = 1;

        // Guardar el archivo modificado en la carpeta "output" con prefijo "modificado_"
        $direccionGuardado = '../assets/img/documentos/';

        if (!file_exists($direccionGuardado)) {
            mkdir($direccionGuardado, 0777, true);
        }

        // Aquí simplemente estableces los nombres que deseas
        $nombreBaseEncabezado = 'Encabezado_Membrete';
        $nombreBasePie = 'Pie_Membrete';
        $nombreBaseFirma = 'Firma_Membrete';

        $nombreGuardadoEncabezado = $direccionGuardado . $nombreBaseEncabezado . $extEncabezado;
        $nombreGuardadoPie = $direccionGuardado . $nombreBasePie . $extPie;
        $nombreGuardadoFirma = $direccionGuardado . $nombreBaseFirma . $extFirma;

        // Verificar y mover el archivo de encabezado
        if (move_uploaded_file($encabezado_Membrete["tmp_name"], $nombreGuardadoEncabezado)) {
            // Si necesitas hacer algo después de mover el archivo, lo haces aquí
        } else {
            $_SESSION['error'] = "Error al subir el archivo de encabezado.";
            header("Location: ../views/membretesDocumentos.php");
            exit;
        }

        // Verificar y mover el archivo de pie
        if (move_uploaded_file($pie_Membrete["tmp_name"], $nombreGuardadoPie)) {
            // Si necesitas hacer algo después de mover el archivo, lo haces aquí
        } else {
            $_SESSION['error'] = "Error al subir el archivo de pie.";
            header("Location: ../views/membretesDocumentos.php");
            exit;
        }

        // Verificar y mover el archivo de firma
        if (move_uploaded_file($firma_Membrete["tmp_name"], $nombreGuardadoFirma)) {
            // Si necesitas hacer algo después de mover el archivo, lo haces aquí
        } else {
            $_SESSION['error'] = "Error al subir el archivo de firma.";
            header("Location: ../views/membretesDocumentos.php");
            exit;
        }

        // Guardar el nombre del archivo en la base de datos
        $stmt = $conn->prepare("UPDATE membretes_documentos 
      SET Direccion_Encabezado_Membrete = ?, Direccion_Pie_Membrete = ?, Firma_Membrete = ? WHERE  Id_Membrete = ?");
        $stmt->bind_param("sssi", $nombreGuardadoEncabezado, $nombreGuardadoPie, $nombreGuardadoFirma, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Los membretes han sido actualizados con éxito.";
        } else {
            $_SESSION['error'] = "Hubo un error al actualizar los membretes.";
        }
    } else {
        $_SESSION['error'] = "Por favor, asegúrate de subir los tres archivos correctamente.";
    }
} else {
    $_SESSION['error'] = "Hubo un error al enviar los datos.";
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

header("Location: ../views/membretesDocumentos.php");
exit;
