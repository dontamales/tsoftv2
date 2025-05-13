<?php
require_once 'sesion.php';
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

$documentos_Aprobados = 1;
$formato_B_Aprobado_Egresado = 1;
$Anexo_III_Aprobado_Egresado = 1;
$fecha_Ceremonia_Estatus = 8;
$titulado_Estatus = 9;

// Obtener la fecha actual
$fecha_actual = date("Y-m-d"); // Formato: YYYY-MM-DD
$mes_actual = date("m"); // Formato: MM
$ano_actual = date("Y"); // Formato: YYYY

// Definir la variable $periodo en función del mes actual
if ($mes_actual >= 1 && $mes_actual <= 6) {
    $periodo = "Enero-Junio";
    $fecha_Inicio_Periodo = $ano_actual . "-01-01";
    $fecha_Cierre_Periodo = $ano_actual . "-06-30";
} else {
    $periodo = "Agosto-Diciembre";
    $fecha_Inicio_Periodo = $ano_actual . "-08-01";
    $fecha_Cierre_Periodo = $ano_actual . "-12-31";
}


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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // SQL para obtener egresados en ese rango de fechas
    $sql = "SELECT * FROM egresado
    JOIN usuario ON Fk_Usuario_Egresado = Id_Usuario 
    JOIN carrera ON Fk_Carrera_Egresado = Id_Carrera
    WHERE Fecha_Usuario >= ? 
    AND (Fecha_Hora_Ceremonia_Egresado <= ?
    OR Fecha_Hora_Ceremonia_Egresado IS NULL
    OR Fecha_Hora_Ceremonia_Egresado = '0000-00-00 00:00:00')";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("ss", $fecha_Inicio_Periodo, $fecha_Cierre_Periodo);
    $stmt2->execute();
    $result = $stmt2->get_result();

    // Crear un nuevo Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Añadir bordes a la celda A1
    $styleArray = [
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];

    // Añadir headers al Excel
    $headers = ['A1' => 'Total inscritos', 'B1' => 'Total titulados', 'C1' => 'Total no titulados', 'D1' => 'Eficiencia terminal', 'A4' => '#', 'B4' => 'No. Control', 'C4' => 'Nombre(s)', 'D4' => 'Apellidos', 'E4' => 'Carrera',  'F4' => 'Promedio', 'G4' => 'Periodo', 'H4' => 'Titulado'];

    foreach ($headers as $cell => $headerText) {
        $sheet->setCellValue($cell, $headerText);
        // Hacer que el texto de los headers esté en negrita
        $sheet->getStyle($cell)->getFont()->setBold(true);
        // Ajustar automáticamente el ancho de la columna al contenido
        $sheet->getColumnDimension(substr($cell, 0, 1))->setAutoSize(true);

        $sheet->getStyle($cell)->applyFromArray($styleArray);
        $sheet->getStyle($cell)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('808080');
    }

    $row = 5;
    $total_Titulados = 0;
    $total_No_Titulados = 0;
    $total_Inscritos = 0;
    $eficiencia_Terminal = 0;

    while ($egresado = $result->fetch_assoc()) {
        // Aquí debes verificar si el estudiante está titulado_Estatus. Por ahora, solo copio los datos en el Excel.
        $total_Inscritos++;
        $sheet->setCellValue('A' . $row, $total_Inscritos);
        $sheet->getStyle('A' . $row)->applyFromArray($styleArray);
        $sheet->setCellValue('B' . $row, $egresado['Num_Control']);
        $sheet->getStyle('B' . $row)->applyFromArray($styleArray);
        $sheet->setCellValue('C' . $row, $egresado['Nombres_Usuario']);
        $sheet->getStyle('C' . $row)->applyFromArray($styleArray);
        $sheet->setCellValue('D' . $row, $egresado['Apellidos_Usuario']);
        $sheet->getStyle('D' . $row)->applyFromArray($styleArray);
        $sheet->setCellValue('E' . $row, $egresado['Nombre_Carrera']);
        $sheet->getStyle('E' . $row)->applyFromArray($styleArray);
        if ($egresado['Promedio_Egresado'] >= 95) {
            $sheet->setCellValue('F' . $row, $egresado['Promedio_Egresado']);
            $sheet->getStyle('F' . $row)->getFont()->setBold(true);
            $sheet->getStyle('F' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
        } else {
            $sheet->setCellValue('F' . $row, $egresado['Promedio_Egresado']);
        }
        $sheet->getStyle('F' . $row)->applyFromArray($styleArray);
        $sheet->setCellValue('G' . $row, $periodo);
        $sheet->getStyle('G' . $row)->applyFromArray($styleArray);

        if ($egresado['FK_Estatus_Egresado'] == $titulado_Estatus) {
            $sheet->setCellValue('H' . $row, 'Sí');
            $total_Titulados++;
            $sheet->getStyle('A' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('00FF00');
            $sheet->getStyle('H' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('00FF00');
        } else {
            $sheet->setCellValue('H' . $row, 'NA');
            $total_No_Titulados++;
            $sheet->getStyle('A' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF0000');
            $sheet->getStyle('H' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF0000');
        }

        $sheet->getStyle('H' . $row)->applyFromArray($styleArray);

        $row++;
    }

    $eficiencia_Terminal = ($total_Titulados / $total_Inscritos) * 100;
    $eficiencia_Terminal = round($eficiencia_Terminal, 2);

    $sheet->setCellValue('A2', $total_Inscritos);
    $sheet->getStyle('A2')->getFont()->setBold(true);
    $sheet->getStyle('A2')->applyFromArray($styleArray);
    $sheet->setCellValue('B2', $total_Titulados);
    $sheet->getStyle('B2')->getFont()->setBold(true);
    $sheet->getStyle('B2')->applyFromArray($styleArray);
    $sheet->setCellValue('C2', $total_No_Titulados);
    $sheet->getStyle('C2')->getFont()->setBold(true);
    $sheet->getStyle('C2')->applyFromArray($styleArray);
    $sheet->setCellValue('D2', $eficiencia_Terminal);
    $sheet->getStyle('D2')->getFont()->setBold(true);
    $sheet->getStyle('D2')->applyFromArray($styleArray);
    $sheet->getStyle('D2')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00FF00');
    $sheet->setCellValue('E2', '%');
    $sheet->getStyle('E2')->getFont()->setBold(true);
    $sheet->getStyle('E2')->applyFromArray($styleArray);
    $sheet->getStyle('E2')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00FF00');


    // Guardar el archivo modificado en la carpeta "output" con prefijo "modificado_"
    $direccionGuardado = '../assets/archivos/' . $periodo_Completo . '/reportes/reportes de eficiencia terminal/';
    if (!file_exists($direccionGuardado)) {
        mkdir($direccionGuardado, 0777, true);
    }

    $nombreGuardado = $direccionGuardado . 'Reporte de eficiencia terminal ' . $periodo . ' ' . $ano_actual . '.xlsx';

    $writer = new Xlsx($spreadsheet);
    $writer->save($nombreGuardado);

    $periodo = $periodo  . " " . $ano_actual;

    $stmt3 = $conn->prepare("SELECT * FROM reporte_eficiencia_terminal WHERE Periodo_Eficiencia_Terminal = ?");
    $stmt3->bind_param("s", $periodo);
    $stmt3->execute();
    $result2 = $stmt3->get_result();

    if ($result2->num_rows > 0) {
        $stmt5 = $conn->prepare("UPDATE reporte_eficiencia_terminal 
        SET Fecha_Creacion_Eficiencia_Terminal = NOW(), 
        Direccion_Eficiencia_Terminal = ? ,
        Total_Inscritos_Eficiencia_Terminal = ?,
        Total_Titulados_Eficiencia_Terminal = ?,
        Total_No_Titulados_Eficiencia_Terminal = ?,
        Promedio_Eficiencia_Terminal = ?
        WHERE Periodo_Eficiencia_Terminal = ?");
        $stmt5->bind_param("siiids", $nombreGuardado, $total_Inscritos, $total_Titulados, $total_No_Titulados, $eficiencia_Terminal, $periodo);
        $stmt5->execute();
        $stmt5->close();
    } else {
        // Guardar el nombre del archivo en la base de datos
        $stmt4 = $conn->prepare("INSERT INTO reporte_eficiencia_terminal (Fecha_Creacion_Eficiencia_Terminal, 
        Periodo_Eficiencia_Terminal, 
        Total_Inscritos_Eficiencia_Terminal, 
        Total_Titulados_Eficiencia_Terminal, 
        Total_No_Titulados_Eficiencia_Terminal, 
        Promedio_Eficiencia_Terminal, 
        Direccion_Eficiencia_Terminal) VALUES (NOW(), ?, ?, ?, ?, ?, ?)");
        $stmt4->bind_param("siiids", $periodo, $total_Inscritos, $total_Titulados, $total_No_Titulados, $eficiencia_Terminal, $nombreGuardado);
        $stmt4->execute();
        $stmt4->close();
    }



    // Redirigir al usuario a la página de gestión de reporte de eficiencia terminal (../views/reporteEficienciaTerminal.php) con un mensaje de éxito
    $_SESSION['mensaje'] = "Reporte generado con éxito.";
    header('Location: ../views/reporteEficienciaTerminal.php');
    exit;
} else {
    // Redirigir al usuario a la página de gestión de reporte de eficiencia terminal (../views/reporteEficienciaTerminal.php) con un mensaje de error
    $_SESSION['mensaje'] = "Ocurrió un error al generar el reporte.";
    header('Location: ../views/reporteEficienciaTerminal.php');
    exit;
}

$stmt->close();
$stmt2->close();
$conn->close();
