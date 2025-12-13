<?php
require_once 'sesion.php';
require_once 'auth.php';
require_roles([2, 3, 5, 6]);
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

date_default_timezone_set('America/Denver');
$conn->query("SET time_zone='-06:00'");

// Validar entrada
if (
    isset($_GET['libro_seleccionado'], $_GET['periodo'], $_GET['anio']) &&
    !empty($_GET['libro_seleccionado']) &&
    !empty($_GET['periodo']) &&
    !empty($_GET['anio'])
) {
    $libroId = intval($_GET['libro_seleccionado']);
    $periodo = $_GET['periodo'];
    $anio = intval($_GET['anio']);

    // Consulta
    $sql = "
    SELECT 
        f.Id_Formato_Foja,
        f.Numero_Formato_Foja,
        f.Periodo_Formato_Foja,
        f.Anio_Formato_Foja,
        e.Num_Control,
        u.Nombres_Usuario,
        u.Apellidos_Usuario
    FROM 
        formato_foja f
    JOIN egresado e ON e.Fk_Formato_Foja_Asignado_Egresado = f.Id_Formato_Foja
JOIN usuario u ON u.Id_Usuario = e.Fk_Usuario_Egresado
    WHERE 
        f.Fk_Libro_Formato_Foja = ?
        AND f.Periodo_Formato_Foja = ?
        AND f.Anio_Formato_Foja = ?
    ORDER BY 
        f.Numero_Formato_Foja ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $libroId, $periodo, $anio);
    $stmt->execute();
    $result = $stmt->get_result();

    // Crear Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Estilos
    $borderStyle = [
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];

    // Información general
    $totalFojas = $result->num_rows;
    $sheet->setCellValue('A1', 'REPORTE DE ACTAS ASIGNADAS A LIBRO');
    $sheet->setCellValue('A2', 'Libro ID: ' . $libroId);
    $sheet->setCellValue('A3', "Periodo: $periodo");
    $sheet->setCellValue('A4', "Año: $anio");
    $sheet->setCellValue('A5', "Total de fojas encontradas: $totalFojas");

    // Encabezados
    $sheet->setCellValue('A7', '#');
    $sheet->setCellValue('B7', 'Número de Foja');
    $sheet->setCellValue('C7', 'Número de Control');
    $sheet->setCellValue('D7', 'Nombre del Alumno');

    $sheet->getStyle('A7:D7')->getFont()->setBold(true);
    $sheet->getStyle('A7:D7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'BDD7EE']
        ],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    ];
    $sheet->getStyle('A7:D7')->applyFromArray($headerStyle);

    // Autoajuste de columnas
    foreach (['A', 'B', 'C', 'D'] as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Insertar datos
    $fila = 8;
    $contador = 1;

    while ($row = $result->fetch_assoc()) {
        $nombreCompleto = trim($row['Nombres_Usuario'] . ' ' . $row['Apellidos_Usuario']);

        $sheet->setCellValue("A{$fila}", $contador++);
        $sheet->setCellValue("B{$fila}", $row['Numero_Formato_Foja']);
        $sheet->setCellValue("C{$fila}", $row['Num_Control']);
        $sheet->setCellValue("D{$fila}", $nombreCompleto);

        $sheet->getStyle("A{$fila}:D{$fila}")->applyFromArray($borderStyle);
        $fila++;
    }

    // Guardar archivo
    $folderPath = '../assets/archivos/reportes/actas_por_libro/';
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }

    $fileName = "Reporte_Actas_Libro{$libroId}_{$periodo}_{$anio}.xlsx";
    $filePath = $folderPath . $fileName;

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    // Insertar o actualizar registro
    $check = $conn->prepare("SELECT Id_Reporte_Actas_Libros FROM reporte_actas_libros WHERE Id_Libro = ? AND Periodo = ? AND Anio = ?");
    $check->bind_param("isi", $libroId, $periodo, $anio);
    $check->execute();
    $resCheck = $check->get_result();

    if ($resCheck->num_rows > 0) {
        $update = $conn->prepare("UPDATE reporte_actas_libros SET Fecha_Creacion_Reporte = NOW(), Direccion_Archivo = ? WHERE Id_Libro = ? AND Periodo = ? AND Anio = ?");
        $update->bind_param("sisi", $filePath, $libroId, $periodo, $anio);
        $update->execute();
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO reporte_actas_libros (Id_Libro, Periodo, Anio, Fecha_Creacion_Reporte, Direccion_Archivo) VALUES (?, ?, ?, NOW(), ?)");
        $insert->bind_param("isis", $libroId, $periodo, $anio, $filePath);
        $insert->execute();
        $insert->close();
    }

    $_SESSION['mensaje'] = "Reporte generado con éxito.";
    header('Location: ../views/reporteAsignacionActasLibros.php');
    exit;
} else {
    $_SESSION['mensaje'] = "Ocurrió un error al generar el reporte.";
    header('Location: ../views/reporteAsignacionActasLibros.php');
    exit;
}
