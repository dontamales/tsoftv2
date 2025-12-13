<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([1, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA
require_once '../php/formatoBData.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

?>

<!DOCTYPE html>
<html lang="es-MX"> <!-- LENGUAJE DE LA PÁGINA WEB (PARA TRADUCTORES) -->

<head>
  <!-- Etiquetas meta, íconos y otros... -->
  <?php echo $meta; ?>
  <meta name="description" content="Pestaña de gestión de registros" />
  <title>T-Soft - Gestión de registros</title>
  <?php echo $icons; ?>

  <!-- Hojas de estilo... -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/pages/baseTsoft.css" />
</head>

<body>
  <?php echo $header; ?>

  <?php echo $menu; ?>

  <div class="main-container">
    <main class="content col ps-md-2 pt-2">
      <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none"><i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable</a>
      
        <!--  Formato B -->

        <?php
        if ($rol == 1) : ?>
          <div class="page-header pt-3">
            <p class="h1">Formato B</p>
          </div>
          <hr />
          <div class="row pt-2 pb-2">
            <div class="col">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title"><?php if ($usuario['Formato_B_Aprobado_Egresado'] == 1) { ?>
                      <!-- Span de aprobado -->
                      <span class="badge bg-success">Aprobado</span>
                  </h5>
                </div>
                <div class="card-body">
                  <p class="card-text"><strong>Usted ya ha sido aprobado</strong>, a menos de que su información esté incorrecta, por favor, <strong>continúe su proceso de titulación.</strong></p>

                  <!-- Span de pendiente -->
                <?php } elseif ($usuario['FK_Estatus_Egresado'] == 1 && $usuario['Formato_B_Aprobado_Egresado'] == 0) { ?>
                  <span class="badge bg-warning text-dark">Pendiente</span>
                  </h5>
                </div>
                <div class="card-body">
                  <p class="card-text">Por favor, <strong> si es la primera vez que inicia sesión verifique que sus datos sean correctos o corrija lo que sea necesario</strong>, si no es la primera vez, posiblemente necesite asistencia de Coordinación de Titulación para modificar algunos campos, así que <strong> lea con atención para no afectar su tiempo estimado de trámite de titulación.</strong></strong></p>

                <?php } elseif ($usuario['Formato_B_Aprobado_Egresado'] == 2) { ?>
                  <span class="badge bg-warning text-dark">En revisión</span>
                  </h5>
                </div>
                <div class="card-body">
                  <p class="card-text"><strong>Su formato B se encuentra en fase de revisión.</strong></p>

                  <!-- Span de rechazado -->
                <?php } elseif ($usuario['Formato_B_Aprobado_Egresado'] == 0) { ?>
                  <span class="badge bg-danger">Rechazado</span>
                  </h5>
                </div>
                <div class="card-body">
                  <p class="card-text"><strong>Su formato B fue rechazado</strong>, por favor, verifique que sus datos sean correctos o haga las correcciones necesarias, <strong> lea con atención para no afectar su tiempo estimado de trámite de titulación.</strong></p>
                  <div class="row justify-content-center mt-2">
                    <h5>Revisión y comentarios:</h5>
                    <div class="col-sm-10">
                      <div class="form-floating m-3">
                        <textarea class="form-control border-black" placeholder="" id="comentariosFormatoBRev" style="height: 7rem; resize: none;" readonly><?php echo isset($usuario['Observaciones_Formato_B_Egresado']) ? $usuario['Observaciones_Formato_B_Egresado'] : ''; ?></textarea>
                        <label for="comentariosFormatoBRev">Observaciones o comentarios de rechazo</label>
                      </div>
                    </div>
                  </div>
                <?php } ?>
                <?php if ($usuario['FK_Estatus_Egresado'] >= 7) { ?>

                  <p class="h4">Ya no tiene acceso a la modificación del formato B debido a que ya se le asignaron sus sinodales en la plataforma, si necesita hacer alguna corrección o aclaración, comuníquese con Coordinación de Titulación</p>

                <?php } else { ?>
                  <form id="formato_B">
                    <div class="row">
                      <div class="border col-lg-6">
                        <h5>Información personal:</h5>
                        <div class="row mb-3 mt-3">
                          <label for="nombresFormatoB" class="col-sm-2 col-form-label">Nombres:</label>
                          <div class="col-sm-10">
                            <input type="text" id="nombresFormatoB" name="nombresFormatoB" class="form-control" value="<?php echo isset($usuario['Nombres_Usuario']) ? $usuario['Nombres_Usuario'] : ''; ?>" required autocomplete="name" placeholder="Ingrese su(s) nombre(s)">
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="apellidosFormatoB" class="col-sm-2 col-form-label">Apellidos:</label>
                          <div class="col-sm-10">
                            <input type="text" id="apellidosFormatoB" name="apellidosFormatoB" class="form-control" value="<?php echo isset($usuario['Apellidos_Usuario']) ? $usuario['Apellidos_Usuario'] : ''; ?>" required autocomplete="family-name" placeholder="Ingrese su(s) apellido(s)">
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="generoFormatoB" class="col-sm-2 col-form-label">Género:</label>
                          <div class="col-sm-10">
                            <div>
                              <input type="radio" id="generoHombreFormatoB" name="generoFormatoB" value="Hombre" class="form-check-input" <?php echo (isset($usuario['Id_Sexo_Genero']) && $usuario['Id_Sexo_Genero'] == '1') ? 'checked' : ''; ?> required>
                              <label for="generoHombreFormatoB" class="form-check-label">Hombre</label>
                            </div>
                            <div>
                              <input type="radio" id="generoMujerFormatoB" name="generoFormatoB" value="Mujer" class="form-check-input" <?php echo (isset($usuario['Id_Sexo_Genero']) && $usuario['Id_Sexo_Genero'] == '2') ? 'checked' : ''; ?> required>
                              <label for="generoMujerFormatoB" class="form-check-label">Mujer</label>
                            </div>
                            <div>
                              <input type="radio" id="generoIndefinidoFormatoB" name="generoFormatoB" value="Indefinido" class="form-check-input" <?php echo (isset($usuario['Id_Sexo_Genero']) && $usuario['Id_Sexo_Genero'] == '3') ? 'checked' : ''; ?> required>
                              <label for="generoIndefinidoFormatoB" class="form-check-label">Indefinido</label>
                            </div>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="edadFormatoB" class="col-sm-2 col-form-label">Edad:</label>
                          <div class="col-sm-10">
                            <input type="number" id="edadFormatoB" name="edadFormatoB" class="form-control" step="0" min="0" max="100" value="<?php echo isset($usuario['Edad_Egresado']) ? $usuario['Edad_Egresado'] : ''; ?>" required placeholder="Ingrese su edad">
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="celularFormatoB" class="col-sm-2 col-form-label">Celular:</label>
                          <div class="col-sm-10">
                            <input type="text" id="celularFormatoB" name="celularFormatoB" class="form-control" value="<?php echo isset($usuario['Celular_Egresado']) ? $usuario['Celular_Egresado'] : ''; ?>" required autocomplete="tel-national" placeholder="Ingrese su número de teléfono celular">
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="telefonoFormatoB" class="col-sm-2 col-form-label">Teléfono:</label>
                          <div class="col-sm-10">
                            <input type="text" id="telefonoFormatoB" name="telefonoFormatoB" class="form-control" value="<?php echo isset($usuario['Telefono_Egresado']) ? $usuario['Telefono_Egresado'] : ''; ?>" required autocomplete="tel-national" placeholder="Ingrese su número de teléfono">
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label class="col-sm-2 col-form-label">Dirección:</label>
                          <div class="col-sm-10">
                            <div class="direccionFormatoB-group">
                              <label for="codigo_postal">Código Postal:</label>
                              <input type="number" id="codigo_postalFormatoB" name="codigo_postalFormatoB" class="form-control" step="000000000" maxlength="9" value="<?php echo isset($usuario['Codigo_Postal_Direccion']) ? $usuario['Codigo_Postal_Direccion'] : ''; ?>" required autocomplete="postal-code" placeholder="Ingrese su código postal">
                            </div>
                            <div class="direccionFormatoB-group">
                              <label for="colonia">Colonia:</label>

                              <input type="text" id="coloniaFormatoB" name="coloniaFormatoB" class="form-control" value="<?php echo isset($usuario['Colonia_Direccion']) ? $usuario['Colonia_Direccion'] : ''; ?>" required placeholder="Ingrese su colonia o fraccionamiento">
                            </div>
                            <div class="direccionFormatoB-group">
                              <label for="calle">Calle:</label>

                              <input type="text" id="calleFormatoB" name="calleFormatoB" class="form-control" value="<?php echo isset($usuario['Calle_Direccion']) ? $usuario['Calle_Direccion'] : ''; ?>" required autocomplete="street-address" placeholder="Ingrese el nombre de su calle">
                            </div>
                            <div class="direccionFormatoB-group">
                              <label for="num_ext">Num. Ext.:</label>
                              <input type="text" id="num_extFormatoB" name="num_extFormatoB" class="form-control" value="<?php echo isset($usuario['Num_Exterior_Direccion']) ? $usuario['Num_Exterior_Direccion'] : ''; ?>" placeholder="Ingrese su número exterior">
                            </div>
                            <div class="direccionFormatoB-group">
                              <label for="num_int">Num. Int.:</label>
                              <input type="text" id="num_intFormatoB" name="num_intFormatoB" class="form-control" value="<?php echo isset($usuario['Num_Interior_Direccion']) ? $usuario['Num_Interior_Direccion'] : ''; ?>" placeholder="Ingrese su número interior si cuenta con uno">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="border col-lg-6">
                        <h5>Información escolar:</h5>
                        <div class="row mb-3 mt-3">
                          <label for="numero_controlFormatoB" class="col-sm-2 col-form-label">Número de control:</label>
                          <div class="col-sm-10">
                            <input type="text" id="numero_controlFormatoB" name="numero_controlFormatoB" class="form-control" value="<?php echo isset($usuario['Num_Control']) ? $usuario['Num_Control'] : ''; ?>" required placeholder="Ingrese su número de control">
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="carreraFormatoB" class="col-sm-2 col-form-label">Carrera:</label>
                          <div class="col-sm-10">
                            <?php
                            $selected_carrera_id = $usuario['Fk_Carrera_Egresado'] ?? '';
                            ?>
                            <select id="carreraFormatoB" name="carreraFormatoB" class="form-control" <?php ($usuario['Formato_B_Aprobado_Egresado'] != 0) ? 'data-selected="<?php echo $selected_carrera_id; ?>"' : ''?> required>
                            <option value="">Seleccione su carrera</option>
                              <?php
                              foreach ($carreras as $carrera) {
                                $selected = ($selected_carrera_id == $carrera['id']) ? 'selected' : '';
                                echo "<option value='{$carrera['id']}' $selected>{$carrera['nombre']}</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                        <div class="row mb-3 promedioFormatoB">
                          <label for="promedioFormatoB" class="col-sm-2 col-form-label">Promedio:</label>
                          <div class="col-sm-10">
                            <input type="number" id="promedioFormatoB" name="promedioFormatoB" class="form-control" step="0.01" min="0" max="100" value="<?php echo isset($usuario['Promedio_Egresado']) ? $usuario['Promedio_Egresado'] : ''; ?>" required placeholder="Ingrese su promedio general final" disabled>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="proyectoFormatoB" class="col-sm-2 col-form-label">Proyecto:</label>
                          <div class="col-sm-10">
                            <textarea id="proyectoFormatoB" name="proyectoFormatoB" class="form-control" style="resize: none;" rows="4" cols="20" placeholder="Ingrese el nombre completo de su proyecto si su tipo de titulación lo requiere, en el caso de Residencia debe ser tal cual está registrado en el SII, puede validarlo en su Constancia de Liberación"><?php echo isset($usuario['Fk_Proyecto_Egresado']) ? $usuario['Nombre_Proyecto'] : '';?></textarea>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="planEstudioFormatoB" class="col-sm-2 col-form-label">Plan de estudio:</label>
                          <div class="col-sm-10">
                            <input type="text" id="planEstudioFormatoB" name="planEstudioFormatoB" class="form-control" readonly>
                            <input type="hidden" id="hiddenPlanEstudioFormatoB" name="hiddenPlanEstudioFormatoB">
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="tipoTitulaciónFormatoB" class="col-sm-2 col-form-label">Tipo de titulación:</label>
                          <div class="col-sm-10">
                            <?php
                            $selected_titulacion_id = $usuario['Fk_Tipo_Titulacion_Egresado'] ?? '';
                            ?>
                            <select id="tipoTitulaciónFormatoB" name="tipoTitulaciónFormatoB" class="form-control" data-selected="<?php echo $selected_titulacion_id; ?>" required>
                              <?php
                              foreach ($tipos_titulacion as $tipo_titulacion) {
                                $selected2 = ($selected_titulacion_id == $tipo_titulacion['id']) ? 'selected' : '';
                                echo "<option value='{$tipo_titulacion['id']}' $selected2>{$tipo_titulacion['nombre']}</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                        <i>Sin importar el día, fecha de ingreso es el primer mes de su primer semestre inscrito ya sea enero o agosto y egreso es el último mes del último semestre inscrito ya sea junio o diciembre</i>
                        <div class="row mb-3">
                          <label for="fechaIngresoFormatoB" class="col-sm-2 col-form-label">Fecha de ingreso:</label>
                          <div class="col-sm-10">
                            <input type="date" id="fechaIngresoFormatoB" name="fechaIngresoFormatoB" class="form-control" value="<?php echo isset($usuario['Fecha_Ingreso_Egresado']) ? $usuario['Fecha_Ingreso_Egresado'] : ''; ?>" required>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <label for="fechaEgresoFormatoB" class="col-sm-2 col-form-label">Fecha de egreso:</label>
                          <div class="col-sm-10">
                            <input type="date" id="fechaEgresoFormatoB" name="fechaEgresoFormatoB" class="form-control" value="<?php echo isset($usuario['Fecha_Egresar_Egresado']) ? $usuario['Fecha_Egresar_Egresado'] : ''; ?>" required>
                          </div>
                        </div>
                        <div class="row mb-3">
                            <i>Comience escribiendo los nombres o los apellidos de su asesor y seleccione el correcto de las coincidencias que muestra abajo</i>
                          <label for="asesorFormatoB" class="col-sm-2 col-form-label">Asesor Interno:</label>
                          <div class="col-sm-10">
                            <input type="text" id="asesorFormatoB" name="asesorFormatoB" class="form-control" value="<?php echo isset($usuario['Nombre_Profesor']) ? $usuario['Nombre_Profesor'] : ''; ?>" placeholder="Ingrese el nombre de su asesor en caso de tener uno">
                            <div id="resultado-busqueda-asesor" class="list-group position-absolute"></div>
                            <input type="hidden" id="hiddenAsesorFormatoB" name="hiddenAsesorFormatoB" value="<?php echo isset($usuario['Fk_Asesor_Interno_Egresado']) ? $usuario['Fk_Asesor_Interno_Egresado'] : ''; ?>">
                          </div>
                        </div>
                        <div class="border-top row mb-3">
                          <h5>Información de equipo:</h5>
                          <div class="col-sm-10">
                            <label for="equipoCheckboxFormatoB" class="col-form-label">Proyecto en equipo:</label>
                            <input class="form-check-input p-3" type="checkbox" id="equipoCheckboxFormatoB" name="equipoCheckboxFormatoB" value="<?php echo isset($usuario['Proyecto_Equipo_Egresado']) && $usuario['Proyecto_Equipo_Egresado'] !== null ? $usuario['Proyecto_Equipo_Egresado'] : '0'; ?>" <?php echo isset($usuario['Proyecto_Equipo_Egresado']) && $usuario['Proyecto_Equipo_Egresado'] == 1 ? 'checked' : ''; ?>>

                            <div id="radioEquipoFormatoB" class="row mb-3" style="display: none;">
                              <label for="radioEquipoFormatoB" class="col-sm-2 col-form-label">Número de integrantes:</label>
                              <div class="col-sm-10">
                                <input type="radio" id="radioEquipo2FormatoB" name="radioEquipoFormatoB" class="form-check-input" value="2" <?php echo (isset($usuario['Numero_Equipo_Egresados']) && $usuario['Numero_Equipo_Egresados'] == '2') ? 'checked' : ''; ?>>
                                <label for="option1">2</label>
                                <input type="radio" id="radioEquipo3FormatoB" name="radioEquipoFormatoB" class="form-check-input" value="3" <?php echo (isset($usuario['Numero_Equipo_Egresados']) && $usuario['Numero_Equipo_Egresados'] == '3') ? 'checked' : ''; ?>>
                                <label for="option2">3</label>
                              </div>
                            </div>
                            <?php
                            $numeroControlEquipo1 = $usuario['NumeroControl_Equipo_Egresado1'];
                            $nombresEquipo1 = $usuario['Nombre_Equipo_Egresado1'];
                            $carreraEquipo1 = $usuario['Fk_Carrera_Equipo_Egresado1'];
                            $numeroControlEquipo2 = $usuario['NumeroControl_Equipo_Egresado2'];
                            $nombresEquipo2 = $usuario['Nombre_Equipo_Egresado2'];
                            $carreraEquipo2 = $usuario['Fk_Carrera_Equipo_Egresado2']; ?>
                            <script>
                              var datos = {
                                numeroControlEquipo1: "<?php echo $numeroControlEquipo1; ?>",
                                nombresEquipo1: "<?php echo $nombresEquipo1; ?>",
                                carreraEquipo1: "<?php echo $carreraEquipo1; ?>",
                                numeroControlEquipo2: "<?php echo $numeroControlEquipo2; ?>",
                                nombresEquipo2: "<?php echo $nombresEquipo2; ?>",
                                carreraEquipo2: "<?php echo $carreraEquipo2; ?>",
                              };
                            </script>
                            <div id="equipoInputFormatoB" style="display: none;"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" id="submitFormatoB" class="btn btn-primary" for="formato-B">Enviar</button>
                    </div>
                  </form>
                <?php } ?>
                </div>
              </div>
            </div>
          <?php endif ?>
    </main>

    <?php echo $footer; ?>

  </div>


  <!-- Librerías de JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

  <?php
  if ($usuario['FK_Estatus_Egresado'] <= 6) : ?>
    <script src="../js/formatoB.js" type="module"></script>
  <?php endif ?>
  <script src="../js/cierrePestaña.js"></script>
  <script>$(".promedioFormatoB").hide();
</script>


</body>

</html>