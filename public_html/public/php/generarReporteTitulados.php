<?php
require_once 'sesion.php';
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
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

if (
    isset($_GET['fecha_Ingreso_Reporte_Titulados'])
    && isset($_GET['fecha_Egreso_Reporte_Titulados']) && $_GET['fecha_Ingreso_Reporte_Titulados'] < $_GET['fecha_Egreso_Reporte_Titulados']
) {
    $fecha_Ingreso_Reporte_Titulados = $_GET['fecha_Ingreso_Reporte_Titulados'];
    $fecha_Egreso_Reporte_Titulados = $_GET['fecha_Egreso_Reporte_Titulados'];

    // SQL para obtener egresados en ese rango de fechas
    $sql = "SELECT *, 
    proVo.Nombre_Profesor AS 'Vocal Suplente',
    proV.Nombre_Profesor AS 'Vocal',
    proS.Nombre_Profesor AS 'Secretario',
    proP.Nombre_Profesor AS 'Presidente'
    FROM egresado e
    LEFT JOIN proyecto p ON e.Fk_Proyecto_Egresado = p.Id_Proyecto
    LEFT JOIN libro l ON e.Fk_Formato_Libro_Asignado_Egresado = l.Id_Libro
    LEFT JOIN planes_estudio ple ON e.Fk_Plan_Estudio_Egresado = ple.Id_PlanEstudio
    LEFT JOIN producto_titulacion pt ON e.Fk_Tipo_Titulacion_Egresado = pt.Id_Titulacion
    LEFT JOIN formato_foja ff ON e.Fk_Formato_Foja_Asignado_Egresado = ff.Id_Formato_Foja
    LEFT JOIN usuario u ON e.Fk_Usuario_Egresado = u.Id_Usuario
    LEFT JOIN genero g ON e.Fk_Sexo_Egresado = g.Id_Sexo_Genero
    LEFT JOIN carrera c ON e.Fk_Carrera_Egresado = c.Id_Carrera
    LEFT JOIN proyecto pe ON e.Fk_Proyecto_Egresado = pe.Id_Proyecto
    LEFT JOIN asignacion_sinodales asi ON e.Fk_Proyecto_Egresado = asi.Fk_Proyecto_Sinodales
    LEFT JOIN profesor proP ON asi.Fk_Sinodal_1 = proP.Id_Profesor
    LEFT JOIN profesor proS ON asi.Fk_Sinodal_2 = proS.Id_Profesor
    LEFT JOIN profesor proV ON asi.Fk_Sinodal_3 = proV.Id_Profesor
    LEFT JOIN profesor proVo ON asi.Fk_Sinodal_4 = proVo.Id_Profesor
    WHERE Fecha_Hora_Ceremonia_Egresado >= ? 
    AND Fecha_Hora_Ceremonia_Egresado <= ? AND FK_Estatus_Egresado = ?";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("ssi", $fecha_Ingreso_Reporte_Titulados, $fecha_Egreso_Reporte_Titulados, $titulado_Estatus);
    $stmt2->execute();
    $result = $stmt2->get_result();

    // Añadir bordes a la celda A1
    $styleArray = [
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];

    // Crear un nuevo Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();


    // Añadir headers al Excel
    $headers = 
    [
        'A1' => 'TOTAL TITULADOS',
         
        'A4' => '#', 
        'B4' => 'No. Control', 
        'C4' => 'Nombre(s)', 
        'D4' => 'Apellido(s)', 
        'E4' => 'Carrera',  
        'F4' => 'Promedio', 
        'G4' => 'Fecha de ingreso', 
        'H4' => 'Fecha de egreso', 
        'I4' => 'Titulado',
        'J4' => 'Libro',
        'K4' => 'Opción Titulación',
        'L4' => 'Foja',
        'M4' => 'Año',
        'N4' => 'Periodo',
        'O4' => 'Sexo',
        'P4' => 'Edad',
        'Q4' => 'Plan De Estudio',
        'R4' => 'Proyecto',
        'S4' => 'Fecha & Hora/Ceremonia',
        'T4' => 'Correo',
        'U4' => 'Presidente',
        'V4' => 'Secretario',
        'W4' => 'Vocal',
        'X4' => 'Vocal Suplente'
    ];

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
    $total_Inscritos = 0;

    while ($egresado = $result->fetch_assoc()) {
        // Aquí debes verificar si el estudiante está titulado. Por ahora, solo copio los datos en el Excel.
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

        $sheet->setCellValue('G' . $row, $egresado['Fecha_Ingreso_Egresado']);
        $sheet->getStyle('G' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('H' . $row, $egresado['Fecha_Egresar_Egresado']);
        $sheet->getStyle('H' . $row)->applyFromArray($styleArray);

        if ($egresado['FK_Estatus_Egresado'] == $titulado_Estatus) {
            $sheet->setCellValue('I' . $row, 'Sí');
            $sheet->getStyle('A' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('00FF00');
            $sheet->getStyle('I' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('00FF00');
            $total_Titulados++;
        } else {
            $sheet->setCellValue('I' . $row, 'NA');
            $sheet->getStyle('A' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF0000');
            $sheet->getStyle('I' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF0000');
        }
        $sheet->getStyle('I' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('J' . $row, $egresado['Descripcion_Libro']);
        $sheet->getStyle('J' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('K' . $row, $egresado['Tipo_Producto_Titulacion']);
        $sheet->getStyle('K' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('L' . $row, $egresado['Numero_Formato_Foja']); // ADDED BY JOSE NAVA 08/01/2024
        $sheet->getStyle('L' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('M' . $row, $egresado['Anio_Formato_Foja']);
        $sheet->getStyle('M' . $row)->applyFromArray($styleArray);
        
        $sheet->setCellValue('N' . $row, $egresado['Periodo_Formato_Foja']);
        $sheet->getStyle('N' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('O' . $row, $egresado['Genero']);
        $sheet->getStyle('O' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('P' . $row, $egresado['Edad_Egresado']);
        $sheet->getStyle('P' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('Q' . $row, $egresado['Descripcion_Del_Plan_De_Año_Plan_Estudio']);
        $sheet->getStyle('Q' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('R' . $row, $egresado['Nombre_Proyecto']);
        $sheet->getStyle('R' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('S' . $row, $egresado['Fecha_Hora_Ceremonia_Egresado']);
        $sheet->getStyle('S' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('T' . $row, $egresado['Correo_Usuario']);
        $sheet->getStyle('T' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('U' . $row, $egresado['Presidente']);
        $sheet->getStyle('U' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('V' . $row, $egresado['Secretario']);
        $sheet->getStyle('V' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('W' . $row, $egresado['Vocal']);
        $sheet->getStyle('W' . $row)->applyFromArray($styleArray);

        $sheet->setCellValue('X' . $row, $egresado['Vocal Suplente']);
        $sheet->getStyle('X' . $row)->applyFromArray($styleArray);

        $row++;
    }
    $sheet->setCellValue('A2', $total_Titulados);
    $sheet->getStyle('A2')->getFont()->setBold(true);
    $sheet->getStyle('A2')->applyFromArray($styleArray);
    $sheet->getStyle('A2')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFFF00');

    // Guardar el archivo modificado en la carpeta "output" con prefijo "modificado_"
    $direccionGuardado = '../assets/archivos/reportes/reportes de titulados/';
    if (!file_exists($direccionGuardado)) {
        mkdir($direccionGuardado, 0777, true);
    }

    $nombreGuardado = $direccionGuardado . 'Reporte de titulados de ' . $fecha_Ingreso_Reporte_Titulados . ' al ' . $fecha_Egreso_Reporte_Titulados . '.xlsx';

    $writer = new Xlsx($spreadsheet);
    $writer->save($nombreGuardado);

    $stmt3 = $conn->prepare("SELECT * FROM reporte_titulados WHERE Fecha_Ingreso_Reporte_Titulados = ? AND Fecha_Egreso_Reporte_Titulados = ?");
    $stmt3->bind_param("ss", $fecha_Ingreso_Reporte_Titulados, $fecha_Egreso_Reporte_Titulados);
    $stmt3->execute();
    $result2 = $stmt3->get_result();

    if ($result2->num_rows > 0) {
        $stmt5 = $conn->prepare("UPDATE reporte_titulados SET Fecha_Creacion_Reporte_Titulados = NOW(), Direccion_Archivo_Reporte_Titulados = ? WHERE Fecha_Ingreso_Reporte_Titulados = ? AND Fecha_Egreso_Reporte_Titulados = ?");
        $stmt5->bind_param("sss", $nombreGuardado, $fecha_Ingreso_Reporte_Titulados, $fecha_Egreso_Reporte_Titulados);
        $stmt5->execute();
        $stmt5->close();
    } else {
        // Guardar el nombre del archivo en la base de datos
        $stmt4 = $conn->prepare("INSERT INTO reporte_titulados (Fecha_Creacion_Reporte_Titulados, Fecha_Ingreso_Reporte_Titulados, Fecha_Egreso_Reporte_Titulados, Direccion_Archivo_Reporte_Titulados) VALUES (NOW(), ?, ?, ?)");
        $stmt4->bind_param("sss", $fecha_Ingreso_Reporte_Titulados, $fecha_Egreso_Reporte_Titulados, $nombreGuardado);
        $stmt4->execute();
        $stmt4->close();
    }



    // Redirigir al usuario a la página de gestión de reporte de titulados (../views/reporteTitulados.php) con un mensaje de éxito
    $_SESSION['mensaje'] = "Reporte generado con éxito.";
    header('Location: ../views/reporteTitulados.php');
    $stmt->close();
    $stmt2->close();
    $conn->close();
    exit;
} else {
    // Redirigir al usuario a la página de gestión de reporte de titulados (../views/reporteTitulados.php) con un mensaje de error
    $_SESSION['mensaje'] = "Ocurrió un error al generar el reporte.";
    header('Location: ../views/reporteTitulados.php');
    $stmt->close();
    $stmt2->close();
    $conn->close();
    exit;
}
