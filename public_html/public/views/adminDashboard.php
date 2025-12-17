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
<html lang="es-MX">

<head>
  <!-- Etiquetas meta, íconos y otros... -->
  <?php echo $meta; ?>
  <meta name="description" content="Panel administrativo" />
  <title>T-Soft - Perfil administrativo</title>
  <?php echo $icons; ?>

  <!-- Hojas de estilo -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

  <!-- CSS Personalizado -->
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/components/sidebar.css">
  <link rel="stylesheet" href="../css/components/cards.css">
  <link rel="stylesheet" href="../css/components/tables.css">
  <link rel="stylesheet" href="../css/layout.css">
  <link rel="stylesheet" href="../css/pages/adminDashboard.css">
</head>

<body>
  <?php echo $header; ?>
  <?php echo $menu; ?>

  <?php if ($rol == 2) : ?>
    <!-- ROL 2: ADMINISTRADOR -->
    <div class="dashboard-body">
      <main class="dashboard-main" id="mainContent">
        <div class="container-fluid">
          <div class="main-header">
            <h2 class="main-title text-center">Bienvenido a su panel de administrador</h2>
            <hr>
          </div>

          <div class="main-content">
            <!-- Estadísticas -->
            <div class="row g-3 mb-4">
              <div class="col-md-4 col-sm-12">
                <div class="stats-card">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="stats-label">Trámites Pendientes</div>
                      <div class="stats-value" id="tramitesPendientes">--</div>
                      <div class="stats-trend">Cargando...</div>
                    </div>
                    <div class="stats-icon">
                      <i class="bi bi-hourglass-split"></i>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <div class="stats-card">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="stats-label">Ceremonias Programadas</div>
                      <div class="stats-value" id="ceremoniasProgramadas">--</div>
                      <div class="stats-trend" id="proximaCeremonia">Cargando...</div>
                    </div>
                    <div class="stats-icon">
                      <i class="bi bi-calendar-event"></i>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <div class="stats-card">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="stats-label">Documentos pendientes</div>
                      <div class="stats-value" id="documentosPendientes">--</div>
                      <div class="stats-trend" id="documentosHoy">Cargando...</div>
                    </div>
                    <div class="stats-icon">
                      <i class="bi bi-file-earmark-x"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Accesos rápidos -->
            <div class="row g-3">
              <div class="col-lg-12">
                <div class="quick-access">
                  <div class="quick-access-header">
                    <h3 class="quick-access-title text-center">Accesos Rápidos</h3>
                  </div>
                  <div class="quick-access-body">
                    <div class="row g-3">
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionDatos.php" class="quick-link">
                          <i class="bi bi-person-lines-fill"></i>
                          <span>Gestión de registros</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="formatosPendientes.php" class="quick-link">
                          <i class="bi bi-file-text"></i>
                          <span>Formatos B</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionSinodal.php" class="quick-link">
                          <i class="bi bi-people"></i>
                          <span>Asignar Sinodales</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="asignacionFechaCeremonia.php" class="quick-link">
                          <i class="bi bi-calendar-check"></i>
                          <span>Fechas Ceremonia</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="reporteConstanciaSinodalia.php" class="quick-link">
                          <i class="bi bi-file-earmark"></i>
                          <span>Constancias</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="reporteTitulados.php" class="quick-link">
                          <i class="bi bi-bar-chart"></i>
                          <span>Reportes</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionLibros&Fojas.php" class="quick-link">
                          <i class="bi bi-book"></i>
                          <span>Libros y Fojas</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="expedienteEgresados.php" class="quick-link">
                          <i class="bi bi-person-badge"></i>
                          <span>Expedientes</span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Documentos pendientes y Sinodales -->
            <div class="row g-3 mt-3">
              <div class="col-lg-6">
                <div class="documents-card">
                  <div class="documents-header">
                    <h3 class="documents-title">Documentos pendientes</h3>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="tablaDocumentosPendientes">
                      <thead class="table-light">
                        <tr>
                          <th>Carrera</th>
                          <th>Pendientes</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Se llenará dinámicamente -->
                      </tbody>
                    </table>
                  </div>
                  <div class="documents-footer">
                    <a href="gestionDocumentos.php">Ver todos los documentos</a>
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="sinodales-card">
                  <div class="sinodales-header">
                    <h3 class="sinodales-title">Asignación de sinodales</h3>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="tablaSinodales">
                      <thead class="table-light">
                        <tr>
                          <th>Carrera</th>
                          <th>Asignados</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Se llenará dinámicamente -->
                      </tbody>
                    </table>
                  </div>
                  <div class="sinodales-footer">
                    <a href="gestionSinodal.php">Asignar Sinodales</a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Calendario de Próximos Eventos -->
            <div class="calendar-card mt-3">
              <div class="calendar-header">
                <h3 class="calendar-title">Próximos Eventos</h3>
              </div>
              <div class="calendar-body" id="calendarioEventos">
                <!-- Se llenará dinámicamente -->
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

  <?php elseif ($rol == 3) : ?>
    <!-- ROL 3: SUPER ADMINISTRADOR -->
    <div class="dashboard-body">
      <main class="dashboard-main" id="mainContent">
        <div class="container-fluid">
          <div class="main-header">
            <h2 class="main-title text-center">Bienvenido a su pantalla de inicio</h2>
            <hr>
          </div>

          <div class="main-content">
            <!-- Estadísticas -->
            <div class="row g-3 mb-4">
              <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="stats-label">Trámites Pendientes</div>
                      <div class="stats-value" id="tramitesPendientes">--</div>
                      <div class="stats-trend">Cargando...</div>
                    </div>
                    <div class="stats-icon">
                      <i class="bi bi-hourglass-split"></i>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="stats-label">Titulados este semestre</div>
                      <div class="stats-value" id="tituladosSemestre">--</div>
                      <div class="stats-trend">Cargando...</div>
                    </div>
                    <div class="stats-icon">
                      <i class="bi bi-mortarboard"></i>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="stats-label">Ceremonias Programadas</div>
                      <div class="stats-value" id="ceremoniasProgramadas">--</div>
                      <div class="stats-trend">Cargando...</div>
                    </div>
                    <div class="stats-icon">
                      <i class="bi bi-calendar-event"></i>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="stats-label">Eficiencia Terminal</div>
                      <div class="stats-value" id="eficienciaTerminal">--</div>
                      <div class="stats-trend">Cargando...</div>
                    </div>
                    <div class="stats-icon">
                      <i class="bi bi-graph-up"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Accesos rápidos y tareas -->
            <div class="row g-3">
              <div class="col-lg-12">
                <div class="quick-access">
                  <div class="quick-access-header">
                    <h3 class="quick-access-title">Accesos Rápidos</h3>
                  </div>
                  <div class="quick-access-body">
                    <div class="row g-3">
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionDatos.php" class="quick-link">
                          <i class="bi bi-person-lines-fill"></i>
                          <span>Gestión de registros</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="formatosPendientes.php" class="quick-link">
                          <i class="bi bi-file-text"></i>
                          <span>Formatos B</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionSinodal.php" class="quick-link">
                          <i class="bi bi-people"></i>
                          <span>Asignar Sinodales</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="asignacionFechaCeremonia.php" class="quick-link">
                          <i class="bi bi-calendar-check"></i>
                          <span>Fechas Ceremonia</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="reporteConstanciaSinodalia.php" class="quick-link">
                          <i class="bi bi-file-earmark"></i>
                          <span>Constancias</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="reporteTitulados.php" class="quick-link">
                          <i class="bi bi-bar-chart"></i>
                          <span>Reportes</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionLibros&Fojas.php" class="quick-link">
                          <i class="bi bi-layers"></i>
                          <span>Gestión Fojas</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionVariablesGlobales.php" class="quick-link">
                          <i class="bi bi-gear"></i>
                          <span>Configuración</span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Calendario -->
            <div class="calendar-card mt-3">
              <div class="calendar-header">
                <h3 class="calendar-title">Próximos Eventos</h3>
              </div>
              <div class="calendar-body" id="calendarioEventos">
                <!-- Se llenará dinámicamente -->
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

  <?php elseif ($rol == 4) : ?>
    <!-- ROL 4: SECRETARIO -->
    <div class="dashboard-body">
      <main class="dashboard-main" id="mainContent">
        <div class="container-fluid">
          <div class="main-header">
            <h2 class="main-title text-center">Bienvenido a su panel de secretario</h2>
            <hr>
          </div>

          <div class="main-content">
            <!-- Acceso rápido grande -->
            <div class="row g-3">
              <div class="col-lg-12">
                <div class="quick-access" style="height: 475px;">
                  <div class="quick-access-header">
                    <h3 class="quick-access-title text-center">Accesos Rápidos</h3>
                  </div>
                  <div class="quick-access-body">
                    <div class="row g-3">
                      <div class="col-md-12">
                        <a href="expedienteEgresados.php" class="quick-link" style="height: 350px;">
                          <i class="bi bi-person-badge" style="font-size: 10rem;"></i>
                          <span style="font-size: 2rem;">Expedientes de egresados</span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

  <?php elseif ($rol == 5) : ?>
    <!-- ROL 5: AUXILIAR -->
    <div class="dashboard-body">
      <main class="dashboard-main" id="mainContent">
        <div class="container-fluid">
          <div class="main-header">
            <h2 class="main-title text-center">Bienvenido a su panel de auxiliar</h2>
            <hr>
          </div>

          <div class="main-content">
            <!-- Estadísticas -->
            <div class="row g-3 mb-4">
              <div class="col-md-6 col-sm-12">
                <div class="stats-card">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="stats-label">Trámites Pendientes</div>
                      <div class="stats-value" id="tramitesPendientes">--</div>
                      <div class="stats-trend">Cargando...</div>
                    </div>
                    <div class="stats-icon">
                      <i class="bi bi-hourglass-split"></i>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6 col-sm-12">
                <div class="stats-card">
                  <div class="d-flex justify-content-between">
                    <div>
                      <div class="stats-label">Documentos pendientes</div>
                      <div class="stats-value" id="documentosPendientes">--</div>
                      <div class="stats-trend">Cargando...</div>
                    </div>
                    <div class="stats-icon">
                      <i class="bi bi-file-earmark-x"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Accesos rápidos -->
            <div class="row g-3">
              <div class="col-lg-12">
                <div class="quick-access">
                  <div class="quick-access-header">
                    <h3 class="quick-access-title text-center">Accesos Rápidos</h3>
                  </div>
                  <div class="quick-access-body">
                    <div class="row g-3">
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionDocumentos.php" class="quick-link">
                          <i class="bi bi-folder-check"></i>
                          <span>Revisar documentos</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionSinodal.php" class="quick-link">
                          <i class="bi bi-people"></i>
                          <span>Asignar Sinodales</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="reporteConstanciaSinodalia.php" class="quick-link">
                          <i class="bi bi-file-earmark"></i>
                          <span>Constancias</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="actualizarTitulados.php" class="quick-link">
                          <i class="bi bi-person-check"></i>
                          <span>Actualizar titulados</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="reporteTitulados.php" class="quick-link">
                          <i class="bi bi-bar-chart"></i>
                          <span>Reportes</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionLibros&Fojas.php" class="quick-link">
                          <i class="bi bi-layers"></i>
                          <span>Gestión Fojas</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="gestionPassword.php" class="quick-link">
                          <i class="bi bi-key"></i>
                          <span>Restablecer contraseña</span>
                        </a>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <a href="expedienteEgresados.php" class="quick-link">
                          <i class="bi bi-person-badge"></i>
                          <span>Expedientes</span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Documentos y Sinodales -->
            <div class="row g-3 mt-3">
              <div class="col-lg-6">
                <div class="documents-card">
                  <div class="documents-header">
                    <h3 class="documents-title">Documentos pendientes</h3>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="tablaDocumentosPendientes">
                      <thead class="table-light">
                        <tr>
                          <th>Carrera</th>
                          <th>Pendientes</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Se llenará dinámicamente -->
                      </tbody>
                    </table>
                  </div>
                  <div class="documents-footer">
                    <a href="gestionDocumentos.php">Ver todos los documentos</a>
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="sinodales-card">
                  <div class="sinodales-header">
                    <h3 class="sinodales-title">Asignación de sinodales</h3>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover" id="tablaSinodales">
                      <thead class="table-light">
                        <tr>
                          <th>Carrera</th>
                          <th>Asignados</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Se llenará dinámicamente -->
                      </tbody>
                    </table>
                  </div>
                  <div class="sinodales-footer">
                    <a href="gestionSinodal.php">Asignar Sinodales</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

  <?php elseif ($rol == 6) : ?>
    <!-- ROL 6: OTRO AUXILIAR -->
    <div class="dashboard-body">
      <main class="dashboard-main" id="mainContent">
        <div class="container-fluid">
          <div class="main-header">
            <h2 class="main-title text-center">Bienvenido a su pantalla de inicio</h2>
            <hr>
          </div>
        </div>
      </main>
    </div>

  <?php endif; ?>

  <?php echo $footer; ?>

  <!-- Librerías de JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

  <!-- Sidebar -->
  <script src="../js/sidebar.js" defer></script>

  <script>
    window.onunload = function() {
      window.location.replace("../index.php");
    };
  </script>

  <!-- Scripts dinámicos según rol -->
  <?php if ($rol == 2 || $rol == 3 || $rol == 5): ?>
    <script src="../js/adminDashboard.js"></script>
  <?php endif; ?>

</body>

</html>