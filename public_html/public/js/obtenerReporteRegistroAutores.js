$(document).ready(function () {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: '../php/obtenerReporteRegistroAutores.php', // Ajusta la ruta si es necesario
    type: 'POST',
    dataType: 'json',
    success: function (data) {
      var tbody = $("#tabla-reporte-autores tbody");

      // Vacía el tbody por si acaso
      tbody.empty();

      // Llena la tabla con los datos obtenidos
      $.each(data, function (index, row) {
        var newRow = $("<tr>");

        newRow.append($("<td>").text(row.Id_Reporte_Registro_Autores));
        newRow.append($("<td>").text(row.Fecha_Creacion_Reporte_Registro_Autores));
        newRow.append($("<td>").text(row.Fecha_Ingreso_Reporte_Registro_Autores));
        newRow.append($("<td>").text(row.Fecha_Egreso_Reporte_Registro_Autores));
        newRow.append($("<td>").html('<a href="' + row.Direccion_Archivo_Reporte_Registro_Autores + '" download>Descargar</a>'));

        tbody.append(newRow);
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Error al obtener los reportes:", textStatus, errorThrown);
    }
  });
});
