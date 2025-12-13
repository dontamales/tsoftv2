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
    <meta name="description" content="Base de estructura" />
    <title>T-Soft - Asignación de fecha de ceremonia</title>
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
            <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none">
                <i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable
            </a>
            <div class="page-header pt-3">
                <p class="h1">Lista de sustentantes con Anexo III aprobado</p>
                <hr><p class="h3">Correos enviados el dia de hoy: <?php echo ($cuenta);?></p>
            </div>
            <p class="card-text">En esta sección se puede visualizar los sustentantes que ya tienen su anexo III aprobado y por lo tanto, están listos para la asignación de fecha de ceremonia.</p>
            <hr />
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="mb-3">
                        <label for="busqueda" class="form-label">Buscar:</label>
                        <input type="text" class="form-control" id="busqueda" placeholder="Ingrese término de búsqueda">
                    </div>
                    <div class="table-responsive" style="max-height: 33.54rem; overflow-y: auto;">
                        <table id="ceremonia-table" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>No. control</th>
                                    <th>Nombres</th>
                                    <th>Carrera</th>
                                    <th>Proyecto</th>
                                    <th>Fecha asignada</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <button id="desasignar-fecha" class="btn btn-primary">Desasignar fecha a sustentantes seleccionados</button>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-3">
                    <hr />
                    <p class="h1">Asignación de fecha</p>
                    <form id="registro-form">
                        <p class="card-text">En esta sección se define la fecha y la hora de ceremonia para los sustentantes seleccionados en la tabla de arriba.</p>
                        <hr />
                        <div class="mb-3">
                            <label for="fechaHora" class="form-label">Fecha y hora de ceremonia:</label>
                            <input type="datetime-local" class="form-control" id="fechaHora" name="fechaHora">
                        </div>
                        <button id="asignar-fecha-seleccionados" class="btn btn-primary">Asignar fecha a sustentantes seleccionados</button>
                    </form>
                </div>
            </div>
        </main>

        <?php echo $footer; ?>
    </div>



    <!-- Librerías de JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Scripts propios -->
    <script>
        window.onunload = function() {
            // Esto es para que cuando se cierre la pestaña, se cierre la sesión
            window.location.replace("../index.php");
        };
    </script>
    <script src="../js/tablaAsignacionFechaCeremonia.js"></script>
</body>

</html>