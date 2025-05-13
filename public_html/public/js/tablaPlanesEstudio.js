$(document).ready(function() {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: '../php/tablaPlanEstudio.php', // Asegúrate de que esta ruta sea la correcta
    type: 'POST',
    dataType: 'json',
    success: function(data) {
      var tbody = $("#planesEstudio-table tbody");
      
      // Vacía el tbody por si acaso
      tbody.empty();
      
      // Llena la tabla con los datos obtenidos
      $.each(data, function(index, row) {
        var newRow = $("<tr>");
        var checkbox = $("<input>").attr("type", "checkbox").addClass("selectUser");
        newRow.append($("<td>").append(checkbox));
        newRow.append($("<td>").text(row.Id_PlanEstudio));
        newRow.append($("<td>").text(row.Periodo_Generacion_Plan_Estudio));
        newRow.append($("<td>").text(row.Descripcion_Del_Plan_De_Año_Plan_Estudio));
        tbody.append(newRow);
      });
    },
    error: function(jqXHR, textStatus, errorThrown) {
      //console.log('Error:', textStatus, errorThrown);
    }
  });
});

$("#buscarPlanEstudioBtn").click(function() {
  var searchTerm = $("#searchPlanEstudio").val().toLowerCase();
  $("#planesEstudio-table tbody tr").each(function() {
      var lineStr = $(this).text().toLowerCase();
      if (lineStr.indexOf(searchTerm) === -1) {
          $(this).hide();
      } else {
          $(this).show();
      }
  });
});