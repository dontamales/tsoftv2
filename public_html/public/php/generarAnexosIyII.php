<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once '../../private/conexion.php';
require_once '../vendor/autoload.php';
// Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer JH20250626
// require_once 'enviarCorreoFunciones.php';
require_once 'enviarCorreos.php';
require_once 'actualizarEstatusFuncionesFormatoB.php';

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

use Ramsey\Uuid\Uuid;


try {
    // Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer JH20250626
    // if (verificarLimiteCorreo($conn) >= 100) {
    //     echo json_encode(['success' => false, 'message' => 'No se ha generado ningún archivo ni se ha enviado ningún correo, se ha alcanzado el límite de 100 correos electrónicos diarios enviados, vuelva a intentarlo mañana.']);
    //     exit;
    // }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al verificar el límite de correos electrónicos enviados.']);
    exit;
}

try {
    if (isset($_GET['numControl'])) {
        $numControl = $_GET['numControl'];

        // Realiza una consulta SQL para obtener los datos del Anexo I.
        $stmt = $conn->prepare("SELECT e.Fk_Carrera_Egresado, c.Nombre_Carrera AS Carrera_Egresado, 
                        c.Fk_Departamento_Carrera, c.Fk_Jefe_Carrera, 
                        c_dep.Nombre_Departamento AS Nombre_Departamento, c_dep.Correo_Proyecto_Departamento,
                        p.Nombre_Proyecto, 
                        pr.Nombre_Profesor, 
                        e.Numero_Equipo_Egresados, 
                        e.NumeroControl_Equipo_Egresado1, 
                        e.Nombre_Equipo_Egresado1, 
                        c1.Nombre_Carrera AS Carrera1, 
                        e.NumeroControl_Equipo_Egresado2, 
                        e.Nombre_Equipo_Egresado2, 
                        c2.Nombre_Carrera AS Carrera2, 
                        pt.Tipo_Producto_Titulacion,
                        e.Fk_Usuario_Egresado, 
                        u1.Nombres_Usuario AS Nombres_Egresado, 
                        u1.Apellidos_Usuario AS Apellidos_Egresado,
                        e.Num_Control,
                        e.Fk_Tipo_Titulacion_Egresado,
                        e.Fk_Direccion_Egresado, 
                        d.Calle_Direccion, 
                        d.Colonia_Direccion, 
                        d.Num_Exterior_Direccion, 
                        d.Codigo_Postal_Direccion,
                        u2.Correo_Usuario AS Correo_Egresado, 
                        e.Telefono_Egresado,
                        jefe.Nombre_Profesor AS Nombre_Jefe_Carrera,
                        jefeDepartamento.Nombre_Profesor AS Nombre_Jefe_Departamento
                        FROM egresado e
                        LEFT JOIN carrera c ON e.Fk_Carrera_Egresado = c.Id_Carrera
                        LEFT JOIN departamento c_dep ON c.Fk_Departamento_Carrera = c_dep.Id_Departamento
                        LEFT JOIN profesor c_jefe ON c.Fk_Jefe_Carrera = c_jefe.Id_Profesor
                        LEFT JOIN proyecto p ON e.Fk_Proyecto_Egresado = p.Id_Proyecto
                        LEFT JOIN profesor pr ON e.Fk_Asesor_Interno_Egresado = pr.Id_Profesor
                        LEFT JOIN carrera c1 ON e.Fk_Carrera_Equipo_Egresado1 = c1.Id_Carrera
                        LEFT JOIN carrera c2 ON e.Fk_Carrera_Equipo_Egresado2 = c2.Id_Carrera
                        LEFT JOIN producto_titulacion pt ON e.Fk_Tipo_Titulacion_Egresado = pt.Id_Titulacion
                        LEFT JOIN usuario u1 ON e.Fk_Usuario_Egresado = u1.Id_Usuario
                        LEFT JOIN direccion d ON e.Fk_Direccion_Egresado = d.Id_Direccion
                        LEFT JOIN usuario u2 ON e.Fk_Usuario_Egresado = u2.Id_Usuario
                        LEFT JOIN profesor jefe ON c.Fk_Jefe_Carrera = jefe.Id_Profesor
                        LEFT JOIN profesor jefeDepartamento ON c_dep.Fk_Jefe_Departamento = jefeDepartamento.Id_Profesor 
                        WHERE e.Num_Control = ?");



        $stmt->bind_param('s', $numControl);
        $stmt->execute();
        $result = $stmt->get_result();

        $stmt2 = $conn->prepare("SELECT * FROM membretes_documentos");
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $membretes = $result2->fetch_assoc();
        $stmt2->close();



        // Función para formatear la fecha en español
        function formatFechaEnEspanol($date)
        {
            $meses = array(
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            );

            $dia = date('d', strtotime($date));
            $mes = $meses[(date('m', strtotime($date)) - 1)];
            $anio = date('Y', strtotime($date));

            return $dia . ' de ' . $mes . ' del ' . $anio;
        }

        //Margenes de página, 56.8 es igual a un milimetro
        $marginLeft = 1420;
        $marginRight = 1136;
        $marginTop = 238.56;
        $marginBottom = 1136;
        $footerHeight = 1440;

        // Fuentes de imagenes y dimensiones, 56.8 es igual a un milimetro
        $encabezadoPath = $membretes['Direccion_Encabezado_Membrete'];
        //$width1 = 300; TR20250113 para el membrete de 2025
        $width1 = 460;
        //$height1 = 36; TR20250113 para el membrete de 2025
        $height1 = 80;
        $firmaCoordinadorPath = $membretes['Firma_Membrete'];
        $width2 = 100;
        $height2 = 50;
        $footerPath = $membretes['Direccion_Pie_Membrete'];
        $width3 = 460;
        //$height1 = 85; TR20250113 para el membrete de 2025
        $height3 = 70;

        //Estilos de párrafos
        // Estilo para centrar el texto
        $paragraphStyle = new \PhpOffice\PhpWord\Style\Paragraph();
        $paragraphStyle->setAlignment('center');
        // Estilo para alinear el texto a la derecha
        $paragraphStyleRight = new \PhpOffice\PhpWord\Style\Paragraph();
        $paragraphStyleRight->setAlignment('right');
        // Estilo de fuente para aplicar negrita
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setBold(true);
        // Estilo de fuente Arial 16 negrita
        $fontStyleArial16 = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleArial16->setBold(true);
        $fontStyleArial16->setName('Arial');
        $fontStyleArial16->setSize(16);
        // Estilo de fuente Arial 14 negrita
        $fontStyleArial14 = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleArial14->setBold(true);
        $fontStyleArial14->setName('Arial');
        $fontStyleArial14->setSize(14);
        // Estilo de fuente Arial 12
        $fontStyleArial12 = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleArial12->setName('Arial');
        $fontStyleArial12->setSize(12);//12
        // Estilo de fuente Arial 12 negrita
        $fontStyleArial12Bold = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleArial12Bold->setBold(true);
        $fontStyleArial12Bold->setName('Arial');
        $fontStyleArial12Bold->setSize(12);//12
        // Estilo de fuente Arial 12 cursiva y negrita
        $fontStyleArial12ItalicBold = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleArial12ItalicBold->setItalic(true);
        $fontStyleArial12ItalicBold->setBold(true);
        $fontStyleArial12ItalicBold->setName('Arial');
        $fontStyleArial12ItalicBold->setSize(12);//12
        // Estilo de fuente para aplicar subrayado
        $fontStyleArial12BoldUnderlined = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleArial12BoldUnderlined->setUnderline('single');
        $fontStyleArial12BoldUnderlined->setBold(true);
        $fontStyleArial12BoldUnderlined->setName('Arial');
        $fontStyleArial12BoldUnderlined->setSize(12);//12
        //Estilo de fuente Arial 11 negrita e italica para para los proyectos
        $fontStyleArial11ItalicBold = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleArial11ItalicBold->setName('Arial');
        $fontStyleArial11ItalicBold->setSize(9);//11
        $fontStyleArial11ItalicBold->setBold(true);
        $fontStyleArial11ItalicBold->setItalic(true);
        //Estilo de fuente Arial 11 negrita e italica para para los proyectos
        $fontStyleArial7 = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleArial7->setName('Arial');
        $fontStyleArial7->setSize(7); //7
        // Estilo para la fuente "Montserrat Medium" con tamaño "9"
        $fontStyleMontserratMedium = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleMontserratMedium->setName('Montserrat Medium');
        $fontStyleMontserratMedium->setSize(9);
        // Estilo para la fuente "Montserrat Medium Date" con tamaño "9"
        $fontStyleMontserratMediumDate = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleMontserratMediumDate->setName('Montserrat Medium');
        $fontStyleMontserratMediumDate->setSize(9);
        $fontStyleMontserratMediumDate->setBgColor('000000');
        $fontStyleMontserratMediumDate->setColor('FFFFFF');
        // Estilo para la fuente "Montserrat ExtraBold" con tamaño "9"
        $fontStyleMontserratExtraBold = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleMontserratExtraBold->setName('Montserrat ExtraBold');
        $fontStyleMontserratExtraBold->setSize(10);
        // Estilo para la fuente "Courirer New Medium" con tamaño "9"
        $fontStyleCourirerNewSmall = new \PhpOffice\PhpWord\Style\Font();
        $fontStyleCourirerNewSmall->setName('Courier New');
        $fontStyleCourirerNewSmall->setSize(9);
        /// Estilo para el párrafo con alineación justificada (ajuste del texto en todo el ancho)
        $paragraphStyleJustify = new \PhpOffice\PhpWord\Style\Paragraph();
        $paragraphStyleJustify->setAlignment('both');

        if ($result->num_rows === 1) {
            $egresadoData = $result->fetch_assoc();
            if ($egresadoData['Numero_Equipo_Egresados'] == 1) {
                $egresadoData['NumeroControl_Equipo_Egresado1'] = '';
                $egresadoData['Nombre_Equipo_Egresado1'] = '';
                $egresadoData['Carrera1'] = '';
                $egresadoData['NumeroControl_Equipo_Egresado2'] = '';
                $egresadoData['Nombre_Equipo_Egresado2'] = '';
                $egresadoData['Carrera2'] = '';
            } else if ($egresadoData['Numero_Equipo_Egresados'] == 2) {
                $egresadoData['NumeroControl_Equipo_Egresado2'] = '';
                $egresadoData['Nombre_Equipo_Egresado2'] = '';
                $egresadoData['Carrera2'] = '';
            }

            $correo_Entrega_Proyecto = trim($egresadoData['Correo_Proyecto_Departamento']);

            // Genera el token
            $uuid = Uuid::uuid4();
            $token = $uuid->toString();

            /**
             * Genera un token y actualiza la base de datos.
             * 
             * @param mysqli $conn Conexión a la base de datos.
             * @param string $idEgresado ID del estudiante.
             * @param string $rutaArchivo Ruta del archivo.
             * @param string $token Token generado.
             * @return void
             */

            function generarTokenYActulizarBD($conn, $idEgresado, $rutaArchivo, $token)
            {
                // Iniciar transacción
                $conn->begin_transaction();

                try {
                    // Verificar si ya existe un registro para el estudiante
                    $query = "SELECT Id_Anexo_I_II FROM anexo_i_ii WHERE Fk_Egresado_Anexo_I_II = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $idEgresado);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();

                    if ($row) {
                        // Si existe, actualizamos el registro
                        $updateStmt  = $conn->prepare("UPDATE anexo_i_ii SET Direccion_De_Archivo_Anexo_I_II = ?, Token_Anexo_I_II = ? WHERE Fk_Egresado_Anexo_I_II = ?");
                        $updateStmt->bind_param("sss", $rutaArchivo, $token, $idEgresado);
                        $updateStmt->execute();
                    } else {
                        // Si no existe, insertamos un nuevo registro
                        $insertStmt  = $conn->prepare("INSERT INTO anexo_i_ii (Fk_Egresado_Anexo_I_II, Direccion_De_Archivo_Anexo_I_II, Token_Anexo_I_II) VALUES (?, ?, ?)");
                        $insertStmt->bind_param("sss", $idEgresado, $rutaArchivo, $token);
                        $insertStmt->execute();
                    }
                    // Confirmar transacción
                    $conn->commit();
                } catch (Exception $e) {
                    // Revertir transacción en caso de error
                    $conn->rollback();
                    // Lanzar la excepción para un manejo de errores más extenso
                    throw $e;
                } finally {
                    // Cerrar declaración
                    $stmt->close();
                }
            }

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

            if (
                $egresadoData['Fk_Tipo_Titulacion_Egresado'] >= 12
            ) {

                // Aquí empiezas a crear el contenido del documento de Anexo I en Word utilizando la librería PHPWord.
                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Formatear la fecha en español
                $fechaEnEspanol = formatFechaEnEspanol(date('Y-m-d'));

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
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
                ));

                $section->addText('');
                $section->addText("Instituto Tecnológico de Ciudad Juárez", $fontStyleCourirerNewSmall, $paragraphStyleRight);
                $section->addText('Anexo I', $fontStyleArial16, $paragraphStyle);
                $section->addText('Registro de proyecto', $fontStyleArial16, $paragraphStyle);
                $textRun = $section->addTextRun();
                $textRun->addText("Departamento de: ", $fontStyleArial12);
                $textRun->addText($egresadoData['Nombre_Departamento'], $fontStyleArial12BoldUnderlined);
                $textRun2 = $section->addTextRun();
                $textRun2->addText("Lugar:", $fontStyleArial12);
                $textRun2->addText(" INSTITUTO TECNOLÓGICO DE CD. JUÁREZ", $fontStyleArial12BoldUnderlined);
                $textRun2->addText(" Fecha: ", $fontStyleArial12);
                $textRun2->addText($fechaEnEspanol, $fontStyleArial12BoldUnderlined);

                // Estilos PHPWord
                $tableStyle = [
                    'borderSize' => 1,
                    'borderColor' => '000000',
                    'cellMargin' => 80,
                    'width' => '100%',
                ];

                // Creamos la primera tabla 
                $table = $section->addTable($tableStyle);

                // Agregar la primera fila y celdas
                $table->addRow();
                $table->addCell(3000, array('bgColor' => 'D9D9D9'))->addText("Nombre del proyecto:", $fontStyleArial12Bold);
                $table->addCell(8000)->addText($egresadoData['Nombre_Proyecto'], $fontStyleArial11ItalicBold);

                // Agregar la segunda fila y celdas
                $table->addRow();
                $table->addCell(3000, array('bgColor' => 'D9D9D9'))->addText("Nombre del asesor:", $fontStyleArial12Bold);
                $table->addCell(8000)->addText($egresadoData['Nombre_Profesor'], $fontStyleArial12ItalicBold);

                // Agregar la tercera fila y celdas
                $table->addRow();
                $table->addCell(3000, array('bgColor' => 'D9D9D9'))->addText("Número de estudiantes:", $fontStyleArial12Bold);
                $table->addCell(8000)->addText($egresadoData['Numero_Equipo_Egresados'], $fontStyleArial12ItalicBold);

                $section->addText("Datos del (de los) estudiante(s):", $fontStyleArial14, $paragraphStyle);

                // Creamos la segunda tabla
                $table2 = $section->addTable($tableStyle);

                // Agregar la segunda fila y celdas
                $table2->addRow();
                $table2->addCell(2000)->addText("Nombre:", $fontStyleArial12);
                $table2->addCell(8000)->addText($egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'], $fontStyleArial12ItalicBold);

                // Agregar la tercera fila y celdas
                $table2->addRow();
                $table2->addCell(2000)->addText("Carrera:", $fontStyleArial12);
                $table2->addCell(8000)->addText($egresadoData['Carrera_Egresado'], $fontStyleArial12ItalicBold);

                // Agregar la primera fila y celdas
                $table2->addRow();
                $table2->addCell(2000)->addText("No. de control:", $fontStyleArial12, $fontStyleArial12);
                $table2->addCell(8000)->addText($egresadoData['Num_Control'], $fontStyleArial12ItalicBold);

                // Agregar la quinta fila y celdas
                $table2->addRow();
                $table2->addCell(2000)->addText("Nombre:", $fontStyleArial12);
                $table2->addCell(8000, array('bgColor' => 'D9D9D9'))->addText($egresadoData['Nombre_Equipo_Egresado1'], $fontStyleArial12ItalicBold);

                // Agregar la sexta fila y celdas
                $table2->addRow();
                $table2->addCell(2000)->addText("Carrera:", $fontStyleArial12);
                $table2->addCell(8000, array('bgColor' => 'D9D9D9'))->addText($egresadoData['Carrera1'], $fontStyleArial12ItalicBold);

                // Agregar la cuarta fila y celdas
                $table2->addRow();
                $table2->addCell(2000)->addText("No. de control:", $fontStyleArial12);
                $table2->addCell(8000, array('bgColor' => 'D9D9D9'))->addText($egresadoData['NumeroControl_Equipo_Egresado1'], $fontStyleArial12ItalicBold);

                // Agregar la octava fila y celdas
                $table2->addRow();
                $table2->addCell(2000)->addText("Nombre:", $fontStyleArial12);
                $table2->addCell(8000)->addText($egresadoData['Nombre_Equipo_Egresado2'], $fontStyleArial12ItalicBold);

                // Agregar la novena fila y celdas
                $table2->addRow();
                $table2->addCell(2000)->addText("Carrera:", $fontStyleArial12);
                $table2->addCell(8000)->addText($egresadoData['Carrera2'], $fontStyleArial12ItalicBold);
                $section->addText("");

                // Agregar la septima fila y celdas
                $table2->addRow();
                $table2->addCell(2000)->addText("No. de control:", $fontStyleArial12);
                $table2->addCell(8000)->addText($egresadoData['NumeroControl_Equipo_Egresado2'], $fontStyleArial12ItalicBold);

                // Creamos la cuarta tabla
                $table3 = $section->addTable($tableStyle);

                // Agregar la primera fila y celdas
                $table3->addRow();
                $table3->addCell(2000)->addText("Observaciones: ", $fontStyleArial14);
                $table3->addCell(8000)->addText($egresadoData['Tipo_Producto_Titulacion'], $fontStyleArial12Bold);

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

                // Aquí agregamos una nueva sección (segunda hoja) al documento
                $section2 = $phpWord->addSection(
                    array(
                        'marginLeft' => $marginLeft,
                        'marginRight' => $marginRight,
                        'marginTop' => $marginTop,
                        'marginBottom' => $marginBottom,
                        'footerHeight' => $footerHeight,
                    )
                );
                $section2->addText('Anexo II', $fontStyleArial16, $paragraphStyle);
                $section2->addText('Solicitud del estudiante', $fontStyleArial16, $paragraphStyle);
                $section2->addText("Cd. Juárez, Chih. " . $fechaEnEspanol, $fontStyleArial12Bold, $paragraphStyleRight);
                //$section2->addText("María Yolanda Frausto Villegas", $fontStyleArial12Bold);  SCS 08-06-2025 Cambio de la maestra Yolanda(QEPD) a La maestra Yadira
                $section2->addText("Yadira Dozal Assmar", $fontStyleArial12Bold);
                $section2->addText("Jefe de la División de Estudios Profesionales", $fontStyleArial12Bold);
                $section2->addText("P r e s e n t e.", $fontStyleArial12Bold);
                $section2->addText('At’n. Tania Guadalupe Ruíz Escobar', $fontStyleArial12Bold, $paragraphStyleRight);
                $section2->addText("Coordinador de Apoyo a Titulación", $fontStyleArial12Bold, $paragraphStyleRight);
                $section2->addText("Por medio del presente, solicito autorización para iniciar Trámites de Titulación Integral:", $fontStyleArial12);

                // Agregar la tabla para mostrar la información
                $table5 = $section2->addTable($tableStyle);

                // Agregar la primera fila y celdas
                $table5->addRow();
                $table5->addCell(4000)->addText("a) Nombre del estudiante:", $fontStyleArial12Bold);
                $table5->addCell(8000)->addText($egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'], $fontStyleArial12ItalicBold);

                // Agregar la segunda fila y celdas
                $table5->addRow();
                $table5->addCell(4000)->addText("b) Carrera:", $fontStyleArial12Bold);
                $table5->addCell(8000)->addText($egresadoData['Carrera_Egresado'], $fontStyleArial12ItalicBold);

                // Agregar la tercera fila y celdas
                $table5->addRow();
                $table5->addCell(4000)->addText("c) No. de control:", $fontStyleArial12Bold);
                $table5->addCell(8000)->addText($egresadoData['Num_Control'], $fontStyleArial12ItalicBold);

                // Agregar la cuarta fila y celdas
                $table5->addRow();
                $table5->addCell(4000)->addText("d) Nombre del proyecto:", $fontStyleArial12Bold);
                $table5->addCell(8000)->addText($egresadoData['Nombre_Proyecto'], $fontStyleArial11ItalicBold);

                // Agregar la quinta fila y celdas
                $table5->addRow();
                $table5->addCell(4000)->addText("e) Producto:", $fontStyleArial12Bold);
                $table5->addCell(8000)->addText($egresadoData['Tipo_Producto_Titulacion'], $fontStyleArial12ItalicBold);

                $section2->addText("En espera del dictamen correspondiente, quedo a sus órdenes.", $fontStyleArial12);
                $section2->addText("A T E N T A M E N T E:", $fontStyleArial12Bold);
                $section2->addText("");
                $section2->addText($egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'], $fontStyleArial12ItalicBold);
                $section2->addText("NOMBRE Y FIRMA DEL SOLICITANTE", $fontStyleArial12Bold);

                // Agregar la tabla para mostrar la información
                $table6 = $section2->addTable($tableStyle);

                // Agregar la sexta fila y celdas para la dirección
                $table6->addRow();
                $table6->addCell(3000)->addText("Dirección:", $fontStyleArial12Bold);
                $table6->addCell(7500)->addText($egresadoData['Calle_Direccion'] . ", " . $egresadoData['Colonia_Direccion'] . ", " . $egresadoData['Num_Exterior_Direccion'] . ", " . $egresadoData['Codigo_Postal_Direccion'], $fontStyleArial12ItalicBold, $paragraphStyle);

                // Agregar la séptima fila y celdas para el teléfono particular
                $table6->addRow();
                $table6->addCell(3000)->addText("Teléfono particular (o de contacto):", $fontStyleArial12Bold);
                $table6->addCell(7500)->addText($egresadoData['Telefono_Egresado'], $fontStyleArial12ItalicBold, $paragraphStyle);

                // Agregar la octava fila y celdas para el correo electrónico
                $table6->addRow();
                $table6->addCell(3000)->addText("Correo electrónico del estudiante:", $fontStyleArial12Bold);
                $table6->addCell(7500)->addText($egresadoData['Correo_Egresado'], $fontStyleArial12ItalicBold, $paragraphStyle);
                $section2->addText("Rev. 1", $fontStyleArial7, $paragraphStyle);


                // Agrega un pie de página a la sección
                $footer = $section2->addFooter();

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

                // Guardar el archivo de Word en la ruta especificada.
                $directorioArchivo = "../assets/archivos/" . $periodo_Completo . "/sustentantes" . "/" . $egresadoData['Num_Control'] . "/ANEXOS I & II" . "/";
                if (!file_exists($directorioArchivo)) {
                    mkdir($directorioArchivo, 0777, true);
                }
                $rutaArchivo = $directorioArchivo . "{$numControl} ANEXOS I & II.docx";
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($rutaArchivo);

                $count = 0;

                do {
                    $response = enviarCorreoArchivo(
                        $conn,
                        $correo_Entrega_Proyecto,
                        'Departamento Académico',
                        "ANEXO I y II de " . $egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'],
                        'ANEXO I y II DE ' . $egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'] . ".",
                        '<strong>ANEXO I y II DE ' . $egresadoData['Nombres_Egresado'] . ' ' . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'] . '.</strong>',
                        $rutaArchivo,
                        date("Y.m.d") . "_{$numControl}.docx"
                    );

                    sleep(2);
                    if (in_array($response->statusCode, [200, 201, 202])) { // Cambio de metodo a propiedad $response->statusCode() JH20250626
                        generarTokenYActulizarBD($conn, $egresadoData['Num_Control'], $rutaArchivo, $token);
                        if(!$conn){
                            logMessage("Conexión a la base de datos no disponible para actualizar estatus de " . $egresadoData['Num_Control']);
                        } else if (!$egresadoData['Num_Control']) {
                            logMessage("Fk_Usuario_Egresado no disponible para " . $egresadoData['Num_Control']);
                        } else {
                            actualizarEstatusFormatoB($conn, $egresadoData['Num_Control'], 5);
                        }
                        echo json_encode(['success' => true, 'message' => 'Archivo ANEXO I y II creado exitosamente para ' . $egresadoData['Num_Control'] . ' y correo electrónico enviado correctamente a departamento académico.']);
                        exit;
                    } else {
                        $count++;
                        if ($count == 3) {
                            echo json_encode(['success' => false, 'message' => 'Error al enviar el correo electrónico a departamento académico correspondiente, por favor verifique la página de envío de anexos I y II sin éxito.']);
                            exit;
                        }
                        sleep(2);
                    }
                } while ($count < 3);
            } elseif (
                $egresadoData['Fk_Tipo_Titulacion_Egresado'] == 3 ||
                $egresadoData['Fk_Tipo_Titulacion_Egresado'] == 4 ||
                $egresadoData['Fk_Tipo_Titulacion_Egresado'] == 5 ||
                $egresadoData['Fk_Tipo_Titulacion_Egresado'] == 6 ||
                $egresadoData['Fk_Tipo_Titulacion_Egresado'] == 7 ||
                $egresadoData['Fk_Tipo_Titulacion_Egresado'] == 9
            ) {

                //ITCJ 2023 SINODALES I, II, III, IV, V, VII, X


                // Aquí empiezas a crear el contenido del documento correspondiente a SINODALES I, II, III, IV, V, VII, X
                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                //Modificaciones de fuente con respecto al documento
                $fontStyle->setName('Montserrat Medium'); // Nombre de la fuente
                $fontStyle->setSize(9); // Tamaño de la fuente

                // Formatear la fecha en español
                $fechaEnEspanol = formatFechaEnEspanol(date('Y-m-d'));

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
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
                ));

                $section->addText('Instituto Tecnológico de Ciudad Juárez', $fontStyleCourirerNewSmall, $paragraphStyleRight);
                $section->addText('');
                $textRun12 = $section->addTextRun($paragraphStyleRight);
                $textRun12->addText('Ciudad Juárez, Chihuahua, ', $fontStyleMontserratMedium);
                $textRun12->addText($fechaEnEspanol, $fontStyleMontserratMediumDate);
                $section->addText('');
                $section->addText('');
                $section->addText('' . $egresadoData['Nombre_Jefe_Departamento'], $fontStyleMontserratExtraBold);
                $section->addText('Jefe del departamento de ' . $egresadoData['Nombre_Departamento'], $fontStyleMontserratExtraBold);
                $section->addText('PRESENTE', $fontStyleMontserratExtraBold);
                $section->addText('');
                $section->addText('');
                // Agregar los estilos para el texto normal y el texto en negrita
                $fontStyleMontserratMedium = array('name' => 'Montserrat Medium', 'size' => 10, 'color' => '000000');
                $fontStyleMontserratBold = array('name' => 'Montserrat', 'size' => 10, 'color' => '000000', 'bold' => true);

                // Crear un nuevo objeto TextRun para combinar el texto normal y el texto en negrita
                $textRun = $section->addTextRun($paragraphStyleJustify);

                // Agregar el texto normal y en negrita al objeto TextRun
                $textRun->addText('Por este medio solicito su amable colaboración para la revisión y asignación de sinodales que le den seguimiento al desarrollo del trabajo titulado ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Nombre_Proyecto'], $fontStyleMontserratBold);
                $textRun->addText(', que presenta el (la) C. ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Nombres_Egresado'] . ' ' . $egresadoData['Apellidos_Egresado'], $fontStyleMontserratBold);
                $textRun->addText(', con número de control ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Num_Control'], $fontStyleMontserratBold);
                $textRun->addText(', egresado del Instituto Tecnológico de Cd. Juárez de la carrera de ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Carrera_Egresado'], $fontStyleMontserratBold);
                $textRun->addText(', quien solicita titularse por medio de la opción, ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Tipo_Producto_Titulacion'], $fontStyleMontserratBold);
                $textRun->addText(", el correo del egresado es ", $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Correo_Egresado'], $fontStyleMontserratBold);
                $textRun->addText('.', $fontStyleMontserratMedium);
                $section->addText('Por lo que le solicito de la manera más atenta, comunique el nombre de los sinodales que revisarán el trabajo y conformarán el jurado para que se lleve a cabo el Acto de Recepción Profesional.', $fontStyleMontserratMedium, $paragraphStyleJustify);
                $section->addText('Sin otro particular de momento, me es grato quedar de usted,', $fontStyleMontserratMedium, $paragraphStyleJustify);

                // Agregar los estilos para el texto normal y el texto en negrita
                $fontStyleMontserratMedium = array('name' => 'Montserrat', 'size' => 9, 'color' => '000000');
                $fontStyleMontserratBold = array('name' => 'Montserrat', 'size' => 9, 'color' => '000000', 'bold' => true);

                // Crear un nuevo objeto TextRun para combinar el texto normal y el texto en negrita
                $textRun = $section->addTextRun($paragraphStyleJustify);

                $section->addText('Atentamente', $fontStyleMontserratBold);
                $section->addText('“Excelencia en Educación Tecnológica”', $fontStyleMontserratMedium);
                $section->addText('          “Patria, Trabajo y Técnica “', $fontStyleMontserratMedium);

                // Agrega la imagen a la sección
                $section->addImage($firmaCoordinadorPath, array(
                    'width' => $width2,
                    'height' => $height2,
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
                ));

                $section->addText('Tania Guadalupe Ruíz Escobar', $fontStyleMontserratMedium);
                $section->addText('Coordinador de titulación', $fontStyleMontserratMedium);

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

                // Guardar el archivo de Word en la ruta especificada.
                $directorioArchivo = "../assets/archivos/" . $periodo_Completo . "/sustentantes" . "/" . $egresadoData['Num_Control'] . "/SINODALES I, II, III, IV, V, VII & X" . "/";
                if (!file_exists($directorioArchivo)) {
                    mkdir($directorioArchivo, 0777, true);
                }

                $rutaArchivo = $directorioArchivo . "{$numControl} SINODALES I, II, III, IV, V, VII & X.docx";
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($rutaArchivo);

                $count = 0;

                do {

                    $response = enviarCorreoArchivo(
                        $conn,
                        $correo_Entrega_Proyecto,
                        'Departamento Académico',
                        "ARCHIVO SINODALES I, II, III, IV, V, VII, X de " . $egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'],
                        'ARCHIVO SINODALES I, II, III, IV, V, VII, X ' . $egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'] . ".",
                        '<strong>ARCHIVO SINODALES I, II, III, IV, V, VII, X ' . $egresadoData['Nombres_Egresado'] . ' ' . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'] . '.</strong>',
                        $rutaArchivo,
                        date("Y.m.d") . "_{$numControl}.docx"
                    );

                    sleep(2);
                    if (in_array($response->statusCode, [200, 201, 202])) { // Cambio de metodo a propiedad $response->statusCode() JH20250626
                        generarTokenYActulizarBD($conn, $egresadoData['Num_Control'], $rutaArchivo, $token);
                        if(!$conn){
                            logMessage("Conexión a la base de datos no disponible para actualizar estatus de " . $egresadoData['Num_Control']);
                        } else if (!$egresadoData['Num_Control']) {
                            logMessage("Fk_Usuario_Egresado no disponible para " . $egresadoData['Num_Control']);
                        } else {
                            actualizarEstatusFormatoB($conn, $egresadoData['Num_Control'], 5);
                        }
                        echo json_encode(['success' => true, 'message' => 'Archivo SINODALES I, II, III, IV, V, VII, X creado exitosamente para ' . $egresadoData['Num_Control'] . ' y correo electrónico enviado correctamente a departamento académico.']);
                        exit;
                    } else {
                        $count++;
                        if ($count == 3) {
                            echo json_encode(['success' => false, 'message' => 'Error al enviar el correo electrónico a departamento académico correspondiente, por favor verifique la página de envío de anexos I y II sin éxito.']);
                            exit;
                        }
                        sleep(2);
                    }
                } while ($count < 3);
            } elseif ($egresadoData['Fk_Tipo_Titulacion_Egresado'] == 8) {

                //ITCJ 2023 SINODALES VI

                // Aquí empiezas a crear el contenido del documento correspondiente a SINODALES VI
                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                //Modificaciones de fuente con respecto al documento
                $fontStyle->setName('Montserrat Medium'); // Nombre de la fuente
                $fontStyle->setSize(9); // Tamaño de la fuente

                // Formatear la fecha en español
                $fechaEnEspanol = formatFechaEnEspanol(date('Y-m-d'));

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
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
                ));

                $section->addText('Instituto Tecnológico de Ciudad Juárez', $fontStyleCourirerNewSmall, $paragraphStyleRight);
                $section->addText('');
                $textRun12 = $section->addTextRun($paragraphStyleRight);
                $textRun12->addText('Ciudad Juárez, Chihuahua, ', $fontStyleMontserratMedium);
                $textRun12->addText($fechaEnEspanol, $fontStyleMontserratMediumDate);
                $section->addText('');
                $section->addText('');
                $section->addText('');
                $section->addText('' . $egresadoData['Nombre_Jefe_Departamento'], array('name' => 'Montserrat', 'size' => 11, 'bold' => true, 'lineHeight' => 0.8));
                $section->addText('Jefe del departamento de ' . $egresadoData['Nombre_Departamento'], array('name' => 'Montserrat', 'size' => 11, 'bold' => true, 'lineHeight' => 0.8));
                $section->addText('P r e s e n t e.', array('name' => 'Montserrat', 'size' => 11, 'bold' => true, 'lineHeight' => 0.8));
                $section->addText('');

                // Agregar los estilos para el texto normal y el texto en negrita
                $fontStyleMontserratMedium = array('name' => 'Montserrat Medium', 'size' => 10, 'color' => '000000');
                $fontStyleMontserratBold = array('name' => 'Montserrat Medium', 'size' => 10, 'color' => '000000', 'bold' => true);

                // Crear un nuevo objeto TextRun para combinar el texto normal y el texto en negrita
                $textRun = $section->addTextRun($paragraphStyleJustify);

                // Agregar el texto normal y en negrita al objeto TextRun
                $textRun->addText('Por este medio hago de su conocimiento que la ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Nombres_Egresado'] . ' ' . $egresadoData['Apellidos_Egresado'], $fontStyleMontserratBold);
                $textRun->addText(', con número de control ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Num_Control'], $fontStyleMontserratBold);
                $textRun->addText(', egresado de la carrera de ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Carrera_Egresado'], $fontStyleMontserratBold);
                $textRun->addText(', del IT Ciudad Juárez, solicita titularse por medio de la opción ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Tipo_Producto_Titulacion'] . '.', $fontStyleMontserratBold);
                $section->addText('(Área: ______________________________________________________).', $fontStyleMontserratMedium, $paragraphStyleJustify);
                $section->addText('');
                $section->addText('Por lo que le solicito de la manera más atenta, me comunique el nombre de los sinodales que revisarán el trabajo y conformarán el jurado para que se lleve a cabo el Acto de Recepción Profesional.', $fontStyleMontserratMedium, $paragraphStyleJustify);
                $section->addText('Sin otro particular de momento, me es grato quedar de usted,', $fontStyleMontserratMedium, $paragraphStyleJustify);
                $section->addText('');
                $section->addText('A T E N T A M E N T E ', $fontStyleMontserratBold);
                $section->addText('Excelencia en Educación Tecnológica', array('name' => 'Montserrat Medium', 'size' => 8, 'lineHeight' => 0.8, 'italic' => true));

                // Agrega la imagen a la sección
                $section->addImage($firmaCoordinadorPath, array(
                    'width' => $width2,
                    'height' => $height2,
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
                ));

                $section->addText('Tania Guadalupe Ruíz Escobar', array('name' => 'Montserrat Medium', 'size' => 10, 'bold' => true, 'lineHeight' => 0.8));
                $section->addText('Coordinador de titulación', array('name' => 'Montserrat Medium', 'size' => 10, 'bold' => true, 'lineHeight' => 0.8));

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

                // Guardar el archivo de Word en la ruta especificada.
                $directorioArchivo = "../assets/archivos/" . $periodo_Completo . "/sustentantes" . "/" . $egresadoData['Num_Control'] . "/SINODALES VI" . "/";
                if (!file_exists($directorioArchivo)) {
                    mkdir($directorioArchivo, 0777, true);
                }

                $rutaArchivo = $directorioArchivo . "{$numControl} SINODALES VI.docx";
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($rutaArchivo);

                $count = 0;

                do {

                    $response = enviarCorreoArchivo(
                        $conn,
                        $correo_Entrega_Proyecto,
                        'Departamento Académico',
                        "ARCHIVO SINODALES VI de " . $egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'],
                        'ARCHIVO SINODALES VI ' . $egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'] . ".",
                        '<strong>ARCHIVO SINODALES VI ' . $egresadoData['Nombres_Egresado'] . ' ' . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'] . '.</strong>',
                        $rutaArchivo,
                        date("Y.m.d") . "_{$numControl}.docx"
                    );

                    sleep(2);
                    if (in_array($response->statusCode, [200, 201, 202])) { // Cambio de metodo a propiedad $response->statusCode() JH20250626
                        generarTokenYActulizarBD($conn, $egresadoData['Num_Control'], $rutaArchivo, $token);
                        if(!$conn){
                            logMessage("Conexión a la base de datos no disponible para actualizar estatus de " . $egresadoData['Num_Control']);
                        } else if (!$egresadoData['Num_Control']) {
                            logMessage("Fk_Usuario_Egresado no disponible para " . $egresadoData['Num_Control']);
                        } else {
                            actualizarEstatusFormatoB($conn, $egresadoData['Num_Control'], 5);
                        }
                        echo json_encode(['success' => true, 'message' => 'Archivo SINODALES VI creado exitosamente para ' . $egresadoData['Num_Control'] . ' y correo electrónico enviado correctamente a departamento académico.']);
                        exit;
                    } else {
                        $count++;
                        if ($count == 3) {
                            echo json_encode(['success' => false, 'message' => 'Error al enviar el correo electrónico a departamento académico correspondiente, por favor verifique la página de envío de anexos I y II sin éxito.']);
                            exit;
                        }
                        sleep(2);
                    }
                } while ($count < 3);
            } elseif (
                $egresadoData['Fk_Tipo_Titulacion_Egresado'] == 10 ||
                $egresadoData['Fk_Tipo_Titulacion_Egresado'] == 11
            ) {

                //ITCJ 2023 SINODALES VIII y IX

                // Aquí empiezas a crear el contenido del documento correspondiente a SINODALES VIII y IX
                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                //Modificaciones de fuente con respecto al documento
                $fontStyle->setName('Montserrat Medium'); // Nombre de la fuente
                $fontStyle->setSize(10); // Tamaño de la fuente

                // Formatear la fecha en español
                $fechaEnEspanol = formatFechaEnEspanol(date('Y-m-d'));

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
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
                ));

                $section->addText('Instituto Tecnológico de Ciudad Juárez', $fontStyleCourirerNewSmall, $paragraphStyleRight);
                $section->addText('');
                $textRun12 = $section->addTextRun($paragraphStyleRight);
                $textRun12->addText('Ciudad Juárez, Chihuahua, ', $fontStyleMontserratMedium);
                $textRun12->addText($fechaEnEspanol, $fontStyleMontserratMediumDate);
                $section->addText('');
                $section->addText('');
                $section->addText('');
                $section->addText('' . $egresadoData['Nombre_Jefe_Departamento'], $fontStyleMontserratExtraBold);
                $section->addText('Jefe del departamento de ' . $egresadoData['Nombre_Departamento'], $fontStyleMontserratExtraBold);
                $section->addText('P r e s e n t e.', $fontStyleMontserratExtraBold);
                $section->addText('');

                // Agregar los estilos para el texto normal y el texto en negrita
                $fontStyleMontserratMedium = array('name' => 'Montserrat Medium', 'size' => 10, 'color' => '000000');
                $fontStyleMontserratBold = array('name' => 'Montserrat Medium', 'size' => 10, 'color' => '000000', 'bold' => true);

                // Crear un nuevo objeto TextRun para combinar el texto normal y el texto en negrita
                $textRun = $section->addTextRun($paragraphStyleJustify);

                // Agregar el texto normal y en negrita al objeto TextRun
                $textRun->addText('Por medio de la presente, hago de su conocimiento que el C. ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Nombres_Egresado'] . ' ' . $egresadoData['Apellidos_Egresado'], $fontStyleMontserratBold);
                $textRun->addText(', con número de control ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Num_Control'], $fontStyleMontserratBold);
                $textRun->addText(', egresado de la carrera de ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Carrera_Egresado'], $fontStyleMontserratBold);
                $textRun->addText(' del IT ');
                $textRun->addText('Cd. Juárez', $fontStyleMontserratBold);
                $textRun->addText(', solicita titularse por medio de la opción ', $fontStyleMontserratMedium);
                $textRun->addText($egresadoData['Tipo_Producto_Titulacion'] . '.', $fontStyleMontserratBold);
                $section->addText('Por lo que solicito de la manera más atenta, me comunique el nombre de los sinodales que integrarán el jurado para que se lleve a cabo el Acto de Recepción Profesional.', $fontStyleMontserratMedium, $paragraphStyleJustify);
                $section->addText('');
                $section->addText('Sin otro particular de momento, me es grato quedar de usted,', $fontStyleMontserratMedium, $paragraphStyleJustify);
                $section->addText('');
                $section->addText('A T E N T A M E N T E ', $fontStyle);
                $section->addText('Excelencia en Educación Tecnológica', array('name' => 'Montserrat Medium', 'size' => 8, 'lineHeight' => 0.8, 'italic' => true));

                // Agrega la imagen a la sección
                $section->addImage($firmaCoordinadorPath, array(
                    'width' => $width2,
                    'height' => $height2,
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
                ));
                $section->addText('Tania Guadalupe Ruíz Escobar', $fontStyle);
                $section->addText('Coordinador de titulación', $fontStyle);

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

                // Guardar el archivo de Word en la ruta especificada.
                $directorioArchivo = "../assets/archivos/" . $periodo_Completo . "/sustentantes" . "/" . $egresadoData['Num_Control'] . "/SINODALES VIII & IX" . "/";
                if (!file_exists($directorioArchivo)) {
                    mkdir($directorioArchivo, 0777, true);
                }
                $rutaArchivo = $directorioArchivo . "{$numControl} SINODALES VIII & IX.docx";
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($rutaArchivo);

                $count = 0;

                do {
                    $response = enviarCorreoArchivo(
                        $conn,
                        $correo_Entrega_Proyecto,
                        'Departamento Académico',
                        "ARCHIVO SINODALES VIII y IX de " . $egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'],
                        'ARCHIVO SINODALES VIII y IX ' . $egresadoData['Nombres_Egresado'] . " " . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'] . ".",
                        '<strong>ARCHIVO SINODALES VIII y IX ' . $egresadoData['Nombres_Egresado'] . ' ' . $egresadoData['Apellidos_Egresado'] . ' - ' . $egresadoData['Num_Control'] . '.</strong>',
                        $rutaArchivo,
                        date("Y.m.d") . "_{$numControl}.docx"
                    );

                    sleep(2);
                    if (in_array($response->statusCode, [200, 201, 202])) { // Cambio de metodo a propiedad $response->statusCode() JH20250626
                        generarTokenYActulizarBD($conn, $egresadoData['Num_Control'], $rutaArchivo, $token);
                        if(!$conn){
                            logMessage("Conexión a la base de datos no disponible para actualizar estatus de " . $egresadoData['Num_Control']);
                        } else if (!$egresadoData['Num_Control']) {
                            logMessage("Fk_Usuario_Egresado no disponible para " . $egresadoData['Num_Control']);
                        } else {
                            actualizarEstatusFormatoB($conn, $egresadoData['Num_Control'], 5);
                        }
                        echo json_encode(['success' => true, 'message' => 'SINODALES VIII y IX creado exitosamente para ' . $egresadoData['Num_Control'] . ' y correo electrónico enviado correctamente a departamento académico.']);
                        exit;
                    } else {
                        $count++;
                        if ($count == 3) {
                            echo json_encode(['success' => false, 'message' => 'Error al enviar el correo electrónico a departamento académico correspondiente, por favor verifique la página de envío de anexos I y II sin éxito.']);
                            exit;
                        }
                        sleep(2);
                    }
                } while ($count < 3);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron datos para el número de control proporcionado.']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Número de control no especificado.']);
    }
} catch (\Exception $e) {
    error_log($e->getMessage(), 3, '../assets/archivos/logs/error al generar documento.log');
    // Envía un mensaje genérico de error al usuario
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
