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

?>

<!DOCTYPE html>
<html lang="es-MX"> <!-- LENGUAJE DE LA PÁGINA WEB (PARA TRADUCTORES) -->

<head>
    <!-- Etiquetas meta, íconos y otros... -->
    <?php echo $meta; ?>
    <meta name="Asignación de super usuario" content="Base de estructura" />
    <title>T-Soft - Cambiar variables globales</title>
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
                <p class="h1">Cambiar variables globales</p>
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
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Cambiar variables globales</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Aquí se pueden modificar las variables globales como el horario de atención y el precio de examen para mostrar en la plataforma.<br><br><strong>Favor de rellenar todos los campos.</strong></p>
                </div>
                <div class="row justify-content-center">
                    <div class="p-2 mb-4 mt-2 col-md-6">
                        <form action="../php/modificarVariablesGlobales.php" method="POST">
                            <div class="mb-3">
                                <label for="modificarVariablesHorario" class="form-label">Horario de atención:</label>
                                <textarea rows="5" cols="50" class="form-control" id="modificarVariablesHorario" name="modificarVariablesHorario" placeholder="Lunes a Miércoles: 07:00 - 15:00 y 17:00 - 21:00 horas. <br><br>Jueves y Viernes: 07:00 - 21:00 horas." required style="resize:none;"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="modificarVariablesPrecioExamenPro" class="form-label">Precio del examen profesional:</label>
                                <input type="number" class="form-control" id="modificarVariablesPrecioExamenPro" name="modificarVariablesPrecioExamenPro" placeholder="1200" required>
                            </div>
                            <div class="mb-3">
                                <label for="modificarVariablesCorreoTitulacion" class="form-label">Correo de Coordinación de Titulación:</label>
                                <input type="email" class="form-control" id="modificarVariablesCorreoTitulacion" name="modificarVariablesCorreoTitulacion" placeholder="coordinacion_titulacion@cdjuarez.tecnm.mx" required>
                            </div>
                            <div class="mb-3">
                                <label for="modificarVariablesTelefonoTitulacion" class="form-label">Teléfono de Coordinación de Titulación:</label>
                                <input type="text" class="form-control" id="modificarVariablesTelefonoTitulacion" name="modificarVariablesTelefonoTitulacion" placeholder="688-25-00 ext. 2323 y 2322." required>
                            </div>
                            <div class="mb-3">
                                <label for="modificarVariablesUbicacionTitulacion" class="form-label">Ubicación de Coordinación de Titulación:</label>
                                <input type="text" class="form-control" id="modificarVariablesUbicacionTitulacion" name="modificarVariablesUbicacionTitulacion" placeholder="Oficina de Titulación en edificio Guillot" required>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Actualizar variables globales</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <?php echo $footer; ?>
    </div>
    <!-- Librerías de JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

    <!-- Scripts propios -->
    <script>
        window.onunload = function() {
            // Esto es para que cuando se cierre la pestaña, se cierre la sesión
            window.location.replace("../index.php");
        };
    </script>
</body>

</html>