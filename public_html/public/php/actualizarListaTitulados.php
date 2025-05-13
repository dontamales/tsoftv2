<?php
require_once("../../private/conexion.php");
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$documentos_Aprobados = 1;
$formato_B_Aprobado_Egresado = 1;
$Anexo_III_Aprobado_Egresado = 1;
$fecha_Ceremonia_Estatus = 8;
$titulado_Estatus = 9;

// Comprobar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Comprobar si el botón se ha pulsado
    if (isset($_POST['btn_Actualizar_Titulados'])) {

        //Actualizar titulados más recientes.
        $stmt = $conn->prepare("UPDATE egresado 
        SET FK_Estatus_Egresado = ? 
        WHERE Fecha_Hora_Ceremonia_Egresado <= NOW() 
        AND Documentos_Entregados_Egresado = ? 
        AND FK_Estatus_Egresado = ?
        AND Formato_B_Aprobado_Egresado = ?
        AND Anexo_III_Egresado = ?;");
        $stmt->bind_param("iiiii", $titulado_Estatus, $documentos_Aprobados, $fecha_Ceremonia_Estatus, $formato_B_Aprobado_Egresado, $Anexo_III_Aprobado_Egresado);
        $stmt->execute();
        $stmt->close();

        // Redirigir al usuario a la página de actualizacion de titulados (../views/actualizarTitulados.php) con un mensaje de éxito
        $_SESSION['mensaje'] = "Lista de titulados generada con éxito.";
        header('Location: ../views/actualizarTitulados.php');
        exit;
    } else {
        // Redirigir al usuario a la página de actualizacion de titulados (../views/actualizarTitulados.php) con un mensaje de errr
        $_SESSION['mensaje'] = "Ocurrió un error al generar la lista de titulados.";
        header('Location: ../views/actualizarTitulados.php');
        exit;
    }
}
