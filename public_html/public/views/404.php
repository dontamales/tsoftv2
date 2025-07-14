<?php
require_once '../php/sesion.php'; #VERIFICACIÓN DE SESIÓN
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

  <style>
    .animated-button {
      transition: transform 0.2s;
      /* Duración de la animación y propiedad a animar */
    }

    .animated-button:hover {
      transform: scale(1.1);
      /* Escala al 110% en el hover */
    }

    .animated-button:active {
      transform: scale(0.9);
      /* Escala al 90% cuando se hace clic */
    }
    
  </style>
  <div class="main-container text-center">
    <main id="mainContent" class="content col ps-md-2 pt-2">
      <h1 class="display-4">Error 404: Recurso no existente</h1>
      <p class="lead">La URL que has solicitado no existe, tiene permisos insuficientes, o hay problemas de red o servidor.</p>
      
      <img src="../assets/img/proyecto/error.png" alt="Imagen descriptiva" width="557" height="557">

      <!-- Cuadro pagina principal -->
      <div class="col-md-6 mx-auto"> <!-- Center the column horizontally -->
        <a href="../index.php" class="btn btn-lg btn-block btn-primary rounded-pill py-4 animated-button">
          <div class="d-flex align-items-center justify-content-center">
            <i class="bi bi-back" style="font-size: 3rem; margin-right: 1rem;"></i> <!-- Icono arriba -->
            <h4 class="m-0"> Ir a la pagina de inicio</h4>
          </div>
        </a>
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