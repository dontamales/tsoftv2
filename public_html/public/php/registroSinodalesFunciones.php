<?php
require_once 'sesion.php'; 
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

// Función para registrar sinodal individuales
function registrarSinodal($conn, $sinodal1, $rolSinodal1, $sinodal2, $rolSinodal2, $sinodal3, $rolSinodal3, $sinodal4, $rolSinodal4, $egresadoProyecto)
{

    $sinodalesAsignados = 7;

    $pagoPendiente = 6;

    if (empty($sinodal1) || empty($rolSinodal1) || empty($sinodal2) || empty($rolSinodal2) || empty($sinodal3) || empty($rolSinodal3) || empty($sinodal4) || empty($rolSinodal4) || empty($egresadoProyecto)) {
        // Reemplaza el echo json_encode por un return
        return json_encode(['message' => 'Error: Todos los campos son obligatorios.']);
    }

    //Verifica que el valor de cada variable no sea nulo
    if ($sinodal1 == null || $rolSinodal1 == null || $sinodal2 == null || $rolSinodal2 == null || $sinodal3 == null || $rolSinodal3 == null || $sinodal4 == null || $rolSinodal4 == null || $egresadoProyecto == null) {
        // Reemplaza el echo json_encode por un return
        return json_encode(['message' => 'Error: Todos valores deben ser diferentes de nulos.']);
    }

    //Verificación de que no se repita ningun sinodal
    if ($sinodal1 == $sinodal2 || $sinodal1 == $sinodal3 || $sinodal1 == $sinodal4 || $sinodal2 == $sinodal3 || $sinodal2 == $sinodal4 || $sinodal3 == $sinodal4) {
        // Reemplaza el echo json_encode por un return
        return json_encode(['message' => 'Error: No se puede repetir ningún sinodal.']);
    }

    // Verificar que no existan duplicados
    $stmt1 = $conn->prepare("SELECT * FROM asignacion_sinodales WHERE Fk_Proyecto_Sinodales = ?");
    $stmt1->bind_param("i", $egresadoProyecto);
    $stmt1->execute();
    $result = $stmt1->get_result();

    if ($result->num_rows > 0) {
        $stmt2 = $conn->prepare("UPDATE asignacion_sinodales 
        SET Fk_Sinodal_1 = ?,
        Fk_Sinodal_1_Rol_Sinodal = ?,
        Fk_Sinodal_2 = ?,
        Fk_Sinodal_2_Rol_Sinodal = ?,
        Fk_Sinodal_3 = ?,
        Fk_Sinodal_3_Rol_Sinodal = ?,
        Fk_Sinodal_4 = ?,
        Fk_Sinodal_4_Rol_Sinodal = ?
        WHERE Fk_Proyecto_Sinodales = ?");
        $stmt2->bind_param("iiiiiiiii", $sinodal1, $rolSinodal1, $sinodal2, $rolSinodal2, $sinodal3, $rolSinodal3, $sinodal4, $rolSinodal4, $egresadoProyecto);
        $stmt2->execute();
        $stmt2->close();

        return json_encode(['message' => 'Actualización de asignación de sinodales exitosa']);
    } else {

        // Preparar e insertar el sinodal en la base de datos usando consultas preparadas
        $query = "INSERT INTO asignacion_sinodales (Fk_Sinodal_1, Fk_Sinodal_1_Rol_Sinodal, Fk_Sinodal_2, Fk_Sinodal_2_Rol_Sinodal, Fk_Sinodal_3, Fk_Sinodal_3_Rol_Sinodal, Fk_Sinodal_4, Fk_Sinodal_4_Rol_Sinodal, Fk_Proyecto_Sinodales) VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt3 = $conn->prepare($query);
        $stmt3->bind_param("iiiiiiiii", $sinodal1, $rolSinodal1, $sinodal2, $rolSinodal2, $sinodal3, $rolSinodal3, $sinodal4, $rolSinodal4, $egresadoProyecto);
        $stmt3->execute();
        $stmt3->close();

        $anexo_III_Egresado = 1;
        $formato_B_Aprobado_Egresado = 1;

        $query2 = "UPDATE egresado 
        SET egresado.FK_Estatus_Egresado = ? 
        WHERE egresado.Fk_Proyecto_Egresado = ? 
        AND egresado.FK_Estatus_Egresado = ?
        AND egresado.Anexo_III_Egresado = ?
        AND egresado.Formato_B_Aprobado_Egresado = ?";
        $stmt4 = $conn->prepare($query2);
        $stmt4->bind_param("iiiii", $sinodalesAsignados, $egresadoProyecto, $pagoPendiente, $anexo_III_Egresado, $formato_B_Aprobado_Egresado);
        $stmt4->execute();
        $stmt4->close();


        // Reemplaza el echo json_encode por un return
        return json_encode(['message' => 'Asignación de sinodales exitoso']);
    }

    $stmt1->close();
    $conn->close();
}
