<?php
require_once 'nivelUsuario.php'; #NIVELES DE USUARIO
require_once '../../private/conexion.php'; #CONEXIÓN A LA BASE DE DATOS

$_SESSION['user_id'] = $id;

$stmt = $conn->prepare("SELECT * FROM usuario WHERE Id_Usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$usuario = $result->fetch_assoc();
$nombreCompleto = $usuario['Nombres_Usuario'];

$header = '
<!-- Header -->
<header class="dashboard-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-auto">
        <div class="logo-container">
          <span>T-S</span>
        </div>
      </div>
      <div class="col text-center">
        <h1 class="header-title position-absolute top-50 start-50 translate-middle m-0">T-Soft: ' . $nivel . '</h1>
      </div>
      <div class="col-auto">
        <div class="d-flex align-items-center">
          <div class="user-info me-3">
            <span><i class="bi bi-person"></i>' . $nombreCompleto . '</span>
          </div>
          <a href="../php/logout.php" target="_self" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
          </a>
        </div>
      </div>
    </div>
  </div>
</header>
'

// $header = 
// '
// <header class="header border" style="border-color: black;">
//   <div class="container-fluid">
//     <div class="row justify-content-between align-items-center">
//       <div class="col">
//         <img class="header_navbar--logo p-2 img-fluid mx-auto d-block" src="../assets/icons/favicon/android-chrome-512x512.png" alt="Admin" style="width: 90px"/>
//       </div>
//       <div class="col text-center">
//         <h2><strong>T-Soft: ' . $nivel . '</strong></h2>
//       </div>
//       <div class="col text-center">
//         <a href="../php/logout.php" target="_self" class="header__row--btn btn btn-sm btn-danger">
//           <i class="bi bi-door-open"></i> Cerrar sesión
//         </a>
//       </div>
//     </div>
//     <div class="row text-center">
//       <div class="col">
//         <p><strong><i class="bi bi-person"></i>' . $nombreCompleto . '</strong></p>
//       </div>
//     </div>
//   </div>
// </header>';
?>