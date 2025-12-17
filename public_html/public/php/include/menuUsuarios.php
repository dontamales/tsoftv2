<?php
require_once 'nivelUsuario.php'; #NIVELES DE USUARIO

// Rol 1: Sustentante
if ($rol == 1) :
  $menu = '
  <!-- Botón para desplegar sidebar -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list" id="sidebarIcon"></i>
  </button>

  <!-- Overlay para móvil -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Sidebar para Sustentante -->
  <aside class="dashboard-sidebar" id="sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-title">Menú</div>
        <ul>
          <li><a href="userDashboard.php"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="formatoB.php"><i class="bi bi-file-text"></i> Formato B</a></li>
          <li><a href="cargarDocumentos.php"><i class="bi bi-folder-check"></i> Documentos pendientes</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
endif;

// Rol 2: Usuario Administrativo
if ($rol == 2) :
  $menu = '
  <!-- Botón para desplegar sidebar -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list" id="sidebarIcon"></i>
  </button>

  <!-- Overlay para móvil -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Sidebar para Usuario Administrativo -->
  <aside class="dashboard-sidebar" id="sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-title">Menú</div>
        <ul>
          <li><a href="adminDashboard.php"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="gestionDatos.php"><i class="bi bi-person-lines-fill"></i> Gestión de registros</a></li>
          <li><a href="formatosPendientes.php"><i class="bi bi-archive"></i> Gestión formatos B</a></li>
          <li><a href="gestionDocumentos.php"><i class="bi bi-card-checklist"></i> Gestión documentos recibidos</a></li>
          <li><a href="gestionSinodal.php"><i class="bi bi-people"></i> Asignación de sinodales</a></li>
          <li><a href="asignacionFechaCeremonia.php"><i class="bi bi-calendar3"></i> Asignación de fechas de ceremonia</a></li>
          <li><a href="actualizarTitulados.php"><i class="bi bi-person-check-fill"></i> Actualizar sustentantes a titulados</a></li>
          <li><a href="reporteConstanciaSinodalia.php"><i class="bi bi-file-earmark-text"></i> Constancia de sinodalias</a></li>
          <li><a href="reporteCohortesGeneracionales.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte cohortes generacionales</a></li>
          <li><a href="reporteTitulados.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte de titulados</a></li>
          <li><a href="reporteEficienciaTerminal.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte de eficiencia terminal</a></li>
          <li><a href="gestionLibros&Fojas.php"><i class="bi bi-file-earmark-text"></i> Gestión de libros y fojas</a></li>
          <li><a href="gestionPassword.php"><i class="bi bi-shield-exclamation"></i> Restablecer contraseña</a></li>
          <li><a href="expedienteEgresados.php"><i class="bi bi-person-lines-fill"></i> Expediente de egresado</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
endif;

// Rol 3: Super Usuario
if ($rol == 3) :
  $menu = '
  <!-- Botón para desplegar sidebar -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list" id="sidebarIcon"></i>
  </button>

  <!-- Overlay para móvil -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Sidebar para Super Usuario -->
  <aside class="dashboard-sidebar" id="sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-title">Menú</div>
        <ul>
          <li><a href="adminDashboard.php"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="gestionDatos.php"><i class="bi bi-person-lines-fill"></i> Gestión de registros</a></li>
          <li><a href="formatosPendientes.php"><i class="bi bi-archive"></i> Gestión formatos B</a></li>
          <li><a href="envioAnexosFallidoEgresado.php"><i class="bi bi-send-exclamation"></i> Envío de anexos I y II fallido</a></li>
          <li><a href="estatusFallidosFormatoB.php"><i class="bi bi-exclamation-diamond-fill"></i> Cambio de estatus fallido</a></li>
          <li><a href="gestionDocumentos.php"><i class="bi bi-card-checklist"></i> Gestión documentos recibidos</a></li>
          <li><a href="gestionSinodal.php"><i class="bi bi-people"></i> Asignación de sinodales</a></li>
          <li><a href="asignacionFechaCeremonia.php"><i class="bi bi-calendar3"></i> Asignación de fechas de ceremonia</a></li>
          <li><a href="actualizarTitulados.php"><i class="bi bi-person-check-fill"></i> Actualizar sustentantes a titulados</a></li>
          <li><a href="reporteConstanciaSinodalia.php"><i class="bi bi-file-earmark-text"></i> Constancia de sinodalias</a></li>
          <li><a href="reporteCohortesGeneracionales.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte cohortes generacionales</a></li>
          <li><a href="reporteTitulados.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte de titulados</a></li>
          <li><a href="reporteEficienciaTerminal.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte de eficiencia terminal</a></li>
          <li><a href="gestionLibros&Fojas.php"><i class="bi bi-file-earmark-text"></i> Gestión de fojas</a></li>
          <li><a href="asignacionFojaTitulado.php"><i class="bi bi-file-earmark-plus"></i> Asignación de Foja</a></li>
          <li><a href="gestionarLibroProductoT.php"><i class="bi bi-pencil-square"></i> Gestionar Libro</a></li>
          <li><a href="gestionPassword.php"><i class="bi bi-shield-exclamation"></i> Restablecer contraseña</a></li>
          <li><a href="expedienteEgresados.php"><i class="bi bi-person-lines-fill"></i> Expediente de egresado</a></li>
          <li><a href="gestionVariablesGlobales.php"><i class="bi bi-list-ol"></i> Definir variables globales</a></li>
          <li><a href="asignacionSuperUsuario.php"><i class="bi bi-person-badge-fill"></i> Asignación de super usuario</a></li>
          <li><a href="membretesDocumentos.php"><i class="bi bi-card-image"></i> Membretes para los documentos</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
endif;

// Rol 4: Secretario
if ($rol == 4) :
  $menu = '
  <!-- Botón para desplegar sidebar -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list" id="sidebarIcon"></i>
  </button>

  <!-- Overlay para móvil -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  
  <!-- Sidebar para Secretario -->
  <aside class="dashboard-sidebar" id="sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-title">Menú</div>
        <ul>
          <li><a href="adminDashboard.php"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="reporteConstanciaSinodalia.php"><i class="bi bi-file-earmark"></i> Constancia de sinodalias</a></li>
          <li><a href="gestionDocumentos.php"><i class="bi bi-folder"></i> Gestión documentos recibidos</a></li>
          <li><a href="expedienteEgresados.php"><i class="bi bi-person-badge"></i> Expediente de egresado</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
endif;

// Rol 5: Coordinador
if ($rol == 5) :
  $menu = '
  <!-- Botón para desplegar sidebar -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list" id="sidebarIcon"></i>
  </button>

  <!-- Overlay para móvil -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Sidebar para Coordinador -->
  <aside class="dashboard-sidebar" id="sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-title">Menú</div>
        <ul>
          <li><a href="adminDashboard.php"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="gestionDocumentos.php"><i class="bi bi-card-checklist"></i> Gestión documentos recibidos</a></li>
          <li><a href="gestionSinodal.php"><i class="bi bi-people"></i> Asignación de sinodales</a></li>
          <li><a href="actualizarTitulados.php"><i class="bi bi-person-check-fill"></i> Actualizar sustentantes a titulados</a></li>
          <li><a href="reporteConstanciaSinodalia.php"><i class="bi bi-file-earmark-text"></i> Constancia de sinodalias</a></li>
          <li><a href="reporteCohortesGeneracionales.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte cohortes generacionales</a></li>
          <li><a href="reporteTitulados.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte de titulados</a></li>
          <li><a href="reporteEficienciaTerminal.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte de eficiencia terminal</a></li>
          <li><a href="gestionLibros&Fojas.php"><i class="bi bi-file-earmark-text"></i> Gestión de fojas</a></li>
          <li><a href="gestionPassword.php"><i class="bi bi-shield-exclamation"></i> Restablecer contraseña</a></li>
          <li><a href="expedienteEgresados.php"><i class="bi bi-person-lines-fill"></i> Expediente de egresado</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
endif;

// Rol 6: Auxiliar
if ($rol == 6) :
  $menu = '
  <!-- Botón para desplegar sidebar -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list" id="sidebarIcon"></i>
  </button>

  <!-- Overlay para móvil -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Sidebar para Auxiliar -->
  <aside class="dashboard-sidebar" id="sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-title">Menú</div>
        <ul>
          <li><a href="adminDashboard.php"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="gestionLibros&Fojas.php"><i class="bi bi-file-earmark-text"></i> Gestión de fojas</a></li>
          <li><a href="reporteTitulados.php"><i class="bi bi-file-earmark-spreadsheet"></i> Reporte de titulados</a></li>
          <li><a href="expedienteEgresados.php"><i class="bi bi-person-lines-fill"></i> Expediente de egresado</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
endif;

// Script para manejar el año actual dinámicamente
$menu .= '
<script>
  document.getElementById("currentYear").textContent = new Date().getFullYear();
</script>
';
?>