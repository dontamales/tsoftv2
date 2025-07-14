<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");


// Obtener la fecha actual
$fecha_actual = date("Y-m-d"); // Formato: YYYY-MM-DD
$mes_actual = date("m"); // Formato: MM
$ano_actual = date("Y"); // Formato: YYYY

// Definir la variable $periodo en función del mes actual
if ($mes_actual >= 1 && $mes_actual <= 6) {
    $periodo = "Enero-Junio";
    $fecha_Inicio_Periodo = $ano_actual . "-01-01";
    $fecha_Cierre_Periodo = $ano_actual . "-06-30";
} else {
    $periodo = "Agosto-Diciembre";
    $fecha_Inicio_Periodo = $ano_actual . "-08-01";
    $fecha_Cierre_Periodo = $ano_actual . "-12-31";
}
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
                <p class="h1">Reporte de eficiencia terminal: <?php echo $periodo . " " . $ano_actual ?></p>
            </div>
            <hr />
            <div class="row">
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
                        <div class="col-12 mb-3">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title">Generación de reporte de eficiencia terminal: <?php echo $periodo . " " . $ano_actual ?></h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">En esta sección podrá generar de manera un reporte de eficiencia terminal del periodo actual con sólo presionar un botón y podrá descargar los anteriores de la lista.</p>
                                    <div class="mb-1">
                                        <form id="formato_Eficiencia_Terminal" action="../php/generarReporteEficienciaTerminal.php" method="post">
                                            <div class="row m-5 text-center justify-content-center align-items-center">
                                                <div class="col">
                                                </div>
                                                <div class="col">
                                                    <input id="btn_Generar_Reporte_Titulados" class="btn btn-primary btn-block rounded-pill" name="btn_Generar_Eficiencia_Terminal" type="submit" data-bs-toggle="Generar reporte de eficiencia terminal" value="Generar reporte de eficiencia terminal" for="formato_Eficiencia_Terminal" />
                                                </div>
                                                <div class="col">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="table-responsive" style="max-height: 33.54rem; overflow-y: auto;">
                                        <table class="table table-bordered table-hover table-striped" id="tabla-eficiencia-terminal">
                                            <thead>
                                                <tr>
                                                    <th>Reporte No.</th>
                                                    <th>Fecha creación</th>
                                                    <th>Periodo</th>
                                                    <th>Total inscritos</th>
                                                    <th>Total titulados</th>
                                                    <th>Total no titulados</th>
                                                    <th>Eficiencia terminal</th>
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
    <script src="../js/obtenerReporteEficienciaTerminal.js"></script>
</body>

</html>