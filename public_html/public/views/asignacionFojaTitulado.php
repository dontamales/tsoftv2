<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_once '../php/obtenerTituladoFoja.php';
require_once '../php/obtenerFoja.php';
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA
?>
<!-- Hojas de estilo... -->
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
            <div class="page-header pt-3 text-center">
                <p class="h1">Asignación de Fojas</p>
            </div>
            <hr />
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Egresados titulados</h5>
                            <p class="card-text">En esta sección podrá asignar un libro a los egresados ya titulados.</p>
                            <div class="mb-3">
                                <label for="campoTexto" class="form-label">Egresado disponible</label>
                                <input type="text" id="filtroEgresado" class="form-control mb-2 text-center" placeholder="Buscar egresado">
                                <select class="form-select text-center" id="campoTexto" onchange="mostrarCampoOculto()">
                                    <option value="">Selecciona un egresado</option>
                                    <?php
                                    // Genera las opciones de la lista desplegable
                                    foreach ($egresados as $numControl => $nombreUsuario) {
                                        echo "<option value='$numControl'>$numControl - $nombreUsuario</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="campoOculto" style="display: none;">
                <div class="col-12 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Asignacion preparada</h5>
                            <div class="mb-3">
                                <div>
                                    <label for="numControl" class="form-label">Num. Control:</label>
                                    <input type="text" class="form-control text-center" id="numControl" readonly>
                                </div>
                                <div>
                                    <label for="tipoProducto" class="form-label">Tipo de titulacion:</label>
                                    <input type="text" class="form-control text-center" id="tipoProducto" readonly>
                                </div>
                                <p></p>
                                <div class="container">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title">Asignacion</h5>
                                            <p class="card-text">En esta sección podrá asignarle al titulado el formato de foja al que corresponde, según los disponibles y añadidos.</p>
                                            <div class="form-group">
                                                <label for="libro" class="form-label">Libros Disponibles:</label>
                                                <select class="form-select text-center" id="libro">
                                                    <option value="">Seleccione el libro</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="formatoFoja" class="form-label">Formato de Foja:</label>
                                                <select class="form-select text-center" id="formatoFoja">
                                                    <option value="">Seleccione el formato de foja</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p></p>
                                <button type="button" class="btn btn-outline-success btn-lg bi bi-file-earmark-check" id="btnAsignarArchivo"> Asignar archivo</button>
                            </div>
                        </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <!-- Scripts propios -->
    <!-- Sidebar JH20250710 -->
    <script src="../js/sidebar.js" defer></script>
    <script>
        window.onunload = function() {
            // Esto es para que cuando se cierre la pestaña, se cierre la sesión
            window.location.replace("../index.php");
        };
    </script>

    <!-- Scripts para desplegar el campo oculto -->
    <script>
        function mostrarCampoOculto() {
            var filtro = document.getElementById("filtroEgresado").value.toLowerCase();
            var select = document.getElementById("campoTexto");
            var campoOculto = document.getElementById("campoOculto");
            var numControlInput = document.getElementById("numControl");
            var tipoProductoInput = document.getElementById("tipoProducto");

            // Filtrar las opciones de la lista desplegable
            for (var i = 0; i < select.options.length; i++) {
                var optionText = select.options[i].text.toLowerCase();
                if (optionText.includes(filtro)) {
                    select.options[i].style.display = "";
                } else {
                    select.options[i].style.display = "none";
                }
            }

            if (select.value !== "" && select.options[select.selectedIndex].style.display !== "none") {
                var numControl = select.value;

                var xhrEgresado = new XMLHttpRequest();
                xhrEgresado.open("GET", "../php/obtenerTodosLosDatosEgresado.php?numControl=" + numControl, true);

                xhrEgresado.onload = function() {
                    if (xhrEgresado.status === 200) {
                        var datosEgresado = JSON.parse(xhrEgresado.responseText);

                        // Llenar los campos del formulario con los datos del egresado
                        numControlInput.value = datosEgresado.Num_Control;
                        tipoProductoInput.value = datosEgresado.Tipo_Producto_Titulacion;

                        // Aquí puedes agregar más líneas para llenar otros campos según los datos del egresado

                        campoOculto.style.display = "block";
                    } else {
                        console.log("Error al obtener los datos del egresado.");
                    }
                };

                xhrEgresado.send();
            } else {
                campoOculto.style.display = "none";
            }
        }

        // Añade el evento de entrada al campo de búsqueda
        document.getElementById("filtroEgresado").addEventListener("input", function() {
            mostrarCampoOculto();
        });
    </script>



    <!-- Scripts para obtener los datos de los elementos de las listas desplegables -->
    <script>
        // Declarar las variables en el alcance global
        var selectLibro;
        var selectFormatoFoja;
        var campoTexto;

        // Asegúrate de que el siguiente código se ejecute después de que se haya cargado el DOM
        document.addEventListener("DOMContentLoaded", function() {
            selectLibro = document.getElementById("libro");
            selectFormatoFoja = document.getElementById("formatoFoja");
            campoTexto = document.getElementById("campoTexto");

            // Cuando se cambia la opción de egresado, toma el valor seleccionado
            campoTexto.addEventListener("change", function() {
                var numControl = campoTexto.value;

                // Realizar una solicitud AJAX para obtener los libros desde obtenerLibro.php
                var xhrLibros = new XMLHttpRequest();
                xhrLibros.open("GET", "../php/obtenerLibroFoja.php?numControl=" + numControl, true);

                xhrLibros.onload = function() {
                    if (xhrLibros.status === 200) {
                        var libros = JSON.parse(xhrLibros.responseText);
                        libros.forEach(function(libro) {
                            var option = document.createElement("option");
                            option.value = libro.idL;
                            option.text = libro.nombreL;
                            selectLibro.appendChild(option);
                        });
                    } else {
                        console.log("Error al obtener los libros.");
                    }
                };

                xhrLibros.send();
            });

            // Cuando se cambia la opción de libro, realizar una solicitud AJAX para obtener los formatos de foja
            selectLibro.addEventListener("change", function() {
                var selectedLibro = selectLibro.value;

                // Limpiar la segunda lista desplegable
                selectFormatoFoja.innerHTML = "<option value=''>Seleccione el formato de foja</option>";

                if (selectedLibro !== "") {
                    // Realizar una solicitud AJAX para obtener los formatos de foja para el libro seleccionado
                    var xhrFormatoFoja = new XMLHttpRequest();
                    xhrFormatoFoja.open("GET", "../php/obtenerFojaCondiciones.php?libro=" + selectedLibro, true);

                    xhrFormatoFoja.onload = function() {
                        if (xhrFormatoFoja.status === 200) {
                            var formatosFoja = JSON.parse(xhrFormatoFoja.responseText);
                            formatosFoja.forEach(function(formatoFoja) {
                                var option = document.createElement("option");
                                option.value = formatoFoja.ID;
                                option.text = formatoFoja['Nombre Foja'];
                                selectFormatoFoja.appendChild(option);
                            });
                        } else {
                            console.log("Error al obtener los formatos de foja.");
                        }
                    };

                    xhrFormatoFoja.send();
                }
            });
        });
    </script>

    <!-- Scripts para mandar los datos al servidor -->
    <script>
        document.getElementById("btnAsignarArchivo").addEventListener("click", function() {
            var selectedLibro = document.getElementById("libro").value;
            var selectedFormatoFoja = document.getElementById("formatoFoja").value;
            var numControl = document.getElementById("numControl").value;

            if (selectedLibro === "" || selectedFormatoFoja === "" || numControl === "") {
                alert("Por favor, complete todos los campos.");
                return;
            }

            // Realizar una solicitud AJAX para verificar si el usuario ya tiene asignados archivos FOJA
            var xhrVerificacion = new XMLHttpRequest();
            xhrVerificacion.open("POST", "../php/verificarAsignacion.php", true);
            xhrVerificacion.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

            xhrVerificacion.onload = function() {
                if (xhrVerificacion.status === 200) {
                    var response = JSON.parse(xhrVerificacion.responseText);
                    if (response.message === 'Usuario no tiene asignados archivos FOJA') {
                        if (confirm("Se asignarán documentos al titulado. Una vez aceptado, los cambios no se pueden revertir desde esta pantalla. En caso de una asignación incorrecta, puede desasignar la FOJA desde el módulo Gestión de Fojas > Desasignar Foja. ¿Desea asignar la FOJA?")) {
                            // Crear un objeto que contenga los datos que se enviarán al servidor
                            var data = {
                                fkFormatoLibro: selectedLibro,
                                fkFormatoFoja: selectedFormatoFoja,
                                numControl: numControl
                            };

                            // Realizar una solicitud AJAX para enviar los datos al servidor
                            var xhr = new XMLHttpRequest();
                            xhr.open("POST", "../php/registroFojaTitulado.php", true);
                            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    var response = JSON.parse(xhr.responseText);
                                    alert(response.message); // Muestra un mensaje de éxito o error

                                    // Recargar la página después de un tiempo (por ejemplo, 1000 milisegundos = 1 segundo)
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    console.log("Error al realizar la inserción.");
                                }
                            };

                            xhr.send(JSON.stringify(data));
                        }
                    } else {
                        alert(response.message);
                    }
                } else {
                    console.log("Error al verificar la asignación.");
                }
            };

            xhrVerificacion.send(JSON.stringify({
                numControl: numControl
            }));
        });
    </script>
</body>

</html>