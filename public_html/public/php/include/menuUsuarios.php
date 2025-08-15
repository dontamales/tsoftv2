<?php
require_once 'nivelUsuario.php'; #NIVELES DE USUARIO

$menu = '<div class="container-fluid">
  <div class="row flex-nowrap">
  <div class="col-auto px-0">
      <div id="sidebar" class="collapse collapse-horizontal border-end">
        <div
          id="sidebar-nav"
          class="list-group border-0 rounded-0 text-sm-start min-vh-100"
        >
        <a
            href="' . $inicio . '"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-house"></i>
            <span>Inicio</span>
          </a>';

if ($rol == 1) :
  $menu .= '
          <a
            href="formatoB.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-lines-fill"></i>
            <span>Formato B</span>
            </a>
          <a
            href="cargarDocumentos.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-break"></i>
            <span>Documentos pendientes</span>
            </a>
        </div>
      </div>
    </div>';
endif;
if ($rol == 2) :
  $menu .= '
          <a
            href="gestionDatos.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-lines-fill"></i>
            <span>Gestión de registros</span>
            </a>
          <a
            href="formatosPendientes.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-archive"></i>
            <span>Gestión formatos B</span>
          </a>
          <a
            href="gestionDocumentos.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-card-checklist"></i>
            <span>Gestión documentos recibidos</span>
            </a>
            <a
            href="gestionSinodal.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-people"></i>
            <span>Asignación de sinodales</span>
            </a>
          <a
            href="asignacionFechaCeremonia.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-calendar3"></i>
            <span>Asignación de fechas de ceremonia</span>
            </a>
            <a
            href="actualizarTitulados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-check-fill"></i>
            <span>Actualizar sustentantes a titulados</span>
            </a>
            <a
            href="reporteConstanciaSinodalia.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-text"></i>
            <span>Constancia de sinodalias</span>
            </a>
            <a
            href="reporteCohortesGeneracionales.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte cohortes generacionales</span>
            </a>
            <a
            href="reporteTitulados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte de titulados</span>
            </a>
            <a
            href="reporteEficienciaTerminal.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte de eficiencia terminal</span>
            </a>
            <a
            href="gestionLibros&Fojas.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-text"></i>
            <span>Gestión de libros y fojas</span>
            </a>
            <a
            href="gestionPassword.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-shield-exclamation"></i>
            <span>Restablecer contraseña</span>
            </a>
            <a
            href="expedienteEgresados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-lines-fill"></i>
            <span>Expediente de egresado</span>
            </a>
        </div>
      </div>
    </div>';
endif;
if ($rol == 3) :
  $menu .= '
          <a
            href="gestionDatos.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-lines-fill"></i>
            <span>Gestión de registros</span>
          </a>
          <a
            href="formatosPendientes.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-archive"></i>
            <span>Gestión formatos B</span>
          </a>
          <a
            href="envioAnexosFallidoEgresado.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-send-exclamation"></i>
            <span>Envío de anexos I y II fallido</span>
            </a>
          <a
            href="estatusFallidosFormatoB.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-exclamation-diamond-fill"></i>
            <span>Cambio de estatus fallido</span>
            </a>
          <a
            href="gestionDocumentos.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-card-checklist"></i>
            <span>Gestión documentos recibidos</span>
            </a>
            <a
            href="gestionSinodal.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-people"></i>
            <span>Asignación de sinodales</span>
            </a>
          <a
            href="asignacionFechaCeremonia.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-calendar3"></i>
            <span>Asignación de fechas de ceremonia</span>
            </a>
            <a
            href="actualizarTitulados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-check-fill"></i>
            <span>Actualizar sustentantes a titulados</span>
            </a>
            <a
            href="reporteConstanciaSinodalia.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-text"></i>
            <span>Constancia de sinodalias</span>
            </a>
            <a
            href="reporteRegistroAutores.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-text"></i>
            <span>Reporte del registro de autores</span>
            </a>
            <a
            href="reporteCohortesGeneracionales.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte cohortes generacionales</span>
            </a>
            <a
            href="reporteTitulados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte de titulados</span>
            </a>
            <a
            href="reporteEficienciaTerminal.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte de eficiencia terminal</span>
            </a>
            <a
            href="gestionLibros&Fojas.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-text"></i>
            <span>Gestión de fojas</span>
            </a>
            <a
            href="asignacionFojaTitulado.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-plus"></i>
            <span>Asignacion de Foja</span>
            </a>
            <a
            href="gestionarLibroProductoT.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-pencil-square"></i>
            <span>Gestionar Libro</span>
            </a>
            <a
            href="gestionPassword.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-shield-exclamation"></i>
            <span>Restablecer contraseña</span>
            </a>
            <a
            href="expedienteEgresados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-lines-fill"></i>
            <span>Expediente de egresado</span>
            </a>
            <a
            href="gestionVariablesGlobales.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-list-ol"></i>
            <span>Definir variables globales</span>
            </a>
            <a
            href="asignacionSuperUsuario.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-badge-fill"></i>
            <span>Asignación de super usuario</span>
            </a>
            <a
            href="membretesDocumentos.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-card-image"></i>
            <span>Membretes para los documentos</span>
            </a>
        </div>
      </div>
    </div>';
endif;
if ($rol == 4) :
  $menu .= '
          <a
            href="reporteConstanciaSinodalia.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-text"></i>
            <span>Constancia de sinodalias</span>
          </a>
          <a
            href="gestionDocumentos.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-card-checklist"></i>
            <span>Gestión documentos recibidos</span>
          </a>
          <a
            href="expedienteEgresados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-lines-fill"></i>
            <span>Expediente de egresado</span>
            </a>
        </div>
      </div>
    </div>';
endif;
if ($rol == 5) :
  $menu .= '
          <a
            href="gestionDocumentos.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-card-checklist"></i>
            <span>Gestión documentos recibidos</span>
          </a>
          <a
            href="gestionSinodal.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-people"></i>
            <span>Asignación de sinodales</span>
            </a>
            <a
            href="actualizarTitulados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-check-fill"></i>
            <span>Actualizar sustentantes a titulados</span>
            </a>
            <a
            href="reporteConstanciaSinodalia.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-text"></i>
            <span>Constancia de sinodalias</span>
          </a>
          <a
            href="reporteCohortesGeneracionales.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte cohortes generacionales</span>
            </a>
            <a
            href="reporteTitulados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte de titulados</span>
            </a>
            <a
            href="reporteEficienciaTerminal.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte de eficiencia terminal</span>
            </a>
            <a
            href="gestionLibros&Fojas.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-text"></i>
            <span>Gestión de fojas</span>
            </a>
            <a
            href="gestionPassword.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-shield-exclamation"></i>
            <span>Restablecer contraseña</span>
            </a>
            <a
            href="expedienteEgresados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-lines-fill"></i>
            <span>Expediente de egresado</span>
            </a>
        </div>
      </div>
    </div>';
endif;
if ($rol == 6) :
  $menu .= '
            <a
            href="gestionLibros&Fojas.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-text"></i>
            <span>Gestión de fojas</span>
            </a>
            <a
            href="reporteTitulados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Reporte de titulados</span>
            </a>
            <a
            href="expedienteEgresados.php"
            class="list-group-item border-end-0 d-inline-block text-truncate"
            data-bs-parent="#sidebar">
            <i class="bi bi-person-lines-fill"></i>
            <span>Expediente de egresado</span>
            </a>
        </div>
      </div>
    </div>';
endif;