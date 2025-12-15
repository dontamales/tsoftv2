<?php
require_once 'sesion.php';
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';

date_default_timezone_set('America/Denver');
$conn->query("SET time_zone='-06:00'");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;

$titulado_Estatus = 9;

if (
    isset($_GET['fecha_Ingreso_Reporte_Titulados']) &&
    isset($_GET['fecha_Egreso_Reporte_Titulados']) &&
    $_GET['fecha_Ingreso_Reporte_Titulados'] < $_GET['fecha_Egreso_Reporte_Titulados']
) {
    $fechaInicio = $_GET['fecha_Ingreso_Reporte_Titulados'];
    $fechaFin = $_GET['fecha_Egreso_Reporte_Titulados'];

    // Consulta para obtener los datos de los egresados titulados por tesis
    // En la consulta del genero puede que se requiera un ajuste en la forma de obtener el género de
    // la tabla de genero, a la tabla de informacion_personal
    $sql = "SELECT 
        u.Nombres_Usuario AS Nombre_Preferido,
        u.Apellidos_Usuario AS Apellidos_Completos,
        CONCAT(u.Nombres_Usuario, ' ', u.Apellidos_Usuario) AS Nombre_Completo,
        ip.CURP,
        ip.DNI,
        ip.ORCID,
        ip.Fecha_Nacimiento,
        LEFT(g.Genero, 1) AS Genero,
        ip.Pais,
        ip.Entidad,
        u.Correo_Usuario
    FROM egresado e
    JOIN usuario u 
        ON e.Fk_Usuario_Egresado = u.Id_Usuario
    JOIN producto_titulacion pt 
        ON e.Fk_Tipo_Titulacion_Egresado = pt.Id_Titulacion
    LEFT JOIN informacion_personal ip 
        ON ip.PkFk_Num_Control = e.Num_Control
    LEFT JOIN genero g 
        ON e.Fk_Sexo_Egresado = g.Id_Sexo_Genero
    WHERE 
        e.FK_Estatus_Egresado = ?
        AND pt.Id_Titulacion IN (3, 15)
        AND e.Fecha_Hora_Ceremonia_Egresado BETWEEN ? AND ?";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $titulado_Estatus, $fechaInicio, $fechaFin);
    $stmt->execute();
    $result = $stmt->get_result();

    $styleArray = [
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = [
        'A1' => 'Nombre Preferido* (Campo Requerido)',
        'B1' => 'Primer Apellido Preferido* (Campo Requerido)',
        'C1' => 'Segundo Apellido Preferido',
        'D1' => 'Nombre Completo',
        'E1' => 'Primer Apellido Completo',
        'F1' => 'Segundo Apellido Completo',
        'G1' => 'CURP',
        'H1' => 'DNI',
        'I1' => 'ORCID',
        'J1' => 'CVU',
        'K1' => 'Fecha Nacimiento por ejemplo [15/01/2001]',
        'L1' => 'Género M/H',
        'M1' => 'País',
        'N1' => 'Entidad'
    ];

    foreach ($headers as $cell => $text) {
        $sheet->setCellValue($cell, $text);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getColumnDimension(substr($cell, 0, 1))->setAutoSize(true);
        $sheet->getStyle($cell)->applyFromArray($styleArray);
        $sheet->getStyle($cell)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('CCCCCC');
    }

    $row = 2;
    while ($r = $result->fetch_assoc()) {
        // Separación tentativa de apellidos
        $primerApellido = $r['Apellidos_Completos'];
        $segundoApellido = '';

        // En algun momento se puede mejorar la separacion de los apellidos validación interactiva
        $apellidos = explode(' ', $r['Apellidos_Completos']);
        if (count($apellidos) >= 3) {
            $primerApellido = $apellidos[0] . ' ' . $apellidos[1];
            $segundoApellido = implode(' ', array_slice($apellidos, 2));
        } elseif (count($apellidos) == 2) {
            $primerApellido = $apellidos[0];
            $segundoApellido = $apellidos[1];
        }

        $sheet->setCellValue('A' . $row, $r['Nombre_Preferido']);
        $sheet->setCellValue('B' . $row, $primerApellido);
        $sheet->setCellValue('C' . $row, $segundoApellido);
        $sheet->setCellValue('D' . $row, $r['Nombre_Completo']);
        $sheet->setCellValue('E' . $row, $primerApellido);
        $sheet->setCellValue('F' . $row, $segundoApellido);
        $sheet->setCellValue('G' . $row, $r['CURP']);
        $sheet->setCellValue('H' . $row, $r['DNI']);
        $sheet->setCellValue('I' . $row, $r['ORCID']);
        $sheet->setCellValue('J' . $row, ''); // CVU vacío por ahora no existe ningun campo dentro de alguna tabla
        // Formatear Fecha_Nacimiento como fecha de Excel dd/mm/yyyy
        if (!empty($r['Fecha_Nacimiento'])) {
            // Convierte 'YYYY-MM-DD' a valor numérico de Excel
            $excelDate = XlsDate::stringToExcel($r['Fecha_Nacimiento']);
            $sheet->setCellValue('K' . $row, $excelDate);
            $sheet->getStyle('K' . $row)
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy');
        } else {
            $sheet->setCellValue('K' . $row, '');
        }
        $sheet->setCellValue('L' . $row, $r['Genero']);
        $sheet->setCellValue('M' . $row, $r['Pais']);
        $sheet->setCellValue('N' . $row, $r['Entidad']);

        foreach (range('A', 'N') as $col) {
            $sheet->getStyle($col . $row)->applyFromArray($styleArray);
        }

        $row++;
    }

    $rutaDirectorio = '../assets/archivos/reportes/reportes de registro de autores/';
    if (!file_exists($rutaDirectorio)) {
        mkdir($rutaDirectorio, 0777, true);
    }

    $nombreArchivo = $rutaDirectorio . 'Reporte registro de autores del ' . $fechaInicio . ' al ' . $fechaFin . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($nombreArchivo);

    // Verificar si ya existe reporte
    $verifica = $conn->prepare("SELECT * FROM reporte_registro_autores WHERE Fecha_Ingreso_Reporte_Registro_Autores = ? AND Fecha_Egreso_Reporte_Registro_Autores = ?");
    $verifica->bind_param("ss", $fechaInicio, $fechaFin);
    $verifica->execute();
    $existe = $verifica->get_result();

    if ($existe->num_rows > 0) {
        $update = $conn->prepare("UPDATE reporte_registro_autores SET Fecha_Creacion_Reporte_Registro_Autores = NOW(), Direccion_Archivo_Reporte_Registro_Autores = ? WHERE Fecha_Ingreso_Reporte_Registro_Autores = ? AND Fecha_Egreso_Reporte_Registro_Autores = ?");
        $update->bind_param("sss", $nombreArchivo, $fechaInicio, $fechaFin);
        $update->execute();
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO reporte_registro_autores (Fecha_Creacion_Reporte_Registro_Autores, Fecha_Ingreso_Reporte_Registro_Autores, Fecha_Egreso_Reporte_Registro_Autores, Direccion_Archivo_Reporte_Registro_Autores) VALUES (NOW(), ?, ?, ?)");
        $insert->bind_param("sss", $fechaInicio, $fechaFin, $nombreArchivo);
        $insert->execute();
        $insert->close();
    }

    $_SESSION['mensaje'] = "Reporte generado con éxito.";
    header('Location: ../views/reporteRegistroAutores.php');
    $stmt->close();
    $conn->close();
    exit;
} else {
    $_SESSION['mensaje'] = "Ocurrió un error al generar el reporte.";
    header('Location: ../views/reporteRegistroAutores.php');
    exit;
}
