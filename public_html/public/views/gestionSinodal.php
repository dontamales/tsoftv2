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

?>

<!DOCTYPE html>
<html lang="es-MX"> <!-- LENGUAJE DE LA PÁGINA WEB (PARA TRADUCTORES) -->

<head>
    <!-- Etiquetas meta, íconos y otros... -->
    <?php echo $meta; ?>
    <meta name="description" content="Base de estructura" />
    <title>T-Soft - Gestión de asignacion de sinodales</title>
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
                <h1>Registro de sinodales.</h1>
            </div>
            <hr />
            <div class="row">
                <div>
                    <form id="formularioRegistroSinodales" class="mb-3">

                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title">Sinodal Presidente</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">En esta sección podrá definir el sinodal presidente para sustentante.</p>

                                <div class="mb-3">
                                    <label for="nombre4" class="form-label">Nombre del sinodal:</label>
                                    <input type="text" name="nombre4" id="nombre4" class="form-control" data-id="" placeholder="Escriba el profesor" required>
                                    <div id="nombre4-suggestions" class="list-group"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title">Sinodal Secretario</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">En esta sección podrá definir el sinodal secretario para sustentante.</p>
                                <div class="mb-3">
                                    <label for="nombre1" class="form-label">Nombre del sinodal:</label>
                                    <input type="text" name="nombre1" id="nombre1" class="form-control" data-id="" placeholder="Escriba el profesor" required>
                                    <div id="nombre1-suggestions" class="list-group"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title">Sinodal Vocal</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">En esta sección podrá definir el sinodal vocal para sustentante.</p>

                                <div class="mb-3">
                                    <label for="nombre2" class="form-label">Nombre del sinodal:</label>
                                    <input type="text" name="nombre2" id="nombre2" class="form-control" data-id="" placeholder="Escriba el profesor" required>
                                    <div id="nombre2-suggestions" class="list-group"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title">Sinodal Vocal Suplente</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">En esta sección podrá definir el sinodal vocal para sustentante.</p>

                                <div class="mb-3">
                                    <label for="nombre3" class="form-label">Nombre del sinodal:</label>
                                    <input type="text" name="nombre3" id="nombre3" class="form-control" data-id="" placeholder="Escriba el profesor" required>
                                    <div id="nombre3-suggestions" class="list-group"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="egresado" class="form-label">Sustentante:</label>
                            <input type="text" name="egresado" id="egresado" class="form-control" data-id="" required placeholder="Ingrese el numero de control de un egresado">
                            <div id="egresado-suggestions" class="list-group"></div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary btn-block">Registrar Sinodales</button>
                        </div>

                    </form>
                </div>
            </div>
        </main>
        <?php echo $footer; ?>
    </div>

    <!-- Librerías de JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script><!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

    <!-- Scripts propios -->
    <script>
        window.onunload = function() {
            // Esto es para que cuando se cierre la pestaña, se cierre la sesión
            window.location.replace("../index.php");
        };
    </script>
    <script>
        function configureAutoComplete(inputId) {
            const nombreInput = document.getElementById(inputId);
            const suggestionsDiv = document.getElementById(`${inputId}-suggestions`);

            nombreInput.addEventListener('input', async function() {
                const inputValue = this.value.trim();
                suggestionsDiv.innerHTML = '';

                if (inputValue.length > 0) {
                    const response = await fetch(`../php/obtenerProfesores.php?q=${inputValue}`);
                    const data = await response.json();

                    data.forEach(profesor => {
                        const suggestionItem = document.createElement('a');
                        suggestionItem.classList.add('list-group-item', 'list-group-item-action');
                        suggestionItem.textContent = profesor.nombre;
                        suggestionItem.addEventListener('click', () => {
                            nombreInput.value = profesor.nombre;
                            nombreInput.dataset.id = profesor.id; // Asigna el id aquí
                            suggestionsDiv.innerHTML = '';
                        });
                        suggestionsDiv.appendChild(suggestionItem);
                    });
                }
            });
        }
        configureAutoComplete('nombre1');
        configureAutoComplete('nombre2');
        configureAutoComplete('nombre3');
        configureAutoComplete('nombre4');
    </script>
    <script>
        const egresadoInput = document.getElementById('egresado');
        const egresadoSuggestionsDiv = document.getElementById('egresado-suggestions');

        egresadoInput.addEventListener('input', async function() {
            const inputValue = this.value.trim();
            egresadoSuggestionsDiv.innerHTML = '';

            if (inputValue.length > 0) {
                const response = await fetch('../php/obtenerDatosEgresadoSinodalia.php');
                const data = await response.json();
                data.forEach(egresado => {
                    if (egresado.nombre.toLowerCase().includes(inputValue.toLowerCase())) {
                        const suggestionItem = document.createElement('a');
                        suggestionItem.classList.add('list-group-item', 'list-group-item-action');
                        suggestionItem.textContent = egresado.nombre;
                        suggestionItem.addEventListener('click', () => {
                            egresadoInput.value = egresado.nombre;
                            egresadoInput.dataset.id = egresado.id; // Asigna el id aquí
                            egresadoSuggestionsDiv.innerHTML = '';
                        });
                        egresadoSuggestionsDiv.appendChild(suggestionItem);
                    }
                });
            }
        });
    </script>
    <script src="../js/registroSinodales.js"></script>

</body>

</html>