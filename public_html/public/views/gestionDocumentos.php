<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
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
    <meta name="description" content="Gestión de documentos recibidos" />
    <title>T-Soft - Gestión de los documentos</title>
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
                <h1 class="text-center">Documentos recibidos</h1>
                <hr>
                <h3 id="correos-restantes">Correos enviados el día de hoy: <?php echo $cuenta; ?></h3>
            </div>
            <hr />

            <div class="row">
                <div class="col-8">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="inputGroup-sizing-default">Buscar</span>
                        <input type="text" class="form-control" id="filtro-documentos" aria-label="Filtrar carpeta" aria-describedby="inputGroup-sizing-default">
                    </div>
                </div>

                <div class="col-4">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="filtro-tipo-documento">Tipo</label>
                        <select id="selector-documento" class="form-select">
                            <option value="">Todos los documentos</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <div class="table-card-style" style="max-height: 33.54rem; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-striped" id="tabla-egresadosDocumentos">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Número de Control</th>
                                    <th>Tipo de titulación</th>
                                    <th>Proyecto</th>
                                    <th>Carrera</th>
                                    <th>Documentos pendientes</th>
                                    <th>Documentos aprobados</th>
                                    <th>Documentos por revisar</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-card-style" style="max-height: 33.54rem; overflow-y: auto;">
                        <table class="table table-bordered table-hover table-striped" id="tabla-documentos">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                    <th>Fecha de subida</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <hr />
        <?php echo $footer; ?>
    </div>

    <!-- Librerías de JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>

    <!-- Sidebar -->
    <script src="../js/sidebar.js" defer></script>

    <!-- Script de control de sesión -->
    <script>
        window.onunload = function() {
            // Esto es para que cuando se cierre la pestaña, se cierre la sesión
            window.location.replace("../index.php");
        };
    </script>

    <!-- Cargar datos iniciales -->
    <script type="module">
        import {
            obtenerDocumentos,
            generarTabla
        } from '../js/documentosPendientes.js';

        window.onload = function() {
            obtenerDocumentos().then(generarTabla);
        }
    </script>

    <!-- Poblar selector de documentos -->
    <script type="module">
        import {
            obtenerDocumentos
        } from '../js/documentosPendientes.js';

        const selector = document.getElementById('selector-documento');
        const cuerpoTabla = document.querySelector('#tabla-egresadosDocumentos tbody');

        // Rellenar el selector con los tipos de documento existentes
        async function poblarSelector() {
            const datos = await obtenerDocumentos();
            const unicos = new Set();

            datos.forEach(row =>
                row.DocumentosPorRevisar.forEach(d =>
                    unicos.add(d.Descripcion_Documentos_Pendientes)
                )
            );

            unicos.forEach(doc => {
                const opt = document.createElement('option');
                opt.value = doc.toLowerCase();
                opt.textContent = doc;
                selector.appendChild(opt);
            });
        }

        // Mostrar / ocultar filas según la selección
        function aplicarFiltro(doc) {
            const filas = cuerpoTabla.querySelectorAll('tr');

            filas.forEach(fila => {
                if (!doc) {
                    fila.style.display = '';
                    return;
                }

                const textos = [...fila.querySelectorAll('td ul li')]
                    .map(li => li.textContent.toLowerCase());

                fila.style.display = textos.some(t => t.includes(doc)) ? '' : 'none';
            });
        }

        // Inicializar todo cuando cargue la página
        window.addEventListener('load', async () => {
            await poblarSelector();
            selector.addEventListener('change', e => aplicarFiltro(e.target.value));
        });
    </script>

    <!-- Filtro por tipo de documento con jQuery -->
    <script>
        /**
         * Oculta o muestra cada fila según la opción elegida
         */
        function filtrarPorDocumento(docSel) {
            $('#tabla-egresadosDocumentos tbody tr').each(function() {
                if (!docSel) {
                    $(this).show();
                    return;
                }

                const coincide = $(this)
                    .find('td:eq(8) li')
                    .toArray()
                    .some(li => li.textContent.toLowerCase().includes(docSel));

                $(this).toggle(coincide);
            });
        }

        // Cuando el usuario cambia el desplegable
        $(document).on('change', '#selector-documento', function() {
            filtrarPorDocumento(this.value.toLowerCase());
        });

        // Cada vez que generarTabla termina, vuelve a aplicar el filtro activo
        document.addEventListener('tabla-egresados-cargada', () => {
            const actual = $('#selector-documento').val() || '';
            filtrarPorDocumento(actual.toLowerCase());
        });
    </script>
</body>

</html>