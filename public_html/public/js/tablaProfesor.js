$(document).ready(function() {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: '../php/tablaProfesor.php', // Asegúrate de que esta ruta sea la correcta
    type: 'POST',
    dataType: 'json',
    success: function(data) {
      var tbody = $("#profesor-table tbody");
      
      // Vacía el tbody por si acaso
      tbody.empty();
      
      // Llena la tabla con los datos obtenidos
      $.each(data, function(index, row) {
        var newRow = $("<tr>");
        var checkbox = $("<input>").attr("type", "checkbox").addClass("selectUser");

        newRow.append($("<td>").append(checkbox));
        newRow.append($("<td>").text(row.Id_Profesor));
        newRow.append($("<td>").text(row.Nombre_Profesor));
        newRow.append($("<td>").text(row.Cedula_Profesor));
        newRow.append($("<td>").text(row.Grado_Academico_Profesor));
        tbody.append(newRow);
      });
    },
    error: function(jqXHR, textStatus, errorThrown) {
      //console.log('Error:', textStatus, errorThrown);
    }
  });
});

$("#buscarBtnProfesor").click(function() {
  var searchTerm = $("#searchProfesor").val().toLowerCase();
  $("#profesor-table tbody tr").each(function() {
      var lineStr = $(this).text().toLowerCase();
      if (lineStr.indexOf(searchTerm) === -1) {
          $(this).hide();
      } else {
          $(this).show();
      }
  });
});