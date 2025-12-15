<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";
require_once '../vendor/autoload.php';
// Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer JH20250626
// require_once 'enviarCorreoFunciones.php';
require_once 'enviarCorreos.php';

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");


function generarPasswordAleatoria($longitud)
{
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numCaracteres = strlen($caracteres);
    $randomPassword = '';

    for ($i = 0; $i < $longitud; $i++) {
        $index = rand(0, $numCaracteres - 1);
        $randomPassword .= $caracteres[$index];
    }

    return $randomPassword;
}

function checkEmailDomain($email)
{
    $domains = [
        "gmail.com",
        "gmail.com.mx",
        "hotmail.com",
        "hotmail.es",
        "outlook.com",
        "outlook.es",
        "live.com",
        "live.com.mx",
        "msn.com",
        "yahoo.com",
        "yahoo.com.mx",
        "cdjuarez.tecnm.mx",
        "itcj.edu.mx",
    ];

    $emailParts = explode("@", $email);
    $domain = end($emailParts);

    foreach ($domains as $validDomain) {
        if ($domain === $validDomain) {
            return true;
        }
        // Si la distancia de Levenshtein es 1, asumimos un error ortográfico.
        if (levenshteinDistance($domain, $validDomain) <= 1) {
            return false;
        }
    }

    return true;
}

function levenshteinDistance($a, $b)
{
    $matrix = [];

    for ($i = 0; $i <= strlen($b); $i++) {
        $matrix[$i] = [$i];
    }

    for ($j = 0; $j <= strlen($a); $j++) {
        $matrix[0][$j] = $j;
    }

    for ($i = 1; $i <= strlen($b); $i++) {
        for ($j = 1; $j <= strlen($a); $j++) {
            if ($b[$i - 1] === $a[$j - 1]) {
                $matrix[$i][$j] = $matrix[$i - 1][$j - 1];
            } else {
                $matrix[$i][$j] = min(
                    $matrix[$i - 1][$j - 1] + 1,
                    min($matrix[$i][$j - 1] + 1, $matrix[$i - 1][$j] + 1)
                );
            }
        }
    }

    return $matrix[strlen($b)][strlen($a)];
}


function capitalizeName($name)
{
    $name = strtolower($name);
    $nameParts = explode(" ", $name);

    for ($i = 0; $i < count($nameParts); $i++) {
        $nameParts[$i] = ucfirst($nameParts[$i]);
    }

    return implode(" ", $nameParts);
}

function validateForm($fk_roles, $nombres, $apellidos, $correo, $password, $numero_control, $carrera, $promedio, $telefono)
{
    if (!$fk_roles || !$nombres || !$apellidos || !$correo || !$password || !$numero_control || !$carrera || !$promedio || !$telefono) {
        return "Hay campos faltantes en los datos del sustentante.";
    }

    // Expresión regular para verificar que solo contiene letras y espacios
    $namePattern = "/^[a-zA-ZÀ-ÿ\s]+$/";
    if (!preg_match($namePattern, $nombres)) {
        return "El nombre contiene números o caracteres especiales (las vocales con tilde no cuentan como caracteres especiales).";
    }
    if (!preg_match($namePattern, $apellidos)) {
        return "El apellido contiene números o caracteres especiales (las vocales con tilde no cuentan como caracteres especiales).";
    }

    $nombres = capitalizeName(trim($nombres));
    $apellidos = capitalizeName(trim($apellidos));
    $correo = strtolower(trim($correo));

    $emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    if (!preg_match($emailPattern, $correo)) {
        return "El formato del correo electrónico es inválido.";
    }

    if (!checkEmailDomain($correo)) {
        return "El dominio de correo electrónico es incorrecto.";
    }

    if (strlen($password) < 8) {
        return "El tamaño de la contraseña es menor a 8 caracteres.";;
    }

    if (!is_numeric($promedio) || $promedio < 70 || $promedio > 100) {
        return "El promedio no es un número entre 70 y 100.";
    }

    return "";
}


// Función para verificar duplicados (para el registro de usuarios individuales)
function isDuplicate($conn, $query, $param, $errorMsg)
{
    $statement = $conn->prepare($query);
    $statement->bind_param("s", $param);
    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    $statement->close();

    if ($row['count'] > 0) {
        $_SESSION['message'] = $errorMsg;
        die(json_encode(['message' => $errorMsg]));
    }
}

// Función para verificar duplicados (para la subida de archivos Excel)
function isDuplicateExcel($conn, $query, $param)
{
    $statement = $conn->prepare($query);
    $statement->bind_param("s", $param);
    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    $statement->close();

    if ($row['count'] > 0) {
        // Registrar el duplicado en un archivo .log
        $logEntry = date('Y-m-d H:i:s') . " - Duplicado detectado: " . $param . "\n";
        file_put_contents('../assets/archivos/logs/lista servicios escolares/' . date("Y.m.d") . ' duplicados.log', $logEntry, FILE_APPEND);

        // Devolver true si se encuentra un duplicado
        return true;
    } else {
        // Devolver false si no se encuentra un duplicado
        return false;
    }
}

// Función para registrar usuarios individuales
function registrarUsuario($conn, $fk_roles, $nombres, $apellidos, $correo, $hashed_password, $numero_control, $carrera, $promedio, $telefono, $password)
{
    if ($_SESSION['user_role'] !== 3) {
        echo json_encode(["message" => "Error: No tiene permiso para registrar usuarios."]);
        die();
    }

    // Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer JH20250626
    // if (verificarLimiteCorreo($conn) >= 100) {
    //     echo json_encode(["message" => "Límite de de 100 correos electrónicos diarios alcanzados."]);
    //     die();
    // }

    if (empty($fk_roles) || empty($nombres) || empty($apellidos) || empty($correo) || empty($password)) {
        echo json_encode(["message" => "Error: Todos los campos son obligatorios."]);
        die();
    }

    if ($fk_roles === 1) {
        if (empty($promedio) || $promedio < 70 || $promedio > 100) {
            echo json_encode(["message" => "Error: El promedio debe ser un número entre 70 y 100."]);
            die();
        }
    }

    // Formatear los datos de fecha
    $fecha = date("Y-m-d");

    // Preparar e insertar el usuario en la base de datos usando consultas preparadas
    $query = "INSERT INTO usuario (Fk_Roles_Usuario, Nombres_Usuario, Apellidos_Usuario, Correo_Usuario, Contraseña_Usuario, Fecha_Usuario) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $fk_roles, $nombres, $apellidos, $correo, $hashed_password, $fecha);

    if ($stmt->execute()) {
        $usuario_id = $stmt->insert_id; // Obtiene el ID del usuario registrado
        if ($fk_roles !== 1) {
            $count = 0;
            do {
                try {
                    $response = enviarCorreo(
                        $conn,
                        $correo,

                        $nombres . " " . $apellidos,
                        "Registro de T-Soft (Coordinación de Titulación)",

                        "Hola, " . $nombres . " " . $apellidos . " se ha registrado su usuario con rol administrativo en T-Soft, la plataforma de Coordinación de Titulación. Con el correo electrónico: " . $correo . " y su contraseña: " . $password . " a continuación, acceda al portal http://login.tsoft.website/ para asistir a los sustentantes con el proceso de titulación.",

                        "Hola, " . $nombres . " " . $apellidos . " se ha registrado su usuario con rol administrativo en T-Soft, la plataforma de Coordinación de Titulación. <br /><br />Correo electrónico: <strong>" . $correo . "</strong><br />Contraseña: <strong>" . $password . "</strong><br /><br />A continuación, acceda al portal http://login.tsoft.website/ para cambiar su contraseña y seguir con su proceso de titulación."
                    );
                    if ($response->statusCode == 200) { // Cambio de metodo a propiedad $response->statusCode() == 202 JH20250626
                        $count = 3;
                        echo json_encode(["message" => "Correo electrónico de bienvenida enviado correctamente y usuario registrado exitosamente."]);
                        die();
                    } else {
                        $count++;
                        echo json_encode(["message" => "Número de intentos máximo al enviar el correo electrónico de bienvenida alcanzado."]);
                        die();
                    }
                } catch (Exception $e) {
                    echo json_encode(["message" => "Excepción al enviar correo electrónico de bienvenida: " . $e->getMessage()]);
                }
            } while ($count < 3);

            echo json_encode(["message" => "Usuario registrado pero envío de correo electrónico de bienvenida pendiente."]);
            die();

            // Si el usuario es un sustentante, también lo agregamos a la tabla de egresados
        } elseif ($fk_roles === 1) {
            $formatoB_Pendiente_Estatus = 1;
            $egresado_query = "INSERT INTO egresado (Fk_Usuario_Egresado, Num_Control, Fk_Carrera_Egresado, FK_Estatus_Egresado, Promedio_Egresado, Telefono_Egresado) VALUES (?, ?, ?, ?, ?, ?)";
            $egresado_stmt = $conn->prepare($egresado_query);
            $egresado_stmt->bind_param("isiids", $usuario_id, $numero_control, $carrera, $formatoB_Pendiente_Estatus, $promedio, $telefono);
            if ($egresado_stmt->execute()) {
                $count = 0;
                do {
                    try {
                        $response = enviarCorreo(
                            $conn,
                            $correo,

                            $nombres . " " . $apellidos,
                            "Registro de T-Soft (Coordinación de Titulación)",

                            "Hola, " . $nombres . " " . $apellidos . " se le ha registrado su usuario con el número de control: " . $numero_control . ", correo: " . $correo . " y su contraseña: " . $password . " a continuación, acceda al portal http://login.tsoft.website/ para seguir con su proceso de titulación.",

                            "Hola, " . $nombres . " " . $apellidos . " se ha registrado su usuario en T-Soft, la plataforma de Coordinación de Titulación. <br /><br />Número de control: <strong>" . $numero_control . "</strong><br />Correo electrónico: <strong>" . $correo . "</strong><br />Contraseña: <strong>" . $password . "</strong><br /><br />A continuación, acceda al portal http://login.tsoft.website/ para cambiar su contraseña y seguir con su proceso de titulación."
                        );
                        if ($response->statusCode == 200) { // Cambio de metodo a propiedad $response->statusCode() == 202 JH20250626
                            $count = 3;
                            echo json_encode(["message" => "Correo electrónico enviado correctamente y sustentante registrado exitosamente."]);
                            die();
                        } else {
                            $count++;
                            echo json_encode(["message" => "Error al enviar el correo electrónico."]);
                        }
                    } catch (Exception $e) {
                        echo json_encode(["message" => "Excepción al enviar correo electrónico: " . $e->getMessage()]);
                        die();
                    }
                } while ($count < 3);
                echo json_encode(["message" => "Sustentante registrado pero envío de correo de registro sin éxito."]);
                die();
            } else {
                echo json_encode(["message" => "Error al registrar el sustentante: " . $stmt->error]);
                die();
            }
            $egresado_stmt->close();
        } else {
            echo json_encode(["message" => "Usuario administrativo registrado exitosamente."]);
            die();
        }
    } else {
        echo json_encode(["message" => "Error al registrar el usuario: " . $stmt->error]);
        die();
    }
    $stmt->close();
}


// Función para registrar usuarios desde un archivo Excel
function registrarUsuarioExcel($conn, $fk_roles, $nombres, $apellidos, $correo, $hashed_password, $numero_control, $carrera, $promedio, $telefono, $password)
{
    // if (verificarLimiteCorreo($conn) >= 100) {
    //     return ['message' => 'Límite de de 100 correos electrónicos diarios alcanzados', 'status' => false];
    // } else {
        $error = validateForm($fk_roles, $nombres, $apellidos, $correo, $hashed_password, $numero_control, $carrera, $promedio, $telefono);

        if ($error === "") {
            // Registrar la verificación fallida en un archivo .log

            // Formatear los datos de fecha
            $fecha = date("Y-m-d");
            // Preparar e insertar el usuario en la base de datos usando consultas preparadas
            $query = "INSERT INTO usuario (Fk_Roles_Usuario, Nombres_Usuario, Apellidos_Usuario, Correo_Usuario, Contraseña_Usuario, Fecha_Usuario) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isssss", $fk_roles, $nombres, $apellidos, $correo, $hashed_password, $fecha);

            if ($stmt->execute()) {
                $usuario_id = $stmt->insert_id; // Obtiene el ID del usuario registrado
                $formatoB_Pendiente_Estatus = 1;
                // Si el usuario es un sustentante, también lo agregamos a la tabla de egresados
                $egresado_query = "INSERT INTO egresado (Fk_Usuario_Egresado, Num_Control, Fk_Carrera_Egresado, FK_Estatus_Egresado, Promedio_Egresado, Telefono_Egresado) VALUES (?, ?, ?, ?, ?, ?)";
                $egresado_stmt = $conn->prepare($egresado_query);
                $egresado_stmt->bind_param("isiids", $usuario_id, $numero_control, $carrera, $formatoB_Pendiente_Estatus, $promedio, $telefono);
                if ($egresado_stmt->execute()) {
                    //Aquí es donde enviamos el correo al nuevo usuario.
                    $count = 0;
                    do {
                        try {
                            $response = enviarCorreo(
                                $conn,
                                $correo,

                                $nombres . " " . $apellidos,
                                "Registro de T-Soft (Coordinación de Titulación)",

                                "Hola, " . $nombres . " " . $apellidos . " se le ha registrado su usuario con el número de control: " . $numero_control . ", correo: " . $correo . " y su contraseña: " . $password . " a continuación, acceda al portal http://login.tsoft.website/ para seguir con su proceso de titulación.",

                                "Hola, " . $nombres . " " . $apellidos . " se le ha registrado su usuario con el número de control: <strong>" . $numero_control . "</strong>, correo: <strong>" . $correo . "</strong> y su contraseña: <strong>" . $password . "</strong> a continuación, acceda al portal http://login.tsoft.website/ para seguir con su proceso de titulación."
                            );
                            if ($response->statusCode == 200) { // Cambio de metodo a propiedad $response->statusCode() == 202 JH20250626
                                $count = 3;
                                return ['message' => 'Usuario registrado y correo enviado exitosamente.', 'status' => true];
                            } else {
                                $count++;
                                return ['message' => 'Error al enviar el correo electrónico.', 'status' => true];
                            }
                        } catch (Exception $e) {
                            echo 'Excepción al enviar correo electrónico: ' . $e->getMessage() . "\n";
                        }
                    } while ($count < 3);
                    return ['message' => 'Usuario registrado pero envío de correo sin éxito.', 'status' => false];
                } else {
                    return ['message' => 'Error al registrar el usuario: ' . $stmt->error];
                }
                $egresado_stmt->close();
                return ['message' => 'Usuario registrado exitosamente.', 'status' => true];
            } else {
                return ['message' => 'Error al registrar el usuario: ' . $stmt->error];
            }
            $stmt->close();
        } else {
            $logEntry = date('Y-m-d H:i:s') . "- Las validaciones del sustentante: " . $nombres . " " . $apellidos . " - " . $correo . " - " . $numero_control . " fallaron debido a: '" . $error . "'.\n";
            file_put_contents('../assets/archivos/logs/lista servicios escolares/' . date("Y.m.d") . ' errores de validacion.log', $logEntry, FILE_APPEND);
            return ['message' => 'Error al registrar el usuario.'];
        }
    // }
}
