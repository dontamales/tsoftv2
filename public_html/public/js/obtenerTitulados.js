$(document).ready(function () {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: "../php/obtenerTitulados.php", // Asegúrate de que esta ruta sea la correcta
    type: "POST",
    dataType: "json",
    success: function (data) {
      var tbody = $("#tabla-actualizar-titulados tbody");

      // Vacía el tbody por si acaso
      tbody.empty();

      // Llena la tabla con los datos obtenidos
      $.each(data, function (index, row) {
        var newRow = $("<tr>");

        newRow.append($("<td>").text(row.Num_Control));
        newRow.append($("<td>").text(row.Nombres_Usuario));
        newRow.append($("<td>").text(row.Apellidos_Usuario));
        newRow.append($("<td>").text(row.Nombre_Carrera));
        newRow.append($("<td>").text(row.Fecha_Hora_Ceremonia_Egresado));
        newRow.append($("<td>").text(row.Correo_Usuario));

        tbody.append(newRow);
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //console.log("Error:", textStatus, errorThrown);
    },
  });
});
