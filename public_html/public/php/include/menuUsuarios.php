<?php
require_once 'nivelUsuario.php'; #NIVELES DE USUARIO

$menu = '
    <!-- Botón para desplegar sidebar -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="bi bi-list" id="sidebarIcon"></i>
    </button>

    <!-- Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
';

// $menu = '<div class="container-fluid">
//   <div class="row flex-nowrap">
//   <div class="col-auto px-0">
//       <div id="sidebar" class="collapse collapse-horizontal border-end">
//         <div
//           id="sidebar-nav"
//           class="list-group border-0 rounded-0 text-sm-start min-vh-100"
//         >
//         <a
//             href="' . $inicio . '"
//             class="list-group-item border-end-0 d-inline-block text-truncate"
//             data-bs-parent="#sidebar">
//             <i class="bi bi-house"></i>
//             <span>Inicio</span>
//           </a>';

if ($rol == 1) :
  // Sidebar para el Sustentante JH20250714
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
          <li><a href="userDashboard.php" class="active"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="formatoB.php"><i class="bi bi-file-text"></i> Formato B</a></li>
          <li><a href="cargarDocumentos.php"><i class="bi bi-folder-check"></i> Documentos pendientes</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft v2.0 © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
  // $menu .= '
  //         <a
  //           href="formatoB.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-lines-fill"></i>
  //           <span>Formato B</span>
  //           </a>
  //         <a
  //           href="cargarDocumentos.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-break"></i>
  //           <span>Documentos pendientes</span>
  //           </a>
  //       </div>
  //     </div>
  //   </div>';
endif;
if ($rol == 2) :
  // Sidebar para el Administrador JH20250714
  $menu = '
  <!-- Botón para desplegar sidebar -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list" id="sidebarIcon"></i>
  </button>

  <!-- Overlay para móvil -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Sidebar para Administrador -->
  <aside class="dashboard-sidebar" id="sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-title">Principal</div>
        <ul>
          <li><a href="adminDashboard.php" class="active"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="gestionDatos.php"><i class="bi bi-person-lines-fill"></i> Gestión de registros</a></li>
          <li><a href="formatosPendientes.php"><i class="bi bi-file-text"></i> Gestión formatos B</a></li>
          <li><a href="gestionDocumentos.php"><i class="bi bi-folder"></i> Gestión documentos recibidos</a></li>
        </ul>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Gestión</div>
        <ul>
          <li><a href="gestionSinodal.php"><i class="bi bi-people"></i> Asignación de sinodales</a></li>
          <li><a href="asignacionFechaCeremonia.php"><i class="bi bi-calendar-event"></i> Asignación de fechas de ceremonia</a></li>
          <li><a href="actualizarTitulados.php"><i class="bi bi-person-check"></i> Actualizar sustentantes a titulados</a></li>
        </ul>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Reportes</div>
        <ul>
          <li><a href="reporteConstanciaSinodalia.php"><i class="bi bi-file-earmark"></i> Constancia de sinodalias</a></li>
          <li><a href="reporteCohortesGeneracionales.php"><i class="bi bi-bar-chart"></i> Cohortes generacionales</a></li>
          <li><a href="reporteTitulados.php"><i class="bi bi-award"></i> Reporte de titulados</a></li>
          <li><a href="reporteEficienciaTerminal.php"><i class="bi bi-graph-up"></i> Reporte de eficiencia terminal</a></li>
        </ul>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Administración</div>
        <ul>
          <li><a href="gestionLibros&Fojas.php"><i class="bi bi-book"></i> Gestión de libros y fojas</a></li>
          <li><a href="gestionPassword.php"><i class="bi bi-key"></i> Restablecer contraseña</a></li>
          <li><a href="expedienteEgresados.php"><i class="bi bi-person-badge"></i> Expediente de egresado</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft v2.0 © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
  // $menu .= '
  //         <a
  //           href="gestionDatos.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-lines-fill"></i>
  //           <span>Gestión de registros</span>
  //           </a>
  //         <a
  //           href="formatosPendientes.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-archive"></i>
  //           <span>Gestión formatos B</span>
  //         </a>
  //         <a
  //           href="gestionDocumentos.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-card-checklist"></i>
  //           <span>Gestión documentos recibidos</span>
  //           </a>
  //           <a
  //           href="gestionSinodal.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-people"></i>
  //           <span>Asignación de sinodales</span>
  //           </a>
  //         <a
  //           href="asignacionFechaCeremonia.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-calendar3"></i>
  //           <span>Asignación de fechas de ceremonia</span>
  //           </a>
  //           <a
  //           href="actualizarTitulados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-check-fill"></i>
  //           <span>Actualizar sustentantes a titulados</span>
  //           </a>
  //           <a
  //           href="reporteConstanciaSinodalia.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-text"></i>
  //           <span>Constancia de sinodalias</span>
  //           </a>
  //           <a
  //           href="reporteCohortesGeneracionales.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte cohortes generacionales</span>
  //           </a>
  //           <a
  //           href="reporteTitulados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte de titulados</span>
  //           </a>
  //           <a
  //           href="reporteEficienciaTerminal.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte de eficiencia terminal</span>
  //           </a>
  //           <a
  //           href="gestionLibros&Fojas.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-text"></i>
  //           <span>Gestión de libros y fojas</span>
  //           </a>
  //           <a
  //           href="gestionPassword.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-shield-exclamation"></i>
  //           <span>Restablecer contraseña</span>
  //           </a>
  //           <a
  //           href="expedienteEgresados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-lines-fill"></i>
  //           <span>Expediente de egresado</span>
  //           </a>
  //       </div>
  //     </div>
  //   </div>';
endif;
if ($rol == 3) :
  // Sidebar para el SuperAdministrador JH20250710
  $menu = '
  <!-- Botón para desplegar sidebar -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list" id="sidebarIcon"></i>
  </button>

  <!-- Overlay para móvil -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  
  <!-- Sidebar Para SuperAdministrador -->
  <aside class="dashboard-sidebar" id="sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-title">Principal</div>
          <ul>
            <li><a href="adminDashboard.php" class="active"><i class="bi bi-house-door"></i> Inicio</a></li>
            <li><a href="gestionDatos.php"><i class="bi bi-person-lines-fill"></i> Gestión de registros</a></li>
            <li><a href="formatosPendientes.php"><i class="bi bi-file-text"></i> Gestión formatos B</a></li>
          </ul>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Gestión</div>
          <ul>
            <li><a href="envioAnexosFallidoEgresado.php"><i class="bi bi-paperclip"></i> Envío de anexos</a></li>
            <li><a href="estatusFallidosFormatoB.php"><i class="bi bi-arrow-repeat"></i> Cambio de estatus</a></li>
            <li><a href="gestionDocumentos.php"><i class="bi bi-folder"></i> Gestión documentos recibidos</a></li>
            <li><a href="gestionSinodal.php"><i class="bi bi-people"></i> Asignación de sinodales</a></li>
            <li><a href="asignacionFechaCeremonia.php"><i class="bi bi-calendar-event"></i> Fechas de ceremonia</a></li>
          </ul>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Reportes</div>
          <ul>
            <li><a href="actualizarTitulados.php"><i class="bi bi-person-check"></i> Actualizar sustentantes</a></li>
            <li><a href="reporteConstanciaSinodalia.php"><i class="bi bi-file-earmark"></i> Constancia de sinodalias</a></li>
            <li><a href="reporteCohortesGeneracionales.php"><i class="bi bi-bar-chart"></i> Cohortes generacionales</a></li>
            <li><a href="reporteTitulados.php"><i class="bi bi-award"></i> Reporte de titulados</a></li>
            <li><a href="reporteEficienciaTerminal.php"><i class="bi bi-graph-up"></i> Eficiencia terminal</a></li>
          </ul>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Administración</div>
          <ul>
            <li><a href="gestionLibros&Fojas.php"><i class="bi bi-layers"></i> Gestión de fojas</a></li>
            <li><a href="asignacionFojaTitulado.php"><i class="bi bi-file-plus"></i> Asignación de Foja</a></li>
            <li><a href="gestionarLibroProductoT.php"><i class="bi bi-book"></i> Gestionar Libro</a></li>
            <li><a href="gestionPassword.php"><i class="bi bi-key"></i> Restablecer contraseña</a></li>
            <li><a href="expedienteEgresados.php"><i class="bi bi-person-badge"></i> Expediente de egresado</a></li>
            <li><a href="gestionVariablesGlobales.php"><i class="bi bi-gear"></i> Variables globales</a></li>
            <li><a href="asignacionSuperUsuario.php"><i class="bi bi-person-plus"></i> Super usuario</a></li>
            <li><a href="membretesDocumentos.php"><i class="bi bi-file-earmark-text"></i> Membretes</a></li>
          </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft v2.0 © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
  // $menu .= '
  //         <a
  //           href="gestionDatos.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-lines-fill"></i>
  //           <span>Gestión de registros</span>
  //         </a>
  //         <a
  //           href="formatosPendientes.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-archive"></i>
  //           <span>Gestión formatos B</span>
  //         </a>
  //         <a
  //           href="envioAnexosFallidoEgresado.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-send-exclamation"></i>
  //           <span>Envío de anexos I y II fallido</span>
  //           </a>
  //         <a
  //           href="estatusFallidosFormatoB.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-exclamation-diamond-fill"></i>
  //           <span>Cambio de estatus fallido</span>
  //           </a>
  //         <a
  //           href="gestionDocumentos.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-card-checklist"></i>
  //           <span>Gestión documentos recibidos</span>
  //           </a>
  //           <a
  //           href="gestionSinodal.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-people"></i>
  //           <span>Asignación de sinodales</span>
  //           </a>
  //         <a
  //           href="asignacionFechaCeremonia.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-calendar3"></i>
  //           <span>Asignación de fechas de ceremonia</span>
  //           </a>
  //           <a
  //           href="actualizarTitulados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-check-fill"></i>
  //           <span>Actualizar sustentantes a titulados</span>
  //           </a>
  //           <a
  //           href="reporteConstanciaSinodalia.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-text"></i>
  //           <span>Constancia de sinodalias</span>
  //           </a>
  //           <a
  //           href="reporteCohortesGeneracionales.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte cohortes generacionales</span>
  //           </a>
  //           <a
  //           href="reporteTitulados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte de titulados</span>
  //           </a>
  //           <a
  //           href="reporteEficienciaTerminal.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte de eficiencia terminal</span>
  //           </a>
  //           <a
  //           href="gestionLibros&Fojas.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-text"></i>
  //           <span>Gestión de fojas</span>
  //           </a>
  //           <a
  //           href="asignacionFojaTitulado.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-plus"></i>
  //           <span>Asignacion de Foja</span>
  //           </a>
  //           <a
  //           href="gestionarLibroProductoT.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-pencil-square"></i>
  //           <span>Gestionar Libro</span>
  //           </a>
  //           <a
  //           href="gestionPassword.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-shield-exclamation"></i>
  //           <span>Restablecer contraseña</span>
  //           </a>
  //           <a
  //           href="expedienteEgresados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-lines-fill"></i>
  //           <span>Expediente de egresado</span>
  //           </a>
  //           <a
  //           href="gestionVariablesGlobales.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-list-ol"></i>
  //           <span>Definir variables globales</span>
  //           </a>
  //           <a
  //           href="asignacionSuperUsuario.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-badge-fill"></i>
  //           <span>Asignación de super usuario</span>
  //           </a>
  //           <a
  //           href="membretesDocumentos.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-card-image"></i>
  //           <span>Membretes para los documentos</span>
  //           </a>
  //       </div>
  //     </div>
  //   </div>';
endif;
if ($rol == 4) :
  // Sidebar para el Secretario JH20250714
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
          <li><a href="adminDashboard.php" class="active"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="reporteConstanciaSinodalia.php"><i class="bi bi-file-earmark"></i> Constancia de sinodalias</a></li>
          <li><a href="gestionDocumentos.php"><i class="bi bi-folder"></i> Gestión documentos recibidos</a></li>
          <li><a href="expedienteEgresados.php"><i class="bi bi-person-badge"></i> Expediente de egresado</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft v2.0 © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
  // $menu .= '
  //         <a
  //           href="reporteConstanciaSinodalia.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-text"></i>
  //           <span>Constancia de sinodalias</span>
  //         </a>
  //         <a
  //           href="gestionDocumentos.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-card-checklist"></i>
  //           <span>Gestión documentos recibidos</span>
  //         </a>
  //         <a
  //           href="expedienteEgresados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-lines-fill"></i>
  //           <span>Expediente de egresado</span>
  //           </a>
  //       </div>
  //     </div>
  //   </div>';
endif;
if ($rol == 5) :
  // Sidebar para el Auxiliar JH20250714
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
        <div class="sidebar-section-title">Principal</div>
        <ul>
          <li><a href="adminDashboard.php" class="active"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="gestionDocumentos.php"><i class="bi bi-folder"></i> Gestión documentos recibidos</a></li>
          <li><a href="gestionSinodal.php"><i class="bi bi-people"></i> Asignación de sinodales</a></li>
        </ul>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Reportes</div>
        <ul>
          <li><a href="actualizarTitulados.php"><i class="bi bi-person-check"></i> Actualizar sustentantes a titulados</a></li>
          <li><a href="reporteConstanciaSinodalia.php"><i class="bi bi-file-earmark"></i> Constancia de sinodalias</a></li>
          <li><a href="reporteCohortesGeneracionales.php"><i class="bi bi-bar-chart"></i> Cohortes generacionales</a></li>
          <li><a href="reporteTitulados.php"><i class="bi bi-award"></i> Reporte de titulados</a></li>
          <li><a href="reporteEficienciaTerminal.php"><i class="bi bi-graph-up"></i> Eficiencia terminal</a></li>
        </ul>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Administración</div>
        <ul>
          <li><a href="gestionLibros&Fojas.php"><i class="bi bi-layers"></i> Gestión de fojas</a></li>
          <li><a href="gestionPassword.php"><i class="bi bi-key"></i> Restablecer contraseña</a></li>
          <li><a href="expedienteEgresados.php"><i class="bi bi-person-badge"></i> Expediente de egresado</a></li>
        </ul>
      </div>
    </nav>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft v2.0 © <span id="currentYear">2025</span></p>
    </div>
  </aside>
  ';
  // $menu .= '
  //         <a
  //           href="gestionDocumentos.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-card-checklist"></i>
  //           <span>Gestión documentos recibidos</span>
  //         </a>
  //         <a
  //           href="gestionSinodal.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-people"></i>
  //           <span>Asignación de sinodales</span>
  //           </a>
  //           <a
  //           href="actualizarTitulados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-check-fill"></i>
  //           <span>Actualizar sustentantes a titulados</span>
  //           </a>
  //           <a
  //           href="reporteConstanciaSinodalia.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-text"></i>
  //           <span>Constancia de sinodalias</span>
  //         </a>
  //         <a
  //           href="reporteCohortesGeneracionales.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte cohortes generacionales</span>
  //           </a>
  //           <a
  //           href="reporteTitulados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte de titulados</span>
  //           </a>
  //           <a
  //           href="reporteEficienciaTerminal.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte de eficiencia terminal</span>
  //           </a>
  //           <a
  //           href="gestionLibros&Fojas.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-text"></i>
  //           <span>Gestión de fojas</span>
  //           </a>
  //           <a
  //           href="gestionPassword.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-shield-exclamation"></i>
  //           <span>Restablecer contraseña</span>
  //           </a>
  //           <a
  //           href="expedienteEgresados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-lines-fill"></i>
  //           <span>Expediente de egresado</span>
  //           </a>
  //       </div>
  //     </div>
  //   </div>';
endif;
if ($rol == 6) :
  // Sidebar para un rol que no existe y nadie tiene ni una sola cuenta JH20250714
  $menu = '
  <!-- Botón para desplegar sidebar -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list" id="sidebarIcon"></i>
  </button>

  <!-- Overlay para móvil -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Sidebar para un rol que no existe -->
  <aside class="dashboard-sidebar" id="sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-section">
        <div class="sidebar-section-title">Menú</div>
        <ul>
          <li><a href="adminDashboard.php" class="active"><i class="bi bi-house-door"></i> Inicio</a></li>
          <li><a href="gestionLibros&Fojas.php"><i class="bi bi-layers"></i> Gestión de fojas</a></li>
          <li><a href="reporteTitulados.php"><i class="bi bi-award"></i> Reporte de titulados</a></li>
          <li><a href="expedienteEgresados.php"><i class="bi bi-person-badge"></i> Expediente de egresados</a></li>
        </ul>
      </div>

    <div class="sidebar-footer">
      <p class="sidebar-footer-text">T-Soft v2.0 © <span id="currentYear">2025</span></p>
    </div>
  </aside>

  ';
  // $menu .= '
  //           <a
  //           href="gestionLibros&Fojas.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-text"></i>
  //           <span>Gestión de fojas</span>
  //           </a>
  //           <a
  //           href="reporteTitulados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-file-earmark-spreadsheet"></i>
  //           <span>Reporte de titulados</span>
  //           </a>
  //           <a
  //           href="expedienteEgresados.php"
  //           class="list-group-item border-end-0 d-inline-block text-truncate"
  //           data-bs-parent="#sidebar">
  //           <i class="bi bi-person-lines-fill"></i>
  //           <span>Expediente de egresado</span>
  //           </a>
  //       </div>
  //     </div>
  //   </div>';
endif;