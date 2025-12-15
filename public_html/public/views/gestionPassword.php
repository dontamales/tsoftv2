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

// Fecha de hoy
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
    <meta name="description" content="Restablecer contraseña de usuario" />
    <title>T-Soft - Restablecer contraseña</title>
    <?php echo $icons; ?>

    <!-- Hojas de estilo -->
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

<body>
    <?php echo $header; ?>

    <?php echo $menu; ?>

    <div class="main-container">
        <main id="mainContent" class="content col ps-md-2 pt-2">
            <div class="page-header pt-3">
                <h1>Restablecer contraseña de un usuario</h1>
                <hr>
                <h3>Correos enviados el día de hoy: <?php echo $cuenta; ?></h3>
            </div>
            <hr />

            <!-- Mensajes de alerta -->
            <?php if (isset($_SESSION['message'])) : ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif ?>

            <?php if (isset($_SESSION['success'])) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif ?>

            <!-- Card con el formulario -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-lock me-2"></i>Restablecer contraseña de un usuario
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Como medida de seguridad en caso de que un usuario olvide su contraseña, es posible para un administrador cambiar la contraseña rellenando los siguientes campos a continuación con la contraseña de su preferencia.
                        <br />
                        <strong class="text-danger">La contraseña debe tener al menos 8 caracteres.</strong>
                    </p>

                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8 col-lg-6">
                            <form action="../php/actualizarPassword.php" method="POST" id="formResetPassword">
                                <div class="mb-3">
                                    <label for="restablecerPasswordCorreo" class="form-label">
                                        <i class="bi bi-envelope me-1"></i>Correo electrónico:
                                    </label>
                                    <input 
                                        type="email" 
                                        class="form-control" 
                                        id="restablecerPasswordCorreo" 
                                        name="restablecerPasswordCorreo" 
                                        placeholder="usuario@ejemplo.com"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="restablecerPasswordNuevo" class="form-label">
                                        <i class="bi bi-key me-1"></i>Nueva contraseña:
                                    </label>
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        id="restablecerPasswordNuevo" 
                                        name="restablecerPasswordNuevo" 
                                        minlength="8"
                                        placeholder="Mínimo 8 caracteres"
                                        required 
                                        autocomplete="new-password">
                                </div>

                                <div class="mb-3">
                                    <label for="restablecerPasswordConfirmar" class="form-label">
                                        <i class="bi bi-check-circle me-1"></i>Confirmar contraseña:
                                    </label>
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        id="restablecerPasswordConfirmar" 
                                        name="restablecerPasswordConfirmar" 
                                        minlength="8"
                                        placeholder="Repita la contraseña"
                                        required 
                                        autocomplete="new-password">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-save me-2"></i>Cambiar contraseña
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php echo $footer; ?>
    </div>

    <!-- Librerías de JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

    <!-- Sidebar -->
    <script src="../js/sidebar.js" defer></script>

    <!-- Scripts propios -->
    <script>
        window.onunload = function() {
            // Esto es para que cuando se cierre la pestaña, se cierre la sesión
            window.location.replace("../index.php");
        };

        // Validación de contraseñas coincidentes
        document.getElementById('formResetPassword').addEventListener('submit', function(e) {
            const password = document.getElementById('restablecerPasswordNuevo').value;
            const confirmPassword = document.getElementById('restablecerPasswordConfirmar').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres.');
                return false;
            }
        });
    </script>
</body>

</html>