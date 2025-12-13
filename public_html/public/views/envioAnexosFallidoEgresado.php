<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
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
<html lang="es-MX">

<head>
    <!-- Etiquetas meta, íconos y otros... -->
    <?php echo $meta; ?>
    <meta name="description" content="Base de estructura" />
    <title>T-Soft - Anexos Creados</title>
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
                <p class="h1">Egresados con envío de anexo I y II sin éxito</p>
                <hr><p class="h3">Correos enviados el dia de hoy: <?php echo ($cuenta);?></p>
            </div>
            <hr />
            <div class="row">
                <div class="col">
                    <div class="table-responsive">
                        <table id="egresadoTable" class="table table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th> Nombre </th>
                                    <th>Apellidos</th>
                                    <th>Correo</th>
                                    <th>Numero de Control</th>
                                    <th>Tipo de titulación</th>
                                    <th>Proyecto</th>
                                    <th>Carrera</th>
                                    <th>Acciones</th> 
                            </thead>
                            <tbody>
                                <!-- Aquí se generará la tabla con datos obtenidos a través de AJAX -->
                            </tbody>
                        </table>
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

    <!-- Script de AJAX -->
    <script>
        window.onunload = function() {
            // Esto es para que cuando se cierre la pestaña, se cierre la sesión
            window.location.replace("../index.php");
        };
    </script>
    <script type="module">
        import {
            obtenerEstatus,
            generarTabla,
            generarDocumento
        } from '../js/anexos1y2.js';

        window.onload = function() {
            obtenerEstatus(4).then(generarTabla);
        }
    </script>
</body>

</html>