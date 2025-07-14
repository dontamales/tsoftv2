<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

?>

<!DOCTYPE html>
<html lang="es-MX"> <!-- LENGUAJE DE LA PÁGINA WEB (PARA TRADUCTORES) -->

<head>
  <!-- Etiquetas meta, íconos y otros... -->
  <?php echo $meta; ?>
  <meta name="description" content="Base de estructura" />
  <title>T-Soft - Perfil administrativo</title>
  <?php echo $icons; ?>

  <!-- Hojas de estilo... -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/pages/baseTsoft.css" />

  <!-- CSS Personalizado -->
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/components/sidebar.css">
  <link rel="stylesheet" href="../css/components/cards.css">
  <link rel="stylesheet" href="../css/components/tables.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/pages/adminDashboard.css">
</head>

<?php
if ($rol == 2) : ?>
  <!-- Contenido específico del administrador -->

  <?php echo $header; ?>
  <?php echo $menu; ?>
  <div class="main-container">
    <main id="mainContent" class="content col ps-md-2 pt-2">
      <!-- Esta parte ya no es necesaria, por los cambios en la sidebar JH20250710 -->
      <!-- <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable</a> -->
      <div class="page-header pt-3">
        <p class="h1 text-center">Bienvenido a su pantalla de inicio</p>
      </div>
      <hr />
    </main>
    <?php echo $footer; ?>
  </div>
<?php
elseif ($rol == 3) : ?>

  <!-- Contenido específico del super administrador -->
  <?php echo $header; ?>
  <?php echo $menu; ?>
  <div class="main-container">
    <main id="mainContent" class="content col ps-md-2 pt-2">
      <!-- Esta parte ya no es necesaria, por los cambios en la sidebar JH20250710 -->
      <!-- <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable</a> -->
      <div class="page-header pt-3">
        <div class="d-flex justify-content-center align-items-center h-100">
          <p class="h1 text-center">Bienvenido a su pantalla de inicio</p>
        </div>
      </div>

      <hr />

      <div class="text-center">
        <div class="text-center">
          <h3 class="display-4">¿Qué deseas hacer?</h3>
          <p class="lead">Selecciona una opción a continuación para comenzar.</p>
        </div>


        <div class="container h-400">
          <div class="row h-100 justify-content-center align-items-center">

            <style>
              .card:hover {
                transform: scale(1.05);
                transition: transform 0.3s ease;
              }
            </style>

            <!-- Cuadro grande 1 -->
            <div class="col-md-6 py-2">
              <a href="../views/gestionDatos.php" class="card btn btn-lg btn-block shadow-lg">
                <div class="card-body text-center">
                  <i class="bi bi-lightning" style="font-size: 3rem;"></i> <!-- Icono arriba -->
                  <h4 class="mt-3">Gestionar registros</h4> <!-- Texto -->
                  <p class="small">Accede a la sección de registros para gestionar datos.</p> <!-- Explicación con diferente letra -->
                </div>
              </a>
            </div>

            <!-- Cuadro grande 2 -->
            <div class="col-md-6 py-2">
              <a href="../views/formatosPendientes.php" class="card btn btn-lg btn-block shadow-lg">
                <div class="card-body text-center">
                  <i class="bi bi-files" style="font-size: 3rem;"></i> <!-- Icono arriba -->
                  <h4 class="mt-3">Formatos B</h4> <!-- Texto -->
                  <p class="small">Gestiona los formatos B pendientes de revisión.</p> <!-- Explicación con diferente letra -->
                </div>
              </a>
            </div>

            <!-- Cuadro grande 3 -->
            <div class="col-md-6 py-2">
              <a href="../views/gestionSinodal.php" class="card btn btn-lg btn-block shadow-lg">
                <div class="card-body text-center">
                  <i class="bi bi-people" style="font-size: 3rem;"></i> <!-- Icono arriba -->
                  <h4 class="mt-3">Asignación Sinodales</h4> <!-- Texto -->
                  <p class="small">Gestiona los sinodales que obtendra cada egresados.</p> <!-- Explicación con diferente letra -->
                </div>
              </a>
            </div>

            <!-- Cuadro grande 4 -->
            <div class="col-md-6 py-2">
              <a href="../views/asignacionFojaTitulado.php" class="card btn btn-lg btn-block shadow-lg">
                <div class="card-body text-center">
                  <i class="bi bi-file-earmark-plus" style="font-size: 3rem;"></i> <!-- Icono arriba -->
                  <h4 class="mt-3">Formatos Foja</h4> <!-- Texto -->
                  <p class="small">Asigna a los titulados su formato foja correspondiente.</p> <!-- Explicación con diferente letra -->
                </div>
              </a>
            </div>

          </div>
        </div>

      </div>
    </main>

    <?php echo $footer; ?>
  </div>

<?php
elseif ($rol == 4) : ?>
  <!-- Contenido específico del secretario -->
  <?php echo $header; ?>
  <?php echo $menu; ?>
  <div class="main-container">
    <main id="mainContent" class="content col ps-md-2 pt-2">
      <!-- Esta parte ya no es necesaria, por los cambios en la sidebar JH20250710 -->
      <!-- <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable</a> -->
      <div class="page-header pt-3">
        <p class="h1 text-center">Bienvenido a su pantalla de inicio</p>
      </div>
      <hr />
    </main>
    <?php echo $footer; ?>
  </div>
<?php
elseif ($rol == 5) : ?>

  <!-- Contenido específico del auxiliar -->
  <?php echo $header; ?>
  <?php echo $menu; ?>
  <div class="main-container">
    <main id="mainContent" class="content col ps-md-2 pt-2">
      <!-- Esta parte ya no es necesaria, por los cambios en la sidebar JH20250710 -->
      <!-- <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable</a> -->
      <div class="page-header pt-3">
        <p class="h1 text-center">Bienvenido a su pantalla de inicio</p>
      </div>
      <hr />
    </main>
    <?php echo $footer; ?>
  </div>
  </div>
<?php
elseif ($rol == 6) : ?>
  <!-- Contenido específico del auxiliar -->
  <?php echo $header; ?>
  <?php echo $menu; ?>
  <div class="main-container">
    <main id="mainContent" class="content col ps-md-2 pt-2">
      <!-- Esta parte ya no es necesaria, por los cambios en la sidebar JH20250710 -->
      <!-- <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable</a> -->
      <div class="page-header pt-3">
        <p class="h1 text-center">Bienvenido a su pantalla de inicio</p>
      </div>
      <hr />
    </main>
    <?php echo $footer; ?>
  </div>
  </div>
<?php endif; ?>

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