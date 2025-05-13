<?php
require_once 'sesion.php'; #VERIFICACIÓN DE SESIÓN
require_once 'auth.php'; #VERIFICACIÓN DE USUARIO ADMINISTRADOR
require_roles([2, 3, 5]); #VERIFICACIÓN DE USUARIO ADMINISTRATIVO
require_once '../../private/conexion.php'; #CONEXIÓN A LA BASE DE DATOS

date_default_timezone_set('America/Denver');

// Configuración de la zona horaria para esta sesión de MySQL
$conn->query("SET time_zone='-06:00'");

if (isset($_POST['submit']) && isset($_POST['periodo']) && isset($_POST['numerolibro']) && isset($_POST['libro']) && isset($_FILES['pdf']) && isset($_POST['anio'])) { // ADDED BY JOSE NAVA 08/01/2024
    $nombrePeriodo = $_POST['periodo'];
    $numerolibro = $_POST['numerolibro']; // ADDED BY JOSE NAVA 08/01/2024
    $nombreSubcarpeta = $_POST['libro'];
    $anio = $_POST['anio'];

    $periodoCompleto = $nombrePeriodo . " " . $anio;

    // Crear la carpeta del período si no existe
    $carpetaPeriodo = "../assets/archivos/$periodoCompleto/libros & fojas/";
    if (!file_exists($carpetaPeriodo)) {
        mkdir($carpetaPeriodo, 0777, true);
    }

    // Crear la subcarpeta dentro de la carpeta del período
    $subcarpetaDestino = "$carpetaPeriodo/$nombreSubcarpeta";
    if (!file_exists($subcarpetaDestino)) {
        mkdir($subcarpetaDestino, 0777, true);
    }
    
    // MODICATE BY JOSE NAVA 08/01/2024
    // Obtener el número autoincrementable
    //$numeroFoja = 1;
    //while (file_exists("$subcarpetaDestino/$nombreSubcarpeta $numeroFoja.pdf")) {
    //    $numeroFoja++;
    //}

    // Subir el archivo con el nombre autoincrementable
    $archivoNombre = "$nombreSubcarpeta $numerolibro.pdf"; // ADDED BY JOSE NAVA 08/01/2024
    $archivoTemporal = $_FILES['pdf']['tmp_name'];
    $archivoDestino = "$subcarpetaDestino/$archivoNombre";

    if (move_uploaded_file($archivoTemporal, $archivoDestino)) {
        //Obtener ID del libro
        $stmt = $conn->prepare("SELECT Id_Libro FROM libro WHERE Descripcion_Libro = ?");
        $stmt->bind_param("s", $nombreSubcarpeta);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $idLibro = $row['Id_Libro'];

            $stmt3 = $conn->prepare("SELECT * FROM formato_foja WHERE Fk_Libro_Formato_Foja = ? AND Nombre_Formato_Foja = ? AND Periodo_Formato_Foja = ? AND Anio_Formato_Foja = ? AND Numero_Formato_Foja = ?"); // ADDED BY JOSE NAVA 08/01/2024
            $stmt3->bind_param("isssi", $idLibro, $archivoNombre, $nombrePeriodo, $anio, $numerolibro); // ADDED BY JOSE NAVA 08/01/2024
            $stmt3->execute();
            $result3 = $stmt3->get_result();
            $row3 = $result3->fetch_assoc();

            if ($row3) {
                $stmt4 = $conn->prepare("UPDATE formato_foja 
                SET Fk_Libro_Formato_Foja = ? 
                AND Nombre_Formato_Foja = ? 
                AND Periodo_Formato_Foja = ? 
                AND Anio_Formato_Foja = ?
                AND Numero_Formato_Foja = ?  
                WHERE Id_Formato_Foja = ?");
                $stmt4->bind_param("isssi", $idLibro, $archivoNombre, $nombrePeriodo, $anio, $numerolibro, $row3['Id_Formato_Foja']); // ADDED BY JOSE NAVA 08/01/2024
                if ($stmt4->execute()){
                    $stmt->close();
                    $stmt3->close();
                    $stmt4->close();
                    $conn->close();
                    $_SESSION['success'] = "El archivo foja se ha subido correctamente.";
                    header("Location: ../views/gestionLibros&Fojas.php");
                    exit;
                } else {
                    $stmt->close();
                    $stmt3->close();
                    $stmt4->close();
                    $conn->close();
                    $_SESSION['error'] = "No se pudo subir el archivo foja de manera correcta.";
                    header("Location: ../views/gestionLibros&Fojas.php");
                    exit;
                }
            } else {
                //Insertar datos de la foja en la base de datos
                $stmt2 = $conn->prepare("INSERT INTO formato_foja (Fk_Libro_Formato_Foja, Nombre_Formato_Foja, Periodo_Formato_Foja, Anio_Formato_Foja, Numero_Formato_Foja, Direccion_Archivo_Formato_Foja) VALUES (?,?, ?, ?, ?, ?)");// ADDED BY JOSE NAVA 08/01/2024
                $stmt2->bind_param("isssis", $idLibro, $archivoNombre, $nombrePeriodo, $anio, $numerolibro, $archivoDestino); // ADDED BY JOSE NAVA 08/01/2024
                if ($stmt2->execute()) {
                    $stmt->close();
                    $stmt2->close();
                    $stmt3->close();
                    $conn->close();
                    $_SESSION['success'] = "El archivo foja se ha subido correctamente.";
                    header("Location: ../views/gestionLibros&Fojas.php");
                    exit;
                } else {
                    $stmt->close();
                    $stmt2->close();
                    $stmt3->close();
                    $conn->close();
                    $_SESSION['error'] = "No se pudo subir el archivo foja de manera correcta.";
                    header("Location: ../views/gestionLibros&Fojas.php");
                    exit;
                }
            }
        } else {
            $stmt->close();
            $conn->close();
            $_SESSION['error'] = "No se pudo subir el archivo foja de manera correcta.";
            header("Location: ../views/gestionLibros&Fojas.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "No se pudo subir el archivo foja de manera correcta.";
        header("Location: ../views/gestionLibros&Fojas.php");
        exit;
    }
}
