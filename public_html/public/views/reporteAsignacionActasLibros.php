<?php
require_once '../php/sesion.php';
require_once '../php/auth.php';
require_roles([2, 3, 5, 6]);
include '../php/include/meta.php';
include '../php/include/icons.php';
include '../php/include/headerUsuarios.php';
include '../php/include/menuUsuarios.php';
include '../php/include/footerUsuarios.php';

date_default_timezone_set('America/Denver');
$conn->query("SET time_zone='-06:00'");
?>

<!DOCTYPE html>
<html lang="es-MX">

<head>
    <?php echo $meta; ?>
    <meta name="description" content="Reporte de actas asignadas a libros">
    <title>T-Soft - Reporte de Actas por Libro</title>
    <?php echo $icons; ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/pages/baseTsoft.css" />
</head>

<body>
    <?php echo $header; ?>
    <?php echo $menu; ?>

    <div class="main-container">
        <main class="content col ps-md-2 pt-2">
            <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none">
                <i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable
            </a>

            <div class="page-header pt-3">
                <p class="h1">Reporte de Actas por Libro</p>
            </div>
            <hr />

            <div class="row">
                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-<?php echo ($_SESSION['mensaje'] === "Reporte generado con éxito.") ? 'success' : 'danger'; ?>" role="alert">
                        <?php echo $_SESSION['mensaje'];
                        unset($_SESSION['mensaje']); ?>
                    </div>
                <?php endif; ?>

                <div class="col-12 mb-3">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Generación de reporte de actas asignadas a libros</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Seleccione el libro y el periodo de tiempo para generar el reporte.</p>

                            <form id="formato_Reporte_Actas_Libros" action="../php/generarReporteActasLibros.php" method="GET">
                                <!-- Selección de libro -->
                                <div class="mb-3 row">
                                    <label for="libro_seleccionado" class="col-sm-2 col-form-label">Libro:</label>
                                    <div class="col-sm-10">
                                        <select id="libro_seleccionado" name="libro_seleccionado" class="form-select" required>
                                            <option value="">Seleccione un libro</option>
                                            <?php
                                            $resLibrosDes = $conn->query("SELECT Id_Libro, Descripcion_Libro FROM libro ORDER BY Id_Libro ASC");
                                            while ($filaDes = $resLibrosDes->fetch_assoc()) {
                                                echo "<option value='{$filaDes['Id_Libro']}'>{$filaDes['Descripcion_Libro']}</option>";
                                            }
                                            $resLibrosDes->free();
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Selección de periodo -->
                                <div class="mb-3 row">
                                    <label for="periodo" class="col-sm-2 col-form-label">Periodo:</label>
                                    <div class="col-sm-10">
                                        <select id="periodo" name="periodo" class="form-select" required>
                                            <option value="">Seleccione un periodo</option>
                                            <option value="ENERO-JUNIO">ENERO-JUNIO</option>
                                            <option value="AGOSTO-DICIEMBRE">AGOSTO-DICIEMBRE</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Selección de año -->
                                <div class="mb-3 row">
                                    <label for="anio" class="col-sm-2 col-form-label">Año:</label>
                                    <div class="col-sm-10">
                                        <select id="anio" name="anio" class="form-select" required>
                                            <option value="">Seleccione un año</option>
                                            <?php
                                            $resAnios = $conn->query("SELECT DISTINCT Anio_Formato_Foja FROM formato_foja ORDER BY Anio_Formato_Foja DESC");
                                            while ($row = $resAnios->fetch_assoc()) {
                                                echo "<option value='{$row['Anio_Formato_Foja']}'>{$row['Anio_Formato_Foja']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Botón para generar reporte -->
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg rounded-pill">Generar reporte</button>
                                </div>
                            </form>

                            <hr>

                            <div class="table-card-style mt-4" style="max-height: 33.54rem; overflow-y: auto;">
                                <table class="table table-bordered table-hover table-striped" id="tabla-reporte-actas-libros">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha de creación</th>
                                            <th>Año</th>
                                            <th>Periodo</th>
                                            <th>Libro</th>
                                            <th>Descarga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Datos se llenan por JS -->
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

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/obtenerReportesActas.js"></script>
    

</body>
</html>