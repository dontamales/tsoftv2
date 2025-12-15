<?php
require_once 'sesion.php';
require_once '../php/auth.php'; # SOLO SUPERADMINISTRADOR
require_roles([3]); # SOLO SUPERADMINISTRADOR
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';

date_default_timezone_set('America/Denver');
$conn->query("SET time_zone='-06:00'");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;

function sanitizeSheetTitle($title) {
    // quitar/caracteres inválidos y limitar a 31
    $title = preg_replace('/[\[\]\:\*\?\/\\\]/', ' ', $title);
    $title = trim(preg_replace('/\s+/', ' ', $title));
    if ($title === '') $title = 'Sin nombre';
    return mb_substr($title, 0, 31, 'UTF-8');
}

$titulado_Estatus = 9;

if (
    isset($_GET['fecha_Ingreso_Reporte_Titulados']) &&
    isset($_GET['fecha_Egreso_Reporte_Titulados']) &&
    $_GET['fecha_Ingreso_Reporte_Titulados'] < $_GET['fecha_Egreso_Reporte_Titulados']
) {
    $fechaInicio = $_GET['fecha_Ingreso_Reporte_Titulados'];
    $fechaFin    = $_GET['fecha_Egreso_Reporte_Titulados'];

    // === Consulta Base ===
    $sql = "SELECT 
                u.Nombres_Usuario                                  AS Nombre_Preferido,
                u.Apellidos_Usuario                                 AS Apellidos_Completos,
                CONCAT(u.Nombres_Usuario, ' ', u.Apellidos_Usuario) AS Nombre_Completo,
                ip.CURP,
                ip.DNI,
                ip.ORCID,
                ip.Fecha_Nacimiento,
                LEFT(g.Genero, 1)                                   AS Genero,
                ip.Pais,
                ip.Entidad,
                u.Correo_Usuario,
                e.Num_Control                                        AS No_Control,
                c.Nombre_Carrera                                     AS Nombre_Carrera,
                CONCAT(u.Nombres_Usuario, ' ', u.Apellidos_Usuario)  AS nombre_completo,
                d.Calle_Direccion                                    AS domicilio_calle,
                d.Colonia_Direccion                                  AS domicilio_colonia,
                d.Codigo_Postal_Direccion                            AS codigo_postal,
                COALESCE(e.Telefono_Egresado, e.Celular_Egresado)    AS telefono,
                ip.CURP                                              AS curp_alumno,
                u.Correo_Usuario                                     AS correo_electronico
            FROM egresado e
            JOIN usuario u                ON e.Fk_Usuario_Egresado = u.Id_Usuario
            JOIN producto_titulacion pt   ON e.Fk_Tipo_Titulacion_Egresado = pt.Id_Titulacion
            LEFT JOIN informacion_personal ip ON ip.PkFk_Num_Control = e.Num_Control
            LEFT JOIN genero g            ON e.Fk_Sexo_Egresado = g.Id_Sexo_Genero
            LEFT JOIN carrera c           ON e.Fk_Carrera_Egresado = c.Id_Carrera
            LEFT JOIN direccion d         ON e.Fk_Direccion_Egresado = d.Id_Direccion
            WHERE e.FK_Estatus_Egresado = ?
              AND pt.Id_Titulacion IN (3, 15)
              AND e.Fecha_Hora_Ceremonia_Egresado BETWEEN ? AND ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $titulado_Estatus, $fechaInicio, $fechaFin);
    $stmt->execute();
    $result = $stmt->get_result();

    // === Estilos generales ===
    $styleOutline = [
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];

    // === Crear Excel con una hoja por alumno ===
    $spreadsheet = new Spreadsheet();
    // Quitar la hoja por defecto para crear una por alumno
    $spreadsheet->removeSheetByIndex(0);

    // Encabezados fila 1
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
        'K1' => 'Fecha Nacimiento por ejemplo(15/01/2001)',
        'L1' => 'Género M/H',
        'M1' => 'País',
        'N1' => 'Entidad',
    ];

    // Encabezados Adicionales (fila 5)
    $extraHeaders = [
        'A5' => 'no_de_control',
        'B5' => 'nombre_completo',
        'C5' => 'domicilio_calle',
        'D5' => 'domicilio_colonia',
        'E5' => 'codigo_postal',
        'F5' => 'telefono',
        'G5' => 'curp_alumno',
        'H5' => 'correo_electronico',
        'I5' => 'nombre_carrera'
    ];

    // Para desambiguar nombres de hoja repetidos
    $sheetNameCount = [];

    while ($r = $result->fetch_assoc()) {
        // Separación tentativa de apellidos
        $primerApellido  = $r['Apellidos_Completos'];
        $segundoApellido = '';
        $apellidos = preg_split('/\s+/', trim((string)$r['Apellidos_Completos']));
        if (count($apellidos) >= 3) {
            $primerApellido  = $apellidos[0] . ' ' . $apellidos[1];
            $segundoApellido = implode(' ', array_slice($apellidos, 2));
        } elseif (count($apellidos) === 2) {
            $primerApellido  = $apellidos[0];
            $segundoApellido = $apellidos[1];
        }

        // Nombre de hoja = nombre completo (saneado)
        $baseName = sanitizeSheetTitle($r['Nombre_Completo']);
        $name = $baseName;
        if (isset($sheetNameCount[$baseName])) {
            $sheetNameCount[$baseName] += 1;
            $suffix = ' (' . $sheetNameCount[$baseName] . ')';
            $maxBase = 31 - mb_strlen($suffix, 'UTF-8');
            $name = mb_substr($baseName, 0, max(1, $maxBase), 'UTF-8') . $suffix;
        } else {
            $sheetNameCount[$baseName] = 1;
        }

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($name);

        // --- Fila 1: Encabezados Principales ---
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getColumnDimension(substr($cell, 0, 1))->setAutoSize(true);
            $sheet->getStyle($cell)->applyFromArray($styleOutline);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('CCCCCC');
        }

        // --- Fila 2: datos principales del Egresado ---
        $row = 2;
        $sheet->setCellValue('A' . $row, $r['Nombre_Preferido']);
        $sheet->setCellValue('B' . $row, $primerApellido);
        $sheet->setCellValue('C' . $row, $segundoApellido);
        $sheet->setCellValue('D' . $row, $r['Nombre_Completo']);
        $sheet->setCellValue('E' . $row, $primerApellido);
        $sheet->setCellValue('F' . $row, $segundoApellido);
        $sheet->setCellValue('G' . $row, $r['CURP']);
        $sheet->setCellValue('H' . $row, $r['DNI']);
        $sheet->setCellValue('I' . $row, $r['ORCID']);
        $sheet->setCellValue('J' . $row, ''); // CVU vacío (Por el momento no existe este campo en ninguna de las tablas JH20250618)

        // Fecha nacimiento dd/mm/yyyy
        if (!empty($r['Fecha_Nacimiento'])) {
            $excelDate = XlsDate::stringToExcel($r['Fecha_Nacimiento']); // 'YYYY-MM-DD'
            $sheet->setCellValue('K' . $row, $excelDate);
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        } else {
            $sheet->setCellValue('K' . $row, '');
        }

        $sheet->setCellValue('L' . $row, $r['Genero']);
        $sheet->setCellValue('M' . $row, $r['Pais']);
        $sheet->setCellValue('N' . $row, $r['Entidad']);

        foreach (range('A', 'N') as $col) {
            $sheet->getStyle($col . $row)->applyFromArray($styleOutline);
        }

        // --- Fila 5: Encabezados ---
        foreach ($extraHeaders as $cell => $text) {
            $sheet->setCellValue($cell, $text);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getColumnDimension(substr($cell, 0, 1))->setAutoSize(true);
            $sheet->getStyle($cell)->applyFromArray($styleOutline);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('CCCCCC');
        }

        // --- Fila 6: Valores de los Encabezados ---
        $sheet->setCellValue('A6', $r['No_Control']);           
        $sheet->setCellValue('B6', $r['nombre_completo']);      
        $sheet->setCellValue('C6', $r['domicilio_calle']);      
        $sheet->setCellValue('D6', $r['domicilio_colonia']);    
        $sheet->setCellValue('E6', $r['codigo_postal']);        
        $sheet->setCellValue('F6', $r['telefono']);             
        $sheet->setCellValue('G6', $r['curp_alumno']);          
        $sheet->setCellValue('H6', $r['correo_electronico']);   
        $sheet->setCellValue('I6', $r['Nombre_Carrera']);       

        foreach (range('A', 'I') as $col) {
            $sheet->getStyle($col . '6')->applyFromArray($styleOutline);
        }
    }

    // Si no hubo alumnos, crea una hoja vacía con aviso
    if ($spreadsheet->getSheetCount() === 0) {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Sin resultados');
        $sheet->setCellValue('A1', 'No se encontraron titulados de tesis en el periodo indicado.');
    }

    // Guardar archivo
    $rutaDirectorio = '../assets/archivos/reportes/reportes de registro individual de autores/';
    if (!file_exists($rutaDirectorio)) {
        mkdir($rutaDirectorio, 0777, true);
    }
    $nombreArchivo = $rutaDirectorio . 'Reporte registro INDIVIDUAL de autores del ' . $fechaInicio . ' al ' . $fechaFin . '.xlsx';

    $writer = new Xlsx($spreadsheet);
    $writer->save($nombreArchivo);

    // Persistir/actualizar en la tabla individual
    $verifica = $conn->prepare("SELECT 1 FROM reporte_registro_individual_autores WHERE Fecha_Ingreso_Reporte_Registro_Individual_Autores = ? AND Fecha_Egreso_Reporte_Registro_Individual_Autores = ?");
    $verifica->bind_param("ss", $fechaInicio, $fechaFin);
    $verifica->execute();
    $existe = $verifica->get_result();

    if ($existe && $existe->num_rows > 0) {
        $update = $conn->prepare("UPDATE reporte_registro_individual_autores 
                                  SET Fecha_Creacion_Reporte_Registro_Individual_Autores = NOW(),
                                      Direccion_Archivo_Reporte_Registro_Individual_Autores = ?
                                  WHERE Fecha_Ingreso_Reporte_Registro_Individual_Autores = ?
                                    AND Fecha_Egreso_Reporte_Registro_Individual_Autores = ?");
        $update->bind_param("sss", $nombreArchivo, $fechaInicio, $fechaFin);
        $update->execute();
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO reporte_registro_individual_autores
                                  (Fecha_Creacion_Reporte_Registro_Individual_Autores,
                                   Fecha_Ingreso_Reporte_Registro_Individual_Autores,
                                   Fecha_Egreso_Reporte_Registro_Individual_Autores,
                                   Direccion_Archivo_Reporte_Registro_Individual_Autores)
                                  VALUES (NOW(), ?, ?, ?)");
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
?>