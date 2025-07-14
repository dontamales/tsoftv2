<?php
require_once '../php/sesion.php'; #verifiqueCIÓN DE SESIÓN
require_once '../php/auth.php'; #verifiqueCIÓN DE USUARIO SUSTENTANTE
require_roles([1]); #verifiqueCIÓN DE USUARIO SUSTENTANTE
require_once("../../private/conexion.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");


$usuario_actual = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT egresado.FK_Estatus_Egresado, estatus.Descripcion_Estatus, egresado.Fecha_Hora_Ceremonia_Egresado
                        FROM usuario
                        LEFT JOIN egresado ON usuario.Id_Usuario = egresado.Fk_Usuario_Egresado
                        LEFT JOIN estatus ON egresado.FK_Estatus_Egresado = estatus.Id_Estatus
                        WHERE usuario.Id_Usuario = ?");
$stmt->bind_param("i", $usuario_actual);
$stmt->execute();
$result = $stmt->get_result();
$estatus = $result->fetch_assoc();
$estatusUsuario = $estatus['Descripcion_Estatus'];
$estatusUsuarioID = $estatus['FK_Estatus_Egresado'];
$stmt->close();

// $stmt para seleccionar todos las columnas de la tabla de variables globales
$stmt2 = $conn->prepare("SELECT * FROM variables_globales WHERE Id_Variables_Globales = 1");
$stmt2->execute();
$result2 = $stmt2->get_result();
$variablesGlobales = $result2->fetch_assoc();

// Variables globales de  Precio_Examen_Profesional_Variables_Globales
$precio_Examen_Profesional = $variablesGlobales['Precio_Examen_Profesional_Variables_Globales'];

//fecha con formato (día/mes/año)
$fecha = date("d/m/Y");

include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA
?>

<!DOCTYPE html>
<html lang="es-MX"> <!-- LENGUAJE DE LA PÁGINA WEB (PARA TRADUCTORES) -->

<head>
  <!-- Etiquetas meta, íconos y otros... -->
  <?php echo $meta; ?>
  <meta name="description" content="Pestaña de sustentante" />
  <title>T-Soft - Sustentante: Inicio</title>
  <?php echo $icons; ?>

  <!-- Hojas de estilo... -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/pages/baseTsoft.css" />

  <style>
    /* Estilos adicionales para personalización */
    .main-container {
      padding: 20px;
    }

    .page-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .status-text {
      font-size: 18px;
      font-weight: bold;
      color: #007bff;
    }

    .instruction-text {
      text-align: center;
      margin-top: 30px;
      font-size: 20px;
      font-weight: bold;
    }
  </style>

  <!-- CSS Personalizado -->
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/components/sidebar.css">
  <link rel="stylesheet" href="../css/components/cards.css">
  <link rel="stylesheet" href="../css/components/tables.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/pages/adminDashboard.css">
</head>

<body class>
  <?php echo $header; ?>

  <?php echo $menu; ?>

  <div class="main-container">
    <main id="mainContent" class="content col ps-md-2 pt-2">
      <!-- Esta parte ya no es necesaria, por los cambios en la sidebar JH20250710 -->
      <!-- <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i>Menú
        desplegable</a> -->
      <div class="page-header pt-3">
        <h1 class="display-4">Bienvenido a su pantalla de inicio</h1>
      </div>
      <hr />
      <div class="row">
        <div class="col-12">
          <div class="text-center">
            <h2 class="mb-4">Su estatus de titulación actual:</h2>
            <div class="status-container">
              <p class="status-text"><?php echo $estatusUsuario; ?></p>
            </div>
            <p class="instruction-text">D E S C R I P C I Ó N</p>
            <?php
            // Muestra mensajes personalizados según el estatus del usuario
            if ($estatusUsuarioID == 1) {
              echo "<p>Su formato B se encuentra pendiente de enviar por usted, favor de dirigirse al menú desplegable y acceder a ''Formato B'', completarlo y enviarlo lo más pronto posible y de matenerse al pendiente esta página acerca de alguna actualización en su proceso de titulación.</p>";

            } elseif ($estatusUsuarioID == 2) {
              echo "<p>Su formato B se encuetra en revisión por Coordinación de Titulación, debe matenerse al pendiente mediante esta página acerca de alguna actualización en su proceso de titulación.</p>";

            } elseif ($estatusUsuarioID == 3) {
              echo "<p>Su envío de formato B ha sido rechazado, debe de verificar su información y hacer el reenvío su formato B lo más pronto posible, favor de matenerse al pendiente mediante esta página acerca de alguna actualización en su proceso de titulación. <br><br><strong>Si se le rechaza el formato B más de una vez y la información está correcta, contacte a Coordinación de Titulación con la información al final de esta página.</strong></p>";
              
            } elseif ($estatusUsuarioID == 4) {
              echo "<p>Sus anexos I & II se encuentran pendientes de ser enviados a su Departamento Académico por Coordinación de Titulación, favor de matenerse al pendiente mediante su correo registrado (verifique la carpeta de SPAM) y/o esta página acerca de alguna actualización en su proceso de titulación.</p>";

            } elseif ($estatusUsuarioID == 5) {
              echo "<p>Tu formato B ha sido aprobado, favor de esperar a que tu departamento académico se comunique contigo para asignarte sinodales y darte el anexo III.<br>La entrega de sus documentos para el trámite de titulación está disponible, procure entregar todos lo más pronto posible, <strong>se aconseja priorizar la entrega del anexo III firmado y sellado.</strong></p>";

            } elseif ($estatusUsuarioID == 6) {
              echo "<p>Su Anexo III ha sido aprobado por un administrador, favor de acudir de inmediato a Coordinación de Titulación por su autorización de pago del examen profesional que al día de hoy " . $fecha . " es de $" . $precio_Examen_Profesional . " MXN y se prosiga con la asignación de su fecha de ceremonia. Favor de matenerse al pendiente mediante su correo registrado (verifique la carpeta de SPAM) y/o esta página acerca de alguna actualización en su proceso de titulación. <br><br><strong>Si tiene documentos pendientes no olvide subirlos lo antes posible.</strong></p>";

            } elseif ($estatusUsuarioID == 7) {
              echo "<p>Ya se le han asignado sus sinodales en la plataforma, la asignación de su fecha y hora de ceremonia está pendiente, favor de matenerse al tanto mediante su correo registrado (verifique la carpeta de SPAM) y/o esta página acerca de alguna actualización en su proceso de titulación.</p>";
              
            } elseif ($estatusUsuarioID == 8) {
              $fechaCeremonia = $estatus['Fecha_Hora_Ceremonia_Egresado'];
              echo "<p>Su fecha y hora de ceremonia han sido asignadas, favor de matenerse al pendiente mediante su correo registrado (verifique la carpeta de SPAM) y/o esta página acerca de alguna actualización de su fecha y hora de ceremonia. <br><br><strong>Si tiene documentos pendientes no olvide entregarlos lo más pronto posible, de lo contrario no se le podrá seguir con su trámite de titulación.<br><br><br>Fecha de ceremonia actualizada: $fechaCeremonia</strong></p>";
               
              
            } elseif ($estatusUsuarioID == 9) {
              echo "<p><strong>Ahora se encuentra en la lista de titulados, los desarrolladores de T-Soft le decimos: ¡muchas felicidades!</strong></p>";
              
            }

            else {
              echo "<p>Estimado usuario, su estatus actual no es reconocido, acudir a Coordinación de Titulación.</p>";
            }
            ?>
          </div>
        </div>
      </div>
    </main>
    <?php echo $footer; ?>
  </div>



  <!-- Librerías de JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

  <!-- Scripts propios -->
  <!-- Sidebar JH20250710 -->
  <script src="../js/sidebar.js" defer></script>
  <script>
    window.onunload = function() {
      // Esto es para que cuando se cierre la pestaña, se cierre la sesión
      window.location.replace("../index.php");
    };
  </script>
</body>

</html>