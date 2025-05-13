<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';
require_once("registroUsuarioFunciones.php");

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

  // Obtener la hoja activa
  $worksheet = $spreadsheet->getActiveSheet();

  // Eliminar fórmulas antes de eliminar las columnas
  $highestRow = $worksheet->getHighestRow();
  $highestColumn = $worksheet->getHighestColumn();
  for ($row = 2; $row <= $highestRow; ++$row) {
    for ($col = 'A'; $col !== $highestColumn; ++$col) {
      $cell = $worksheet->getCell($col . $row);
      if ($cell->isFormula()) {
        $worksheet->setCellValue($col . $row, $cell->getOldCalculatedValue());
      }
    }
  }

  // Eliminar columnas innecesarias
  $columnsToRemove = ['M', 'L', 'K', 'H', 'G', 'A'];
  foreach ($columnsToRemove as $column) {
    $worksheet->removeColumn($column);
  }

  // Guardar el archivo modificado en la carpeta "output" con prefijo "modificado_"
  $outputDirectory = '../assets/archivos/' . $periodo_Completo . '/' . 'lista de servicios escolares/';
  if (!file_exists($outputDirectory)) {
    mkdir($outputDirectory, 0777, true);
  }
  $outputFileName = $outputDirectory . date("Y.m.d") . "_modificado_" . $originalFileName . '.xlsx';
  $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
  $writer->save($outputFileName);


  /////////////////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////
  /////////////////////////////////////////////////////////////////////////////////////////
  // Leer el archivo Excel modificado
  $spreadsheet = IOFactory::load($outputFileName);
  $worksheet = $spreadsheet->getActiveSheet();
  $duplicados = 0;
  $mensajesAlerta = "";
  
  // Obtén el número actual de correos enviados
  $correosEnviadosAntes = verificarLimiteCorreo($conn);

  // Iterar sobre las filas del archivo Excel
  $respuestas = array();
  foreach ($worksheet->getRowIterator(3) as $row) {
    // Obtener los datos de la fila
    $rol = 1;
    //$password = 12345678;
    $password = generarPasswordAleatoria(8);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $numControl = trim($worksheet->getCell('A' . $row->getRowIndex())->getValue());
    $nombres = trim($worksheet->getCell('B' . $row->getRowIndex())->getValue());
    $nombres = capitalizeName(trim($nombres));
    $worksheet->getCell('B' . $row->getRowIndex())->setValue($nombres);
    $apellidos = trim($worksheet->getCell('C' . $row->getRowIndex())->getValue());
    $apellidos = capitalizeName(trim($apellidos));
    $worksheet->getCell('C' . $row->getRowIndex())->setValue($apellidos);
    $correo = trim($worksheet->getCell('G' . $row->getRowIndex())->getValue());
    $correo = strtolower(trim($correo));
    $worksheet->getCell('G' . $row->getRowIndex())->setValue($correo);

    // Verificar si las celdas obligatorias están vacías y, si es así, salir del bucle
    if (empty($numControl) || empty($nombres) || empty($apellidos) || empty($correo)) {
      break;
    }
    $carrera = $worksheet->getCell('D' . $row->getRowIndex())->getValue();
    switch (strtoupper($carrera)) {
      case 'LIC. EN ADMON':
        $carrera = 'Licenciatura en Administración';
        break;
      case 'INDUSTRIAL EAD':
      case 'INDUSTRIAL':
      case 'INGENIERIA INDUSTRIAL':
      case 'INDUTRIAL':
        $carrera = 'Ingeniería Industrial';
        break;
      case 'ELETROMECANICA':
      case 'INGENIERIA ELECTROMECANICA':
        $carrera = 'Ingeniería Electromecánica';
        break;
      case 'ING. LOGISTICA':
        $carrera = 'Ingeniería en Logística';
        break;
      case 'IGE':
      case 'ING. GEST. EMP.':
      case 'IGE EAD':
      case 'ING. GEST. EMP. EAD':
        $carrera = 'Ingeniería en Gestión Empresarial';
        break;
      case 'ING. MECAT.':
      case 'ING. MECATR':
        $carrera = 'Ingeniería en Mecatrónica';
        break;
      case 'TIC´S':
        $carrera = 'Ingeniería en Tecnologías de la Información y Comunicaciones';
        break;
      case 'ELECTRONICA':
        $carrera = 'Ingeniería Electrónica';
        break;
      case 'ELECTRICA':
        $carrera = 'Ingeniería Eléctrica';
        break;
      case 'INGENIERIA EN SISTEMAS COMPUTACIONALES':
        $carrera = 'Ingeniería en Sistemas Computacionales';
        break;
      default:
        $carrera = 'Licenciatura en Administración';
    }
    $carreraParam = '%' . $carrera . '%';
    $stmt = $conn->prepare("SELECT Id_Carrera, Nombre_Carrera FROM carrera WHERE Nombre_Carrera LIKE ?");
    $stmt->bind_param('s', $carreraParam);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $carrera_row = $result->fetch_assoc();
      $idCarrera = $carrera_row['Id_Carrera'];
    } else {
      $idCarrera = 1; // El valor predeterminado si no se encuentra ninguna carrera
    }
    $worksheet->getCell('D' . $row->getRowIndex())->setValue($carrera);
    $promedioCellValue = str_replace(',', '.', $worksheet->getCell('E' . $row->getRowIndex())->getValue());
    if (!is_numeric($promedioCellValue) || $promedioCellValue > 100 || $promedioCellValue < 70) {
      $promedio = 70;
    } else {
      $promedio = round($promedioCellValue, 2); // Redondear a dos decimales
    }
    $worksheet->getCell('E' . $row->getRowIndex())->setValue($promedio);
    $telefono = $worksheet->getCell('F' . $row->getRowIndex())->getValue();
    if (!isDuplicateExcel($conn, "SELECT COUNT(*) AS count FROM usuario WHERE Correo_Usuario = ?", $correo)) {
      // Verificar si ya existe un egresado con el mismo número de control (solo para sustentantes)
      if ($rol === 1 && !isDuplicateExcel($conn, "SELECT COUNT(*) AS count FROM egresado WHERE Num_Control = ?", $numControl)) {
        // Registrar el usuario
        $respuestas[] = registrarUsuarioExcel($conn, $rol, $nombres, $apellidos, $correo, $hashed_password, $numControl, $idCarrera, $promedio, $telefono, $password);
      } else {
        $duplicados++;
      }
    } else {
      $duplicados++;
    }
  }

  // Obtén el número final de correos enviados
  $correosEnviadosDespues = verificarLimiteCorreo($conn);

  // Calcula cuántos correos se enviaron en esta sesión
  $correosEnviadosEstaSesion = $correosEnviadosDespues - $correosEnviadosAntes;

  // Calcula cuántos correos faltan para alcanzar el límite de 100
  $correosRestantes = 100 - $correosEnviadosDespues;

  $exitos = array_filter($respuestas, function ($respuesta) {
    return $respuesta['status'] === true;
  });



  $fallos = count($respuestas) - count($exitos);

  $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
  $writer->save($outputFileName);

  // Redirigir a gestionDatos.php con un mensaje de éxito
  $_SESSION['success'] = "El archivo se procesó correctamente. <strong> Recuerde que hay un máximo de 100 correos diarios</strong> y después de completar los 100 correos ya no se permite registrar mediante el archivo de Excel por cuestiones de su contraseña.<br /><br /> Usuarios registrados exitosamente: " . count($exitos) . ".<br /> Registros fallidos: " . $fallos . ".<br />Registros duplicados: " . $duplicados .".<br /><br /> Correos enviados en esta sesión: " . $correosEnviadosEstaSesion . ".<br /> Total de correos enviados hoy: " . $correosEnviadosDespues . ".<br /> Total de correos restantes hoy: " . $correosRestantes . ".<br /><br /> Revise el archivo de errores o el de duplicados para más información.";
  header('Location: ../views/gestionDatos.php');
  exit;
} else {
  // Redirigir a gestionDatos.php con un mensaje de error
  $_SESSION['error'] = 'Ocurrió un error al procesar el archivo.';
  header('Location: ../views/gestionDatos.php');
  exit;
}
