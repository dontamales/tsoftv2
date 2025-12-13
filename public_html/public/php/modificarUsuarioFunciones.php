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

function validateForm($fk_roles, $nombres, $apellidos, $correo)
{
    if (!$fk_roles || !$nombres || !$apellidos || !$correo) {
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

// Función para registrar usuarios individuales
function modificarUsuario($conn, $fk_roles, $nombres, $apellidos, $correo, $idUsuario)
{
    if ($_SESSION['user_role'] == !3) {
        $respuesta = [
            "message" => "Error: No tiene permiso para modificar usuarios.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    if (empty($fk_roles) || empty($nombres) || empty($apellidos) || empty($correo)) {
        $respuesta = [
            "message" => "Error: Todos los campos son obligatorios.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    $stmt2 = $conn->prepare("SELECT * FROM usuario WHERE Id_Usuario = ?");
    $stmt2->bind_param("i", $idUsuario);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $stmt2->close();

    if (($row['Fk_Roles_Usuario'] == 1 && $fk_roles != 1) || ($row['Fk_Roles_Usuario'] != 1 && $fk_roles == 1)) {
        $respuesta = [
            "error" => "Un sustentante no puede volverse administrativo ni viceversa.",
            "error" => isset($_SESSION['error']) ? $_SESSION['error'] : null
        ];

        echo json_encode($respuesta);
        die();
    }

    if ($row['Correo_Usuario'] != $correo) {
        isDuplicate($conn, "SELECT COUNT(*) AS count FROM usuario WHERE Correo_Usuario = ?", $correo, 'Error: Ya existe un usuario con ese correo electrónico.');
    }

    // Formatear los datos de fecha
    $fecha = date("Y-m-d");

    // Preparar e insertar el usuario en la base de datos usando consultas preparadas
    $query = "UPDATE usuario 
    SET Fk_Roles_Usuario = ?, 
    Nombres_Usuario = ?, 
    Apellidos_Usuario = ?, 
    Correo_Usuario = ?, 
    Fecha_Usuario = ?
    WHERE Id_Usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssi", $fk_roles, $nombres, $apellidos, $correo, $fecha, $idUsuario);

    if ($stmt->execute()) {

        $respuesta = [
            "message" => "Usuario modificado con éxito.",
            "success" => isset($_SESSION['success']) ? $_SESSION['success'] : null
        ];

        echo json_encode($respuesta);
        die();

        $stmt->close();
    }
}
