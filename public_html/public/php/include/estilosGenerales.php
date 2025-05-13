<?php
$estilos = '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">';

if (session_status() == PHP_SESSION_NONE) :
    $estilos .= '<link rel="stylesheet" href="css/pages/index.css" />';
endif;

if (session_status() == PHP_SESSION_ACTIVE) :
    $estilos .= '<link rel="stylesheet" href="../css/pages/baseTsoft.css" />';
endif;
?>