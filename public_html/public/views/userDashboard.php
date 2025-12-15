<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO SUSTENTANTE
require_roles([1]); #VERIFICACIÓN DE USUARIO SUSTENTANTE
require_once("../../private/conexion.php");

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

$usuario_actual = $_SESSION['user_id'];

// Consulta para obtener estatus y datos de egresado, carrera y departamento
$stmt = $conn->prepare("SELECT 
  egresado.FK_Estatus_Egresado, 
  estatus.Descripcion_Estatus, 
  egresado.Fecha_Hora_Ceremonia_Egresado,
  egresado.Fk_Carrera_Egresado,
  carrera.Fk_Departamento_Carrera,
  departamento.Correo_Proyecto_Departamento
FROM usuario
LEFT JOIN egresado ON usuario.Id_Usuario = egresado.Fk_Usuario_Egresado
LEFT JOIN estatus ON egresado.FK_Estatus_Egresado = estatus.Id_Estatus
LEFT JOIN carrera ON egresado.Fk_Carrera_Egresado = carrera.Id_Carrera
LEFT JOIN departamento ON carrera.Fk_Departamento_Carrera = departamento.Id_Departamento
WHERE usuario.Id_Usuario = ?");
$stmt->bind_param("i", $usuario_actual);
$stmt->execute();
$result = $stmt->get_result();
$estatus = $result->fetch_assoc();
$estatusUsuario = $estatus['Descripcion_Estatus'];
$estatusUsuarioID = $estatus['FK_Estatus_Egresado'];
$correoDepartamento = $estatus['Correo_Proyecto_Departamento'];
$stmt->close();

// Seleccionar variables globales
$stmt2 = $conn->prepare("SELECT * FROM variables_globales WHERE Id_Variables_Globales = 1");
$stmt2->execute();
$result2 = $stmt2->get_result();
$variablesGlobales = $result2->fetch_assoc();
$precio_Examen_Profesional = $variablesGlobales['Precio_Examen_Profesional_Variables_Globales'];
$stmt2->close();

// Fecha con formato (día/mes/año)
$fecha = date("d/m/Y");

include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA
?>

<!DOCTYPE html>
<html lang="es-MX">

<head>
  <!-- Etiquetas meta, íconos y otros... -->
  <?php echo $meta; ?>
  <meta name="description" content="Panel de sustentante" />
  <title>T-Soft - Sustentante: Inicio</title>
  <?php echo $icons; ?>

  <!-- Hojas de estilo -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

  <!-- CSS Personalizado -->
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/components/sidebar.css">
  <link rel="stylesheet" href="../css/components/cards.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/pages/userDashboard.css">
</head>

<body>
  <?php echo $header; ?>

  <?php echo $menu; ?>

  <div class="dashboard-body">
    <main class="dashboard-main" id="mainContent">
      <div class="container-fluid">
        <div class="main-header">
          <h2 class="main-title text-center">Bienvenido a su panel de usuario</h2>
          <hr>
        </div>

        <div class="main-content">
          <!-- Estado del trámite -->
          <div class="row g-3 mb-4">
            <div class="col-md-12">
              <div class="stats-card">
                <div class="d-flex justify-content-center align-items-center">
                  <div class="text-center">
                    <div class="stats-label">Estado de su trámite</div>
                    <div class="stats-value"><?php echo $estatusUsuario; ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Descripción del estatus -->
          <div class="row g-3 mb-4">
            <div class="col-12">
              <div class="card">
                <div class="card-header text-white" style="background-color: #1F2937;">
                  <h5 class="mb-0">DESCRIPCIÓN</h5>
                </div>
                <div class="card-body">
                  <?php
                  // Muestra mensajes personalizados según el estatus del usuario
                  if ($estatusUsuarioID == 1) {
                    echo "<p>Su formato B se encuentra pendiente de enviar por usted, favor de dirigirse al menú desplegable y acceder a 'Formato B', completarlo y enviarlo lo más pronto posible y de mantenerse al pendiente esta página acerca de alguna actualización en su proceso de titulación.</p>";
                  } elseif ($estatusUsuarioID == 2) {
                    echo "<p>Su formato B se encuentra en revisión por Coordinación de Titulación, debe mantenerse al pendiente mediante esta página acerca de alguna actualización en su proceso de titulación.</p>";
                  } elseif ($estatusUsuarioID == 3) {
                    echo "<p>Su envío de formato B ha sido rechazado, debe de verificar su información y hacer el reenvío su formato B lo más pronto posible, favor de mantenerse al pendiente mediante esta página acerca de alguna actualización en su proceso de titulación. <br><br><strong>Si se le rechaza el formato B más de una vez y la información está correcta, contacte a Coordinación de Titulación con la información al final de esta página.</strong></p>";
                  } elseif ($estatusUsuarioID == 4) {
                    echo "<p>Sus anexos I & II se encuentran pendientes de ser enviados a su Departamento Académico por Coordinación de Titulación, favor de mantenerse al pendiente mediante su correo registrado (verifique la carpeta de SPAM) y/o esta página acerca de alguna actualización en su proceso de titulación.</p>";
                  } elseif ($estatusUsuarioID == 5) {
                    if (!empty($correoDepartamento)) {
                      echo "<p>Tu formato B ha sido aprobado.<br>Por favor, espera comunicación de tu departamento académico desde el correo <strong>$correoDepartamento</strong> en un plazo de 5 días hábiles escolares para recibir tu anexo III. Si no recibes respuesta en ese plazo, comunícate directamente a ese correo.<br>En cuanto tengas el anexo III, súbelo en la sección de documentos.<br>La entrega de tus documentos para el trámite de titulación está disponible, procura entregar todos lo más pronto posible. <strong>Se aconseja priorizar la entrega del anexo III firmado y sellado.</strong></p>";
                    } else {
                      echo "<p>Tu formato B ha sido aprobado. Por favor, espera comunicación de tu departamento académico para recibir tu anexo III. Si no recibes respuesta en 5 días hábiles escolares, comunícate con Coordinación de Titulación.<br>En cuanto tengas el anexo III, súbelo en la sección de documentos.<br>La entrega de tus documentos para el trámite de titulación está disponible, procura entregar todos lo más pronto posible. <strong>Se aconseja priorizar la entrega del anexo III firmado y sellado.</strong></p>";
                    }
                  } elseif ($estatusUsuarioID == 6) {
                    echo "<p>Su Anexo III ha sido aprobado por un administrador, favor de acudir de inmediato a Coordinación de Titulación por su autorización de pago del examen profesional que al día de hoy " . $fecha . " es de $" . $precio_Examen_Profesional . " MXN y se prosiga con la asignación de su fecha de ceremonia. Favor de mantenerse al pendiente mediante su correo registrado (verifique la carpeta de SPAM) y/o esta página acerca de alguna actualización en su proceso de titulación. <br><br><strong>Si tiene documentos pendientes no olvide subirlos lo antes posible.</strong></p>";
                  } elseif ($estatusUsuarioID == 7) {
                    echo "<p>Ya se le han asignado sus sinodales en la plataforma, la asignación de su fecha y hora de ceremonia está pendiente, favor de mantenerse al tanto mediante su correo registrado (verifique la carpeta de SPAM) y/o esta página acerca de alguna actualización en su proceso de titulación.</p>";
                  } elseif ($estatusUsuarioID == 8) {
                    $fechaCeremonia = $estatus['Fecha_Hora_Ceremonia_Egresado'];
                    echo "<p>Su fecha y hora de ceremonia han sido asignadas, favor de mantenerse al pendiente mediante su correo registrado (verifique la carpeta de SPAM) y/o esta página acerca de alguna actualización de su fecha y hora de ceremonia. <br><br><strong>Si tiene documentos pendientes no olvide entregarlos lo más pronto posible, de lo contrario no se le podrá seguir con su trámite de titulación.<br><br><br>Fecha de ceremonia actualizada: $fechaCeremonia</strong></p>";
                  } elseif ($estatusUsuarioID == 9) {
                    echo "<p><strong>Ahora se encuentra en la lista de titulados, los desarrolladores de T-Soft le decimos: ¡muchas felicidades!</strong></p>";
                  } else {
                    echo "<p>Estimado usuario, su estatus actual no es reconocido, acudir a Coordinación de Titulación.</p>";
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Accesos rápidos y Estado de documentos -->
          <div class="row g-3">
            <!-- Accesos rápidos -->
            <div class="col-lg-6">
              <div class="quick-access">
                <div class="quick-access-header">
                  <h3 class="quick-access-title">Accesos Rápidos</h3>
                </div>
                <div class="quick-access-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <a href="formatoB.php" class="quick-link">
                        <i class="bi bi-file-text"></i>
                        <span>Formato B</span>
                      </a>
                    </div>
                    <div class="col-md-6">
                      <a href="cargarDocumentos.php" class="quick-link">
                        <i class="bi bi-upload"></i>
                        <span>Subir documentos</span>
                      </a>
                    </div>
                    <div class="col-md-12">
                      <a href="#" class="quick-link help-link">
                        <i class="bi bi-question-circle"></i>
                        <span>Ayuda</span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <?php if ($estatusUsuarioID >= 5): ?>
              <!-- Estado de documentos -->
              <div class="col-lg-6">
                <div class="tasks-card">
                  <div class="tasks-header">
                    <h3 class="tasks-title">Estado de tus documentos</h3>
                  </div>
                  <div class="document-list">
                    <table class="table table-sm" id="tablaDocumentosEgresado">
                      <thead>
                        <tr>
                          <th>Documento</th>
                          <th>Estado</th>
                          <th>Fecha</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
  <?php echo $footer; ?>

  <!-- Librerías de JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

  <!-- Sidebar -->
  <script src="../js/sidebar.js"></script>

  <!-- Scripts propios -->
  <script>
    window.onunload = function() {
      window.location.replace("../index.php");
    };
  </script>
  <script src="../js/obtenerDocumentosDashboard.js"></script>
</body>

</html>