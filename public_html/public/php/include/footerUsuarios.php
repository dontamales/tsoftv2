<?php
//Obtener directorio relativo actual\
$conexion;
$path_relativo_actual = $_SERVER['PHP_SELF'];
if ($path_relativo_actual == "/index.php") {
  $conexion = "../private/conexion.php";
} else {
  $conexion = "../../private/conexion.php";
}

require_once $conexion;

// $stmt para seleccionar todos las columnas de la tabla de variables globales
$stmt = $conn->prepare("SELECT * FROM variables_globales WHERE Id_Variables_Globales = 1");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$variablesGlobales = $result->fetch_assoc();

// Variables globales de  Precio_Examen_Profesional_Variables_Globales
$precioExamenPro = $variablesGlobales['Precio_Examen_Profesional_Variables_Globales'];
$horario = $variablesGlobales['Horario_Atencion_Variables_Globales'];
$correoTitulacion = $variablesGlobales['Correo_Coordinacion_Titulacion_Variables_Globales'];
$telefonoTitulacion = $variablesGlobales['Telefono_Coordinacion_Titulacion_Variables_Globales'];
$ubicacionTitulacion = $variablesGlobales['Ubicacion_Coordinacion_Titulacion_Variables_Globales'];

$year = date("Y");
if (session_status() == PHP_SESSION_ACTIVE) {
  $logofooter = '<img class="footer--logo m-2" src="../assets/icons/favicon/favicon-32x32.png" alt="Logo de T-Soft" style="width: 25px;" />';
} else {
  $logofooter = '<img class="footer--logo m-2" src="assets/icons/favicon/favicon-32x32.png" alt="Logo de T-Soft" style="width: 25px;" />';
}
$footer = '<footer class="footer container-fluid text-center bg-light mt-3 border" style="border-color: black;"> 
<div class="row"> 
  <div class="col">
    ' . $logofooter . ' 
  </div>
</div>
<div class="row"> 
  <div class="col"> 
    <p class="text-black"><strong>Contacto:</strong></p> 
    <p class="text-black">' . $correoTitulacion . '</p> 
    <p class="text-black">' . $telefonoTitulacion . '</p> 
  </div>
  <div class="col"> 
    <p class="text-black"><strong>T-Soft© </strong> de 2022 a ' . $year . '</p>
    <p class="text-black"><strong>Ubicación:</strong></p> 
    <p class="text-black">' . $ubicacionTitulacion . '</p> 
  </div>
  <div class="col"> 
    <p class="text-black"><strong>Horario de atención:</strong></p> 
    <p class="text-black">' . $horario . '</p> 
  </div>
</div>
</footer>';
?>