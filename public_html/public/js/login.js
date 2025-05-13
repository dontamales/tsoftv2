$(document).ready(function() {
  $("#login-form").submit(function(event) {
    event.preventDefault();
    var femail = $("#femail").val();
    var fpass = $("#fpass").val();

    $.ajax({
      type: "POST",
      url: "php/login.php",
      data: {
        femail: femail,
        fpass: fpass
      },
      dataType: "json",
      success: function(response) {
        if (response.error) {
          $("#login-alert").text(response.error).fadeIn();
      
          // Si el error comienza con "Ha alcanzado", aplica el tiempo de espera.
          if (response.error.startsWith("Ha alcanzado")) {
              var cooldownTime = response.cooldown || 60000; // Si no hay un cooldown especificado, se usa 1 minuto por defecto.
              $("#login-button").prop("disabled", true).addClass("btn-danger").removeClass("btn-primary");
      
              setTimeout(function() {
                  $("#login-button").prop("disabled", false).addClass("btn-primary").removeClass("btn-danger");
              }, cooldownTime);
          }
      }
       else if (response.url) {
          window.location.href = response.url;
        } else {
          $("#login-alert").text("Error inesperado, por favor inténtalo de nuevo.").fadeIn();
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        $("#login-alert").text("Error de comunicación con el servidor, por favor inténtalo de nuevo.").fadeIn();
      }
    });
  });
});
