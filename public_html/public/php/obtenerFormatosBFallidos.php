<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php";

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT e.Num_Control, u.Nombres_Usuario, u.Apellidos_Usuario, e.FK_Estatus_Egresado
                       FROM egresado AS e
                       INNER JOIN usuario AS u ON e.Fk_Usuario_Egresado = u.id_usuario
                       INNER JOIN anexo_i_ii AS a ON e.Num_Control = a.Fk_Egresado_Anexo_I_II
                       WHERE e.Formato_B_Aprobado_Egresado = 1 AND e.FK_Estatus_Egresado = 4");
$stmt->execute();
$result = $stmt->get_result();

$egresadoEstatus = [];

while ($row = $result->fetch_assoc()) {
    $egresadoEstatus[] = [
        'id' => $row['Num_Control'],
        'nombre' => $row['Nombres_Usuario'],
        'apellido' => $row['Apellidos_Usuario'],
        'estatus' => $row['FK_Estatus_Egresado']
    ];
}

echo json_encode($egresadoEstatus);

$stmt->close();
$conn->close();
