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
      <!-- <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable</a> -->
      <div class="page-header pt-3">
        <p class="h1 text-center">Reporte de cohortes generacionales</p>
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

        <!-- Mensaje genérico (info/warning) para casos como: "No hay titulados registrados para el año XXXX" -->
        <?php if (isset($_SESSION['mensaje']) && $_SESSION['mensaje'] !== "Reporte generado con éxito." && $_SESSION['mensaje'] !== "Ocurrió un error al generar el reporte.") : ?>
          <div class="alert alert-info" role="alert">
            <?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
          </div>
        <?php endif; ?>

        <div class="col-12 mb-3">
          <!-- Tabs -->
          <ul class="nav nav-tabs" id="tabsCohortes" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-archivo" data-bs-toggle="tab" data-bs-target="#panel-archivo" type="button" role="tab">Por archivo de egresados</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-anual" data-bs-toggle="tab" data-bs-target="#panel-anual" type="button" role="tab">Por año de ingreso</button>
            </li>
          </ul>

          <div class="tab-content border-start border-end border-bottom p-3">

            <!-- ======= PANEL ARCHIVO ======= -->
            <div class="tab-pane fade show active" id="panel-archivo" role="tabpanel">
              <div class="card mb-3">
                <div class="card-header">
                  <h5 class="card-title">Generación de reporte de cohorte generacional (archivo de egresados)</h5>
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
                        <label for="archivo_Cohorte_Generacional" class="col-sm-2 col-form-label">Excel de egresados:</label>
                        <div class="col-sm-10">
                          <input type="file" class="form-control" id="archivo_Cohorte_Generacional" name="archivo_Cohorte_Generacional" accept=".xls,.xlsx" required>
                        </div>
                      </div>
                      <div class="row m-4 text-center justify-content-center align-items-center">
                        <div class="col">
                          <input id="btn_Generar_Cohorte" class="btn btn-primary btn-block rounded-pill" name="btn_Generar_Cohorte" type="submit" value="Generar reporte de cohorte generacional" />
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="table-card-style" style="max-height: 33.54rem; overflow-y: auto;">
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

            <!-- ======= PANEL ANUAL ======= -->
            <div class="tab-pane fade" id="panel-anual" role="tabpanel">
              <div class="card mb-3">
                <div class="card-header">
                  <h5 class="card-title">Generación de reporte de titulados por año de ingreso</h5>
                </div>
                <div class="card-body">
                  <p class="card-text">Seleccione un año de ingreso para generar el reporte de todos los titulados que ingresaron en ese año.</p>
                  <div class="mb-3">
                    <form id="formato_Reporte_Anual" action="../php/generarReporteCohorteAnual.php">
                      <div class="row mb-3">
                        <label for="anio_Ingreso_Cohorte" class="col-sm-2 col-form-label">Año de ingreso:</label>
                        <div class="col-sm-10">
                          <input type="number" id="anio_Ingreso_Cohorte" name="anio_Ingreso_Cohorte" class="form-control" min="1966" max="<?= date('Y'); ?>" required>
                        </div>
                      </div>
                      <div class="row m-5 text-center justify-content-center align-items-center">
                        <div class="col"></div>
                        <div class="col">
                          <input id="btn_Generar_Reporte_Anual" class="btn btn-primary btn-block rounded-pill" name="btn_Generar_Reporte_Anual" type="submit" value="Generar reporte anual" />
                        </div>
                        <div class="col"></div>
                      </div>
                    </form>
                  </div>
                  <div class="table-card-style" style="max-height: 33.54rem; overflow-y: auto;">
                    <table class="table table-bordered table-hover table-striped" id="tabla-reporte-anual">
                      <thead>
                        <tr>
                          <th>Reporte No.</th>
                          <th>Fecha creación</th>
                          <th>Año de ingreso</th>
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
        </div>
    </main>

    <?php echo $footer; ?>
  </div>
  <!-- Librerías de JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
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

    // Validación del formulario de reporte anual
    const f = document.getElementById('formato_Reporte_Anual');
    if (f) {
      f.addEventListener('submit', e => {
        const anio = f.querySelector('input[name="anio_Ingreso_Cohorte"]').value;
        if (!anio) { 
          e.preventDefault(); 
          alert('Debes seleccionar un año.'); 
          return; 
        }
      });
    }
  </script>
  <script>
    // Mantener la pestaña activa según el parámetro 'tab' en la URL
    (function() {
      try {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab'); // 'anual' o 'archivo'
        if (tab === 'anual') {
          const el = document.querySelector('#tab-anual');
          if (el) new bootstrap.Tab(el).show();
        } else if (tab === 'archivo') {
          const el = document.querySelector('#tab-archivo');
          if (el) new bootstrap.Tab(el).show();
        }
      } catch (e) {
        // fallthrough silencioso
      }
    })();
  </script>
  <script src="../js/obtenerCohortes.js"></script>
  <script src="../js/obtenerReportesCohorteAnual.js"></script>
</body>

</html>