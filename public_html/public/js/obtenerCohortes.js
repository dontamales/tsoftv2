$(document).ready(function() {
    // Realiza una solicitud AJAX para obtener los datos
    $.ajax({
      url: '../php/obtenerCohortes.php', // Asegúrate de que esta ruta sea la correcta
      type: 'POST',
      dataType: 'json',
      success: function(data) {
        var tbody = $("#tabla-cohortes tbody");
        
        // Vacía el tbody por si acaso
        tbody.empty();
        
        // Llena la tabla con los datos obtenidos
        $.each(data, function(index, row) {
          var newRow = $("<tr>");
          
          newRow.append($("<td>").text(row.Id_Cohorte_Generacional));
          newRow.append($("<td>").text(row.Fecha_Creacion_Cohorte_Generacional));
          newRow.append($("<td>").html('<a href="'+row.Direccion_Archivo_Cohorte_Generacional+'" download>Descargar</a>'));
          
          tbody.append(newRow);
        });
      },
      error: function(jqXHR, textStatus, errorThrown) {
        //console.log('Error:', textStatus, errorThrown);
      }
    });
  });