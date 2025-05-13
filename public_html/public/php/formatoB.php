<?php
require_once("sesion.php");
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([1]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once("../../private/conexion.php");
require_once("formatoBData.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Recibir la información del formulario id="formato-B" y guardarla en variables
$data = json_decode(file_get_contents('php://input'), true);



// Sanitizar y validar los datos
$nombres = trim($data['nombres']);
$apellidos = trim($data['apellidos']);
$genero = intval($data['genero']);
$edad = intval($data['edad']);
$celular = trim($data['celular']);
$telefono = trim($data['telefono']);
$codigo_postal = trim($data['codigo_postal']);
$colonia = trim($data['colonia']);
$calle = trim($data['calle']);
$num_ext = trim($data['num_ext']);
$num_int = trim($data['num_int']);
$numero_control = trim($data['numero_control']);
$carrera = intval($data['carrera']);
$promedio = floatval($data['promedio']);
$proyecto = trim($data['proyecto']);
$plan_estudio = intval($data['plan_estudio']);
$tipo_titulacion = intval($data['tipo_titulacion']);
$fecha_ingreso = trim($data['fecha_ingreso']);
$fecha_egreso = trim($data['fecha_egreso']);
if ($data['asesor'] === '') {
    $asesor = null;
} else {
    $asesor = intval($data['asesor']);
}
$valid_titulacion_types = [7, 8, 10, 11, 12];
if (!in_array($tipo_titulacion, $valid_titulacion_types) && (empty($asesor) || empty($proyecto))) {
    echo json_encode(['message' => 'Para ese tipo de titulación se necesita rellenar el proyecto y el asesor.']);
    exit();
} 
$equipo = trim($data['equipo']);
$numero_integrantes_equipo = intval($data['numero_integrantes_equipo']);
$numero_control_equipo_1 = trim($data['numero_control_equipo_1']);
$nombres_equipo_1 = trim($data['nombres_equipo_1']);
$carrera_equipo_1 = intval($data['carrera_equipo_1']);
$numero_control_equipo_2 = trim($data['numero_control_equipo_2']);
$nombres_equipo_2 = trim($data['nombres_equipo_2']);
$carrera_equipo_2 = intval($data['carrera_equipo_2']);
$estatus_Revision_Pendiente = 2;
$formato_B_Aprobado = 2;



// Verificar que el numero de control no se repita en la base de datos
$stmt = $conn->prepare("SELECT * FROM egresado WHERE Num_Control = ? AND Fk_Usuario_Egresado != ?;");
$stmt->bind_param("si", $numero_control, $usuario_actual);
$stmt->execute();
$result = $stmt->get_result();
$numero_control_rep = $result->fetch_assoc();
$stmt->close();

if ($numero_control_rep !== null) {
    echo json_encode(['message' => 'El número de control ya está registrado.']);
} else {
    // Prepara la consulta SQL para insertar los nombres y apellidos en la tabla de usuario
    $stmt = $conn->prepare("UPDATE usuario SET Nombres_Usuario = ?, Apellidos_Usuario = ? WHERE Id_Usuario = ?;");
    $stmt->bind_param("ssi", $nombres, $apellidos, $usuario_actual);
    $stmt->execute();
    $stmt->close();

    // Prepara la consulta SQL para insertar los datos en la tabla de dirección
    if ($usuario['Fk_Direccion_Egresado'] === null) {
        // Si el usuario no tiene dirección, inserta los datos en la tabla dirección
        $stmt = $conn->prepare("
    INSERT INTO direccion (Codigo_Postal_Direccion, Colonia_Direccion, Calle_Direccion, Num_Exterior_Direccion, Num_Interior_Direccion) 
    VALUES (?, ?, ?, ?, ?);
    ");
        $stmt->bind_param("issss", $codigo_postal, $colonia, $calle, $num_ext, $num_int);
        $stmt->execute();

        // Obtiene el ID de la dirección insertada
        $direccion_id = $stmt->insert_id;
        $stmt->close();

        // Prepara la consulta SQL para insertar los datos en la tabla
        $stmt = $conn->prepare("
    UPDATE egresado 
    SET Fk_Direccion_Egresado = ? 
    WHERE Fk_Usuario_Egresado = ?;
    ");
        $stmt->bind_param("ii", $direccion_id, $usuario_actual);
        $stmt->execute();
        $stmt->close();
    } else {
        // Si el usuario ya tiene dirección, actualiza los datos en la tabla
        $stmt = $conn->prepare("
    UPDATE direccion 
    SET Codigo_Postal_Direccion = ?, Colonia_Direccion = ?, Calle_Direccion = ?, Num_Exterior_Direccion = ?, Num_Interior_Direccion = ? 
    WHERE Id_Direccion = ?;
    ");
        $stmt->bind_param("isssii", $codigo_postal, $colonia, $calle, $num_ext, $num_int, $usuario['Fk_Direccion_Egresado']);
        $stmt->execute();
        $stmt->close();
    }

    //Prepara la consulta SQL para insertar los datos de proyecto en la tabla de proyecto
    if ($usuario['Fk_Proyecto_Egresado'] === null) {
        $stmt = $conn->prepare("INSERT INTO proyecto (Nombre_Proyecto) VALUES (?);");
        $stmt->bind_param("s", $proyecto);
        $stmt->execute();
        $proyecto_id = $stmt->insert_id;
        $stmt->close();
        $stmt = $conn->prepare("UPDATE egresado SET Fk_Proyecto_Egresado = ? WHERE Fk_Usuario_Egresado = ?;");
        $stmt->bind_param("ii", $proyecto_id, $usuario_actual);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("UPDATE proyecto SET Nombre_Proyecto = ? WHERE Id_Proyecto = ?;");
        $stmt->bind_param("si", $proyecto, $usuario['Fk_Proyecto_Egresado']);
        $stmt->execute();
        $stmt->close();
    }

    if ($usuario['FK_Estatus_Egresado'] <= 3) {

        // Prepara la consulta SQL para insertar los datos en la tabla
        $stmt = $conn->prepare("UPDATE egresado
            SET Fk_Sexo_Egresado = ?, 
            Edad_Egresado = ?, 
            Celular_Egresado = ?, 
            Telefono_Egresado = ?, 
            Num_Control = ?, 
            Fk_Carrera_Egresado = ?, 
            Promedio_Egresado = ?, 
            Fk_Plan_Estudio_Egresado = ?, 
            Fk_Tipo_Titulacion_Egresado = ?, 
            Fecha_Ingreso_Egresado = ?, 
            Fecha_Egresar_Egresado = ?, 
            Fecha_Envio_Formato_B_Egresado = NOW(), 
            FK_Estatus_Egresado = ?, 
            Fk_Asesor_Interno_Egresado = ?, 
            Proyecto_Equipo_Egresado = ?, 
            Numero_Equipo_Egresados = ?, 
            NumeroControl_Equipo_Egresado1 = ?, 
            NumeroControl_Equipo_Egresado2 = ?, 
            Nombre_Equipo_Egresado1 = ?, 
            Nombre_Equipo_Egresado2 = ?, 
            Fk_Carrera_Equipo_Egresado1 = ?, 
            Fk_Carrera_Equipo_Egresado2 = ?, 
            Formato_B_Aprobado_Egresado = ?
            WHERE Fk_Usuario_Egresado = ?; ");

        // Vincula los parámetros a la consulta SQL
        $stmt->bind_param(
            "iisssidiissiiiissssiiii",
            $genero,
            $edad,
            $celular,
            $telefono,
            $numero_control,
            $carrera,
            $promedio,
            $plan_estudio,
            $tipo_titulacion,
            $fecha_ingreso,
            $fecha_egreso,
            $estatus_Revision_Pendiente,
            $asesor,
            $equipo,
            $numero_integrantes_equipo,
            $numero_control_equipo_1,
            $numero_control_equipo_2,
            $nombres_equipo_1,
            $nombres_equipo_2,
            $carrera_equipo_1,
            $carrera_equipo_2,
            $formato_B_Aprobado,
            $usuario_actual
        );

        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Si la consulta fue exitosa, envía una respuesta JSON con un mensaje de éxito
            echo json_encode(['message' => 'Datos guardados exitosamente.']);
        } else {
            // Si hubo un error, envía una respuesta JSON con el mensaje de error
            echo json_encode(['message' => 'Error al guardar los datos: ' . $stmt->error]);
        }

        // Cierra la consulta y la conexión
        $stmt->close();
    } else {

        // Prepara la consulta SQL para insertar los datos en la tabla
        $stmt = $conn->prepare("UPDATE egresado
            SET Fk_Sexo_Egresado = ?, 
            Edad_Egresado = ?, 
            Celular_Egresado = ?, 
            Telefono_Egresado = ?, 
            Num_Control = ?, 
            Fk_Carrera_Egresado = ?, 
            Promedio_Egresado = ?, 
            Fk_Plan_Estudio_Egresado = ?, 
            Fk_Tipo_Titulacion_Egresado = ?, 
            Fecha_Ingreso_Egresado = ?, 
            Fecha_Egresar_Egresado = ?, 
            Fecha_Envio_Formato_B_Egresado = NOW(), 
            Fk_Asesor_Interno_Egresado = ?, 
            Proyecto_Equipo_Egresado = ?, 
            Numero_Equipo_Egresados = ?, 
            NumeroControl_Equipo_Egresado1 = ?, 
            NumeroControl_Equipo_Egresado2 = ?, 
            Nombre_Equipo_Egresado1 = ?, 
            Nombre_Equipo_Egresado2 = ?, 
            Fk_Carrera_Equipo_Egresado1 = ?, 
            Fk_Carrera_Equipo_Egresado2 = ?, 
            Formato_B_Aprobado_Egresado = ?  
            WHERE Fk_Usuario_Egresado = ?;");

        // Vincula los parámetros a la consulta SQL
        $stmt->bind_param(
            "iisssidiissiiissssiiii",
            $genero,
            $edad,
            $celular,
            $telefono,
            $numero_control,
            $carrera,
            $promedio,
            $plan_estudio,
            $tipo_titulacion,
            $fecha_ingreso,
            $fecha_egreso,
            $asesor,
            $equipo,
            $numero_integrantes_equipo,
            $numero_control_equipo_1,
            $numero_control_equipo_2,
            $nombres_equipo_1,
            $nombres_equipo_2,
            $carrera_equipo_1,
            $carrera_equipo_2,
            $formato_B_Aprobado,
            $usuario_actual
        );

        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Si la consulta fue exitosa, envía una respuesta JSON con un mensaje de éxito
            echo json_encode(['message' => 'Datos actualizados exitosamente.']);
        } else {
            // Si hubo un error, envía una respuesta JSON con el mensaje de error
            echo json_encode(['message' => 'Error al guardar los datos: ' . $stmt->error]);
        }

        // Cierra la consulta y la conexión
        $stmt->close();
    }
}

$conn->close();
