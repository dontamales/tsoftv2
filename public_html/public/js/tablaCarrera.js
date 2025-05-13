$(document).ready(function() {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: '../php/tablaCarrera.php', // Asegúrate de que esta ruta sea la correcta
    type: 'POST',
    dataType: 'json',
    success: function(data) {
      var tbody = $("#carrera-table tbody");
      
      // Vacía el tbody por si acaso
      tbody.empty();
      
      // Llena la tabla con los datos obtenidos
      $.each(data, function(index, row) {
        var newRow = $("<tr>");
        var checkbox = $("<input>").attr("type", "checkbox").addClass("selectUser");

        newRow.append($("<td>").append(checkbox));
        newRow.append($("<td>").text(row.Id_Carrera));
        newRow.append($("<td>").text(row.Nombre_Carrera));
        newRow.append($("<td>").text(row.Nombre_Departamento));
        newRow.append($("<td>").text(row.Nombre_Jefe_Carrera));
        newRow.append($("<td>").text(row.Iniciales_Carrera));
        newRow.append($("<td>").text(row.Tipo_Carrera));
        tbody.append(newRow);
      });
    },
    error: function(jqXHR, textStatus, errorThrown) {
      //console.log('Error:', textStatus, errorThrown);
    }
  });
});

$("#buscarBtnCarrera").click(function() {
  var searchTerm = $("#searchCarrera").val().toLowerCase();
  $("#carrera-table tbody tr").each(function() {
      var lineStr = $(this).text().toLowerCase();
      if (lineStr.indexOf(searchTerm) === -1) {
          $(this).hide();
      } else {
          $(this).show();
      }
  });
});