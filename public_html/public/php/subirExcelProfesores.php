<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';
require_once("registroProfesorFunciones.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

use PhpOffice\PhpSpreadsheet\IOFactory;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $file = $_FILES['archivo'];
    $inputFileName = $file['tmp_name'];
    $originalFileName = pathinfo($file['name'], PATHINFO_FILENAME);

    // Leer el archivo Excel
    $spreadsheet = IOFactory::load($inputFileName);

    $worksheet = $spreadsheet->getActiveSheet();
    $duplicados = 0;
    $mensajesAlerta = "";

    // Iterar sobre las filas del archivo Excel
    $respuestas = array();
    foreach ($worksheet->getRowIterator(2) as $row) {

        $nombre_Profesor = trim($worksheet->getCell('A' . $row->getRowIndex())->getValue());
        $cedula = trim($worksheet->getCell('B' . $row->getRowIndex())->getValue());
        $grado_Academico = trim($worksheet->getCell('C' . $row->getRowIndex())->getValue());
        $nombre_Profesor = allUppercase(trim($nombre_Profesor));
        $grado_Academico = allUppercase(trim($grado_Academico));


        // Verificar si las celdas obligatorias están vacías y, si es así, salir del bucle
        if (empty($nombre_Profesor) || empty($grado_Academico) || empty($cedula)) {
            break;
        }


        // Verificar si ya existe un profesor con la misma cedula
        if (!isDuplicateExcel($conn, "SELECT COUNT(*) AS count FROM profesor WHERE Nombre_Profesor = ?", $nombre_Profesor)) {
            if (!isDuplicateExcel($conn, "SELECT COUNT(*) AS count FROM profesor WHERE Cedula_Profesor = ?", $nombre_Profesor)) {
                // Registrar el usuario
                $respuestas[] = registrarProfesorExcel($conn, $nombre_Profesor, $cedula, $grado_Academico);
            } else {
                $duplicados++;
            }
        } else {
            $duplicados++;
        }
    }

    $exitos = array_filter($respuestas, function ($respuesta) {
        return $respuesta['status'] === true;
    });



    $fallos = count($respuestas) - count($exitos);

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($inputFileName);

    // Redirigir a gestionDatos.php con un mensaje de éxito
    $_SESSION['success'] = "El archivo se procesó correctamente. Profesores registrados exitosamente: " . count($exitos) . ".<br /> Registros fallidos: " . $fallos . ".<br />Registros duplicados: " . $duplicados . ".<br /><br /> Revise el archivo de errores o el de duplicados para más información.";
    header('Location: ../views/gestionDatos.php');
    exit;
} else {
    // Redirigir a gestionDatos.php con un mensaje de error
    $_SESSION['error'] = 'Ocurrió un error al procesar el archivo.';
    header('Location: ../views/gestionDatos.php');
    exit;
}
