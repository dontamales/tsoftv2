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
    <title>T-Soft - Actualizacion manual de estatus fallidos</title>
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
                <p class="h1">Actualizar estatus manualmente</p>
            </div>
            <hr />
            <p>En este apartado se mostraran los sustentantes que su "formato B" fue aprobado y su estatus no fue modificado.</p>
            <hr />
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table id="tablaEstatus" class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Num. Control</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Apellido</th>
                                    <th scope="col">Estatus</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí puedes dejar las filas vacías para llenar con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Botón de actualización -->
            <div class="row justify-content-center">
                <div class="col-4">
                    <button class="btn btn-success btn-block" id="actualizarRegistros">Actualizar todos los registros</button>
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
    <script>
        $(document).ready(function() {
            // Función para obtener y actualizar los datos de la tabla
            function obtenerYActualizarDatos() {
                $.ajax({
                    url: '../php/obtenerFormatosBFallidos.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Limpiar el tbody de la tabla
                        $('#tablaEstatus tbody').empty();

                        // Recorrer los datos obtenidos y agregar filas a la tabla
                        $.each(data, function(index, row) {
                            var newRow = '<tr>' +
                                '<td>' + row.id + '</td>' +
                                '<td>' + row.nombre + '</td>' +
                                '<td>' + row.apellido + '</td>' +
                                '<td>' + row.estatus + '</td>' +
                                '<td>' +
                                '<button class="btn btn-primary ver-detalles" data-id="' + row.id + '">Actualizar Estatus</button>' +
                                '</td>' +
                                '</tr>';
                            $('#tablaEstatus tbody').append(newRow);
                        });

                        // Agregar un evento clic al botón "Ver Detalles"
                        $('.ver-detalles').click(function() {
                            var id = $(this).data('id');
                            // Hacer una solicitud AJAX para actualizar el estatus
                            $.ajax({
                                url: '../php/actualizacionEstatusFormatoBFallidos.php',
                                type: 'POST',
                                data: {
                                    id: id
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        // Actualización exitosa, puedes realizar acciones adicionales aquí
                                        alert('Estatus actualizado con éxito para el número de control ' + id);
                                        // Volver a cargar los datos después de la actualización
                                        obtenerYActualizarDatos();
                                    } else {
                                        alert('Error al actualizar el estatus');
                                    }
                                },
                                error: function() {
                                    console.log('Error en la solicitud AJAX');
                                }
                            });
                        });
                    },
                    error: function() {
                        console.log('Error al obtener datos');
                    }
                });
            }

            // Llamar a la función para obtener y actualizar datos inicialmente
            obtenerYActualizarDatos();

            // Configurar un intervalo de tiempo para actualizar los datos cada 5 segundos
            setInterval(obtenerYActualizarDatos, 5000); // 5000 milisegundos = 5 segundos

            // Manejador de clic para el botón "Actualizar todos los registros"
            $('#actualizarRegistros').click(function() {
                // Obtener todos los IDs de la tabla
                var ids = [];
                $('#tablaEstatus tbody tr').each(function() {
                    var id = $(this).find('td:first-child').text();
                    ids.push(id);
                });

                // Actualizar el estatus a 5 para cada registro
                for (var i = 0; i < ids.length; i++) {
                    actualizarEstatus(ids[i]);
                }

                // Volver a cargar los datos después de la actualización
                obtenerYActualizarDatos();
            });

            // Función para actualizar el estatus de un registro
            function actualizarEstatus(id) {
                $.ajax({
                    url: '../php/actualizacionEstatusFormatoBFallidos.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            console.log('Estatus actualizado con éxito para el número de control ' + id);
                        } else {
                            console.error('Error al actualizar el estatus para el número de control ' + id);
                        }
                    },
                    error: function() {
                        console.error('Error en la solicitud AJAX para actualizar el estatus');
                    }
                });
            }
        });
    </script>


</body>

</html>