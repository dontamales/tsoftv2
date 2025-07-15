<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([3]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA

?>

<!DOCTYPE html>
<html lang="es-MX"> <!-- LENGUAJE DE LA PÁGINA WEB (PARA TRADUCTORES) -->

<head>
    <!-- Etiquetas meta, íconos y otros... -->
    <?php echo $meta; ?>
    <meta name="description" content="Base de estructura" />
    <title>T-Soft - Agregar tipo de titulacion al libro</title>
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

<style>
    .pagination {
        margin-top: 20px;
    }

    .page-item {
        margin-right: 5px;
    }

    /* Estilos personalizados para la tabla */
    .table-responsive-container {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
        margin-top: 20px;
    }

    .table-responsive {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
    }

    .table-responsive th,
    .table-responsive td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .table-responsive thead th {
        background-color: #007bff;
        color: #fff;
        border-top: 2px solid #0056b3;
    }

    .table-responsive tbody tr:hover {
        background-color: #f2f2f2;
    }
</style>

<body class>
    <?php echo $header; ?>

    <?php echo $menu; ?>

    <div class="main-container">
        <main id="mainContent" class="content col ps-md-2 pt-2">
            <!-- Esta parte ya no es necesaria, por los cambios en la sidebar JH20250710 -->
            <!-- <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none">
                <i class="bi bi-list bi-lg py-2 p-1"></i>Menú desplegable
            </a> -->
            <div class="page-header pt-3">
                <p class="h1 text-center">Gestionar Libro Asignado</p>
            </div>
            <hr />

            <!-- Mensaje -->
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="alert alert-info" role="alert">
                        <strong class="bi bi-info-circle-fill"></strong> Si usted no encuentra el libro deseado, probablemente no este registrado en el apartado correspondiente
                        , para registrarlo ingrese al apartado del menu y seleccione "Gestion Fojas".
                    </div>
                </div>
            </div>

            <hr class="border-primary my-4 text-center">

            <!-- Tabla de Libros Documentos -->
            <div class="row">
                <div class="col-12">
                    <h2 class="h4">Libros & Documentos</h2>
                    <p>En este apartado se mostraran todos los libros agregados a la validación para poder asignarle un tipo de titulación</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="Libro-Documentos">
                            <thead>
                                <tr>
                                    <th>Descripción del Libro</th>
                                    <th>Tipo de Producto de Titulación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán dinámicamente aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <ul class="pagination justify-content-end flex-wrap" id="pagination"></ul>
                </div>
            </div>

            <!-- Mensaje -->
            <div id="mensaje" class="row" style="display: none;">
                <div class="col-12 mb-3">
                    <div class="alert alert-success text-center" role="alert">
                        <strong class="bi bi-bookmark-check-fill"></strong> Libros validados, refresque la pagina y continue porfavor.
                    </div>
                </div>
            </div>

            <!-- ComboBox y botones -->
            <div class="container-fluid mt-5 p-4 border rounded shadow-lg">

                <h2 class="h4 text-center">Opciones Disponibles</h2>

                <!-- ComboBox Libro -->
                <div class="row mt-3">
                    <div class="col-12">
                        <select id="libroSelect" class="form-control">
                            <!-- Los datos se cargarán dinámicamente aquí -->
                        </select>
                    </div>

                    <!-- ComboBox Tipo Titulación -->
                    <div class="col-12 mt-3">
                        <select id="titulacionSelect" class="form-control">
                            <!-- Los datos se cargarán dinámicamente aquí -->
                        </select>
                    </div>
                </div>

                <!-- Botón de agregar -->
                <div class="col-12 mt-3 container-fluid text-end d-flex justify-content-end">
                    <button id="AgregarTipoALibro" class="bi bi-plus-square-dotted btn btn-primary"> Agregar</button>
                </div>

                <!-- Resultado -->
                <div class="row">
                    <div class="col-12">
                        <p></p>
                        <div class="table-responsive">
                            <table id="resultadoTabla" class="table table-bordered">
                                <thead>
                                    <tr class="table-primary">
                                        <th class="text-center">Libro Seleccionado</th>
                                        <th class="text-center">Titulación a Asignar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Filas agregadas dinámicamente por JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Botón de eliminar -->
                    <div class="col-12 mt-3 container-fluid text-end d-flex justify-content-end">
                        <button id="RegresarTipoALibro" class="bi bi-layer-backward btn btn-warning"> Regresar</button>
                    </div>
                </div>

            </div>

            <!-- Botón de asignar -->
            <div class="col-12 mt-4 d-flex justify-content-center">
                <button id="AgregarTipoALibroF" class="bi bi-plus-circle-dotted btn btn-success"> Guardar</button>
            </div>
            
            <!-- Modal -->
            <div class="modal fade" id="informacionModal" tabindex="-1" role="dialog" aria-labelledby="informacionModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="informacionModalLabel">Información Detallada</h5>
                        </div>
                        <div class="modal-body" id="informacionModalBody">
                            <!-- Contenido dinámico del modal -->
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Scripts propios -->
    <!-- Sidebar JH20250710 -->
    <script src="../js/sidebar.js" defer></script>
    <script>
        window.onunload = function() {
            // Esto es para que cuando se cierre la pestaña, se cierre la sesión
            window.location.replace("../index.php");
        };
    </script>
    <script src="../js/tablaLibroTipoTitulacion.js"></script>
</body>

</html>