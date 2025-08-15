<?php
require_once '../php/sesion.php';
require_once '../php/auth.php';
require_roles([2, 3, 5, 6]);
include '../php/include/meta.php';
include '../php/include/icons.php';
include '../php/include/headerUsuarios.php';
include '../php/include/menuUsuarios.php';
include '../php/include/footerUsuarios.php';

date_default_timezone_set('America/Denver');
$conn->query("SET time_zone='-06:00'");
?>

<!DOCTYPE html>
<html lang="es-MX">

<head>
  <?php echo $meta; ?>
  <meta name="description" content="Reporte de Registro de Autores" />
  <title>T-Soft - Reporte Registro de Autores</title>
  <?php echo $icons; ?>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/pages/baseTsoft.css" />
</head>

<body>
  <?php echo $header; ?>
  <?php echo $menu; ?>

  <div class="main-container">
    <main class="content col ps-md-2 pt-2">
      <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none">
        <i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable
      </a>
      <div class="page-header pt-3">
        <p class="h1">Reporte de registro de autores</p>
      </div>
      <hr />

      <div class="row">
        <?php if (isset($_SESSION['mensaje']) && $_SESSION['mensaje'] === "Reporte generado con éxito.") : ?>
          <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje']) && $_SESSION['mensaje'] === "Ocurrió un error al generar el reporte.") : ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
          </div>
        <?php endif; ?>

        <div class="col-12 mb-3">
          <div class="card mb-3">
            <div class="card-header">
              <h5 class="card-title">Generación de reporte de autores</h5>
            </div>
            <div class="card-body">
              <p class="card-text">Seleccione un rango de fechas para generar el reporte de autores titulados con producto tipo "Tesis".</p>
              <div class="mb-3">
                <form id="formato_Reporte_Autores" action="../php/generarReporteRegistroAutores.php">
                  <div class="row mb-3">
                    <label for="fecha_Ingreso_Reporte_Titulados" class="col-sm-2 col-form-label">Fecha de inicio:</label>
                    <div class="col-sm-10">
                      <input type="date" id="fecha_Ingreso_Reporte_Titulados" name="fecha_Ingreso_Reporte_Titulados" class="form-control" required>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="fecha_Egreso_Reporte_Titulados" class="col-sm-2 col-form-label">Fecha de fin:</label>
                    <div class="col-sm-10">
                      <input type="date" id="fecha_Egreso_Reporte_Titulados" name="fecha_Egreso_Reporte_Titulados" class="form-control" required>
                    </div>
                  </div>
                  <div class="row m-5 text-center justify-content-center align-items-center">
                    <div class="col"></div>
                    <div class="col">
                      <input id="btn_Generar_Reporte_Autores" class="btn btn-primary btn-block rounded-pill" name="btn_Generar_Reporte_Autores" type="submit" value="Generar reporte de autores" for="formato_Reporte_Autores" />
                    </div>
                    <div class="col"></div>
                  </div>
                </form>
              </div>
              <div class="table-responsive" style="max-height: 33.54rem; overflow-y: auto;">
                <table class="table table-bordered table-hover table-striped" id="tabla-reporte-autores">
                  <thead>
                    <tr>
                      <th>Reporte No.</th>
                      <th>Fecha creación</th>
                      <th>Fecha inicio</th>
                      <th>Fecha fin</th>
                      <th>Descarga</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <?php echo $footer; ?>
  </div>

  <!-- JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.onunload = function() {
      window.location.replace("../index.php");
    };
  </script>
  <script src="../js/obtenerReporteRegistroAutores.js"></script>
</body>

</html>
