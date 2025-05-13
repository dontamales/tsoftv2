<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 4, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO

if (isset($_GET["folderPath"])) {
  $folderPath = $_GET["folderPath"];
  if (!file_exists($folderPath)) {
    mkdir($folderPath, 0777, true); // Crea el directorio y todos los subdirectorios necesarios
    echo "Carpeta creada exitosamente.";
  } else {
    echo "La carpeta ya existe.";
  }
} else {
  echo "No se proporcionó una ruta de carpeta válida.";
}
?> 