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
                <p class="h1 text-center">Membretes para los documentos</p>
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
                <div class="card-header" style="background-color: #3f6b83; color: white;">
                    <h5 class="card-title">Sobrescribir los membretes de los documentos.</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Aquí se pueden subir las imagenes para los membretes de los documentos, deben ser formatos PNG.<br><br><strong>Favor de subir los tres membretes a la vez.</strong></p>
                </div>
                <div class="row justify-content-center">
                    <div class="p-2 mb-4 mt-2 col-md-6">
                        <form action="../php/actualizarMembretes.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="actualizarMembreteEncabezado" class="form-label">Encabezado:</label>
                                <input type="file" class="form-control" id="encabezado_Membrete" name="encabezado_Membrete" accept=".png, .jpg, .jpeg" for="actualizarMembrete">
                            </div>
                            <div class="mb-3">
                                <label for="actualizarMembretePie" class="form-label">Pie:</label>
                                <input type="file" class="form-control" id="pie_Membrete" name="pie_Membrete" accept=".png, .jpg, .jpeg" for="actualizarMembretePie">
                            </div>
                            <div class="mb-3">
                                <label for="actualizarMembreteFirma" class="form-label">Firma de encargado de Coordinación de Titulación:</label>
                                <input type="file" class="form-control" id="firma_Membrete" name="firma_Membrete" accept=".png, .jpg, .jpeg" for="actualizarMembreteFirma">
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Actualizar membretes para los documentos</button>
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
    <!-- Sidebar JH20250710 -->
    <script src="../js/sidebar.js" defer></script>
    <script>
        window.onunload = function() {
            // Esto es para que cuando se cierre la pestaña, se cierre la sesión
            window.location.replace("../index.php");
        };
    </script>
</body>

</html>