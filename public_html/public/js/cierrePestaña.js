window.onunload = function() {
    // Esto es para que cuando se cierre la pestaña, se cierre la sesión
    window.location.replace("../index.php");
  };