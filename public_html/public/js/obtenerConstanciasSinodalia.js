$(document).ready(function() {
    // Realiza una solicitud AJAX para obtener los datos
    $.ajax({
      url: '../php/obtenerConstanciasSinodalia.php', // Asegúrate de que esta ruta sea la correcta
      type: 'POST',
      dataType: 'json',
      success: function(data) {
        var tbody = $("#tabla-constancia-sinodales tbody");
        
        // Vacía el tbody por si acaso
        tbody.empty();
        
        // Llena la tabla con los datos obtenidos
        $.each(data, function(index, row) {
          var newRow = $("<tr>");
          
          newRow.append($("<td>").text(row.Id_Constancia_Sinodalia));
          newRow.append($("<td>").text(row.Fecha_Creacion_Constancia_Sinodalia));
          newRow.append($("<td>").text(row.Fecha_Inicio_Constancia_Sinodalia));
          newRow.append($("<td>").text(row.Fecha_Cierre_Constancia_Sinodalia));
          newRow.append($("<td>").text(row.Nombre_Profesor));
          newRow.append($("<td>").html('<a href="'+row.Direccion_Archivo_Constancia_Sinodalia+'" download>Descargar</a>'));
          
          tbody.append(newRow);
        });
      },
      error: function(jqXHR, textStatus, errorThrown) {
        //console.log('Error:', textStatus, errorThrown);
      }
    });
  });
  