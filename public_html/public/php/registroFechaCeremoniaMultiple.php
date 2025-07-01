<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once '../../private/conexion.php';
require_once 'enviarCorreos.php';
require_once '../vendor/autoload.php'; #LIBRERÍA SENDGRID

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seleccionados = json_decode($_POST['seleccionados']);
    $fechaHora = $_POST['fechaHora'];
    //Fecha y hora actual
    $fechaActual = date('Y-m-d H:i:s');
    $fecha_Ceremonia_Estatus = 8;
    $sinodales_Asignados_Estatus = 7;
    $anexo_III_Egresado = 1;
    $formato_B_Aprobado_Egresado = 1;

    // Esta parte del código ya no es necesaria, ya que a partir de ahora se enviarán correos electrónicos a través de phpmailer
    // if (verificarLimiteCorreo($conn) == 100) {
    //     echo json_encode(['success' => false, 'message' => 'Se ha alcanzado el límite de correos enviados por día.']);
    //     exit();
    // }

    $conn->begin_transaction();

    try {

        if ($fechaHora == null || $fechaHora == '0000-00-00 00:00:00' || $fechaHora == '' || $fechaHora < $fechaActual){
            echo json_encode(["error" => "No se ha seleccionado una fecha y hora válida, verifique que la fecha y hora seleccionada sea menor a la fecha y hora actual y que no esté vacía."]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE egresado 
        SET Fecha_Hora_Ceremonia_Egresado = ?, FK_Estatus_Egresado = ? 
        WHERE Num_Control = ? 
        AND (FK_Estatus_Egresado = ?
        OR FK_Estatus_Egresado = ?)
        AND Anexo_III_Egresado = ?
        AND Formato_B_Aprobado_Egresado = ?");

        foreach ($seleccionados as $numControl) {
            $stmt->bind_param("sisiiii", $fechaHora, $fecha_Ceremonia_Estatus, $numControl, $sinodales_Asignados_Estatus, $fecha_Ceremonia_Estatus, $anexo_III_Egresado, $formato_B_Aprobado_Egresado);
            $stmt->execute();

            // Query para obtener los datos del egresado
            $stmt_e = $conn->prepare("SELECT * FROM egresado JOIN usuario ON egresado.Fk_Usuario_Egresado = usuario.Id_Usuario WHERE Num_Control = ?");
            $stmt_e->bind_param('s', $numControl);
            $stmt_e->execute();
            $result = $stmt_e->get_result();
            $egresadoData = $result->fetch_assoc();

            $count = 0;

            do {
                $response = enviarCorreo(
                    $conn,

                    $egresadoData['Correo_Usuario'],

                    $egresadoData['Nombres_Usuario'] . " " . $egresadoData['Apellidos_Usuario'],

                    'Fecha y hora de ceremonia de titulación asignada.',

                    'La fecha y hora de su ceremonia de titulación es la siguiente: '. $egresadoData['Fecha_Hora_Ceremonia_Egresado'] .', en caso de cualquier cambio inesperado favor de estar atento a su correo y a la plataforma de http://login.tsoft.website/.',

                    'La fecha y hora de su ceremonia de titulación es la siguiente: <strong>'. $egresadoData['Fecha_Hora_Ceremonia_Egresado'] .'</strong>, en caso de cualquier cambio inesperado favor de estar atento a su correo y a la plataforma de http://login.tsoft.website/.'
                );

                if ($response->statusCode == 200) {
                    $count = 3;
                } else {
                    $count++;
                }
            } while ($count < 3);
        }

        $conn->commit();
        $response = ["message" => "Fechas actualizadas exitosamente"];

        echo json_encode($response);

    } catch (Exception $e) {

        $conn->rollback();
        echo json_encode(["error" => $e->getMessage()]);
    }

    $stmt->close();
    $stmt_e->close();
    $conn->close();
}
