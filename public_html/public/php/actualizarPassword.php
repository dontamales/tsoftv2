<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once '../../private/conexion.php'; #CONEXIÓN A LA BASE DE DATOS
require_once '../vendor/autoload.php'; #LIBRERÍA SENDGRID
require_once '../php/enviarCorreos.php'; #FUNCIONES PARA ENVIAR CORREOS

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer
// if (verificarLimiteCorreo($conn) >= 100) {
//     $_SESSION['error'] = "Cantidad máxima de correos enviados, intentelo de nuevo el día de mañana.";
// }

if (isset($_POST['restablecerPasswordCorreo']) && isset($_POST['restablecerPasswordNuevo']) && isset($_POST['restablecerPasswordConfirmar'])) {

    // Obtén los datos del formulario
    $correo = $_POST['restablecerPasswordCorreo'];
    $nuevaContraseña = $_POST['restablecerPasswordNuevo'];
    $confirmarContraseña = $_POST['restablecerPasswordConfirmar'];

    $sql1 = "SELECT * FROM usuario WHERE Correo_Usuario = ?";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "s", $correo);
    mysqli_stmt_execute($stmt1);
    $result = mysqli_stmt_get_result($stmt1);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if ($nuevaContraseña == $confirmarContraseña && strlen($nuevaContraseña) >= 8) {
            $nuevaContraseñaHash = password_hash($nuevaContraseña, PASSWORD_DEFAULT);
            // Verifica que el correo exista con consultas preparadas
            $sql = "UPDATE usuario SET Contraseña_Usuario = ? WHERE Correo_Usuario = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $nuevaContraseñaHash, $correo);
            if (mysqli_stmt_execute($stmt)) {
                $count = 0;
                do {
                    $response = enviarCorreo(
                        $conn,

                        $row["Correo_Usuario"],

                        $row["Nombres_Usuario"] . " " . $row["Apellidos_Usuario"],

                        "Cambio de contraseña en T-Soft (Coordinación de Titulación)",

                        "Hola, " . $row["Nombres_Usuario"] . " " . $row["Apellidos_Usuario"] . " se ha actualizado su contraseña. Su nueva contraseña es: " . $nuevaContraseña . " a continuación, acceda al portal http://login.tsoft.website/ para seguir con su proceso de titulación.",

                        "Hola, " . $row["Nombres_Usuario"] . " " . $row["Apellidos_Usuario"] . " se ha actualizado su contraseña. <br><br>Su nueva contraseña es: <strong>" . $nuevaContraseña . " </strong><br><br>A continuación, acceda al portal http://login.tsoft.website/ para seguir con su proceso de titulación."
                    );
                    if ($response->statusCode == 200) {
                        $_SESSION['success'] = "Contraseña actualizada correctamente, se ha enviado un correo electrónico a ". $row["Correo_Usuario"] ." con su nueva contraseña.";
                        $count = 3;
                    } else {
                        $count++;
                        if ($count == 3) {
                            $_SESSION['error'] = "Error al enviar el correo electrónico, intente de nuevo.";
                        }
                    }
                } while ($count < 3);
                $_SESSION['success'] = "Contraseña actualizada correctamente.";
            } else {
                $_SESSION['error'] = "Hubo un error al actualizar la contraseña.";
            }
        } else {
            $_SESSION['error'] = "Las contraseñas no coinciden o no cumplen con los requisitos.";
        }
    } else {
        $_SESSION['error'] = "No se encontró un usuario con esa dirección de correo.";
    }
} else {
    $_SESSION['error'] = "No se recibieron los datos correctamente.";
}

// Redirigir de vuelta a gestionPassword
header("Location: ../views/gestionPassword.php");
exit();
