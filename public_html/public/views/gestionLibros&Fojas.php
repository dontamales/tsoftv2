<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5, 6]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");


//Año de hoy
$anio = date("Y");
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
                <h1 class="text-center">Gestión de libros y fojas</h1>
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
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']); // Limpiar el mensaje de la sesión una vez que se haya mostrado
                    ?>
                </div>
            <?php endif ?>
            <br>
            <p class="h2">Libros</p><br>

            <!-- Tabla de libro -->
            <div class="m-1">
                <div class="card">
                    <div class="card-header">
                        <a class="btn" data-bs-toggle="collapse" href="#collapseTablaLibros">
                            <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Tabla de libros</h5>
                        </a>
                    </div>
                    <div id="collapseTablaLibros" class="collapse" data-bs-parent="#accordion">
                        <div class="card-body">
                            <div class="card-body">
                                <p class="card-text">En esta sección se podrán gestionar los libros.</p>
                                <div class="d-grid gap-2">

                                    <div class="d-grid gap-2">
                                        <input type="text" id="searchLibro" class="form-control" placeholder="Buscar libro..." />
                                        <button class="btn btn-primary mb-2" id="buscarLibroBtn">Buscar</button>
                                    </div>
                                    <div style="max-height: 20rem; overflow-y: auto;">
                                        <table class="table table-striped table-bordered table-hover" id="libro-table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>ID libro</th>
                                                    <th>Descripción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Los datos de libros se cargarán dinámicamente aquí -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                    if ($rol == 3) :
                                    ?>
                                        <button class="btn btn-danger" id="borrarBtnLibro">Borrar libros</button>
                                    <?php
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if ($rol != 6) :
            ?>
                <!-- Registrar libro -->

                <div class="m-1">
                    <div class="card">
                        <div class="card-header">
                            <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioRegistroLibro">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Registrar un nuevo libro</h5>
                            </a>
                        </div>
                        <div id="collapseFormularioRegistroLibro" class="collapse" data-bs-parent="#accordion">
                            <div class="card-body">
                                <p class="card-text">En esta sección podrá registrar nuevos libros.</p>
                                <div class="card-body">
                                    <form id="formularioRegistroLibro">
                                        <div class="row mb-3">
                                            <label for="nombreLibro" class="col-2 form-label">Descripción:</label>
                                            <div class="col">
                                                <input type="text" id="nombreLibroInput" class="form-control" required />
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary mb-2">Registrar libro</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                if ($rol == 3) :
                ?>
                    <!-- Modificar libro -->

                    <div class="m-1">
                        <div class="card">
                            <div class="card-header">
                                <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioModificarLibro">
                                    <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Modificar un libro</h5>
                                </a>
                            </div>
                            <div id="collapseFormularioModificarLibro" class="collapse" data-bs-parent="#accordion">
                                <div class="card-body">
                                    <p class="card-text">En esta sección podrá modificar libros existentes.</p>
                                    <div class="card-body">
                                        <form id="formularioModificarLibro">
                                            <div class="row mb-3">
                                                <label for="modificarLibro" class="col-2 form-label">Plan de estudio:</label>
                                                <div class="col mb-3">
                                                    <select id="modificarLibro" name="modificarLibro" class="form-select" required>
                                                        <option value="">Seleccione el plan de estudio</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <script>
                                                // Obtener los datos del libro dinámicamente
                                                function obtenerLibro() {
                                                    var xhr = new XMLHttpRequest();
                                                    xhr.open('POST', '../php/obtenerLibro.php', true);
                                                    xhr.onreadystatechange = function() {
                                                        if (xhr.readyState === 4 && xhr.status === 200) {
                                                            var libros = JSON.parse(xhr.responseText);
                                                            var selectLibro = document.getElementById('modificarLibro');
                                                            selectLibro.innerHTML = '<option value="">Seleccione el plan de estudio</option>';
                                                            libros.forEach(function(libro) {
                                                                var option = document.createElement('option');
                                                                option.value = libro.idL;
                                                                option.textContent = libro.nombreL;
                                                                selectLibro.appendChild(option);
                                                            });
                                                        }
                                                    };
                                                    xhr.send();
                                                }
                                                // Llamar a la función para obtener los datos de departamento al cargar la página
                                                window.addEventListener('load', obtenerLibro);
                                            </script>
                                            <div class="row mb-3">
                                                <label for="modificarNombreLibro" class="col-2 form-label">Descripción:</label>
                                                <div class="col">
                                                    <input type="text" id="modificarNombreLibro" class="form-control" required />
                                                </div>
                                            </div>
                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-warning mb-2">Modificar libro</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                endif;
                ?>
            <?php
            endif;
            ?>

            <hr />
            <br>
            <p class="h2">Fojas</p><br>

            <!-- Tabla de fojas libro -->

            <div class="m-1">
                <div class="card">
                    <div class="card-header">
                        <a class="btn" data-bs-toggle="collapse" href="#collapseTablaFojas">
                            <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Tabla de fojas</h5>
                        </a>
                    </div>
                    <div id="collapseTablaFojas" class="collapse" data-bs-parent="#accordion">
                        <div class="card-body">
                            <div class="card-body">
                                <p class="card-text">En esta sección se podrán ver y/o borrar las fojas.</p>
                                <div class="d-grid gap-2">
                                    <div class="d-grid gap-2">
                                        <input type="text" id="searchFoja" class="form-control" placeholder="Buscar tipo de foja..." />
                                        <button class="btn btn-primary mb-2" id="buscarBtnFoja">Buscar</button>
                                    </div>
                                    <div style="max-height: 20rem; overflow-y: auto;">
                                        <table class="table table-striped table-bordered table-hover" id="foja-table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Período</th>
                                                    <th>Año</th>
                                                    <th>Dirección de foja</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Los datos de tipos de titulación se cargarán dinámicamente aquí -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                    if ($rol == 3) :
                                    ?>
                                        <button class="btn btn-danger" id="borrarBtnFoja">Borrar fojas</button>
                                    <?php
                                    endif;
                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if ($rol != 6) :
            ?>

                <!-- Subir foja -->

                <div class="m-1">
                    <div class="card">
                        <div class="card-header">
                            <a class="btn" data-bs-toggle="collapse" href="#collapseFormularioFoja">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Subir una foja</h5>
                            </a>
                        </div>
                        <div id="collapseFormularioFoja" class="collapse" data-bs-parent="#accordion">
                            <div class="card-body">
                                <p class="card-text">En esta sección podrá subir las fojas.</p>
                                <div class="card-body">
                                    <form action="../php/procesarCargaLibro.php" method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="anio" class="form-label">Ingresar año:</label>
                                            <input type="number" name="anio" id="anio" class="form-control" min="1950" max="2150" a placeholder="Ejemplo: <?php echo $anio ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="periodo" class="form-label">Selecciona período:</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="periodo" id="enero-junio" value="ENERO-JUNIO" required>
                                                <label class="form-check-label" for="enero-junio">
                                                    ENERO-JUNIO
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="periodo" id="agosto-diciembre" value="AGOSTO-DICIEMBRE">
                                                <label class="form-check-label" for="agosto-diciembre">
                                                    AGOSTO-DICIEMBRE
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="numerolibro" class="form-label">Numero Foja:</label>
                                            <input type="number" name="numerolibro" id="numerolibro" class="form-control" placeholder="Ejemplo: 123"></input>
                                        </div>
                                        <div class="mb-3">
                                            <label for="libro" class="form-label">Seleccionar libro:</label>
                                            <select name="libro" id="libro" class="form-select"></select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pdf" class="form-label">Subir archivo PDF:</label>
                                            <input type="file" name="pdf" id="pdf" class="form-control" accept=".pdf">
                                        </div>
                                        <input type="hidden" name="subcarpeta" id="subcarpeta" value="">
                                        <button type="submit" class="btn btn-primary" name="submit">Subir PDF</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== Desasignar Foja ===== -->
                 <!-- HJ20250616 Seccion para desasignar fojas dandole un Libro y Seleccionando una Foja -->
                <div class="m-1">
                    <div class="card">
                        <div class="card-header">
                            <a class="btn" data-bs-toggle="collapse" href="#collapseDesasignarFoja">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Desasignar Foja</h5>
                            </a>
                        </div>
                        <div id="collapseDesasignarFoja" class="collapse" data-bs-parent="#accordion">
                            <div class="card-body">
                                <p class="card-text">En esta sección podrá desasignar una foja que ya esté asignada a un egresado.</p>
                                <form id="formDesasignarFoja">
                                    <div class="mb-3">
                                        <label for="selectLibroDes" class="form-label">Seleccione Libro:</label>
                                        <select id="selectLibroDes" class="form-select" required>
                                            <option value="">-- Elija el libro --</option>
                                            <?php
                                            $resLibrosDes = $conn->query("
                                SELECT Id_Libro, Descripcion_Libro FROM libro ORDER BY Id_Libro ASC
                            ");
                                            while ($filaDes = $resLibrosDes->fetch_assoc()) {
                                                echo "<option value='{$filaDes['Id_Libro']}'>{$filaDes['Descripcion_Libro']}</option>";
                                            }
                                            $resLibrosDes->free();
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="selectFojaDes" class="form-label">Número de Foja:</label>
                                        <select id="selectFojaDes" class="form-select" required>
                                            <option value="">-- Elija la foja --</option>
                                        </select>
                                    </div>
                                    <button type="button" id="btnDesasignarFoja" class="btn btn-danger">Desasignar Foja</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Fin Desasignar Foja -->
                <?php
            endif;
                ?>
                <br>
                <hr>
                <br>

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
    <script src="../js/tablaLibro.js"></script>
    <script src="../js/tablaFojas.js"></script>
    <script src="../js/desasignarFoja.js"></script>

    <?php if ($rol != 6) : ?>
        <script src="../js/libros.js"></script>
        <script src="../js/registroLibro.js"></script>
        <script src="../js/modificarLibros.js"></script>
    <?php endif; ?>

</body>

</html>