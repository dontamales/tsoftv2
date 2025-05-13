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
  <meta name="Reportes de cohortes generacionales" content="Base de estructura" />
  <title>T-Soft - Reportes de cohortes generacionales</title>
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
        <p class="h1">Reporte de cohortes generacionales</p>
      </div>
      <hr />
      <div class="row">
        <?php
        if (isset($_SESSION['mensaje']) && $_SESSION['mensaje'] === "Reporte generado con éxito.") : ?>
          <div class="alert alert-success" role="alert">
          <?php
          echo $_SESSION['mensaje'];
          unset($_SESSION['mensaje']);  // Eliminar el mensaje de la sesión para que no se muestre nuevamente

        endif; ?>
          <?php
          if (isset($_SESSION['mensaje']) && $_SESSION['mensaje'] === "Ocurrió un error al generar el reporte.") : ?>
            <div class="alert alert-danger" role="alert">
            <?php
            echo $_SESSION['mensaje'];
            unset($_SESSION['mensaje']);  // Eliminar el mensaje de la sesión para que no se muestre nuevamente
          endif; ?>
            <br>

            <div class="col-12 mb-3">
              <div class="card mb-3">
                <div class="card-header">
                  <h5 class="card-title">Generación de reporte de cohorte generacional</h5>
                </div>
                <div class="card-body">
                  <p class="card-text">En esta sección podrá subirse una lista de egresados de Servicios Escolares para verificar quiénes ya se titularon. <br><br><strong>Para que esto funcione se necesitará modificar previamente el Excel para que en el renglón 1 estén encabezados y partir del renglón 2 en adelante estén los contenidos como se muestran en el siguiente ejemplo:</strong></p>
                  <div style="max-height: 20rem; overflow-y: auto;">
                    <table class="table table-striped table-bordered table-hover" id="usuario-table">
                      <thead>
                        <tr>
                          <th>No. de Control</th>
                          <th>Nombre(s)</th>
                          <th>Apellidos</th>
                          <th>Carrera</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Relleno generico de la tabla -->
                        <tr>
                          <td>18112000</td>
                          <td>Francisco</td>
                          <td>Gómez Bolaños</td>
                          <td>Ingeniería Industrial</td>
                      </tbody>
                    </table>
                  </div>
                  <br>
                  <div class="mb-3">
                    <form id="formato_Periodo_Cohorte" action="../php/generarCohorteGeneracional.php" method="post" enctype="multipart/form-data">
                      <div class="row mb-3">
                        <label for="formato_Periodo_Cohorte" class="col-sm-2 col-form-label">Excel de egresados:</label>
                        <div class="col-sm-10">
                          <input type="file" class="form-control" id="archivo_Cohorte_Generacional" name="archivo_Cohorte_Generacional" accept=".xls,.xlsx" for="formato_Periodo_Cohorte">
                        </div>
                      </div>
                      <div class="row m-4 text-center justify-content-center align-items-center">
                        <div class="col">
                          <input id="btn_Generar_Cohorte" class="btn btn-primary btn-block rounded-pill" name="btn_Generar_Cohorte" type="submit" data-bs-toggle="Generar reporte de cohorte generacional" value="Generar reporte de cohorte generacional" for="formato_Periodo_Cohorte" />
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="table-responsive" style="max-height: 33.54rem; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-striped" id="tabla-cohortes">
                      <thead>
                        <tr>
                          <th>Cohorte No.</th>
                          <th>Fecha creación</th>
                          <th>Descarga</th>
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
  <script src="../js/obtenerCohortes.js"></script>
</body>

</html>