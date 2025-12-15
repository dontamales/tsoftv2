<?php
require_once 'sesion.php';
require_once 'auth.php'; // VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); // VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';

date_default_timezone_set('America/Denver');
$conn->query("SET time_zone='-06:00'");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

$titulado_Estatus = 9;

if (isset($_GET['anio_Ingreso_Cohorte']) && !empty($_GET['anio_Ingreso_Cohorte'])) {
    $anioIngreso = intval($_GET['anio_Ingreso_Cohorte']);

    // Validar que sea un anio válido
    $anioActual = date('Y');
    if ($anioIngreso < 1966 || $anioIngreso > $anioActual) {
        $_SESSION['mensaje'] = "Año inválido. Ingresa un año entre 1980 y " . $anioActual . ".";
        header('Location: ../views/reporteCohortesGeneracionales.php?tab=anual');
        exit;
    }

    // Consulta para obtener los titulados que ingresaron en el anio especificado
    // Los numeros de control pueden empezar con: anio, anio+B, año+C
    $sql = "SELECT
        e.Num_Control,
        CONCAT(u.Nombres_Usuario, ' ', u.Apellidos_Usuario) AS Nombre_Egresado,
    c.nombre_carrera AS Nombre_carrera,
        LEFT(g.Genero, 1) AS Sexo,
        e.Promedio_Egresado,
        e.Edad_Egresado,
        e.Telefono_Egresado,
        e.Fecha_Ingreso_Egresado,
        e.Fecha_Egresar_Egresado,
        e.Celular_Egresado,
        e.Fecha_Hora_Ceremonia_Egresado
    FROM egresado e
    JOIN usuario u ON e.Fk_Usuario_Egresado = u.Id_Usuario
    JOIN carrera c ON e.Fk_Carrera_Egresado = c.id_Carrera
    LEFT JOIN genero g ON e.Fk_Sexo_Egresado = g.Id_Sexo_Genero
    WHERE e.FK_Estatus_Egresado = ?
    AND (
        e.Num_Control LIKE ? 
        OR e.Num_Control LIKE ?
        OR e.Num_Control LIKE ?
    )
    ORDER BY e.Num_Control ASC";

    $stmt = $conn->prepare($sql);
    
    // Crear los patrones de busqueda para los diferentes formatos de numero de control
        // Los números de control usan últimos 2 dígitos del año (ej: 2018 → 18)
        $ultimosDosDigitos = substr($anioIngreso, -2);  // Toma últimos 2 dígitos
    
        $patron1 = $ultimosDosDigitos . '%';           // Empieza con 18 (para 2018)
        $patron2 = 'B' . $ultimosDosDigitos . '%';     // Empieza con B18
        $patron3 = 'C' . $ultimosDosDigitos . '%';     // Empieza con C18
    
    $stmt->bind_param("isss", $titulado_Estatus, $patron1, $patron2, $patron3);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Log para diagnóstico
        error_log("generarReporteCohorteAnual.php - Sin resultados para año: " . $anioIngreso);
        error_log("Patrones buscados: " . $patron1 . ", " . $patron2 . ", " . $patron3);
        
        $_SESSION['mensaje'] = "No hay titulados registrados para el año " . $anioIngreso . ".";
        header('Location: ../views/reporteCohortesGeneracionales.php?tab=anual');
        $stmt->close();
        $conn->close();
        exit;
    }

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

    // Definir headers
    $headers = [
        'A1' => 'Num_Control',
        'B1' => 'Nombre_Egresado',
        'C1' => 'Nombre_carrera',
        'D1' => 'Sexo',
        'E1' => 'Promedio_Egresado',
        'F1' => 'Edad_Egresado',
        'G1' => 'Telefono_Egresado',
        'H1' => 'Fecha_Ingreso_Egresado',
        'I1' => 'Fecha_Egresar_Egresado',
        'J1' => 'Celular_Egresado',
        'K1' => 'Fecha_Hora_Ceremonia_Egresado'
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
        // Usar null coalescing para evitar warnings si alguna columna no existe o es NULL
        $sheet->setCellValue('A' . $row, $r['Num_Control'] ?? '');
        $sheet->setCellValue('B' . $row, $r['Nombre_Egresado'] ?? '');
        // nombre_carrera puede venir con distinto case/alias; preferimos el alias SQL Nombre_carrera
        $sheet->setCellValue('C' . $row, $r['Nombre_carrera'] ?? $r['nombre_carrera'] ?? $r['Nombre_carrera'] ?? '');
        $sheet->setCellValue('D' . $row, $r['Sexo'] ?? '');
        $sheet->setCellValue('E' . $row, $r['Promedio_Egresado'] ?? '');
        $sheet->setCellValue('F' . $row, $r['Edad_Egresado'] ?? '');
        $sheet->setCellValue('G' . $row, $r['Telefono_Egresado'] ?? '');
        $sheet->setCellValue('H' . $row, $r['Fecha_Ingreso_Egresado'] ?? '');
        $sheet->setCellValue('I' . $row, $r['Fecha_Egresar_Egresado'] ?? '');
        $sheet->setCellValue('J' . $row, $r['Celular_Egresado'] ?? '');
        $sheet->setCellValue('K' . $row, $r['Fecha_Hora_Ceremonia_Egresado'] ?? '');

        foreach (range('A', 'K') as $col) {
            $sheet->getStyle($col . $row)->applyFromArray($styleArray);
        }

        $row++;
    }

    $rutaDirectorio = '../assets/archivos/reportes/cohortes anuales/';
    if (!file_exists($rutaDirectorio)) {
        mkdir($rutaDirectorio, 0777, true);
    }

    $nombreArchivo = $rutaDirectorio . 'Reporte cohorte anual ' . $anioIngreso . ' - ' . date('Y.m.d') . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($nombreArchivo);

    // Guardar información en la base de datos
    $insert = $conn->prepare("INSERT INTO reporte_cohorte_anual (Fecha_Creacion_Reporte_Cohorte_Anual, Anio_Ingreso_Reporte_Cohorte_Anual, Direccion_Archivo_Reporte_Cohorte_Anual) VALUES (NOW(), ?, ?)");
    $insert->bind_param("is", $anioIngreso, $nombreArchivo);
    $insert->execute();
    $insert->close();

    $_SESSION['mensaje'] = "Reporte generado con éxito.";
    header('Location: ../views/reporteCohortesGeneracionales.php?tab=anual');
    $stmt->close();
    $conn->close();
    exit;
} else {
    $_SESSION['mensaje'] = "Ocurrió un error al generar el reporte.";
    header('Location: ../views/reporteCohortesGeneracionales.php?tab=anual');
    exit;
}
?>
