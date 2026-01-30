<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
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
  <title>T-Soft - Constancia sinodalia</title>
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
      <!-- <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none">
        <i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable
      </a> -->
      <div class="page-header pt-3">
        <p class="h1 text-center">Constancia de sinodalia</p>
      </div>
      <hr />
      <div class="row m-1">
        <?php
        if (isset($_SESSION['mensaje']) && $_SESSION['mensaje'] === "Reporte generado con éxito.") : ?>
          <div class="alert alert-success" role="alert">
          <?php
          echo $_SESSION['mensaje'];
          unset($_SESSION['mensaje']);  // Eliminar el mensaje de la sesión para que no se muestre nuevamente

        endif; ?>
          <?php
          if (isset($_SESSION['mensaje']) && $_SESSION['mensaje'] === "Ocurrió un error al generar el reporte.") : ?>
            <div class="alert alert-danger" role="alert">
            <?php
            echo $_SESSION['mensaje'];
            unset($_SESSION['mensaje']);  // Eliminar el mensaje de la sesión para que no se muestre nuevamente
          endif; ?>
            <br>

            <div class="row mb-3">
              <div class="col">
                <form method="get" class="d-flex">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Buscar por nombre" name="nombreBusqueda">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                  </div>
                </form>
              </div>
            </div>

            <div class="row">
              <div class="col-12 mb-3" style="max-height: 20rem; overflow-y: auto;">
                <table class="table table-striped table-bordered table-hover" id="sinodales-table">
                  <thead>
                    <tr>
                      <th>ID Sinodal</th>
                      <th>Sinodal 1</th>
                      <th>Sinodal 2</th>
                      <th>Sinodal 3</th>
                      <th>Sinodal 4</th>
                      <th>Proyecto Sinodales</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    require_once "../../private/conexion.php";

                    $nombreBusqueda = isset($_GET['nombreBusqueda']) ? $_GET['nombreBusqueda'] : '';

                    $stmt = $conn->prepare("SELECT
                            a.Id_Sinodal,
                            p1.Nombre_Profesor AS Sinodal1,
                            p2.Nombre_Profesor AS Sinodal2,
                            p3.Nombre_Profesor AS Sinodal3,
                            p4.Nombre_Profesor AS Sinodal4,
                            pr.Nombre_Proyecto AS Nombre_Proyecto
                            FROM asignacion_sinodales a
                            LEFT JOIN profesor p1 ON a.Fk_Sinodal_1 = p1.Id_Profesor
                            LEFT JOIN profesor p2 ON a.Fk_Sinodal_2 = p2.Id_Profesor
                            LEFT JOIN profesor p3 ON a.Fk_Sinodal_3 = p3.Id_Profesor
                            LEFT JOIN profesor p4 ON a.Fk_Sinodal_4 = p4.Id_Profesor
                            LEFT JOIN proyecto pr ON a.Fk_Proyecto_Sinodales = pr.Id_Proyecto
                            WHERE p1.Nombre_Profesor LIKE ? OR
                                  p2.Nombre_Profesor LIKE ? OR
                                  p3.Nombre_Profesor LIKE ? OR
                                  p4.Nombre_Profesor LIKE ? OR
                                  pr.Nombre_Proyecto LIKE ?");

                    $param = "%" . $nombreBusqueda . "%";
                    $stmt->bind_param("sssss", $param, $param, $param, $param, $param);

                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . $row['Id_Sinodal'] . "</td>";
                      echo "<td>" . $row['Sinodal1'] . "</td>";
                      echo "<td>" . $row['Sinodal2'] . "</td>";
                      echo "<td>" . $row['Sinodal3'] . "</td>";
                      echo "<td>" . $row['Sinodal4'] . "</td>";
                      echo "<td>" . $row['Nombre_Proyecto'] . "</td>";
                      echo "</tr>";
                    }

                    $stmt->close();
                    $conn->close();
                    ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-12 mt-2">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Formulario para generar constancia de sinodalia</h5>
                  <p class="card-text">En esta sección podrá seleccionarse dos fechas de tiempo para generar un periodo a la constancia.</p>

                  <form method="get" id="formConstanciaSinodalia">
                    <div class="mb-3">
                      <label for="teacherDropdown" class="form-label">Seleccionar profesor:</label>
                      <select class="form-select" id="teacherDropdown" name="selectedTeacher">
                        <option value="">Seleccione un profesor</option>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label for="startDate" class="form-label">Fecha de inicio:</label>
                      <input type="date" class="form-control" id="startDate" name="startDate" required>
                    </div>

                    <div class="mb-3">
                      <label for="endDate" class="form-label">Fecha de terminación:</label>
                      <input type="date" class="form-control" id="endDate" name="endDate" required>
                    </div>

                    <div class="row m-2 text-center justify-content-center align-items-center">
                      <div class="col">
                      </div>
                      <div class="col">
                        <button type="button" id="generateDocumentButton" class="btn btn-primary mt-2" disabled>Generar Constancia</button> <!-- SC20250520 Cambiar el tipo del boton a button, antes no tenia asi que era SUBMIT y posiblemente se recargaba antes de completar la peticion-->
                      </div>
                      <div class="col">
                      </div>
                    </div>

                  </form>
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="table-card-style" style="max-height: 33.54rem; overflow-y: auto;">
                <table class="table table-bordered table-hover table-striped" id="tabla-constancia-sinodales">
                  <thead>
                    <tr>
                      <th>Constancia No.</th>
                      <th>Fecha creación</th>
                      <th>Fecha inicio constancia</th>
                      <th>Fecha cierre constancia</th>
                      <th>Profesor</th>
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
  </script>
  <script src="../js/obtenerConstanciasSinodalia.js"></script>

  <!-- --------------------------------------------------------------------------------------------------- -->

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const teacherDropdown = document.getElementById("teacherDropdown");

      // Obtener profesores desde obtenerProfesores.php y llenar la lista desplegable
      fetch("../php/obtenerProfesores.php?q=")
        .then(response => response.json())
        .then(data => {
          data.forEach(teacher => {
            const option = document.createElement("option");
            option.value = teacher.id;
            option.textContent = teacher.nombre;
            teacherDropdown.appendChild(option);
          });
        })
        .catch(error => {
          //console.error("Error al obtener los profesores:", error);
        });
    });
  </script>

  <!-- --------------------------------------------------------------------------------------------------- -->

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const teacherDropdown = document.getElementById("teacherDropdown");
      const generateDocumentButton = document.getElementById("generateDocumentButton");

      teacherDropdown.addEventListener("change", function() {
        if (teacherDropdown.value !== "") {
          generateDocumentButton.removeAttribute("disabled");
        } else {
          generateDocumentButton.setAttribute("disabled", "disabled");
        }
      });

      generateDocumentButton.addEventListener("click", function() {
        console.log("Botón clickeado"); //SC 19-05-2025 Prueba en consola
        const teacherDropdownValue = teacherDropdown.value; // Mueve esta línea aquí para actualizar el valor
        const selectedTeacher = teacherDropdown.options[teacherDropdown.selectedIndex].text;
        const folderPath = `../assets/archivos/reportes/constancias de sinodalia/${selectedTeacher}/`;

        const startDate = document.getElementById("startDate").value;
        const endDate = document.getElementById("endDate").value;

        createFolderAndGenerateDocument(folderPath, selectedTeacher, teacherDropdownValue, startDate, endDate);
         //SC20250520 Delay de 5 segundos(5000 milisegundos) y refresco de la pagina
         setTimeout(() => {
             location.reload();
         }, 5000);
      });
    });

    function createFolderAndGenerateDocument(folderPath, selectedTeacher, teacherId, startDate, endDate) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", `../php/create_folder.php?folderPath=${encodeURIComponent(folderPath)}`, true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            //console.log("Carpeta creada exitosamente en el servidor.");
            console.log("Carpeta creada, ahora llamando a generarConstanciaSinodalia"); //SC 19-05-2025 Prueba en consola
            generateWordDocument(folderPath, selectedTeacher, teacherId, startDate, endDate); // Usar teacherId en lugar de teacherIndex
          } else {
            //console.error("Error al crear la carpeta en el servidor.");
          }
        }
      };
      xhr.send();
    }

    async function generateWordDocument(folderPath, selectedTeacher, teacherId, startDate, endDate) { // Cambié teacherIndex a teacherId
      const url = '../php/generarConstanciaSinodalia.php';
      const params = new URLSearchParams({
        folderPath: folderPath,
        profesor: selectedTeacher,
        profesorId: teacherId,
        startDate: startDate,
        endDate: endDate
      }).toString();
      console.log("Llamando al fetch de generarConstanciaSinodalia.php"); //SC 19-05-2025 Prueba en consola
      console.log("URL:", `${url}?${params}`);
      const response = await fetch(`${url}?${params}`, {
        method: 'GET', // Considera usar POST aquí
      });


     
      if (response.ok) {
        //console.log("Documento Word generado y guardado en la carpeta.");
        // Agregar aquí la lógica para descargar el archivo generado si lo deseas
      } else {
        //console.error("Error al generar el documento y guardar en la carpeta.");
      }
    }
  </script>


</body>

</html>