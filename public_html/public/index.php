<?php
require_once 'php/sesion.php'; #VERIFICACIÓN DE SESIÓN
// Verificar si el usuario ya ha iniciado sesión
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    $dashboardUrl = '';
    switch ($_SESSION['user_role']) {
        case "1":
            $dashboardUrl = 'views/userDashboard.php';
            break;
        case "2":
            $dashboardUrl = 'views/adminDashboard.php';
            break;
        case "3":
            $dashboardUrl = 'views/adminDashboard.php';
            break;
        case "4":
            $dashboardUrl = 'views/adminDashboard.php';
            break;
        case "5":
            $dashboardUrl = 'views/adminDashboard.php';
            break;
        case "6":
            $dashboardUrl = 'views/adminDashboard.php';
            break;
    }
}
include 'php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include 'php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include 'php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA
?>

<!DOCTYPE html>
<html lang="es-MX">

<head>
    <!-- Etiquetas meta, íconos y otros... -->
    <?php echo $meta; ?>
    <meta name="description" content="Pestaña de inicio de sesión de plataforma T-Soft" />
    <title>T-Soft - Inicio de sesión</title>
    <?php echo $icons; ?>

    <!-- Hojas de estilo... -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/pages/index.css" />

</head>

<body>
    <?php if (isset($dashboardUrl) && $dashboardUrl !== ''): ?>
        <div class="alert alert-info" role="alert">
            Ya ha iniciado sesión. Por favor, haga clic <a href="<?= $dashboardUrl ?>">aquí para ir a su panel de control</a> o haga clic <a href="php/logout.php">aquí para salir</a> .
        </div>
    <?php else: ?>
        <!--<header class="header container-fluid border-radius">
            <div class="header__div shadow container-fluid rounded">
                <img class="container-fluid rounded mx-auto d-block" id="logoSuperior" src="assets/img/logo/logo.png" alt="Logo de T-Soft" />
            </div>
        </header>-->
        <!-- HEADER --> <!-- JH20251905 -->
        <header class="header__div mb-4">
            <img class="container-fluid rounded mx-auto mt-2 d-block" id="logoSuperior" src="assets/img/logo/logo-tsoft.png" alt="Logo de T-Soft" />
        </header>

        <!-- CONTENEDOR PRINCIPAL --> <!-- JH20252005 -->
        <!-- <main class="content main container pb-5"> -->
        <main class="content main-container">
            <!-- CONTENEDOR de INICIO DE SESIÓN -->
            <!-- <div class="main__inicioSesion shadow container rounded-5 bg-light p-5 "> -->
            <div class="login-card card mx-auto mb-3 p-auto shadow-lg">
                <!-- <p class="main__inicioSesion--header h1 text-center">Iniciar sesión</p> -->
                <h1 class="text-center mb-4">Iniciar sesión</h1>
                <form action="php/login.php" id="login-form" name="loginform" target="_self" method="POST">
                    <div class="row">
                        <div id="login-alert" class="alert alert-danger" role="alert" style="display:none;">
                        </div>
                    </div>
                    <!--<div class="container">
                        <i class="bi-envelope me-1" style="font-size: 2rem;"></i> <label class="main__inicioSesion--femail h3" for="femail">Correo:</label><br />
                        <div class="row">
                            <div class="col">
                                <input class="main__inicioSesion--finput container-fluid" type="email" id="femail" name="femail" maxlength="45" placeholder="Correo electrónico" autocomplete="email" required autofocus />
                            </div>
                        </div>
                        <br />
                        <i class="bi-key me-1" style="font-size: 2rem;"></i><label class="main__inicioSesion--fpass h3" for="fpass">Contraseña:</label><br />
                        <div class="row">
                            <div class="col">
                                <input class="main__inicioSesion--finput container-fluid" type="password" id="fpass" name="fpass" maxlength="255" placeholder="Contraseña" autocomplete="current-password" required />
                            </div>
                        </div>
                        <br />
                        <br />
                        <input id="login-button" class="main__inicioSesion--submit container-fluid btn btn-primary btn-block rounded-pill" name="finput" type="submit" data-bs-toggle="Iniciar T-Soft" value="Iniciar sesión" for="loginform" />
                    </div>-->
                    <!-- CAMPO DE CORREO -->
                    <div class="mb-3">
                        <label for="femail" class="form-label visually-hidden">Correo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="femail" name="femail" maxlength="45" placeholder="Correo electrónico" autocomplete="email" required autofocus />
                        </div>
                    </div>

                    <!-- CAMPO DE CONTRASEÑA -->
                    <div class="mb-4">
                        <label for="fpass" class="form-label visually-hidden">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="fpass" name="fpass" maxlength="255" placeholder="Contraseña" autocomplete="current-password" required />
                        </div>
                    </div>

                    <!-- BOTÓN DE ENVÍO -->
                    <button id="login-button" type="submit" class="btn btn-primary w-100 rounded-pill">Iniciar sesión</button>
                </form>
            </div>

            <!-- DIV DE PREGUNTAS FRECUENTES -->
            <div class="faq shadow container rounded-5 p-5">
                <p class="faq-header text-center display-5">
                    Ayuda y preguntas frecuentes
                </p>
                <div class="main__preguntasFrecuentes__info con" id="accordion">

                    <!-- DIV DEL HEADER DE LA TARJETA 1 -->
                    <!--<div class="main__preguntasFrecuentes__info__tarjeta1 card">-->
                    <div class="faq-question card">
                        <!--<div class="main__preguntasFrecuentes__info__tarjeta1--header card-header">-->
                        <div class="faq-question-header card-header">
                            <a class="faq-btn btn w-100" data-bs-toggle="collapse" href="#collapseOne">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>¿Qué es T-Soft?</h5>
                            </a>
                        </div>
                        <div id="collapseOne" class="collapse" data-bs-parent="#accordion">
                            <!--<div class="main__preguntasFrecuentes__info__tarjeta1--texto card-body">-->
                            <div class="faq-answer card-body">
                                T-Soft es la plataforma de Coordinación de Titulación del ITCJ,
                                por favor, proceda a iniciar sesión con su correo registrado y
                                la contraseña que se le mandó por correo electrónico.
                            </div>
                        </div>
                    </div>

                    <!-- DIV DE LA TARJETA 2 DE PREGUNTAS -->
                    <!--<div class="main__preguntasFrecuentes__info__tarjeta2 card">-->
                    <div class="faq-question card">
                        <!--<div class="main__preguntasFrecuentes__info__tarjeta2--header card-header">-->
                        <div class="faq-question-header card-header">
                            <a class="faq-btn btn w-100" data-bs-toggle="collapse" href="#collapseTwo">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>¿Dónde conocer los requisitos de titulación?</h5>
                            </a>
                        </div>
                        <div id="collapseTwo" class="collapse" data-bs-parent="#accordion">
                            <!--<div class="main__preguntasFrecuentes__info__tarjeta2--texto card-body">-->
                            <div class="faq-answer card-body">
                                Ingrese a la página del ITCJ: <a href="http://cdjuarez.tecnm.mx/" target="_blank">cdjuarez.tecnm.mx</a> y en la esquina superior derecha da clic a "Egresados", luego diríjase a la opción "Proceso de titulación" y después elija "Licenciatura" o "Posgrado" según sea el caso. <br><br>Dentro encontrará un resumen del proceso, la lista de requisitos y las fechas para cada actividad.
                            </div>
                        </div>
                    </div>

                    <!-- DIV DE LA TARJETA 3 DE PREGUNTAS -->
                    <!--<div class="main__preguntasFrecuentes__info__tarjeta3 card">-->
                    <div class="faq-question card">
                        <!--<div class="main__preguntasFrecuentes__info__tarjeta3--header card-header">-->
                        <div class="faq-question-header card-header">
                            <a class="faq-btn btn w-100" data-bs-toggle="collapse" href="#collapseThree">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>¿Dónde se inicia el proceso de titulación?</h5>
                            </a>
                        </div>
                        <div id="collapseThree" class="collapse" data-bs-parent="#accordion">
                            <!--<div class="main__preguntasFrecuentes__info__tarjeta3--texto card-body">-->
                            <div class="faq-answer card-body">
                                Con su registro en la convocatoria que Servicios Escolares lanza inicio de semestre.
                            </div>
                        </div>
                    </div>

                    <!-- DIV DE LA TARJETA 4 DE PREGUNTAS -->
                    <!--<div class="main__preguntasFrecuentes__info__tarjeta4 card">-->
                    <div class="faq-question card">
                        <!--<div class="main__preguntasFrecuentes__info__tarjeta4--header card-header">-->
                        <div class="faq-question-header card-header">
                            <a class="faq-btn btn w-100" data-bs-toggle="collapse" href="#collapseFour">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Si ya me registré en la convocatoria, ¿qué sigue?</h5>
                            </a>
                        </div>
                        <div id="collapseFour" class="collapse" data-bs-parent="#accordion">
                            <!--<div class="main__preguntasFrecuentes__info__tarjeta4--texto card-body">-->
                            <div class="faq-answer card-body">
                                Si usted es de número de control del 2010 en delante, debe acceder a Moodle para subir sus documentos. <br><br>Si usted es de un plan anterior a competencias, comuníquese con Servicios Escolares para que lo orienten en la entrega de documentos.
                            </div>
                        </div>
                    </div>

                    <!-- DIV DE LA TARJETA 5 DE PREGUNTAS -->
                    <!--<div class="main__preguntasFrecuentes__info__tarjeta5 card">-->
                    <div class="faq-question card">
                        <!--<div class="main__preguntasFrecuentes__info__tarjeta5--header card-header">-->
                        <div class="faq-question-header card-header">
                            <a class="faq-btn btn w-100" data-bs-toggle="collapse" href="#collapseFive">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Ya entregué papelería y Servicios Escolares la aceptó.</h5>
                            </a>
                        </div>
                        <div id="collapseFive" class="collapse" data-bs-parent="#accordion">
                            <!--<div class="main__preguntasFrecuentes__info__tarjeta5--texto card-body">-->
                            <div class="faq-answer card-body">
                                Titulación lo registra en la plataforma y se le enviará un correo con su acceso, tendrá que ingresar y llenar la información que se le solicita para continuar con la petición de sus sinodales.
                            </div>
                        </div>
                    </div>

                    <!-- DIV DE LA TARJETA 6 DE PREGUNTAS -->
                    <!-- <div class="main__preguntasFrecuentes__info__tarjeta6 card"> -->
                    <div class="faq-question card">
                        <!--<div class="main__preguntasFrecuentes__info__tarjeta6--header card-header">-->
                        <div class="faq-question-header card-header">
                            <a class="faq-btn btn w-100" data-bs-toggle="collapse" href="#collapseSix">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>¿Quién asigna los sinodales?</h5>
                            </a>
                        </div>
                        <div id="collapseSix" class="collapse" data-bs-parent="#accordion">
                            <!--<div class="main__preguntasFrecuentes__info__tarjeta6--texto card-body">-->
                            <div class="faq-answer card-body">
                                Titulación envía su petición de sinodales, pero es el Departamento Académico de su carrera quién le asigna sus sinodales.
                            </div>
                        </div>
                    </div>

                    <!-- DIV DE LA TARJETA 7 DE PREGUNTAS -->
                    <!--<div class="main__preguntasFrecuentes__info__tarjeta7 card">-->
                    <div class="faq-question card">
                        <!--<div class="main__preguntasFrecuentes__info__tarjeta7--header card-header">-->
                        <div class="faq-question-header card-header">
                            <a class="faq-btn btn w-100" data-bs-toggle="collapse" href="#collapseSeven">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Los sinodales ya me liberaron, ¿qué hago?</h5>
                            </a>
                        </div>
                        <div id="collapseSeven" class="collapse" data-bs-parent="#accordion">
                            <!--<div class="main__preguntasFrecuentes__info__tarjeta7--texto card-body">-->
                            <div class="faq-answer card-body">
                                Conseguir el anexo III, llevarlo a firmas y subirlo en la plataforma para su validación, posteriormente tendrá que acercarse a titulación por la autorización de su segundo pago. </div>
                        </div>
                    </div>

                    <!-- DIV DE LA TARJETA 8 DE PREGUNTAS -->
                    <!--<div class="main__preguntasFrecuentes__info__tarjeta8 card">-->
                    <div class="faq-question card">
                        <!--<div class="main__preguntasFrecuentes__info__tarjeta8--header card-header">-->
                        <div class="faq-question-header card-header">
                            <a class="faq-btn btn w-100" data-bs-toggle="collapse" href="#collapseEight">
                                <h5 class="card-title"><i class="m-1 bi bi-chevron-down"></i>Opciones de titulación.</h5>
                            </a>
                        </div>
                        <div id="collapseEight" class="collapse" data-bs-parent="#accordion">
                            <!--<div class="main__preguntasFrecuentes__info__tarjeta8--texto card-body">-->
                            <div class="faq-answer card-body">
                                ¿Sabía que los primeros dos dígitos de su número de control indican el año en el que entró al ITCJ?<br><br>Si usted es plan competencias, entonces los primeros dos dígitos de su número de control son 10 o superior, por ejemplo: 1011XXXX, 1311XXXX, 1711XXXX, son números de control de <b>plan competencias</b> y sus opciones de titulación son las siguientes:<br><br>
                                <ul>
                                    <li>Informe técnico de residencia profesional **</li>
                                    <li>Tésis</li>
                                    <li>Tesina</li>
                                    <li>Proyecto de Investigación</li>
                                    <li>Informe de Estancia (al menos 18 meses de egresado)</li>
                                    <li><a href="https://ceneval.edu.mx/examenes-egreso-egel/" target="_blank">EGEL (Ceneval)</a></li>
                                </ul>
                                ** Siempre y cuándo su residencia haya sido apta para titulación.
                                <br><br>
                                <strong>Si su número de control es anterior al 10, ponga atención a los siguiente: </strong>
                                <br><br>
                                Si su número de control es anterior al 04, por ejemplo: 8311XXX, 9411XXXX, 0311XXXX, puede presentar las siguientes opciones:
                                <ul>
                                    <li>I. Tésis Profesional.</li>
                                    <li>II. Libro de Texto o Prototipo Didáctico.</li>
                                    <li>III. Proyecto de investigacion.</li>
                                    <li>IV. Diseño o rediseño de Equipo, Aparato o Maquinaria.</li>
                                    <li><del>V. Curso Especial de Titulación.</del></li>
                                    <li>VI. Examen Global por Áreas de Conocimiento.</li>
                                    <li>VII. Memoria de Experiencia.</li>
                                    <li>VIII. Escolaridad por Promedio.</li>
                                    <li>IX. Escolaridad por Estudios de Posgrados.</li>
                                </ul>
                                Si su número de control comienza con 04 y 05, por ejemplo: 0411XXXX, 0511XXXX, puede presentar las siguientes opciones:
                                <br><br>
                                <ul>
                                    <li>I. Tésis Profesional.</li>
                                    <li>III. Proyecto de investigación.</li>
                                    <li>VI. Examen Global por Áreas de Conocimiento.</li>
                                    <li>VIII. Escolaridad por Promedio</li>
                                </ul>
                                Los números de control que inician en 06 hasta el 09: 0611XXXX, 0811XXXX, 0911XXXX presentan uno de los siguientes productos:
                                <ul>
                                    <li>I. Tésis Profesional.</li>
                                    <li>III. Proyecto de investigación.</li>
                                    <li>VIII. Escolaridad por Promedio</li>
                                    <li><a href="https://ceneval.edu.mx/examenes-egreso-egel/" target="_blank">EGEL (Ceneval)</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- FOOTER DEL INICIO DE SESIÓN -->
        <div>
            <?php echo $footer; ?>
        </div>

        <!-- Librerías de JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

        <!-- Scripts propios -->
        <script src="js/login.js"></script>

    <?php endif; ?>

</body>

</html>