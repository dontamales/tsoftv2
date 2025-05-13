<?php
if (session_status() == PHP_SESSION_ACTIVE) {
    $iconpath = '../assets/icons/favicon/';
  } else {
    $iconpath = 'assets/icons/favicon/';
  }
$icons = '<link
rel="shortcut icon"
type="image/x-icon"
href="' . $iconpath . 'favicon.ico"
/>
<link
rel="apple-touch-icon"
sizes="180x180"
href="' . $iconpath . 'apple-touch-icon.png"
/>
<link
rel="icon"
type="image\png"
href="' . $iconpath . 'favicon-32x32.png"
sizes="32x32"
/>
<link
rel="icon"
type="image\png"
href="' . $iconpath . 'favicon-16x16.png"
sizes="16x16"
/>
<link rel="manifest" href="' . $iconpath . 'site.webmanifest" />
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5" />';
?>

