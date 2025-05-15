<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once '../../private/conexion.php';
require_once '../vendor/autoload.php'; // Carga las dependencias de Composer

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;

// Margenes de página, 56.8 es igual a un milimetro
$marginLeft = 1420;
$marginRight = 1136;
$marginTop = 238.56;
$marginBottom = 1136;
$footerHeight = 1440;

$stmt2 = $conn->prepare("SELECT * FROM membretes_documentos");
$stmt2->execute();
$result2 = $stmt2->get_result();
$membretes = $result2->fetch_assoc();
$stmt2->close();

// Fuentes de imagenes y dimensiones, 56.8 es igual a un milimetro
$encabezadoPath = $membretes['Direccion_Encabezado_Membrete'];
//$width1 = 300; TR20250116 para el membrete de 2025
//$height1 = 36; TR20250116 para el membrete de 2025
$width1 = 460;
$height1 = 80;

$footerPath = $membretes['Direccion_Pie_Membrete'];
$width3 = 460;
$height3 = 85;

// Obtén el nombre del profesor del parámetro de la URL
if (isset($_GET['profesor']) && isset($_GET['folderPath']) && isset($_GET['profesorId']) && isset($_GET['startDate']) && isset($_GET['endDate'])) {
    $profesor = $_GET['profesor'];
    $documentPath = $_GET['folderPath'];
    $profesorId = $_GET['profesorId'];
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];

    // Crea el objeto PhpWord
    $phpWord = new PhpWord();

    $section = $phpWord->addSection(
        array(
            'marginLeft' => $marginLeft,
            'marginRight' => $marginRight,
            'marginTop' => $marginTop,
            'marginBottom' => $marginBottom,
            'footerHeight' => $footerHeight,
        )
    );

    $header = $section->addHeader();
    // Agrega la imagen a la sección
    $header->addImage($encabezadoPath, array(
        'width' => $width1,
        'height' => $height1,
        'alignment' => Jc::START
    ));

    //TR20250321 para el tipo de letra del membretado de 2025
    //Agregar los estilos para el texto normal y el texto en negrita
    //$fontStyleTimesNewRoman = array('name' => 'Times New Roman', 'size' => 12, 'color' => '000000');
    //$fontStyleTimesNewRomanBold = array('name' => 'Times New Roman', 'size' => 12, 'color' => '000000', 'bold' => true);
    //$fontStyleTimesNewRomanTiny = array('name' => 'Times New Roman', 'size' => 8, 'color' => '000000');
    //$fontStyleTimesNewRoman = array('name' => 'Times New Roman', 'size' => 12, 'bold' => false, 'italic' => false, 'color' => '000000'); //JH20250514

    // Agregar los estilos para el texto normal y el texto en negrita
    $fontStyleNotoSans = array('name' => 'Noto Sans', 'size' => 10, 'color' => '000000');
    $fontStyleNotoSansBold = array('name' => 'Noto Sans', 'size' => 10, 'color' => '000000', 'bold' => true);
    $fontStyleNotoSansTiny = array('name' => 'Noto Sans', 'size' => 8, 'color' => '000000');


    // Crear un nuevo objeto TextRun para combinar el texto normal y el texto en negrita
    $textRun = $section->addTextRun();

    $paragraphStyle = array('alignment' => Jc::BOTH); // Esto alineará el texto justificado

    // Agregar el texto normal y en negrita al objeto TextRun
    $textRun->addTextBreak();  // Salto de línea
    $textRun->addTextBreak();  // Salto de línea
    $textRun->addText('A quien corresponda:', $fontStyleNotoSans);
    $textRun->addTextBreak();  // Salto de línea
    $textRun->addTextBreak();  // Salto de línea
    $textRun->addTextBreak();  // Salto de línea
    $textRun->addTextBreak();  // Salto de línea
    $textRun->addText('Por este medio hago constar que el/la C. ', $fontStyleNotoSans, $paragraphStyle);
    $textRun->addText($profesor, $fontStyleNotoSansBold, $paragraphStyle);
    $textRun->addText(' catedrático/a de este Instituto y de acuerdo a los registros que guarda la División de Estudios Profesionales, participó en los siguientes ', $fontStyleTimesNewRoman, $paragraphStyle);
    $textRun->addText('Actos Recepcionales y/o Exámenes Profesionales ', $fontStyleNotoSansBold, $paragraphStyle);
    $textRun->addText('durante el periodo comprendido del ', $fontStyleNotoSans, $paragraphStyle);

    //Array de días hasta el treinta
    $dias = array(
        1 => 'un',
        2 => 'dos',
        3 => 'tres',
        4 => 'cuatro',
        5 => 'cinco',
        6 => 'seis',
        7 => 'siete',
        8 => 'ocho',
        9 => 'nueve',
        10 => 'diez',
        11 => 'once',
        12 => 'doce',
        13 => 'trece',
        14 => 'catorce',
        15 => 'quince',
        16 => 'dieciséis',
        17 => 'diecisiete',
        18 => 'dieciocho',
        19 => 'diecinueve',
        20 => 'veinte',
        21 => 'veintiún',
        22 => 'veintidós',
        23 => 'veintitrés',
        24 => 'veinticuatro',
        25 => 'veinticinco',
        26 => 'veintiséis',
        27 => 'veintisiete',
        28 => 'veintiocho',
        29 => 'veintinueve',
        30 => 'treinta',
        31 => 'treinta y un'
    );

    $meses = array(
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    );

    // Función para formatear fechas en español
    function formatFechaEspañol($fecha)
    {

        $meses = array(
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        );

        $diasSemana = array(
            'domingo',
            'lunes',
            'martes',
            'miércoles',
            'jueves',
            'viernes',
            'sábado'
        );

        $timestamp = strtotime($fecha);

        $dia = date('j', $timestamp);
        $mes = date('n', $timestamp);
        $año = date('Y', $timestamp);
        $diaSemana = date('w', $timestamp);

        $fechaFormateada = "$dia de " . $meses[$mes] . " del $año";

        return $fechaFormateada;
    }

    function generarAsteriscos($cantidad)
    {
        // Inicializar una cadena vacía para almacenar los asteriscos
        $cadena = "";

        // Usar un bucle para añadir asteriscos a la cadena
        for ($i = 0; $i < $cantidad; $i++) {
            $cadena .= "*";
        }

        // Devolver la cadena de asteriscos
        return $cadena;
    }

    // Crear objetos DateTime a partir de las fechas proporcionadas
    $startDateFormatted = formatFechaEspañol($startDate);
    $endDateFormatted = formatFechaEspañol($endDate);

    $textRun->addText("$startDateFormatted al $endDateFormatted ", $fontStyleNotoSansBold, $paragraphStyle);
    $textRun->addText('según desglose presentado a continuación: ', $fontStyleNotoSans, $paragraphStyle);

    // Estilos PHPWord
    $tableStyle = [
        'borderSize' => 1,
        'borderColor' => '000000',
        'cellMargin' => 80,
        'width' => '100%',
    ];

    // Crear una tabla en el documento
    $table = $section->addTable($tableStyle);

    $table->addRow();
    $table->addCell(2000, array('bgColor' => 'D9D9D9'))->addText("PRODUCTO", $fontStyleNotoSansBold); // Agregar el encabezado "Producto" en la primera columna
    $table->addCell(2000, array('bgColor' => 'D9D9D9'))->addText("CANTIDAD", $fontStyleNotoSansBold); // Agregar el encabezado "Cantidad" en la segunda columna

    // Ejecutar la consulta SQL para obtener los proyectos del profesor
    $stmt = $conn->prepare("SELECT pt.Tipo_Producto_Titulacion, 
    COUNT(*) AS Cantidad_Involucramientos 
    FROM asignacion_sinodales a 
    JOIN profesor pr ON a.Fk_Sinodal_1 = ? 
    OR a.Fk_Sinodal_2 = ? 
    OR a.Fk_Sinodal_3 = ? 
    OR a.Fk_Sinodal_4 = ? 
    JOIN proyecto p ON a.Fk_Proyecto_Sinodales = p.Id_Proyecto 
    JOIN egresado e ON p.Id_Proyecto = e.Fk_Proyecto_Egresado 
    JOIN producto_titulacion pt ON e.Fk_Tipo_Titulacion_Egresado = pt.Id_Titulacion 
    WHERE pr.Id_Profesor = ?
    AND e.Fecha_Hora_Ceremonia_Egresado >= ? AND e.Fecha_Hora_Ceremonia_Egresado <= ? 
    GROUP BY pt.Tipo_Producto_Titulacion;");
    $stmt->bind_param("iiiiiss", $profesorId, $profesorId, $profesorId, $profesorId, $profesorId, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_Involucramientos = 0;
    // Agregar filas a la tabla con los datos de los proyectos
    while ($row = $result->fetch_assoc()) {
        $table->addRow();
        $table->addCell(8000)->addText($row['Tipo_Producto_Titulacion'], $fontStyleNotoSans);
        $table->addCell(2000)->addText($row['Cantidad_Involucramientos'], $fontStyleNotoSans);
        $total_Involucramientos += $row['Cantidad_Involucramientos'];
    }
    $table->addRow();
    $table->addCell(8000, array('bgColor' => 'D9D9D9'))->addText('TOTAL', $fontStyleNotoSansBold);
    $table->addCell(2000, array('bgColor' => 'D9D9D9'))->addText('*' . $total_Involucramientos . '*', $fontStyleNotoSansBold);

    $stmt->close();

    $section->addTextBreak();  // Salto de línea

    $fechaActual = date('j \d\e F \d\e Y');
    $fechaActual = str_replace(
        array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
        array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'),
        $fechaActual
    );

    $dia_actual = date('j');  // 'j' devuelve el día del mes sin ceros iniciales (1 a 31)
    $mes_actual = date('n');  // 'n' devuelve el mes actual sin ceros iniciales (1 a 12)

    $section->addText('Se extiende la presente constancia para los fines que a el/la interesado(a) convengan en Ciudad Juárez, Chihuahua a los ' . $dias[$dia_actual] . ' días del mes de ' . $meses[$mes_actual] . '.', $fontStyleNotoSans, $paragraphStyle);

    $section->addText('');
    $section->addText('A T E N T A M E N T E', $fontStyleNotoSansBold);
    $section->addText('Excelencia en Educación Tecnológica®', $fontStyleNotoSansBold);
    $section->addText('');

    $departamento_Division_Estudios_Profesionales = 'División de Estudios Profesionales';

    $stmt2 = $conn->prepare("SELECT * FROM departamento 
    JOIN profesor ON departamento.Fk_Jefe_Departamento = profesor.Id_Profesor 
    WHERE departamento.Nombre_Departamento = ?");
    $stmt2->bind_param("s", $departamento_Division_Estudios_Profesionales);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $row2 = $result2->fetch_assoc();

    $section->addText($row2['Nombre_Profesor'], $fontStyleNotoSansBold);
    $section->addText('Jefe(a) de la División de Estudios Profesionales', $fontStyleNotoSansBold);
    $section->addText('');
    $section->addText('c.c.p. División de Estudios Profesionales', $fontStyleNotoSansTiny, $fontStyleNotoSans);
    $section->addText('YDA/ tgre*', $fontStyleNotoSansTiny);

    $stmt2->close();




    // Agrega un pie de página a la sección
    $footer = $section->addFooter();

    $footer->addImage(
        $footerPath,
        array(
            'width'         => $width3,
            'height'        => $height3,
            'wrappingStyle' => 'behind',
            'posHorizontal' => 'left',
            'posVertical'   => 'top',
            'positioning'   => 'absolute',

        )
    );

    // Genera el documento Word
    $filename = 'Constancia de sinodalia ' . $profesor . ' ' . $startDate . ' al ' . $endDate . '.docx';
    $fullPath = $documentPath . $filename;

    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($fullPath);

    $stmt3 = $conn->prepare("SELECT * FROM constancias_sinodalia 
    WHERE Fecha_Inicio_Constancia_Sinodalia = ? 
    AND Fecha_Cierre_Constancia_Sinodalia = ? 
    AND Fk_Profesor_Constancia_Sinodalia = ?");
    $stmt3->bind_param("ssi", $startDate, $endDate, $profesorId);
    $stmt3->execute();
    $result2 = $stmt3->get_result();

    if ($result2->num_rows > 0) {
        $stmt5 = $conn->prepare("UPDATE constancias_sinodalia 
        SET Fecha_Creacion_Constancia_Sinodalia = NOW(), 
        Direccion_Archivo_Constancia_Sinodalia = ? 
        WHERE Fecha_Inicio_Constancia_Sinodalia = ? 
        AND Fecha_Cierre_Constancia_Sinodalia = ?
        AND Fk_Profesor_Constancia_Sinodalia = ?");
        $stmt5->bind_param("sssi", $fullPath, $startDate, $endDate, $profesorId);
        $stmt5->execute();
        $stmt5->close();
    } else {
        // Guardar el nombre del archivo en la base de datos
        $stmt4 = $conn->prepare("INSERT INTO constancias_sinodalia 
    (Fecha_Creacion_Constancia_Sinodalia, 
    Fecha_Inicio_Constancia_Sinodalia, 
    Fecha_Cierre_Constancia_Sinodalia,
    Fk_Profesor_Constancia_Sinodalia, 
    Direccion_Archivo_Constancia_Sinodalia) 
    VALUES (NOW(), ?, ?, ?, ?)");
        $stmt4->bind_param("ssis", $startDate, $endDate, $profesorId, $fullPath);
        $stmt4->execute();
        $stmt4->close();
    }

    // Cierra la conexión
    $conn->close();

    $_SESSION['mensaje'] = "Reporte generado con éxito.";
    header('Location: ../views/reporteConstanciaSinodalia.php');
    exit;
} else {
    $_SESSION['mensaje'] = "Ocurrió un error al generar el reporte.";
    header('Location: ../views/reporteConstanciaSinodalia.php');
    exit;
}
