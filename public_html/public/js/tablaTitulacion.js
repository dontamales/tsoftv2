$(document).ready(function() {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: '../php/tablaTitulacion.php', // Asegúrate de que esta ruta sea la correcta
    type: 'POST',
    dataType: 'json',
    success: function(data) {
      var tbody = $("#tipoTitulacion-table tbody");
      
      // Vacía el tbody por si acaso
      tbody.empty();
      
      // Llena la tabla con los datos obtenidos
      $.each(data, function(index, row) {
        var newRow = $("<tr>");
        var checkbox = $("<input>").attr("type", "checkbox").addClass("selectUser");

        newRow.append($("<td>").append(checkbox));
        newRow.append($("<td>").text(row.Id_Titulacion));
        newRow.append($("<td>").text(row.Tipo_Producto_Titulacion));
        tbody.append(newRow);
      });
    },
    error: function(jqXHR, textStatus, errorThrown) {
      //console.log('Error:', textStatus, errorThrown);
    }
  });
});

$("#buscarBtnTitulacion").click(function() {
  var searchTerm = $("#searchTipoTitulacion").val().toLowerCase();
  $("#tipoTitulacion-table tbody tr").each(function() {
      var lineStr = $(this).text().toLowerCase();
      if (lineStr.indexOf(searchTerm) === -1) {
          $(this).hide();
      } else {
          $(this).show();
      }
  });
});