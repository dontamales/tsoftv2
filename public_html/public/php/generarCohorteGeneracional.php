<?php
require_once 'sesion.php';
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

$documentos_Aprobados = 1;
$formato_B_Aprobado_Egresado = 1;
$Anexo_III_Aprobado_Egresado = 1;
$fecha_Ceremonia_Estatus = 8;
$titulado_Estatus = 9;

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

//Función para capitalizar nombres
function capitalizeName($name)
{
    $name = strtolower($name);
    $nameParts = explode(" ", $name);

    for ($i = 0; $i < count($nameParts); $i++) {
        $nameParts[$i] = ucfirst($nameParts[$i]);
    }

    return implode(" ", $nameParts);
}


// Obtener el archivo Excel
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_FILES['archivo_Cohorte_Generacional']) &&
    $_FILES['archivo_Cohorte_Generacional']['error'] === UPLOAD_ERR_OK
) {

    $archivo_Cohorte_Generacional = $_FILES['archivo_Cohorte_Generacional'];
    $inputFileName = $archivo_Cohorte_Generacional['tmp_name'];
    $originalFileName = pathinfo($archivo_Cohorte_Generacional['name'], PATHINFO_FILENAME);

    // Añadir bordes a la celda A1
    $styleArray = [
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];

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
    $columnsToRemove = ['Z', 'X', 'W', 'V', 'U', 'T', 'S', 'R', 'Q', 'P', 'O', 'N', 'M', 'L', 'K', 'J', 'I', 'H', 'G', 'F', 'E'];
    foreach ($columnsToRemove as $column) {
        $worksheet->removeColumn($column);
    }

    // Añadir headers al Excel
    $headers = ['A1' => 'No. Control', 'B1' => 'Nombre(s)', 'C1' => 'Apellidos', 'D1' => 'Carrera', 'E1' => 'Titulado', 'F1' => 'TOTAL TITULADOS'];

    foreach ($headers as $cell => $headerText) {
        $worksheet->setCellValue($cell, $headerText);
        // Hacer que el texto de los headers esté en negrita
        $worksheet->getStyle($cell)->getFont()->setBold(true);
        // Ajustar automáticamente el ancho de la columna al contenido
        $worksheet->getColumnDimension(substr($cell, 0, 1))->setAutoSize(true);

        $worksheet->getStyle($cell)->applyFromArray($styleArray);
        $worksheet->getStyle($cell)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('808080');
    }

    // SQL para obtener egresados con ese número de control
    $stmt2 = $conn->prepare("SELECT * FROM egresado
    JOIN usuario ON Fk_Usuario_Egresado = Id_Usuario 
    JOIN carrera ON Fk_Carrera_Egresado = Id_Carrera");
    //$stmt2->bind_param("s", $numControl);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $egresados  = [];
    while ($row = $result->fetch_assoc()) {
        $egresados[$row['Num_Control']] = $row;
    }

    $total_Titulados = 0;

    foreach ($worksheet->getRowIterator(2) as $row) {
        $rowIndex = $row->getRowIndex();
        $numControl = trim($worksheet->getCell('A' . $rowIndex)->getValue());
        $worksheet->getStyle('A' . $rowIndex)->applyFromArray($styleArray);
        $nombres = trim($worksheet->getCell('B' . $rowIndex)->getValue());
        $worksheet->getStyle('B' . $rowIndex)->applyFromArray($styleArray);
        $nombres = capitalizeName(trim($nombres));
        $apellidos = trim($worksheet->getCell('C' . $rowIndex)->getValue());
        $worksheet->getStyle('C' . $rowIndex)->applyFromArray($styleArray);
        $apellidos = capitalizeName(trim($apellidos));
        $carrera = trim($worksheet->getCell('D' . $rowIndex)->getValue());
        $worksheet->getStyle('D' . $rowIndex)->applyFromArray($styleArray);

        if (isset($egresados[$numControl])) {
            $egresado = $egresados[$numControl];
            if ($egresado['FK_Estatus_Egresado'] == $titulado_Estatus) {
                $worksheet->setCellValue('E' . $rowIndex, 'Sí');
                $worksheet->getStyle('E' . $rowIndex)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('00FF00');
                $total_Titulados++;
            } else {
                $worksheet->setCellValue('E' . $rowIndex, 'NA');
                $worksheet->getStyle('E' . $rowIndex)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF0000');
            }
        } else {
            $worksheet->setCellValue('E' . $rowIndex, 'No se encontró en la base de datos');
            $worksheet->getStyle('A' . $rowIndex)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFFF00');
        }
        $worksheet->getStyle('E' . $rowIndex)->applyFromArray($styleArray);
    }

    $worksheet->setCellValue('F2', $total_Titulados);
    $worksheet->getStyle('F2')->getFont()->setBold(true);
    $worksheet->getStyle('F2')->applyFromArray($styleArray);
    $worksheet->getStyle('F2')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFFF00');


    // Guardar el archivo modificado en la carpeta "output" con prefijo "modificado_"
    $direccionGuardado = '../assets/archivos/reportes/cohortes generacionales/';

    if (!file_exists($direccionGuardado)) {
        mkdir($direccionGuardado, 0777, true);
    }

    $nombreGuardado = $direccionGuardado . 'Reporte de cohorte generacional de ' . date("Y.m.d") . '.xlsx';

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($nombreGuardado);

    // Guardar el nombre del archivo en la base de datos
    $stmt4 = $conn->prepare("INSERT INTO cohortes_generacionales (Fecha_Creacion_Cohorte_Generacional, Direccion_Archivo_Cohorte_Generacional) VALUES (NOW(), ?)");
    $stmt4->bind_param("s", $nombreGuardado);
    $stmt4->execute();
    $stmt4->close();



    // Redirigir al usuario a la página de gestión de reporte de cohortes generacionales (../views/reporteCohortesGeneracionales.php) con un mensaje de éxito
    $_SESSION['mensaje'] = "Reporte generado con éxito.";
    header('Location: ../views/reporteCohortesGeneracionales.php');
    exit;
} else {
    // Redirigir al usuario a la página de gestión de reporte de cohortes generacionales (../views/reporteCohortesGeneracionales.php) con un mensaje de error
    $_SESSION['mensaje'] = "Ocurrió un error al generar el reporte.";
    header('Location: ../views/reporteCohortesGeneracionales.php');
    exit;
}

$stmt->close();
$conn->close();
