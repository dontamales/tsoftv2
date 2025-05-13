<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
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
  <title>T-Soft - Base de la estructura de una página</title>
  <?php echo $icons; ?>

  <!-- Hojas de estilo... -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/pages/baseTsoft.css" />
</head>

<body class>
  <?php echo $header; ?>

  <?php echo $menu; ?>

  <div class="main-container">
    <main class="content col ps-md-2 pt-2">
      <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable</a>
      <div class="page-header pt-3">
        <p class="h1">Actualizar sustentantes al estatus de titulados</p>
      </div>
      <hr />
      <div class="row">
        <?php
        if (isset($_SESSION['mensaje']) && $_SESSION['mensaje'] === "Lista de titulados generada con éxito.") : ?>
          <div class="alert alert-success" role="alert">
          <?php
          echo $_SESSION['mensaje'];
          unset($_SESSION['mensaje']);  // Eliminar el mensaje de la sesión para que no se muestre nuevamente

        endif; ?>
          <?php
          if (isset($_SESSION['mensaje']) && $_SESSION['mensaje'] === "Ocurrió un error al generar la lista de titulados.") : ?>
            < class="alert alert-danger" role="alert">
            <?php
            echo $_SESSION['mensaje'];
            unset($_SESSION['mensaje']);  // Eliminar el mensaje de la sesión para que no se muestre nuevamente
          endif; ?>
            <br>
            <div class="col-12 mb-3">
              <div class="card mb-3">
                <div class="card-header">
                  <h5 class="card-title">Actualizar la tabla de los titulados</h5>
                </div>
                <div class="card-body">
                  <p class="card-text">Al presionar el siguiente botón se actualiza la lista de los egresados titulados en la base de datos siempre y cuando su fecha de ceremonia ya haya pasado y ya haya entregado todos sus documentos.</p>
                  <div class="mb-1">
                    <form id="formato_Actualizar_Titulados" action="../php/actualizarListaTitulados.php" method="post">
                      <div class="row m-5 text-center justify-content-center align-items-center">
                        <div class="col">
                          <input id="btn_Actualizar_Titulados" class="btn btn-primary btn-block rounded-pill" name="btn_Actualizar_Titulados" type="submit" data-bs-toggle="Actualizar tabla de egresados" value="Actualizar tabla de egresados" for="formato_Actualizar_Titulados" />
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="table-responsive" style="max-height: 33.54rem; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-striped" id="tabla-actualizar-titulados">
                      <thead>
                        <tr>
                          <th>No. Control</th>
                          <th>Nombre(s)</th>
                          <th>Apellidos</th>
                          <th>Carrera</th>
                          <th>Fecha de ceremonia</th>
                          <th>Correo</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
    </main>

    <?php echo $footer; ?>
  </div>
  <!-- Librerías de JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

  <!-- Scripts propios -->
  <script>
    window.onunload = function() {
      // Esto es para que cuando se cierre la pestaña, se cierre la sesión
      window.location.replace("../index.php");
    };
  </script>
  <script src="../js/obtenerTitulados.js"></script>
</body>

</html>