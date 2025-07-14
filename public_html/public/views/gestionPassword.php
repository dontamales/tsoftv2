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
                <p class="h1">Restablecer contraseña de un usuario</p>
                <hr><p class="h3">Correos enviados el día de hoy: <?php echo ($cuenta);?></p>
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
                    <h5 class="card-title">Restablecer contraseña de un usuario </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Como medida de seguridad en caso de que un usuario olvide su contraseña, es posible para un administrador cambiar la contraseña rellenando los siguientes campos a continuación con la contraseña de su preferencia.<br /><strong>La contraseña debe tener al menos 8 caracteres.</strong></p>
                </div>
                <div class="row justify-content-center">
                    <div class="p-2 mb-4 mt-2 col-md-6">
                        <form action="../php/actualizarPassword.php" method="POST">
                            <div class="mb-3">
                                <label for="restablecerPasswordCorreo" class="form-label">Correo:</label>
                                <input type="email" class="form-control" id="restablecerPasswordCorreo" name="restablecerPasswordCorreo" required>
                            </div>
                            <div class="mb-3">
                                <label for="restablecerPasswordNuevo" class="form-label">Nueva contraseña:</label>
                                <input type="password" class="form-control" id="restablecerPasswordNuevo" name="restablecerPasswordNuevo" required autocomplete="new-password">
                            </div>
                            <div class="mb-3">
                                <label for="restablecerPasswordConfirmar" class="form-label">Confirmar contraseña:</label>
                                <input type="password" class="form-control" id="restablecerPasswordConfirmar" name="restablecerPasswordConfirmar" required autocomplete="new-password">
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                            </div>
                        </form>
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