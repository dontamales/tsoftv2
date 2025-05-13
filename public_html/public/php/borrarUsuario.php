<?php
require_once 'sesion.php';
require_once 'auth.php';
require_roles([2, 3]);
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

if (isset($_POST['ids'])) {
    $ids = $_POST['ids'];

    // Comprobar que todos los valores en $ids son enteros
    foreach ($ids as $key => $id) {
        $ids[$key] = intval($id);  // Convertir a entero
    }

    if (in_array($_SESSION['user_id'], $ids)) {
        echo json_encode(["success" => false, "message" => "No puedes eliminarte a ti mismo."]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $formato_B_Rechazado = 3;
    $super_Usuario = 1;

    $sqlEgresado = "DELETE FROM egresado 
                    WHERE Fk_Usuario_Egresado IN ($placeholders)
                    AND (FK_Estatus_Egresado <= ? OR FK_Estatus_Egresado IS NULL)";

    $sqlUsuario = "DELETE FROM usuario 
                   WHERE Id_Usuario IN ($placeholders)
                   AND Super_Usuario != ?";

    $typesEgresado = str_repeat('i', count($ids)) . 'i'; 
    $paramsEgresado = array_merge($ids, [$formato_B_Rechazado]);

    $typesUsuario = str_repeat('i', count($ids)) . 'i'; 
    $paramsUsuario = array_merge($ids, [$super_Usuario]);

    $stmtEgresado = $conn->prepare($sqlEgresado);
    $stmtEgresado->bind_param($typesEgresado, ...$paramsEgresado);

    $stmtUsuario = $conn->prepare($sqlUsuario);
    $stmtUsuario->bind_param($typesUsuario, ...$paramsUsuario);

    $conn->begin_transaction();
    try {
        $stmtEgresado->execute();
        $stmtUsuario->execute();

        $conn->commit();

        $stmtEgresado->close();  // Cerrar prepared statement
        $stmtUsuario->close();  // Cerrar prepared statement

        echo json_encode(["success" => true, "message" => "Usuarios eliminados correctamente."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Error al eliminar los usuarios: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No se recibieron los datos necesarios."]);
}

