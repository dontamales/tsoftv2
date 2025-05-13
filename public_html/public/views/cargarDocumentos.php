<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once '../php/auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([1]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once "../../private/conexion.php"; #CONEXIÓN A LA BASE DE DATOS
include '../php/include/meta.php'; #META INFORMACIÓN DE LA PÁGINA
include '../php/include/icons.php'; #ICONOS Y MANIFIESTO DE LA PÁGINA
include '../php/include/headerUsuarios.php'; #HEADER DE LA PÁGINA
include '../php/include/menuUsuarios.php'; #MENU DESPLEGABLE DE LA PÁGINA
include '../php/include/footerUsuarios.php'; #FOOTER DE LA PÁGINA
require_once '../php/formatoBData.php';

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

function verificarDocumentoSubidoEgresado($conn, $usuarioId, $documentoId)
{

    $stmt = $conn->prepare("SELECT * FROM egresados_documentos WHERE Fk_NumeroControl = ? AND Fk_Documentos_Pendientes2 = ?");
    $stmt->bind_param("si", $usuarioId, $documentoId);
    $stmt->execute();

    $result = $stmt->get_result();
    $registroExiste = $result->num_rows > 0;

    $stmt->close();

    return $registroExiste; // Devuelve TRUE si el documento ya existe, y FALSE si no.
}

// Consulta que devuelve los documentos relacionados con un tipo de titulación específico
$titulacionId = $usuario["Fk_Tipo_Titulacion_Egresado"];

$stmt = $conn->prepare("
    SELECT dp.Id_Documentos_Pendientes, dp.Descripcion_Documentos_Pendientes
    FROM producto_titulacion_documentos_pendientes ptdp
    JOIN documentos_pendientes dp ON dp.Id_Documentos_Pendientes = ptdp.Fk_Documentos_Pendientes
    WHERE ptdp.Fk_Producto_Titulacion_Documentos_Pendientes = ?
");
$stmt->bind_param("i", $titulacionId);
$stmt->execute();

$result = $stmt->get_result();
$documentos = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();

$usuarioId = $usuario["Num_Control"];

?>

<!DOCTYPE html>
<html lang="es-MX"> <!-- LENGUAJE DE LA PÁGINA WEB (PARA TRADUCTORES) -->

<head>
    <!-- Etiquetas meta, íconos y otros... -->
    <?php echo $meta; ?>
    <meta name="description" content="Base de estructura" />
    <title>T-Soft - Documentos Pendientes</title>
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
                <p class="h1">Documentos pendientes por subir</p>
            </div>
            <hr />
            <?php if ($usuario["FK_Estatus_Egresado"] >= 5 && $usuario["Formato_B_Aprobado_Egresado"] == 1) { ?>
                <div class="row">
                    <div class="row">
                        <?php

                        // Verifica si la columna "Tipo_Producto_Titulacion" existe en $usuario y no es nula antes de mostrarla
                        if (isset($usuario["Tipo_Producto_Titulacion"]) && $usuario["Tipo_Producto_Titulacion"] !== null) {
                            echo "<h3>Producto de titulación de " . $usuario["Nombres_Usuario"] . " " . $usuario["Apellidos_Usuario"] . ":<br/><br/>" . $usuario["Tipo_Producto_Titulacion"] . ".</h3><hr/>";

                            if (isset($_SESSION['alerta'])) {
                                echo "<script>alert('" . $_SESSION['alerta'] . "');</script>";
                                unset($_SESSION['alerta']);  // Borra el mensaje de la sesión para que no se muestre nuevamente
                            }

                            if (isset($_SESSION['alertaToken'])) {
                                echo "<script>alert('" . $_SESSION['alertaToken'] . "');</script>";
                                unset($_SESSION['alertaToken']);  // Borra el mensaje de la sesión para que no se muestre nuevamente
                            }
                        } else {
                            // Si la columna no existe o es nula, puedes mostrar un mensaje alternativo o simplemente no mostrar nada.
                            echo "<h2>No se encontró información de tipo de titulación para este usuario.</h2>";
                        }
                        ?>
                    </div>

                    <div>
                        <p>Aquí se suben los documentos necesarios según su producto de titulación, <strong>SOLAMENTE SE PUEDEN SUBIR UNA VEZ HASTA SU REVISIÓN</strong> por lo que, se aconseja de la manera más atenta que se revise con cuidado si los documentos son los correctos antes de subirlos.</p>
                    </div>

                    <!-- Cuadro 1 -->
                    <?php foreach ($documentos as $documento) {
                        $documentoId = $documento['Id_Documentos_Pendientes'];
                        $documentoSubido = verificarDocumentoSubidoEgresado($conn, $usuarioId, $documentoId);
                        if ($documentoId == 1) {
                            $descripcionDocumento = "Identificación oficial: INE, Licencia de conducir o Pasaporte Mexicano <b>por ambos lados en tamaño original</b>";
                        } elseif ($documentoId == 2) {
                            $descripcionDocumento = "Anexo III: Firmado y sellado por depto. académico";
                        } elseif ($documentoId == 3) {
                            $descripcionDocumento = "Asignación de sinodales: Oficio o captura del correo donde se vea el remitente y la tabla de asignación";
                        } elseif ($documentoId == 4) {
                            $descripcionDocumento = "Documento final: Esperar a subirlo hasta que le den la indicación de hacerlo ya que le compartirán la portada oficial";
                        } elseif ($documentoId == 5) {
                            $descripcionDocumento = "Presentación: 25 diapositivas máximo, diseño y contenido a su criterio";
                        } elseif ($documentoId == 6) {
                            $descripcionDocumento = "Carta de acreditación de residencias: La obtiene de su asesor interno. Al subir este documento se le bloqueará la Constancia, con uno de los dos que se presente de forma correcta es suficiente";
                        } elseif ($documentoId == 7) {
                            $descripcionDocumento = "Constancia de liberación de residencias: La obtiene de su coordinador de carrera. Al subir este documento se le bloqueará la Carta de acreditación, con uno de los dos que se presente de forma correcta es suficiente";
                        } else {
                            $descripcionDocumento = $documento['Descripcion_Documentos_Pendientes'];
                        }
                    ?>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title"><?= $documento['Descripcion_Documentos_Pendientes']; ?></h5>
                                </div>
                                <div class="card-body">
                                    <form id="form_fileUpload<?= $documentoId; ?>" action="../php/guardarArchivo.php" method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="fileUpload<?= $documentoId; ?>" class="form-label">Seleccionar archivo</label>
                                            <input type="file" class="form-control" id="fileUpload<?= $documentoId; ?>" name="fileUpload<?= $documentoId; ?>" accept=".pdf, .doc, .docx" <?= $documentoSubido ? 'disabled' : ''; ?> required>
                                        </div>
                                        <button type="submit" class="btn btn-primary" for="form_fileUpload<?= $documentoId; ?>" <?= $documentoSubido ? 'disabled' : ''; ?>>Subir archivo</button>
                                    </form>
                                    <p>En esta sección podrá subir su "<?= $descripcionDocumento; ?>".</p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="row">
                    <div class="col-12 mb-3">
                        <p class="h4">Usted no tiene acceso para subir los documentos verifique que su formato B esté aprobado.</p>
                    </div>
                </div>
            <?php } ?>
        </main>
        <hr />

        <?php echo $footer; ?>
    </div>



    <!-- Librerías de JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
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