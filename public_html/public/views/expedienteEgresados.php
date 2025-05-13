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
        <p class="h1">Expedientes de los egresados</p>
      </div>
      <hr />
      <div class="row">
        <div class="col-12 mb-3">
          <div class="card mb-3">
            <div class="card-header">
              <h5 class="card-title">Expediente del sustentante seleccionado</h5>
            </div>
            <div class="card-body">
              <p class="card-text">Al seleccionar un sustentante y presionar el botón se podrá visualizar toda la información en la plataforma relacionada con el mismo.</p>
              <div class="mb-1">
                <div class="row m-5 text-center justify-content-center align-items-center">
                  <div class="col">
                    <div class="mb-3">
                      <div class="col-1">
                        <label for="inputUsuarioExpediente" class="form-label">Sustentante:</label>
                      </div>
                      <input id="inputUsuarioExpediente" type="text" class="form-control" autocomplete="off">
                      <input type="hidden" id="selectedUsuarioId" name="selectedUsuarioId">
                      <div id="listContainer" class="list-group"></div>
                    </div>
                    <div class="mb-3">
                      <input id="btn_Expediente_Egresado" class="btn btn-primary btn-block rounded-pill" name="btn_Expediente_Egresado" type="submit" data-bs-toggle="Ver expediente de egresado" value="Ver expediente de egresado" for="formato_Expediente_Egresados" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb-1">
                <br>
                <hr>
                <p class="h2">Estatus del sustentante</p><br>
                <label for="estatusEgresadoExp" class="form-label"><strong>Estatus:</strong></label>
                <input type="text" id="estatusEgresadoExp" name="estatusEgresadoExp" class="form-control h1" value="" required disabled>
                <form id="modificarEstatusEgresadoExp">
                <label for="modificarEstatusEgresadoExp" class="form-label"><strong>Modificar estatus:</strong></label>
                  <select id="selectModificarEstatusEgresadoExp" name="selectModificarEstatusEgresadoExp" class="form-control h1" disabled>
                    <option value="">Seleccione una opción</option>
                    <option value="1">Envío de formato B pendiente</option>
                    <option value="2">Revisión de formato B pendiente</option>
                    <option value="3">Rechazo de formato B (reenvío pendiente)</option>
                    <option value="4">Envío de anexos I y II a Servicios Académicos pendiente</option>
                    <option value="5">Anexo III pendiente</option>
                    <option value="6">Pago de autorización de examen profesional pendiente</option>
                    <option value="7">Asignación de sinodales realizada</option>
                    <option value="8">Fecha de ceremonia asignada</option>
                    <option value="9">Titulado</option>
                  </select>
                  <div class="mb-1 text-center justify-content-center align-items-center">
                    <button type="submit" id="btnModificarEstatusEgresadoExp" class="btn btn-warning " for="modificarEstatusEgresadoExp" disabled><strong>Modificar estatus</strong></button>
                  </div>
                </form>
              </div>
              <div class="mb-1">
                <br>
                <hr>
                <p class="h2">Formato B</p><br>
                <div class="row">
                  <div class="border col-lg-6">
                    <h5 class="card-title mt-2">Información personal:</h5>
                    <div class="row mb-3">
                      <label for="nombresFormatoBExp" class="col-sm-2 col-form-label">Nombres:</label>
                      <div class="col-sm-10">
                        <input type="text" id="nombresFormatoBExp" name="nombresFormatoBExp" class="form-control" value="" required autocomplete="name" readonly disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="apellidosFormatoBExp" class="col-sm-2 col-form-label">Apellidos:</label>
                      <div class="col-sm-10">
                        <input type="text" id="apellidosFormatoBExp" name="apellidosFormatoBExp" class="form-control" value="" required autocomplete="family-name" readonly disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="generoFormatoBExp" class="col-sm-2 col-form-label">Género:</label>
                      <div class="col-sm-10">
                        <div>
                          <input type="radio" id="generoHombreFormatoBExp" name="generoFormatoBExp" value="Hombre" class="form-check-input" disabled>
                          <label for="generoHombreFormatoBExp" class="form-check-label">Hombre</label>
                        </div>
                        <div>
                          <input type="radio" id="generoMujerFormatoBExp" name="generoFormatoBExp" value="Mujer" class="form-check-input" disabled>
                          <label for="generoMujerFormatoBExp" class="form-check-label">Mujer</label>
                        </div>
                        <div>
                          <input type="radio" id="generoIndefinidoFormatoBExp" name="generoFormatoBExp" value="Indefinido" class="form-check-input" disabled>
                          <label for="generoIndefinidoFormatoBExp" class="form-check-label">Indefinido</label>
                        </div>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="edadFormatoBExp" class="col-sm-2 col-form-label">Edad:</label>
                      <div class="col-sm-10">
                        <input type="number" id="edadFormatoBExp" name="edadFormatoBExp" class="form-control" step="0" min="0" max="100" value="" required readonly disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="celularFormatoBExp" class="col-sm-2 col-form-label">Celular:</label>
                      <div class="col-sm-10">
                        <input type="tel" id="celularFormatoBExp" name="celularFormatoBExp" class="form-control" value="" required pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" autocomplete="tel-national" readonly disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="telefonoFormatoBExp" class="col-sm-2 col-form-label">Teléfono:</label>
                      <div class="col-sm-10">
                        <input type="tel" id="telefonoFormatoBExp" name="telefonoFormatoBExp" class="form-control" value="" required pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" autocomplete="tel-national" readonly disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Dirección:</label>
                      <div class="col-sm-10">
                        <div class="direccionFormatoB-group">
                          <label for="codigo_postalFormatoBExp">Código Postal:</label>
                          <input type="number" id="codigo_postalFormatoBExp" name="codigo_postalFormatoBExp" class="form-control" step="00000" maxlength="5" value="" required autocomplete="postal-code" readonly disabled>
                        </div>
                        <div class="direccionFormatoB-group">
                          <label for="coloniaFormatoBExp">Colonia:</label>
                          <input type="text" id="coloniaFormatoBExp" name="coloniaFormatoBExp" class="form-control" value="" required readonly disabled>
                        </div>
                        <div class="direccionFormatoB-group">
                          <label for="calleFormatoBExp">Calle:</label>
                          <input type="text" id="calleFormatoBExp" name="calleFormatoBExp" class="form-control" value="" required autocomplete="street-address" readonly disabled>
                        </div>
                        <div class="direccionFormatoB-group">
                          <label for="num_extFormatoBExp">Num. Ext.:</label>
                          <input type="text" id="num_extFormatoBExp" name="num_extFormatoBExp" class="form-control" value="" required readonly disabled>
                        </div>
                        <div class="direccionFormatoB-group">
                          <label for="num_intFormatoBExp">Num. Int. (si no cuentas con uno Ingrese 0):</label>
                          <input type="text" id="num_intFormatoBExp" name="num_intFormatoBExp" class="form-control" value="" readonly disabled>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="border col-lg-6">
                    <h5 class="card-title mt-2">Información escolar:</h5>
                    <div class="row mb-3">
                      <label for="numero_controlFormatoBExp" class="col-sm-2 col-form-label">Número de control:</label>
                      <div class="col-sm-10">
                        <input type="text" id="numero_controlFormatoBExp" name="numero_controlFormatoBExp" class="form-control" value="" required readonly disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="carreraFormatoBExp" class="col-sm-2 col-form-label">Carrera:</label>
                      <div class="col-sm-10">
                        <input type="text" id="carreraFormatoBExp" name="carreraFormatoBExp" class="form-control" value="" required disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="promedioFormatoBExp" class="col-sm-2 col-form-label">Promedio:</label>
                      <div class="col-sm-10">
                        <input type="number" id="promedioFormatoBExp" name="promedioFormatoBExp" class="form-control" step="0.01" min="0" max="100" value="" required readonly disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="proyectoFormatoBExp" class="col-sm-2 col-form-label">Proyecto:</label>
                      <div class="col-sm-10">
                        <textarea id="proyectoFormatoBExp" name="proyectoFormatoBExp" class="form-control" required style="resize: none;" rows="4" cols="20" readonly disabled></textarea>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="planEstudioFormatoBExp" class="col-sm-2 col-form-label">Plan de estudio:</label>
                      <div class="col-sm-10">
                        <input type="text" id="planEstudioFormatoBExp" name="planEstudioFormatoBExp" class="form-control" readonly disabled>
                        <input type="hidden" id="hiddenPlanEstudioFormatoBExp" name="hiddenPlanEstudioFormatoBExp">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="tipoTitulaciónFormatoBExp" class="col-sm-2 col-form-label">Tipo de titulación:</label>
                      <div class="col-sm-10">
                        <input type="text" id="tipoTitulaciónFormatoBExp" name="tipoTitulaciónFormatoBExp" class="form-control" value="" required disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="fechaIngresoFormatoBExp" class="col-sm-2 col-form-label">Fecha de ingreso:</label>
                      <div class="col-sm-10">
                        <input type="text" id="fechaIngresoFormatoBExp" name="fechaIngresoFormatoBExp" class="form-control" value="" required disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="fechaEgresoFormatoBExp" class="col-sm-2 col-form-label">Fecha de egreso:</label>
                      <div class="col-sm-10">
                        <input type="text" id="fechaEgresoFormatoBExp" name="fechaEgresoFormatoBExp" class="form-control" value="" required disabled>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="asesorFormatoBExp" class="col-sm-2 col-form-label">Asesor Interno:</label>
                      <div class="col-sm-10">
                        <input type="text" id="asesorFormatoBExp" name="asesorFormatoBExp" class="form-control" value="" required disabled>
                        <div id="resultado-busqueda-asesorExp" class="list-group position-absolute"></div>
                        <input type="hidden" id="hiddenAsesorFormatoBExp" name="hiddenAsesorFormatoBExp" value="">
                      </div>
                    </div>
                    <div class="border-top row mb-3">
                      <h5 class="card-title mt-2">Información de equipo:</h5>
                      <div class="col-sm-10">
                        <label for="equipoCheckboxFormatoBExp" class="col-form-label">Proyecto en equipo:</label>
                        <input class="form-check-input p-3 ml-sm-6" type="checkbox" id="equipoCheckboxFormatoBExp" name="equipoCheckboxFormatoBExp" value="" disabled>
                        <div id="radioEquipoFormatoBExp" class="row mb-3">
                          <label for="radioEquipoFormatoBExp" class="col-sm-2 col-form-label">Número de integrantes:</label>
                          <div class="col-sm-10">
                            <input type="radio" id="radioEquipo2FormatoBExp" name="radioEquipoFormatoBExp" class="form-check-input" value="2" disabled>
                            <label for="option1">2</label>
                            <input type="radio" id="radioEquipo3FormatoBExp" name="radioEquipoFormatoBExp" class="form-check-input" value="3" disabled>
                            <label for="option2">3</label>
                          </div>
                        </div>
                        <div id="equipoInputFormatoBExp">
                          <div class="row mb-3"><label for="equipoInput0FormatoBExp" class="col-sm-2 col-form-label">Número de control de integrante 1:</label>
                            <div class="col-sm-10"><input type="text" id="equipoInput0FormatoBExp" name="equipoInput0FormatoBExp" class="form-control" value="" disabled></div>
                          </div>
                          <div class="row mb-3"><label for="equipoInput1FormatoBExp" class="col-sm-2 col-form-label">Nombre y apellidos de integrante 1:</label>
                            <div class="col-sm-10"><input type="text" id="equipoInput1FormatoBExp" name="equipoInput1FormatoBExp" class="form-control" value="" disabled></div>
                          </div>
                          <div class="row mb-3"><label for="equipoInput2FormatoBExp" class="col-sm-2 col-form-label">Carrera de integrante 1:</label>
                            <div class="col-sm-10"><input type="text" id="equipoInput2FormatoBExp" name="equipoInput2FormatoBExp" class="form-control" value="" required disabled></div>
                          </div>
                          <div class="row mb-3"><label for="equipoInput3FormatoBExp" class="col-sm-2 col-form-label">Número de control de integrante 2:</label>
                            <div class="col-sm-10"><input type="text" id="equipoInput3FormatoBExp" name="equipoInput3FormatoBExp" class="form-control" value="" required disabled></div>
                          </div>
                          <div class="row mb-3"><label for="equipoInput4FormatoBExp" class="col-sm-2 col-form-label">Nombre y apellidos de integrante 2:</label>
                            <div class="col-sm-10"><input type="text" id="equipoInput4FormatoBExp" name="equipoInput4FormatoBExp" class="form-control" value="" required disabled></div>
                          </div>
                          <div class="row mb-3"><label for="equipoInput5FormatoBExp" class="col-sm-2 col-form-label">Carrera de integrante 2:</label>
                            <div class="col-sm-10"><input type="text" id="equipoInput5FormatoBExp" name="equipoInput5FormatoBExp" class="form-control" value="" required disabled></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb-1">
                <br>
                <hr>
                <p class="h2">Anexo II o petición de sinodales</p><br>
                <div class="table-responsive" style="max-height: 33.54rem; overflow-y: auto;">
                  <table class="table table-bordered table-hover table-striped" id="tabla-egresadosPeticionSinodalesExp">
                    <thead>
                      <tr>
                        <th>Número de control</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Anexo II</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="mb-1">
                <br>
                <hr>
                <p class="h2">Documentos sin enviar, aprobados o pendientes de revisar de sustentante</p><br>
                <div class="table-responsive" style="max-height: 33.54rem; overflow-y: auto;">
                  <table class="table table-bordered table-hover table-striped" id="tabla-egresadosDocumentosListaExp">
                    <thead>
                      <tr>
                        <th>Documentos pendientes de enviar</th>
                        <th>Documentos aprobados</th>
                        <th>Documentos por revisar</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="mb-1">
                <br>
                <hr>
                <p class="h2">Documentos entregados de sustentante</p><br>
                <div class="table-responsive" style="max-height: 33.54rem; overflow-y: auto;">
                  <table class="table table-bordered table-hover table-striped" id="tabla-egresadosDocumentosEntregadosExp">
                    <thead>
                      <tr>
                        <th>Documento</th>
                        <th>Estado</th>
                        <th>Fecha de subida</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="mb-1">
                <br>
                <hr>
                <p class="h2">Sinodales del sustentante</p><br>
                <div class="table-responsive" style="max-height: 33.54rem; overflow-y: auto;">
                  <table class="table table-bordered table-hover table-striped" id="tabla-egresadoSinodalesExp">
                    <thead>
                      <tr>
                        <th>Proyecto</th>
                        <th>Sinodal Presidente</th>
                        <th>Sinodal Secretario</th>
                        <th>Sinodal Vocal</th>
                        <th>Sinodal Vocal Suplente</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="mb-1">
                <br>
                <hr>
                <p class="h2">Fecha y hora de ceremonia</p><br>
                <div>
                  <label for="fechaCeremoniaEgresadoExp" class="form-label"><strong>Fecha y hora de ceremonia:</strong></label>
                  <input type="text" id="fechaCeremoniaEgresadoExp" name="fechaCeremoniaEgresadoExp" class="form-control h1" value="" required disabled>
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
  <script>
    window.onunload = function() {
      // Esto es para que cuando se cierre la pestaña, se cierre la sesión
      window.location.replace("../index.php");
    };
  </script>
  <script src="../js/obtenerUsuarioEgresado.js"></script>
</body>

</html>