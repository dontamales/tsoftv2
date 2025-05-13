$(document).ready(function() {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: '../php/tablaDepartamento.php', // Asegúrate de que esta ruta sea la correcta
    type: 'POST',
    dataType: 'json',
    success: function(data) {
      var tbody = $("#departamento-table tbody");
      
      // Vacía el tbody por si acaso
      tbody.empty();
      
      // Llena la tabla con los datos obtenidos
      $.each(data, function(index, row) {
        var newRow = $("<tr>");
        var checkbox = $("<input>").attr("type", "checkbox").addClass("selectUser");

        newRow.append($("<td>").append(checkbox));
        newRow.append($("<td>").text(row.Id_Departamento));
        newRow.append($("<td>").text(row.Nombre_Departamento));
        newRow.append($("<td>").text(row.Nombre_Jefe_Departamento));
        newRow.append($("<td>").text(row.Correo_Jefatura_Departamento));
        newRow.append($("<td>").text(row.Correo_Proyecto_Departamento));
        tbody.append(newRow);
      });
    },
    error: function(jqXHR, textStatus, errorThrown) {
      //console.log('Error:', textStatus, errorThrown);
    }
  });
});

$("#buscarBtnDepartamento").click(function() {
  var searchTerm = $("#searchDepartamento").val().toLowerCase();
  $("#departamento-table tbody tr").each(function() {
      var lineStr = $(this).text().toLowerCase();
      if (lineStr.indexOf(searchTerm) === -1) {
          $(this).hide();
      } else {
          $(this).show();
      }
  });
});