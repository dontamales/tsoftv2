<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");


$formatosPendientes = require_once '../php/tablaFormatoBRev.php';

//fecha de hoy
$fecha = date("Y-m-d");

$stmt2 = $conn->prepare("SELECT id, fecha, conteo FROM correos_enviados WHERE fecha = ?");
$stmt2->bind_param("s", $fecha);
$stmt2->execute();
$result2 = $stmt2->get_result();
$conteo = $result2->fetch_assoc();

$stmt2->close();

$cuenta = $conteo['conteo'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es-MX"> <!-- LENGUAJE DE LA PÁGINA WEB (PARA TRADUCTORES) -->

<head>
  <!-- Etiquetas meta, íconos y otros... -->
  <?php echo $meta; ?>
  <meta name="description" content="Pestaña de formatos pendientes" />
  <title>T-Soft - Formatos pendientes</title>
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
        <p class="h1">Formatos pendientes por aprobar</p>
        <hr><p class="h3">Correos enviados el dia de hoy: <?php echo ($cuenta);?></p>
      </div>
      <hr />
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between">
              <h5 class="card-title">Egresados con formato B pendientes de revisión</h5>
              <div class="d-flex align-items-center">
                <label for="buscarEgresado" class="m-2">Buscar egresado:</label>
                <input type="text" id="buscarEgresado" name="buscarEgresado" class="form-control m-1" style="width: auto;" placeholder="Número de control" autocomplete="off">
                <button type="button" id="buscarEgresadoBtn" name="buscarEgresadoBtn" class="btn btn-primary btn-sm m-1 ">Buscar</button>
              </div>
            </div>
            <div class="card-body">
              <div style="max-height: 33.54rem; overflow-y: auto;">
                <table id="tablaDeFormatoB" class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Número de control</th>
                      <th scope="col">Nombre del egresado</th>
                      <th scope="col">Fecha de envío de formato B</th>
                      <th scope="col">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($formatosPendientes as $formato) : ?>
                      <tr id="formatoB-<?php echo $formato['Num_Control']; ?>">
                        <td><?php echo $formato['Num_Control']; ?></td>
                        <td><?php echo $formato['Nombres_Usuario'] . " " . $formato['Apellidos_Usuario']; ?></td>
                        <td><?php echo $formato['Fecha_Envio_Formato_B_Egresado']; ?></td>
                        <td>
                          <a href="#formato_BRev" class="view-button btn btn-primary btn-sm" data-id="<?php echo $formato['Id_Usuario']; ?>">Ver</a>
                          <a href="#" class="approve-button btn btn-success btn-sm" data-id="<?php echo $formato['Id_Usuario']; ?>">Aprobar</a>
                          <a href="#" class="reject-button btn btn-danger btn-sm" data-id="<?php echo $formato['Id_Usuario']; ?>">Rechazar</a>

                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <div class="row mt-3 justify-content-center">
                <div class="col-sm-10 text-center">
                  <div class="row justify-content-center">
                    <div class="col-sm-10 text-center">
                      <p class="badge bg-warning text-dark">Formatos pendientes por aprobar: <span id="studentCount"></span></p>
                    </div>
                  </div>
                  <a href="#tablaDeFormatoB" id="verTodosFormatoBRev" name="verTodosFormatoBRev" class="btn btn-primary btn-lg m-1">Ver todos los formatos</a>
                  <a href="#tablaDeFormatoB" id="verDiezFormatoBRev" name="verDiezFormatoBRev" class="btn btn-primary btn-lg m-1">Ver 10 formatos</a>
                </div>
              </div>
              <div class="col-sm-10 text-center">
              </div>
            </div>
          </div>
          <div class="row pt-2 pb-2">
            <div class="col">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title">Formato B alumno: <?php  ?></h5>
                </div>
                <div class="card-body">
                  <p class="card-text">Verificar si la información del egresado es correcta, si lo es hay que dar clic en <strong>'Aprobar'</strong>, si algo no tiene sentido o claramente es incorrecto, hay que dar clic en <strong>'Rechazar'</strong>.</p>
                  <form id="formato_BRev">
                    <div class="row">
                      <div class="border col-lg-6">
                        <h5 class="card-title">Información personal:</h5>
                        <div class="row mb-3">
                          <label for="nombresFormatoBRev" class="col-sm-2 col-form-label">Nombres:</label>
                          <div class="col-sm-10">
                            <input type="text" id="nombresFormatoBRev" name="nombresFormatoBRev" class="form-control" value="" required autocomplete="name" readonly disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="apellidosFormatoBRev" class="col-sm-2 col-form-label">Apellidos:</label>
                          <div class="col-sm-10">
                            <input type="text" id="apellidosFormatoBRev" name="apellidosFormatoBRev" class="form-control" value="" required autocomplete="family-name" readonly disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="generoFormatoBRev" class="col-sm-2 col-form-label">Género:</label>
                          <div class="col-sm-10">
                            <div>
                              <input type="radio" id="generoHombreFormatoBRev" name="generoFormatoBRev" value="Hombre" class="form-check-input" disabled>
                              <label for="generoHombreFormatoBRev" class="form-check-label">Hombre</label>
                            </div>
                            <div>
                              <input type="radio" id="generoMujerFormatoBRev" name="generoFormatoBRev" value="Mujer" class="form-check-input" disabled>
                              <label for="generoMujerFormatoBRev" class="form-check-label">Mujer</label>
                            </div>
                            <div>
                              <input type="radio" id="generoIndefinidoFormatoBRev" name="generoFormatoBRev" value="Indefinido" class="form-check-input" disabled>
                              <label for="generoIndefinidoFormatoBRev" class="form-check-label">Indefinido</label>
                            </div>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="edadFormatoBRev" class="col-sm-2 col-form-label">Edad:</label>
                          <div class="col-sm-10">
                            <input type="number" id="edadFormatoBRev" name="edadFormatoBRev" class="form-control" step="0" min="0" max="100" value="" required readonly disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="celularFormatoBRev" class="col-sm-2 col-form-label">Celular:</label>
                          <div class="col-sm-10">
                            <input type="tel" id="celularFormatoBRev" name="celularFormatoBRev" class="form-control" value="" required pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" autocomplete="tel-national" readonly disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="telefonoFormatoBRev" class="col-sm-2 col-form-label">Teléfono:</label>
                          <div class="col-sm-10">
                            <input type="tel" id="telefonoFormatoBRev" name="telefonoFormatoBRev" class="form-control" value="" required pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" autocomplete="tel-national" readonly disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label class="col-sm-2 col-form-label">Dirección:</label>
                          <div class="col-sm-10">
                            <div class="direccionFormatoB-group">
                              <label for="codigo_postalFormatoBRev">Código Postal:</label>
                              <input type="number" id="codigo_postalFormatoBRev" name="codigo_postalFormatoBRev" class="form-control" step="00000" maxlength="5" value="" required autocomplete="postal-code" readonly disabled>
                            </div>
                            <div class="direccionFormatoB-group">
                              <label for="coloniaFormatoBRev">Colonia:</label>
                              <input type="text" id="coloniaFormatoBRev" name="coloniaFormatoBRev" class="form-control" value="" required readonly disabled>
                            </div>
                            <div class="direccionFormatoB-group">
                              <label for="calleFormatoBRev">Calle:</label>
                              <input type="text" id="calleFormatoBRev" name="calleFormatoBRev" class="form-control" value="" required autocomplete="street-address" readonly disabled>
                            </div>
                            <div class="direccionFormatoB-group">
                              <label for="num_extFormatoBRev">Num. Ext.:</label>
                              <input type="text" id="num_extFormatoBRev" name="num_extFormatoBRev" class="form-control" value="" required readonly disabled>
                            </div>
                            <div class="direccionFormatoB-group">
                              <label for="num_intFormatoBRev">Num. Int. (si no cuentas con uno Ingrese 0):</label>
                              <input type="text" id="num_intFormatoBRev" name="num_intFormatoBRev" class="form-control" value="" readonly disabled>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="border col-lg-6">
                        <h5 class="card-title">Información escolar:</h5>
                        <div class="row mb-3">
                          <label for="numero_controlFormatoBRev" class="col-sm-2 col-form-label">Número de control:</label>
                          <div class="col-sm-10">
                            <input type="text" id="numero_controlFormatoBRev" name="numero_controlFormatoBRev" class="form-control" value="" required readonly disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="carreraFormatoBRev" class="col-sm-2 col-form-label">Carrera:</label>
                          <div class="col-sm-10">
                            <input type="text" id="carreraFormatoBRev" name="carreraFormatoBRev" class="form-control" value="" required disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="promedioFormatoBRev" class="col-sm-2 col-form-label">Promedio:</label>
                          <div class="col-sm-10">
                            <input type="number" id="promedioFormatoBRev" name="promedioFormatoBRev" class="form-control" step="0.01" min="0" max="100" value="" required readonly disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="proyectoFormatoBRev" class="col-sm-2 col-form-label">Proyecto:</label>
                          <div class="col-sm-10">
                            <textarea id="proyectoFormatoBRev" name="proyectoFormatoBRev" class="form-control" required style="resize: none;" rows="4" cols="20" readonly disabled></textarea>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="planEstudioFormatoBRev" class="col-sm-2 col-form-label">Plan de estudio:</label>
                          <div class="col-sm-10">
                            <input type="text" id="planEstudioFormatoBRev" name="planEstudioFormatoBRev" class="form-control" readonly disabled>
                            <input type="hidden" id="hiddenPlanEstudioFormatoBRev" name="hiddenPlanEstudioFormatoBRev">
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="tipoTitulaciónFormatoBRev" class="col-sm-2 col-form-label">Tipo de titulación:</label>
                          <div class="col-sm-10">
                            <input type="text" id="tipoTitulaciónFormatoBRev" name="tipoTitulaciónFormatoBRev" class="form-control" value="" required disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="fechaIngresoFormatoBRev" class="col-sm-2 col-form-label">Fecha de ingreso:</label>
                          <div class="col-sm-10">
                            <input type="text" id="fechaIngresoFormatoBRev" name="fechaIngresoFormatoBRev" class="form-control" value="" required disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="fechaEgresoFormatoBRev" class="col-sm-2 col-form-label">Fecha de egreso:</label>
                          <div class="col-sm-10">
                            <input type="text" id="fechaEgresoFormatoBRev" name="fechaEgresoFormatoBRev" class="form-control" value="" required disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="asesorFormatoBRev" class="col-sm-2 col-form-label">Asesor Interno:</label>
                          <div class="col-sm-10">
                            <input type="text" id="asesorFormatoBRev" name="asesorFormatoBRev" class="form-control" value="" required disabled>
                            <div id="resultado-busqueda-asesorRev" class="list-group position-absolute"></div>
                            <input type="hidden" id="hiddenAsesorFormatoBRev" name="hiddenAsesorFormatoBRev" value="">
                          </div>
                        </div>
                        <div class="border-top row mb-3">
                          <h5>Información de equipo:</h5>
                          <div class="col-sm-10">
                            <label for="equipoCheckboxFormatoBRev" class="col-form-label">Proyecto en equipo:</label>
                            <input class="form-check-input p-3 ml-sm-6" type="checkbox" id="equipoCheckboxFormatoBRev" name="equipoCheckboxFormatoBRev" value="" disabled>
                            <div id="radioEquipoFormatoBRev" class="row mb-3">
                              <label for="radioEquipoFormatoBRev" class="col-sm-2 col-form-label">Número de integrantes:</label>
                              <div class="col-sm-10">
                                <input type="radio" id="radioEquipo2FormatoBRev" name="radioEquipoFormatoBRev" class="form-check-input" value="2" disabled>
                                <label for="option1">2</label>
                                <input type="radio" id="radioEquipo3FormatoBRev" name="radioEquipoFormatoBRev" class="form-check-input" value="3" disabled>
                                <label for="option2">3</label>
                              </div>
                            </div>
                            <div id="equipoInputFormatoBRev">
                              <div class="row mb-3"><label for="equipoInput0FormatoBRev" class="col-sm-2 col-form-label">Número de control de integrante 1:</label>
                                <div class="col-sm-10"><input type="text" id="equipoInput0FormatoBRev" name="equipoInput0FormatoBRev" class="form-control" value="" disabled></div>
                              </div>
                              <div class="row mb-3"><label for="equipoInput1FormatoBRev" class="col-sm-2 col-form-label">Nombre y apellidos de integrante 1:</label>
                                <div class="col-sm-10"><input type="text" id="equipoInput1FormatoBRev" name="equipoInput1FormatoBRev" class="form-control" value="" disabled></div>
                              </div>
                              <div class="row mb-3"><label for="equipoInput2FormatoBRev" class="col-sm-2 col-form-label">Carrera de integrante 1:</label>
                                <div class="col-sm-10"><input type="text" id="equipoInput2FormatoBRev" name="equipoInput2FormatoBRev" class="form-control" value="" required disabled></div>
                              </div>
                              <div class="row mb-3"><label for="equipoInput3FormatoBRev" class="col-sm-2 col-form-label">Número de control de integrante 2:</label>
                                <div class="col-sm-10"><input type="text" id="equipoInput3FormatoBRev" name="equipoInput3FormatoBRev" class="form-control" value="" required disabled></div>
                              </div>
                              <div class="row mb-3"><label for="equipoInput4FormatoBRev" class="col-sm-2 col-form-label">Nombre y apellidos de integrante 2:</label>
                                <div class="col-sm-10"><input type="text" id="equipoInput4FormatoBRev" name="equipoInput4FormatoBRev" class="form-control" value="" required disabled></div>
                              </div>
                              <div class="row mb-3"><label for="equipoInput5FormatoBRev" class="col-sm-2 col-form-label">Carrera de integrante 2:</label>
                                <div class="col-sm-10"><input type="text" id="equipoInput5FormatoBRev" name="equipoInput5FormatoBRev" class="form-control" value="" required disabled></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row justify-content-center mt-2">
                      <h5>Revisión y comentarios:</h5>
                      <div class="col-sm-10 text-center">
                        <div class="form-floating m-3">
                          <textarea class="form-control border-black" placeholder="" id="comentariosFormatoBRev" style="height: 7rem; resize: none;"></textarea>
                          <label for="comentariosFormatoBRev">Observaciones o comentarios de rechazo</label>
                        </div>
                        <a href="#tablaDeFormatoB" id="aprobarFormatoBRev" name="aprobarFormatoBRev" class="btn btn-success btn-lg m-1">Aprobar</a>
                        <a href="#tablaDeFormatoB" id="rechazarFormatoBRev" name="rechazarFormatoBRev" class="btn btn-danger btn-lg m-1">Rechazar</a>
                      </div>
                    </div>
                  </form>
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
  <script src="../js/cierrePestaña.js"></script>
  <script type="module" src="../js/formatosPendientes.js"></script>
</body>

</html>