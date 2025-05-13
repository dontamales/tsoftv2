<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

if (isset($_POST['asignarSuperUsuario'])) {
    $idUsuario = $_POST['asignarSuperUsuario'];
    $usuarioActual = $_SESSION['user_id'];
    $suTrue = 1;
    $suFalse = 0;

    $stmt = $conn->prepare("SELECT * FROM usuario WHERE Id_Usuario = ?");
    $stmt->bind_param("i", $usuarioActual);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    if (($idUsuario == $usuarioActual) && ($row['Super_Usuario'] == $suTrue)) {
        $_SESSION['error'] = "Ya eres super usuario.";
        header("Location: ../views/asignacionSuperUsuario.php");
        exit();
    } else {
        if (($row['Super_Usuario'] == $suTrue) && ($idUsuario != $usuarioActual)) {
            $stmt2 = $conn->prepare("UPDATE usuario SET Super_Usuario = ? WHERE Id_Usuario = ?");
            $stmt2->bind_param("ii", $suTrue, $idUsuario);
            if ($stmt2->execute()) {
                $stmt2->close();
                $stmt3 = $conn->prepare("UPDATE usuario SET Super_Usuario = ? WHERE Id_Usuario = ?");
                $stmt3->bind_param("ii", $suFalse, $usuarioActual);
                if (!$stmt3->execute()) {
                    $stmt3->close();
                    $_SESSION['error'] = "No se ha podido quitar el super usuario actual.";
                    header("Location: ../views/asignacionSuperUsuario.php");
                    exit();
                }
                $stmt3->close();
                $_SESSION['success'] = "Super usuario actualizado correctamente.";
            } else {
                $_SESSION['error'] = "No se ha podido asignar el super usuario.";
            }
        } else {
            $_SESSION['error'] = "Sólo un super usuario puede transferir sus privilegios a otro usuario.";
        }
    }
} else {
    $_SESSION['error'] = "No se recibieron los datos correctamente.";
}


$conn->close();

header("Location: ../views/asignacionSuperUsuario.php");
exit();
