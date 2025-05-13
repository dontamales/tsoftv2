<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

function incrementFailedLoginAttempts($email)
{
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $attemptData = getFailedLoginAttempts($email);
    $errores = $attemptData['attempts'];
    $seconds_since_last_attempt = $attemptData['seconds_since_last_attempt'] ?? 61;

    if ($errores >= 5) {
        $times_blocked = isset($attemptData['times_blocked']) ? $attemptData['times_blocked'] + 1 : 1;
        $query = "UPDATE login_attempts SET failed_attempts = 1, times_blocked = ?, attempt_time = NOW() WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $times_blocked, $email);
    } elseif ($seconds_since_last_attempt > 60) {
        $query = "INSERT INTO login_attempts (email, ip_address, attempt_time, failed_attempts, times_blocked) VALUES (?, ?, NOW(), 1, 0) ON DUPLICATE KEY UPDATE failed_attempts = 1, attempt_time = NOW()";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $email, $ip_address);
    } else {
        $query = "INSERT INTO login_attempts (email, ip_address, attempt_time, failed_attempts, times_blocked) VALUES (?, ?, NOW(), 1, 0) ON DUPLICATE KEY UPDATE failed_attempts = failed_attempts + 1, attempt_time = NOW()";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $email, $ip_address);
    }

    if (mysqli_stmt_execute($stmt)) {
        error_log("Statement executed successfully");
    } else {
        error_log("Statement execution failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}


function getFailedLoginAttempts($email)
{
    global $conn;
    $query = "SELECT failed_attempts, TIMESTAMPDIFF(SECOND, attempt_time, NOW()) as seconds_since_last_attempt, times_blocked FROM login_attempts WHERE email = ? ORDER BY attempt_time DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $attempts, $seconds_since_last_attempt, $times_blocked);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return ['attempts' => $attempts, 'seconds_since_last_attempt' => $seconds_since_last_attempt, 'times_blocked' => $times_blocked];
}

//Crear la función para restablecer los intentos fallidos y las veces bloqueadas de un usuario
function resetFailedLoginAttempts($email)
{
    global $conn;
    $query = "UPDATE login_attempts SET failed_attempts = 0, times_blocked = 0 WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


if (isset($_POST['femail']) && isset($_POST['fpass'])) {
    $femail = mysqli_real_escape_string($conn, $_POST['femail']);
    $fpass = $_POST['fpass'];

    $attemptData = getFailedLoginAttempts($femail);
    $errores = isset($attemptData['attempts']) ? $attemptData['attempts'] : 0;
    $seconds_since_last_attempt = isset($attemptData['seconds_since_last_attempt']) ? $attemptData['seconds_since_last_attempt'] : 61;

    $sql = "SELECT * FROM usuario WHERE Correo_Usuario = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $femail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $cooldown = 60; // Valor por defecto: 60 segundos
    $times_blocked = $attemptData['times_blocked'] ?? 0;
    if ($times_blocked > 0) {
        $multiplier = min($times_blocked, 15); // Máximo de 15 multiplicadores
        $cooldown = $multiplier * 60;
    }


    $cooldown_minutes = $cooldown / 60; // Convertir a minutos para mostrar en el mensaje
    $cooldown_milliseconds = $cooldown * 1000; // Convertir a milisegundos para usar en el script de JS

    // Si los intentos fallidos son 5 y no ha pasado un minuto desde el último intento
    if ($errores >= 5 && $seconds_since_last_attempt <= $cooldown) {
        $message = 'Ha alcanzado el límite de intentos fallidos.';
        if ($cooldown_minutes > 1) {
            $message .= "Por favor, espera {$cooldown_minutes} minutos antes de intentar de nuevo. Si olvidó su contraseña, puede reestablecerla comunicándose con Coordinación de  Tiulación.";
        } else {
            $message .= "Por favor, espera 1 minuto antes de intentar de nuevo.";
        }
        echo json_encode(['error' => $message, 'cooldown' => $cooldown_milliseconds]);
        exit;
    }

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($fpass, $row['Contraseña_Usuario'])) { // Aquí se verifica la contraseña
            resetFailedLoginAttempts($femail);
            $_SESSION['user_id'] = $row["Id_Usuario"];
            $_SESSION['user_role'] = $row["Fk_Roles_Usuario"];

            switch ($row["Fk_Roles_Usuario"]) {
                case "1":
                    session_regenerate_id(true);
                    mysqli_free_result($result);
                    mysqli_close($conn);
                    echo json_encode(['url' => '../views/userDashboard.php']);
                    exit;
                case "2":
                case "3":
                case "4":
                case "5":
                case "6":
                    session_regenerate_id(true);
                    mysqli_free_result($result);
                    mysqli_close($conn);
                    echo json_encode(['url' => '../views/adminDashboard.php']);
                    exit;
                default:
                    mysqli_free_result($result);
                    mysqli_close($conn);
                    echo json_encode(['error' => 'Error al iniciar sesión']);
                    exit;
            }
        } else {
            incrementFailedLoginAttempts($femail);
            echo json_encode(['error' => 'Datos incorrectos, por favor verifique que su correo y contraseña sean correctos.']);
            mysqli_free_result($result);
            mysqli_close($conn);
            exit;
        }
    } else {
        incrementFailedLoginAttempts($femail);
        echo json_encode(['error' => 'Datos incorrectos, por favor verifique que su correo y contraseña sean correctos.']);
        mysqli_free_result($result);
        mysqli_close($conn);
        exit;
    }
} else {
    echo json_encode(['error' => 'Faltan datos para iniciar sesión']);
    mysqli_free_result($result);
    mysqli_close($conn);
    exit;
}

mysqli_free_result($result);
mysqli_close($conn);
