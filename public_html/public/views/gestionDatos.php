<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA
require_once '../vendor/autoload.php';

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");



$query = "SELECT Id_Documentos_Pendientes, Descripcion_Documentos_Pendientes FROM documentos_pendientes";
$result = $conn->query($query);
$documentos = [];
while ($row = $result->fetch_assoc()) {
  $documentos[] = $row;
}

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
      <?php
      if ($rol == 3 || $rol == 2) : ?>
        <div class="page-header pt-3">
          <p class="h1">Gestión de registros</p>
          <hr>
          <p class="h3">Correos restantes de hoy: <?php echo (100 - $cuenta); ?></p>
        </div>
        <hr />
        <?php if (isset($_SESSION['message'])) : ?>
          <div class="alert alert-warning" role="alert">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']); // Limpiar el mensaje de la sesión una vez que se haya mostrado
            ?>
          </div>
        <?php endif ?>
        <?php if (isset($_SESSION['success'])) : ?>
          <div class="alert alert-success" role="alert">
            <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']); // Limpiar el mensaje de la sesión una vez que se haya mostrado
            ?>
          </div>
        <?php endif ?>
        <?php if (isset($_SESSION['error'])) : ?>
          <div class="alert alert-danger" role="alert">
            <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']); // Limpiar el mensaje de la sesión una vez que se haya mostrado
            ?>
          </div>
        <?php endif ?>

        <p class="h2">Sustentantes</p><br>

        <!-- Tabla de usuarios  -->
        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseTablaUsuarios">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Tabla de usuarios</h5>
              </a>
            </div>
            <div id="collapseTablaUsuarios" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <div class="card-body">
                  <p class="card-text">En esta sección se podrán gestionar los datos de los usuarios y se podrán borrar los seleccionados, los que tengan la casilla deshabilitada será debido a que ya se inició su proceso de titulación y ya es necesaria para los reportes.</p>
                  <div class="d-grid gap-2">
                    <div class="d-grid gap-2">
                      <input type="text" id="searchUsuario" class="form-control" placeholder="Buscar usuario..." />
                      <button class="btn btn-primary mb-2" id="buscarBtn">Buscar</button>
                    </div>
                    <div style="max-height: 20rem; overflow-y: auto;">
                      <table class="table table-striped table-bordered table-hover" id="tabla-usuario-lista">
                        <thead>
                          <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Rol</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>Fecha de creación</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                    <button class="btn btn-danger" id="borrarBtnUsuarios">Borrar usuarios</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Registrar un usuario -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseRegistrarNuevoUsuario">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registrar un nuevo usuario</h5>
              </a>
            </div>
            <div id="collapseRegistrarNuevoUsuario" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección se podrá registrar de manera individual un usuario. <br><strong>NOTA: Tenga precaución al crear sustentantes, una vez creado un sustentante no se podrá cambiar su rol y una vez que se apruebe su Formato B ya no se podrá borrar de la base de datos para los futuros reportes.</strong></p>
                <form id="register-form_usuario">
                  <div class="row mb-3">
                    <label for="fk_roles" class="col-sm-2 col-form-label">Rol de usuario:</label>
                    <div class="col-sm-10">
                      <select id="fk_roles" name="fk_roles" class="form-control" required>
                        <option value="">Selecciona un rol</option>
                        <option value="1">Sustentante</option>
                        <option value="6">Servicios escolares</option>
                        <option value="5">Auxiliar</option>
                        <option value="4">Secretario</option>
                        <option value="2">Administrador</option>
                        <option value="3">Super administrador</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="nombres" class="col-sm-2 col-form-label">Nombres:</label>
                    <div class="col-sm-10">
                      <input type="text" id="nombres" name="nombres" class="form-control" required placeholder="Karla Ivonne">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
                    <div class="col-sm-10">
                      <input type="text" id="apellidos" name="apellidos" class="form-control" required placeholder="Morales Guerra">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="correo" class="col-sm-2 col-form-label">Correo:</label>
                    <div class="col-sm-10">
                      <input type="email" id="correo" name="correo" class="form-control" maxlength="45" required autocomplete="username" placeholder="correo@cdjuarez.tecnm.mx">
                    </div>
                  </div>
                  <div id="numero_control_container" style="display: none;">
                    <div class="row mb-3">
                      <label for="numero_control" class="col-sm-2 col-form-label">Número de control:</label>
                      <div class="col-sm-10">
                        <input type="text" id="numero_control" name="numero_control" class="form-control" placeholder="18111974">
                      </div>
                    </div>
                  </div>
                  <div id="carrera_container" style="display: none;">
                    <div class="row mb-3">
                      <label for="carrera" class="col-sm-2 col-form-label">Carrera:</label>
                      <div class="col-sm-10">
                        <select id="carrera" name="carrera" class="form-control">
                          <!-- Aquí se agregarán las opciones de carrera dinámicamente con JavaScript -->
                        </select>
                      </div>
                    </div>
                  </div>
                  <div id="promedio_container" style="display: none;">
                    <div class="row mb-3">
                      <label for="promedio" class="col-sm-2 col-form-label">Promedio:</label>
                      <div class="col-sm-10">
                        <input type="number" id="promedio" name="promedio" class="form-control" step="0.01" min="0" max="100" placeholder="70">
                      </div>
                    </div>
                  </div>
                  <div id="telefonoRegistro_container" style="display: none;">
                    <div class="row mb-3">
                      <label for="telefonoRegistro" class="col-sm-2 col-form-label">Teléfono:</label>
                      <div class="col-sm-10">
                        <input type="number" id="telefonoRegistro" name="telefonoRegistro" class="form-control" placeholder="6561234567">
                      </div>
                    </div>
                  </div>
                  <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" for="register-form_usuario">Registrar usuario</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>


        <!-- Registro de sustentantes con archivo de carga -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseModificarListaServiciosEscolares">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registro de sustentantes con archivo de carga</h5>
              </a>
            </div>
            <div id="collapseModificarListaServiciosEscolares" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección se debe seleccionar el formato dado por Servicios Escolares con el formato de columnas para una modificación instantánea y registro de los alumnos egresados.</p>

                <p class="card-text">Formato de columnas:</p>
                <p class="card-text"><strong>En donde los datos deben comenzar en el renglón 3.</strong></p>
                <div style="max-height: 20rem; overflow-y: auto;">
                  <table class="table table-striped table-bordered table-hover" id="usuario-table">
                    <thead>
                      <tr>
                        <th>No.</th>
                        <th>No. de Control</th>
                        <th>Nombre(s)</th>
                        <th>Apellidos</th>
                        <th>Carrera</th>
                        <th>Promedio</th>
                        <th>Cita</th>
                        <th>Comentarios</th>
                        <th>Teléfono</th>
                        <th>Correo Electrónico</th>
                        <th>Estatus</th>
                        <th>Fecha</th>
                        <th>No Inconveniencia</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Relleno generico de la tabla -->
                      <tr>
                        <td>1</td>
                        <td>18111977</td>
                        <td>Carlos Armandordo</td>
                        <td>Gardea Hernández</td>
                        <td>Ingeniería en Sistemas Computacionales</td>
                        <td>100</td>
                        <td>01/01/2021</td>
                        <td>Comentarios</td>
                        <td>656-909-7931</td>
                        <td>correo@gmail.com</td>
                        <td>Activo</td>
                        <td>01/01/2021</td>
                        <td>Aprobado</td>
                    </tbody>
                  </table>
                </div>
                <form action="../php/subirExcel.php" method="post" enctype="multipart/form-data">
                  <label label for="archivo">Seleccionar archivo:</label>
                  <div class="input-group mb-3">
                    <input type="file" class="form-control" id="modificarxls" name="archivo" accept=".xls,.xlsx">
                    <button class="btn btn-outline-secondary" type="submit" id="button-addon3">Subir</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Modificar un usuario -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseModificarNuevoUsuario">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Modificar un usuario</h5>
              </a>
            </div>
            <div id="collapseModificarNuevoUsuario" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección se podrá modificar la información de manera individual un usuario, <strong> pero un sustentante no podrá volverse administrativo ni viceversa.</strong></p>
                <form id="modificar-form_usuario">
                  <div class="row mb-3">
                    <label for="modificarUsuario" class="col-sm-2 form-label">Usuario:</label>
                    <div class="col-sm-10">

                      <input id="inputModificarUsuario" type="text" class="form-control" autocomplete="off" placeholder="Rol - Nombre">
                      <input type="hidden" id="selectedModificarUsuarioId" name="selectedModificarUsuarioId">
                      <div id="listContainer" class="list-group"></div>

                    </div>
                  </div>
                  
                  <div class="row mb-3">
                    <label for="modificarFk_roles" class="col-sm-2 col-form-label">Rol de usuario:</label>
                    <div class="col-sm-10">
                      <select id="modificarFk_roles" name="modificarFk_roles" class="form-control" required>
                        <option value="">Selecciona un rol</option>
                        <option value="1">Sustentante</option>
                        <option value="6">Servicios escolares</option>
                        <option value="5">Auxiliar</option>
                        <option value="4">Secretario</option>
                        <option value="2">Administrador</option>
                        <option value="3">Super administrador</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="modificarUsuarioNombres" class="col-sm-2 col-form-label">Nombres:</label>
                    <div class="col-sm-10">
                      <input type="text" id="modificarUsuarioNombres" name="modificarUsuarioNombres" class="form-control" required placeholder="Jose Emanuel">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="modificarUsuarioApellidos" class="col-sm-2 col-form-label">Apellidos:</label>
                    <div class="col-sm-10">
                      <input type="text" id="modificarUsuarioApellidos" name="modificarUsuarioApellidos" class="form-control" required placeholder="Nava Nava">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="modificarUsuarioCorreo" class="col-sm-2 col-form-label">Correo:</label>
                    <div class="col-sm-10">
                      <input type="email" id="modificarUsuarioCorreo" name="modificarUsuarioCorreo" class="form-control" maxlength="45" required autocomplete="username" placeholder="correo@itcj.edu.mx">
                    </div>
                  </div>
                  <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning" for="modificar-form_usuario">Modificar usuario</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>


        <br>
        <hr>
        <p class="h2">Profesores</p><br>


        <!-- Tabla de profesor  -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseTablaProfesor">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Tabla de profesores</h5>
              </a>
            </div>
            <div id="collapseTablaProfesor" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <div class="card-body">
                  <p class="card-text">En esta sección se podrán gestionar los datos de los profesores. <br><strong>NOTA: No se recomienda borrar nada para conservar la integridad lógica de la plataforma. Por lo tanto, de momento no se implementará dicha funcionalidad.</strong></p>
                  <div class="d-grid gap-2">
                    <input type="text" id="searchProfesor" class="form-control" placeholder="Buscar usuario..." />
                    <button class="btn btn-primary mb-2" id="buscarBtnProfesor">Buscar</button>
                  </div>
                  <div style="max-height: 20rem; overflow-y: auto;">
                    <table class="table table-striped table-bordered table-hover" id="profesor-table">
                      <thead>
                        <tr>
                          <th></th>
                          <th>ID</th>
                          <th>Nombre completo</th>
                          <th>Cédula</th>
                          <th>Grado acádemico</th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                  <br>
                  <div class="d-grid gap-2">
                    <!-- <button class="btn btn-danger" id="borrarBtnProfesores">Borrar profesores</button> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!--  Registrar un profesor -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseRegistrarProfesor">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registrar un nuevo profesor</h5>
              </a>
            </div>
            <div id="collapseRegistrarProfesor" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección se podrá registrar de manera individual un profesor.</p>
                <form id="register-form_profesor" action="../php/registroProfesor.php" method="post">
                  <div class="row mb-3">
                    <label for="nombresyapellidos_profesor" class="col-sm-5 col-form-label">Nombre completo en mayúsculas comenzando por apellidos:</label>
                    <div class="col-sm-7">
                      <input type="text" id="nombresyapellidos_profesor" name="nombreCompleto" class="form-control" required placeholder="FERNANDEZ VICENTE">
                      <small id="nombre-mayusculas" class="form-text text-muted"></small>
                    </div>
                  </div>
                  <script>
                    var inputNombre = document.getElementById('nombresyapellidos_profesor');
                    var mensajeMayusculas = document.getElementById('nombre-mayusculas');
                    inputNombre.addEventListener('input', function() {
                      var nombre = inputNombre.value.toUpperCase();
                      inputNombre.value = nombre;
                      mensajeMayusculas.textContent = 'Solo se puede admitir el nombre en mayúsculas.';
                    });
                  </script>
                  <div class="row mb-3">
                    <label for="cedula_profesor" class="col-sm-2 col-form-label">Cédula:</label>
                    <div class="col-sm-10">
                      <input type="text" id="cedula_profesor" name="cedula" class="form-control" required placeholder="12345678">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="grado_profesor" class="col-sm-2 col-form-label">Grado académico:</label>
                    <div class="col-sm-10">
                      <input type="text" id="grado_profesor" name="grado" class="form-control" maxlength="45" required placeholder="INGENIERO EN ADMINISTRACIÓN">
                    </div>
                  </div>
                  <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Registrar profesor</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Registro de profesores con archivo de carga -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseModificarExcelProfesores">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registro de profesores con archivo de carga</h5>
              </a>
            </div>
            <div id="collapseModificarExcelProfesores" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección se debe seleccionar un Excel con los datos de los profesores.</p>

                <p class="card-text">Formato de columnas:</p>
                <p class="card-text"><strong>En donde los datos deben comenzar en el renglón 2.</strong></p>
                <div style="max-height: 20rem; overflow-y: auto;">
                  <table class="table table-striped table-bordered table-hover" id="profesorExcel-table">
                    <thead>
                      <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Grado académico</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Relleno generico de la tabla -->
                      <tr>
                        <td>MORALES GUERRA KARLA IVONNE</td>
                        <td>1234567890</td>
                        <td>INGENIERO INDUSTRIAL</td>
                    </tbody>
                  </table>
                </div>
                <form action="../php/subirExcelProfesores.php" method="post" enctype="multipart/form-data">
                  <label label for="archivo">Seleccionar archivo:</label>
                  <div class="input-group mb-3">
                    <input type="file" class="form-control" id="modificarxls3" name="archivo" accept=".xls,.xlsx">
                    <button class="btn btn-outline-secondary" type="submit" id="button-addon33">Subir</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!--  Modificar un profesor -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseModificarProfesor">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Modificar un profesor</h5>
              </a>
            </div>
            <div id="collapseModificarProfesor" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección se podrá modificar de manera individual a un profesor.</p>
                <form id="modificar-form_profesor" action="../php/modificarProfesor.php" method="post">
                  <div class="row mb-3">
                    <label for="modificarProfesor" class="col form-label">Profesor:</label>
                    <div class="col-10">
                      <select id="modificarProfesor" name="modificarProfesor" class="form-select" required>
                        <option value="">Seleccione el profesor</option>
                      </select>
                    </div>
                  </div>
                  <script>
                    // Obtener los datos de profesor dinámicamente
                    function obtenerModificarProfesores() {
                      var xhr = new XMLHttpRequest();
                      xhr.open('POST', '../php/obtenerJefeDepartamento.php', true);
                      xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                          var modificarProfesores = JSON.parse(xhr.responseText);
                          var selectModificarProfesor = document.getElementById('modificarProfesor');
                          selectModificarProfesor.innerHTML = '<option value="">Seleccione el profesor</option>';
                          modificarProfesores.forEach(function(modificarProfesor) {
                            var option = document.createElement('option');
                            option.value = modificarProfesor.id;
                            option.textContent = modificarProfesor.nombre;
                            selectModificarProfesor.appendChild(option);
                          });
                        }
                      };
                      xhr.send();
                    }

                    window.addEventListener('load', obtenerModificarProfesores);
                  </script>
                  <div class="row mb-3">
                    <label for="modificarNombresYApellidos_Profesor" class="col-sm-5 col-form-label">Nombre completo en mayúsculas comenzando por apellidos:</label>
                    <div class="col-sm-7">
                      <input type="text" id="modificarNombresYApellidos_Profesor" name="nombreCompleto" class="form-control" required placeholder="INFANTE PEDRO">
                      <small id="modificarNombre-Mayusculas" class="form-text text-muted"></small>
                    </div>
                  </div>
                  <script>
                    var inputNombreModificar = document.getElementById('modificarNombresYApellidos_Profesor');
                    var mensajeMayusculasModificar = document.getElementById('modificarNombre-Mayusculas');
                    inputNombreModificar.addEventListener('input', function() {
                      var nombreModificar = inputNombreModificar.value.toUpperCase();
                      inputNombreModificar.value = nombreModificar;
                      mensajeMayusculasModificar.textContent = 'Solo se puede admitir el nombre en mayúsculas.';
                    });
                  </script>
                  <div class="row mb-3">
                    <label for="modificarCedula_profesor" class="col-sm-2 col-form-label">Cédula:</label>
                    <div class="col-sm-10">
                      <input type="text" id="modificarCedula_profesor" name="cedula" class="form-control" required placeholder="12345678">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="modificarGrado_profesor" class="col-sm-2 col-form-label">Grado académico:</label>
                    <div class="col-sm-10">
                      <input type="text" id="modificarGrado_profesor" name="grado" class="form-control" maxlength="45" required placeholder="INGENIERO EN ADMINISTRACIÓN">
                    </div>
                  </div>
                  <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning">Modificar profesor</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <br>
        <hr>
        <p class="h2">Carreras</p><br>

        <!-- Tabla de carreras  -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseTablaCarreras">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Tabla de carreras</h5>
              </a>
            </div>
            <div id="collapseTablaCarreras" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <div class="card-body">
                  <p class="card-text">En esta sección se podrán gestionar los datos de las carreras. <br><strong>NOTA: No se recomienda borrar nada para conservar la integridad lógica de la plataforma. Por lo tanto, de momento no se implementará dicha funcionalidad.</strong></p>
                  <div class="d-grid gap-2">
                    <div class="d-grid gap-2">
                      <input type="text" id="searchCarrera" class="form-control" placeholder="Buscar carrera..." />
                      <button class="btn btn-primary mb-2" id="buscarBtnCarrera">Buscar</button>
                    </div>
                    <div style="max-height: 20rem; overflow-y: auto;">
                      <table class="table table-striped table-bordered table-hover" id="carrera-table">
                        <thead>
                          <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Nombre de la carrera</th>
                            <th>Departamento</th>
                            <th>Jefe de carrera</th>
                            <th>Iniciales</th>
                            <th>Tipo de carrera</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Los datos de las carreras se cargarán dinámicamente aquí -->
                        </tbody>
                      </table>
                    </div>
                    <div class="d-grid gap-2">
                      <!-- <button class="btn btn-danger" id="borrarBtnCarreras">Borrar carreras</button> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!--  Registrar carreras -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioCarreras">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registrar una nueva carrera</h5>
              </a>
            </div>
            <div id="collapseFormularioCarreras" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá registrar carreras nuevas.</p>
                <div class="card-body">
                  <form id="carrera-form">
                    <div class="row mb-3">
                      <label for="nombreCarrera" class="col-2 form-label">Nombre de la carrera:</label>
                      <div class="col">
                        <input type="text" id="nombreCarrera" name="nombreCarrera" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="departamentoCarrera" class="col-2 col-form-label">Departamento:</label>
                      <div class="col">
                        <select id="departamentoCarrera" name="departamentoCarrera" class="form-select" required>
                          <option value="">Seleccione el departamento</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerDepartamento() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerDepartamento.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var departamentos = JSON.parse(xhr.responseText);
                            var selectDepartamento = document.getElementById('departamentoCarrera');
                            selectDepartamento.innerHTML = '<option value="">Seleccione el departamento</option>';
                            departamentos.forEach(function(departamento) {
                              var option = document.createElement('option');
                              option.value = departamento.id;
                              option.textContent = departamento.nombre;
                              selectDepartamento.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerDepartamento);
                    </script>
                    <div class="row mb-3">
                      <label for="jefeCarrera" class="col-2 form-label">Jefe de carrera:</label>
                      <div class="col">
                        <select id="jefeCarrera" name="jefeCarrera" class="form-select" required>
                          <option value="">Seleccione el profesor</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerProfesores() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerJefeDepartamento.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var profesores = JSON.parse(xhr.responseText);
                            var selectProfesor = document.getElementById('jefeCarrera');
                            selectProfesor.innerHTML = '<option value="">Seleccione el profesor</option>';
                            profesores.forEach(function(profesor) {
                              var option = document.createElement('option');
                              option.value = profesor.id;
                              option.textContent = profesor.nombre;
                              selectProfesor.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerProfesores);
                    </script>
                    <div class="row mb-3">
                      <label for="inicialesCarrera" class="col-2 form-label">Iniciales:</label>
                      <div class="col">
                        <input type="text" id="inicialesCarrera" name="inicialesCarrera" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="tipoCarrera" class="col-2 form-label">Tipo de carrera:</label>
                      <div class="col">
                        <select id="tipoCarrera" name="tipoCarrera" class="form-control" required>
                          <option value="Sin Definir">Seleccione una</option>
                          <option value="Licenciatura">Licenciatura</option>
                          <option value="Maestria">Maestría</option>
                          <option value="Doctorado">Doctorado</option>
                        </select>
                      </div>
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-primary mb-2">Registrar carrera</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modificar carreras -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioModificarCarreras">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Modificar una carrera</h5>
              </a>
            </div>
            <div id="collapseFormularioModificarCarreras" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá modificar carreras existentes.</p>
                <div class="card-body">
                  <form id="modificar-carrera-form">
                    <div class="row mb-3">
                      <label for="modificarCarrera" class="col-2 form-label">Carrera:</label>
                      <div class="col">
                        <select id="modificarCarrera" name="modificarCarrera" class="form-select" required>
                          <option value="">Seleccione la carrera</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de carrera dinámicamente
                      function obtenerDepartamento() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerCarrerasModificar.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var carreras = JSON.parse(xhr.responseText);
                            var selectCarrera = document.getElementById('modificarCarrera');
                            selectCarrera.innerHTML = '<option value="">Seleccione la carrera</option>';
                            carreras.forEach(function(carrera) {
                              var option = document.createElement('option');
                              option.value = carrera.id;
                              option.textContent = carrera.nombre;
                              selectCarrera.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerDepartamento);
                    </script>
                    <div class="row mb-3">
                      <label for="modificarNombreCarrera" class="col-2 form-label">Nombre de la carrera:</label>
                      <div class="col">
                        <input type="text" id="modificarNombreCarrera" name="modificarNombreCarrera" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="modificarDepartamentoCarrera" class="col-2 col-form-label">Departamento:</label>
                      <div class="col">
                        <select id="modificarDepartamentoCarrera" name="modificarDepartamentoCarrera" class="form-select" required>
                          <option value="">Seleccione el departamento</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerDepartamento() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerDepartamento.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var departamentos = JSON.parse(xhr.responseText);
                            var selectDepartamento = document.getElementById('modificarDepartamentoCarrera');
                            selectDepartamento.innerHTML = '<option value="">Seleccione el departamento</option>';
                            departamentos.forEach(function(departamento) {
                              var option = document.createElement('option');
                              option.value = departamento.id;
                              option.textContent = departamento.nombre;
                              selectDepartamento.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerDepartamento);
                    </script>
                    <div class="row mb-3">
                      <label for="modificarJefeCarrera" class="col-2 form-label">Jefe de carrera:</label>
                      <div class="col">
                        <select id="modificarJefeCarrera" name="modificarJefeCarrera" class="form-select" required>
                          <option value="">Seleccione el profesor</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerProfesores() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerJefeDepartamento.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var profesores = JSON.parse(xhr.responseText);
                            var selectProfesor = document.getElementById('modificarJefeCarrera');
                            selectProfesor.innerHTML = '<option value="">Seleccione el profesor</option>';
                            profesores.forEach(function(profesor) {
                              var option = document.createElement('option');
                              option.value = profesor.id;
                              option.textContent = profesor.nombre;
                              selectProfesor.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerProfesores);
                    </script>
                    <div class="row mb-3">
                      <label for="modificarInicialesCarrera" class="col-2 form-label">Iniciales:</label>
                      <div class="col">
                        <input type="text" id="modificarInicialesCarrera" name="modificarInicialesCarrera" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="modificarTipoCarrera" class="col-2 form-label">Tipo de carrera:</label>
                      <div class="col">
                        <select id="modificarTipoCarrera" name="modificarTipoCarrera" class="form-control" required>
                          <option value="Sin Definir">Seleccione una</option>
                          <option value="Licenciatura">Licenciatura</option>
                          <option value="Maestria">Maestría</option>
                          <option value="Doctorado">Doctorado</option>
                        </select>
                      </div>
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-warning">Modificar carrera</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <br>
        <hr>
        <p class="h2">Departamentos</p><br>

        <!-- Tabla de departamentos  -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseTablaDepartamentos">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Tabla de departamentos</h5>
              </a>
            </div>
            <div id="collapseTablaDepartamentos" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <div class="card-body">
                  <p class="card-text">En esta sección se podrán gestionar los datos de los departamentos. <br><strong>NOTA: No se recomienda borrar nada para conservar la integridad lógica de la plataforma. Por lo tanto, de momento no se implementará dicha funcionalidad.</strong></p>
                  <div class="d-grid gap-2">
                    <div class="d-grid gap-2">
                      <input type="text" id="searchDepartamento" class="form-control" placeholder="Buscar departamento..." />
                      <button class="btn btn-primary mb-2" id="buscarBtnDepartamento">Buscar</button>
                    </div>
                    <div style="max-height: 20rem; overflow-y: auto;">
                      <table class="table table-striped table-bordered table-hover" id="departamento-table">
                        <thead>
                          <tr>
                            <th></th>
                            <th>ID de Departamento</th>
                            <th>Nombre del departamento</th>
                            <th>Jefe del departamento</th>
                            <th>Correo de la jefatura</th>
                            <th>Correo para los proyectos</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Los datos de los departamentos se cargarán dinámicamente aquí -->
                        </tbody>
                      </table>
                    </div>
                    <!-- <button class="btn btn-danger" id="borrarBtnDepartamento">Borrar departamentos</button> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Registrar departamentos -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioDepartamento">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registrar un nuevo departamento</h5>
              </a>
            </div>
            <div id="collapseFormularioDepartamento" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá registrar departamentos nuevos.</p>
                <div class="card-body">
                  <form id="formularioDepartamento">
                    <div class="row mb-3">
                      <label for="nombreDepartamento" class="col-2 form-label">Nombre del departamento:</label>
                      <div class="col">
                        <input type="text" id="nombreDepartamentoInput" name="nombreDepartamento" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="jefeDepartamentoInput" class="col-2 form-label">Jefe del departamento:</label>
                      <div class="col">
                        <select id="jefeDepartamentoInput" name="jefeDepartamentoInput" class="form-select" required>
                          <option value="">Seleccione el profesor</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerJefeDepartamento() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerJefeDepartamento.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var jefes = JSON.parse(xhr.responseText);
                            var selectJefe = document.getElementById('jefeDepartamentoInput');
                            selectJefe.innerHTML = '<option value="">Seleccione el jefe</option>';
                            jefes.forEach(function(jefe) {
                              var option = document.createElement('option');
                              option.value = jefe.id;
                              option.textContent = jefe.nombre;
                              selectJefe.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerJefeDepartamento);
                    </script>
                    <div class="row mb-3">
                      <label for="correoJefatura" class="col-2 form-label">Correo de Jefatura:</label>
                      <div class="col">
                        <input type="email" id="correoJefaturaInput" name="correoJefatura" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="correoProyecto" class="col-2 form-label">Correo de Proyecto:</label>
                      <div class="col">
                        <input type="email" id="correoProyectoInput" name="correoProyecto" class="form-control" />
                      </div>
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-primary mb-2">Registrar departamento</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modificar departamentos -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioModificarDepartamento">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Modificar un departamento</h5>
              </a>
            </div>
            <div id="collapseFormularioModificarDepartamento" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá modificar departamentos existentes.</p>
                <div class="card-body">
                  <form id="formularioModificarDepartamento">
                    <div class="row mb-3">
                      <label for="modificarDepartamento" class="col-2 col-form-label">Departamento:</label>
                      <div class="col">
                        <select id="modificarDepartamento" name="modificarDepartamento" class="form-select" required>
                          <option value="">Seleccione el departamento</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerDepartamento() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerDepartamento.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var departamentos = JSON.parse(xhr.responseText);
                            var selectDepartamento = document.getElementById('modificarDepartamento');
                            selectDepartamento.innerHTML = '<option value="">Seleccione el departamento</option>';
                            departamentos.forEach(function(departamento) {
                              var option = document.createElement('option');
                              option.value = departamento.id;
                              option.textContent = departamento.nombre;
                              selectDepartamento.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerDepartamento);
                    </script>
                    <div class="row mb-3">
                      <label for="modificarNombreDepartamento" class="col-2 form-label">Nombre del departamento:</label>
                      <div class="col">
                        <input type="text" id="modificarNombreDepartamento" name="modificarNombreDepartamento" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="modificarJefeDepartamentoInput" class="col-2 form-label">Jefe del departamento:</label>
                      <div class="col">
                        <select id="modificarJefeDepartamentoInput" name="modificarJefeDepartamentoInput" class="form-select" required>
                          <option value="">Seleccione el profesor</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerJefeDepartamento() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerJefeDepartamento.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var jefes = JSON.parse(xhr.responseText);
                            var selectJefe = document.getElementById('modificarJefeDepartamentoInput');
                            selectJefe.innerHTML = '<option value="">Seleccione el jefe</option>';
                            jefes.forEach(function(jefe) {
                              var option = document.createElement('option');
                              option.value = jefe.id;
                              option.textContent = jefe.nombre;
                              selectJefe.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerJefeDepartamento);
                    </script>
                    <div class="row mb-3">
                      <label for="modificarCorreoJefatura" class="col-2 form-label">Correo de Jefatura:</label>
                      <div class="col">
                        <input type="email" id="modificarCorreoJefatura" name="modificarCorreoJefatura" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="modificarCorreoProyecto" class="col-2 form-label">Correo de Proyecto:</label>
                      <div class="col">
                        <input type="email" id="modificarCorreoProyecto" name="modificarCorreoProyecto" class="form-control" />
                      </div>
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-warning">Modificar departamento</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <br>
        <hr>
        <p class="h2">Documentos que suben los sustentantes</p><br>

        <!-- Tabla de documentos -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseTablaDocumentos">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Tabla de documentos</h5>
              </a>
            </div>
            <div id="collapseTablaDocumentos" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <div class="card-body">
                  <p class="card-text">En esta sección se podrán visualizar todos los documentos que debe subir los egresados según sea el caso. <br><strong>NOTA: No se recomienda borrar nada para conservar la integridad lógica de la plataforma. Por lo tanto, de momento no se implementará dicha funcionalidad.</strong></p>
                  <div class="d-grid gap-2">
                    <div class="d-grid gap-2">
                      <input type="text" id="searchDocumento" class="form-control" placeholder="Buscar documento..." />
                      <button class="btn btn-primary mb-2" id="buscarDocumentoBtn">Buscar</button>
                    </div>
                    <div style="max-height: 20rem; overflow-y: auto;">
                      <table class="table table-striped table-bordered table-hover" id="documento-table">
                        <thead>
                          <tr>
                            <th></th>
                            <th>ID documento</th>
                            <th>Descripción</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Los datos de documento se cargarán dinámicamente aquí -->
                        </tbody>
                      </table>
                    </div>
                    <!-- <button class="btn btn-danger" id="borrarBtnDocumento">Borrar documento</button> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Registrar un documento -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioRegistroDocumento">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registrar un nuevo documento</h5>
              </a>
            </div>
            <div id="collapseFormularioRegistroDocumento" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá registrar nuevos documentos.</p>
                <div class="card-body">
                  <form id="formularioRegistroDocumento">
                    <div class="row mb-3">
                      <label for="documentoDescripcion" class="col-2 form-label">Descripción del documento:</label>
                      <div class="col">
                        <input type="text" id="documentoDescripcionInput" class="form-control" required />
                      </div>
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-primary mb-2">Registrar documento</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Registrar un documento -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioModificarDocumento">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Modificar un documento</h5>
              </a>
            </div>
            <div id="collapseFormularioModificarDocumento" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá modificar documentos existentes.</p>
                <div class="card-body">
                  <form id="formularioModificarDocumento">
                    <div class="row mb-3">
                      <label for="modificarDocumento" class="col-2 col-form-label">Departamento:</label>
                      <div class="col">
                        <select id="modificarDocumento" name="modificarDocumento" class="form-select" required>
                          <option value="">Seleccione el documento</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerDepartamento() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerDocumentos.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var departamentos = JSON.parse(xhr.responseText);
                            var selectDepartamento = document.getElementById('modificarDocumento');
                            selectDepartamento.innerHTML = '<option value="">Seleccione el documento</option>';
                            departamentos.forEach(function(departamento) {
                              var option = document.createElement('option');
                              option.value = departamento.id;
                              option.textContent = departamento.nombre;
                              selectDepartamento.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerDepartamento);
                    </script>
                    <div class="row mb-3">
                      <label for="modificarDocumentoDescripcion" class="col-2 form-label">Descripción del documento:</label>
                      <div class="col">
                        <input type="text" id="modificarDocumentoDescripcion" class="form-control" required />
                      </div>
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-warning">Modificar documento</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <br>
        <hr>
        <p class="h2">Planes de estudio</p><br>

        <!-- Tabla de planes de estudio -->
        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseTablaPlanesEstudio">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Tabla de planes de estudio</h5>
              </a>
            </div>
            <div id="collapseTablaPlanesEstudio" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <div class="card-body">
                  <p class="card-text">En esta sección se podrán gestionar los planes de estudios. <br><strong>NOTA: No se recomienda borrar nada para conservar la integridad lógica de la plataforma. Por lo tanto, de momento no se añadirá dicha funcionalidad.</strong></p>
                  </p>
                  <div class="d-grid gap-2">
                    <div class="d-grid gap-2">
                      <input type="text" id="searchPlanEstudio" class="form-control" placeholder="Buscar plan..." />
                      <button class="btn btn-primary mb-2" id="buscarPlanEstudioBtn">Buscar</button>
                    </div>
                    <div style="max-height: 20rem; overflow-y: auto;">
                      <table class="table table-striped table-bordered table-hover" id="planesEstudio-table">
                        <thead>
                          <tr>
                            <th></th>
                            <th>ID plan de estudio</th>
                            <th>Generación del periodo</th>
                            <th>Descripción del plan de año</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Los datos de planes de estudio se cargarán dinámicamente aquí -->
                        </tbody>
                      </table>
                    </div>
                    <!-- <button class="btn btn-danger" id="borrarBtnPlanEstudio">Borrar planes de estudio</button> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Registrar un plan de estudio -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioRegistroPlanEstudio">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registrar un nuevo plan de estudio</h5>
              </a>
            </div>
            <div id="collapseFormularioRegistroPlanEstudio" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá registrar nuevos planes de estudios. <br><strong>NOTA: Hay que tener en cuenta que este apartado es casi inútil, porque aunque se pueda crear otro plan estudio no tendrá ningún efecto en la plataforma porque los egresados no eligen su plan de estudios, si no que se les asigna automáticamente mediante su número de control, por lo que requeriría una actualización.</strong></p>
                <div class="card-body">
                  <form id="formularioRegistroPlanEstudio">
                    <div class="row mb-3">
                      <label for="periodoGeneracion" class="col-3 form-label">Periodo de generación:</label>
                      <div class="col">
                        <input type="text" id="periodoGeneracionInput" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="descripcionPlanAnio" class="col-3 form-label">Descripción del plan del año:</label>
                      <div class="col">
                        <input type="text" id="descripcionPlanAnioInput" class="form-control" required />
                      </div>
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-primary mb-2">Registrar plan de estudio</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modificar un plan de estudio -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioModificarPlanEstudio">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Modificar un plan de estudio</h5>
              </a>
            </div>
            <div id="collapseFormularioModificarPlanEstudio" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá modificar los planes de estudios existentes. <br><strong>NOTA: Hay que tener en cuenta que el plan de estudio se asigna automáticamente según el número de control, así que el que modifique afectará a sus respectivos números de control (tomando en cuenta que la excepción de IGE).</strong></p>
                <div class="card-body">
                  <form id="formularioModificarPlanEstudio">
                    <div class="row mb-3">
                      <label for="modificarPlanEstudio" class="col-3 col-form-label">Plan de estudio:</label>
                      <div class="col">
                        <select id="modificarPlanEstudio" name="modificarPlanEstudio" class="form-select" required>
                          <option value="">Seleccione el plan de estudio</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerDepartamento() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerPlanesEstudio.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var departamentos = JSON.parse(xhr.responseText);
                            var selectDepartamento = document.getElementById('modificarPlanEstudio');
                            selectDepartamento.innerHTML = '<option value="">Seleccione el documento</option>';
                            departamentos.forEach(function(departamento) {
                              var option = document.createElement('option');
                              option.value = departamento.id;
                              option.textContent = departamento.nombre;
                              selectDepartamento.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerDepartamento);
                    </script>
                    <div class="row mb-3">
                      <label for="periodoGeneracion" class="col-3 form-label">Periodo de generación:</label>
                      <div class="col">
                        <input type="text" id="modificarPeriodoGeneracionInput" class="form-control" required />
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="descripcionPlanAnio" class="col-3 form-label">Descripción del plan del año:</label>
                      <div class="col">
                        <input type="text" id="modificarDescripcionPlanAnioInput" class="form-control" required />
                      </div>
                    </div>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-warning">Modificar plan de estudio</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <br>
        <hr>
        <p class="h2">Tipos de titulación</p><br>

        <!-- Tabla de tipos de titulación -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseTablaTiposTitulacion">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Tabla de tipos de titulación</h5>
              </a>
            </div>
            <div id="collapseTablaTiposTitulacion" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <div class="card-body">
                  <p class="card-text">En esta sección se podrán gestionar los tipos de titulación. <br><strong>NOTA: No se recomienda borrar nada para conservar la integridad lógica de la plataforma. Por lo tanto, de momento no se añadirá dicha funcionalidad.</strong></p>
                  </p>
                  <div class="d-grid gap-2">
                    <div class="d-grid gap-2">
                      <input type="text" id="searchTipoTitulacion" class="form-control" placeholder="Buscar tipo de titulación..." />
                      <button class="btn btn-primary mb-2" id="buscarBtnTitulacion">Buscar</button>
                    </div>
                    <div style="max-height: 20rem; overflow-y: auto;">
                      <table class="table table-striped table-bordered table-hover" id="tipoTitulacion-table">
                        <thead>
                          <tr>
                            <th></th>
                            <th>ID tipo de titulación</th>
                            <th>Nombre del tipo de titulación</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Los datos de tipos de titulación se cargarán dinámicamente aquí -->
                        </tbody>
                      </table>
                    </div>
                    <!-- <button class="btn btn-danger" id="borrarBtnTipoTitulacion">Borrar tipos de titulación</button> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!--  Registrar tipo de titulacion -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioTipoTitulacion">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registrar un nuevo tipo de titulación</h5>
              </a>
            </div>
            <div id="collapseFormularioTipoTitulacion" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá registrar nuevos tipos de titulación. <br><strong>NOTA: El registro de tipos de titulación solo admite un plan de estudio por titulación debido a que ese es el enfoque que se planea con el plan competencias. Se recomienda ampliamente verificar que todo esté correcto antes de registrar un nuevo tipo de titulación.</strong></p>
                <div class="card-body">
                  <form id="formularioTipoTitulacion">
                    <div class="row mb-3">
                      <label for="nombreTipoTitulacion" class="col-3 form-label">Nombre del tipo de titulación:</label>
                      <div class="col">
                        <input type="text" id="nombreTipoTitulacionInput" name="nombreTipoTitulacion" class="form-control" required />
                      </div>
                    </div>
                    <!-- Sección de documentos -->
                    <div class="mb-3">
                      <label for="docsTipoTitulacion" class="form-label">Documentos relacionados:</label>
                      <?php
                      foreach ($documentos as $documento) {
                        echo '<div class="form-check">';
                        echo '<input class="form-check-input" type="checkbox" name="documentos[]" value="' . $documento['Id_Documentos_Pendientes'] . '" id="doc' . $documento['Id_Documentos_Pendientes'] . 'TipoTitulacion">';
                        echo '<label class="form-check-label" for="doc' . $documento['Id_Documentos_Pendientes'] . 'TipoTitulacion">' . $documento['Descripcion_Documentos_Pendientes'] . '</label>';
                        echo '</div>';
                      }
                      ?>
                    </div>
                    <div class="row mb-3">
                      <label for="planEstudioTipoTitulacion" class="col-2 form-label">Plan de estudio:</label>
                      <div class="col mb-3">
                        <select id="planEstudioTipoTitulacion" name="planEstudioTipoTitulacion" class="form-select" required>
                          <option value="">Seleccione el plan de estudio</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerPlanEstudio() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerPlanesEstudio.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var planesEstudio = JSON.parse(xhr.responseText);
                            var selectPlanEstudio = document.getElementById('planEstudioTipoTitulacion');
                            selectPlanEstudio.innerHTML = '<option value="">Seleccione el plan de estudio</option>';
                            planesEstudio.forEach(function(planEstudio) {
                              var option = document.createElement('option');
                              option.value = planEstudio.id;
                              option.textContent = planEstudio.nombre;
                              selectPlanEstudio.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerPlanEstudio);
                    </script>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-primary mb-2">Registrar tipo</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!--  Modificar tipo de titulacion -->

        <div class="m-1">
          <div class="card">
            <div class="card-header">
              <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioModificarTipoTitulacion">
                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Modificar un tipo de titulación</h5>
              </a>
            </div>
            <div id="collapseFormularioModificarTipoTitulacion" class="collapse" data-bs-parent="#accordion">
              <div class="card-body">
                <p class="card-text">En esta sección podrá modificar tipos de titulación existentes. <br><strong>NOTA: Modificar un tipo de titulación puede afectar de cierto modo la lógica de la plataforma en el sentido de que sólo puede estar asociado a un plan de estudios y si alguien con dicho tipo de titulación ya entregó sus documentos, podría afectarle, en todo caso posiblemente se requiera otro enfoque.</strong></p>
                <div class="card-body">
                  <form id="modificarFormularioTipoTitulacion">
                    <div class="row mb-3">
                      <label for="modificarTipoTitulacion" class="col-2 form-label">Tipo de titulación:</label>
                      <div class="col mb-3">
                        <select id="modificarTipoTitulacion" name="modificarTipoTitulacion" class="form-select" required>
                          <option value="">Seleccione el tipo de titulación</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerTipoTitulacion() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerTipoTitulacionModificar.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var planesEstudio = JSON.parse(xhr.responseText);
                            var selectPlanEstudio = document.getElementById('modificarTipoTitulacion');
                            selectPlanEstudio.innerHTML = '<option value="">Seleccione el tipo de titulacion</option>';
                            planesEstudio.forEach(function(planEstudio) {
                              var option = document.createElement('option');
                              option.value = planEstudio.id;
                              option.textContent = planEstudio.nombre;
                              selectPlanEstudio.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerTipoTitulacion);
                    </script>
                    <div class="row mb-3">
                      <label for="modificarNombreTipoTitulacion" class="col-3 form-label">Nombre del tipo de titulación:</label>
                      <div class="col">
                        <input type="text" id="modificarNombreTipoTitulacionInput" name="modificarNombreTipoTitulacion" class="form-control" required />
                      </div>
                    </div>
                    <!-- Sección de documentos -->
                    <div class="mb-3">
                      <label for="modificarDocsTipoTitulacion" class="form-label">Documentos relacionados:</label>
                      <?php
                      foreach ($documentos as $documento) {
                        echo '<div class="form-check">';
                        echo '<input class="form-check-input" type="checkbox" name="modificarDocumentos[]" value="' . $documento['Id_Documentos_Pendientes'] . '" id="modificarDoc' . $documento['Id_Documentos_Pendientes'] . 'TipoTitulacion">';
                        echo '<label class="form-check-label" for="doc' . $documento['Id_Documentos_Pendientes'] . 'TipoTitulacion">' . $documento['Descripcion_Documentos_Pendientes'] . '</label>';
                        echo '</div>';
                      }
                      ?>
                    </div>
                    <div class="row mb-3 mt-3">
                      <label for="modificarPlanEstudioTipoTitulacion" class="col-2 form-label">Plan de estudio:</label>
                      <div class="col mb-3">
                        <select id="modificarPlanEstudioTipoTitulacion" name="modificarPlanEstudioTipoTitulacion" class="form-select" required>
                          <option value="">Seleccione el plan de estudio</option>
                        </select>
                      </div>
                    </div>
                    <script>
                      // Obtener los datos de departamento dinámicamente
                      function obtenerPlanEstudio() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../php/obtenerPlanesEstudio.php', true);
                        xhr.onreadystatechange = function() {
                          if (xhr.readyState === 4 && xhr.status === 200) {
                            var planesEstudio = JSON.parse(xhr.responseText);
                            var selectPlanEstudio = document.getElementById('modificarPlanEstudioTipoTitulacion');
                            selectPlanEstudio.innerHTML = '<option value="">Seleccione el plan de estudio</option>';
                            planesEstudio.forEach(function(planEstudio) {
                              var option = document.createElement('option');
                              option.value = planEstudio.id;
                              option.textContent = planEstudio.nombre;
                              selectPlanEstudio.appendChild(option);
                            });
                          }
                        };
                        xhr.send();
                      }

                      window.addEventListener('load', obtenerPlanEstudio);
                    </script>
                    <div class="d-grid gap-2">
                      <button type="submit" class="btn btn-warning">Modificar tipo de titulación</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <br>
          <hr>
          <br>
        <?php endif ?>
    </main>

    <?php echo $footer; ?>

  </div>


  <!-- Librerías de JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

  <?php
  if ($rol == 3 || $rol == 2) : ?>
    <!-- Scripts propios -->
    <script src="../js/subirExcel.js"></script>
    <script src="../js/tablaUsuario.js"></script>
    <script src="../js/tablaProfesor.js"></script>
    <script src="../js/tablaCarrera.js"></script>
    <script src="../js/tablaTitulacion.js"></script>
    <script src="../js/tablaDepartamento.js"></script>
    <script src="../js/tablaPlanesEstudio.js"></script>
    <script src="../js/tablaDocumento.js"></script>
    <script src="../js/registroUsuario.js"></script>
    <script src="../js/registroProfesor.js"></script>
    <script src="../js/registroCarrera.js"></script>
    <script src="../js/registroDepartamento.js"></script>
    <script src="../js/registroTipoTitulacion.js"></script>
    <script src="../js/registroPlanEstudio.js"></script>
    <script src="../js/registroDocumento.js"></script>
    <script src="../js/modificarUsuario.js"></script>
    <script src="../js/modificarProfesor.js"></script>
    <script src="../js/modificarCarreras.js"></script>
    <script src="../js/modificarDepartamentos.js"></script>
    <script src="../js/modificarDocumentos.js"></script>
    <script src="../js/modificarTiposTitulacion.js"></script>
    <script src="../js/modificarPlanEstudio.js"></script>
  <?php endif ?>
  <script src="../js/cierrePestaña.js"></script>


</body>

</html>