$(document).ready(function() {
    // Realiza una solicitud AJAX para obtener los datos
    $.ajax({
      url: '../php/obtenerReporteEficienciaTerminal.php', // Asegúrate de que esta ruta sea la correcta
      type: 'POST',
      dataType: 'json',
      success: function(data) {
        var tbody = $("#tabla-eficiencia-terminal tbody");
        
        // Vacía el tbody por si acaso
        tbody.empty();
        
        // Llena la tabla con los datos obtenidos
        $.each(data, function(index, row) {
          var newRow = $("<tr>");
          
          newRow.append($("<td>").text(row.Id_Eficiencia_Terminal));
          newRow.append($("<td>").text(row.Fecha_Creacion_Eficiencia_Terminal));
          newRow.append($("<td>").text(row.Periodo_Eficiencia_Terminal));
          newRow.append($("<td>").text(row.Total_Inscritos_Eficiencia_Terminal));
          newRow.append($("<td>").text(row.Total_Titulados_Eficiencia_Terminal));
          newRow.append($("<td>").text(row.Total_No_Titulados_Eficiencia_Terminal));
          newRow.append($("<td>").text(row.Promedio_Eficiencia_Terminal));
          newRow.append($("<td>").html('<a href="'+row.Direccion_Eficiencia_Terminal+'" download>Descargar</a>'));
          
          tbody.append(newRow);
        });
      },
      error: function(jqXHR, textStatus, errorThrown) {
        //console.log('Error:', textStatus, errorThrown);
      }
    });
  });
  