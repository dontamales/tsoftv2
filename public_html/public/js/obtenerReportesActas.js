$(document).ready(function () {
  $.ajax({
    url: '../php/obtenerReportesActas.php',
    type: 'POST',
    dataType: 'json',
    success: function (data) {
      var tbody = $("#tabla-reporte-actas-libros tbody");
      tbody.empty();

      $.each(data, function (index, row) {
        var newRow = $("<tr>");
        newRow.append($("<td>").text(row.Id_Reporte_Actas_Libros));
        newRow.append($("<td>").text(row.Fecha_Creacion_Reporte));
        newRow.append($("<td>").text(row.Anio));
        newRow.append($("<td>").text(row.Periodo));
        newRow.append($("<td>").text(row.Nombre_Libro));
        newRow.append(
          $("<td>").html('<a href="' + row.Direccion_Archivo + '" download>Descargar</a>')
        );
        tbody.append(newRow);
      });
    },
    error: function (xhr, status, error) {
      console.error("Error al obtener reportes:", error);
    }
  });
});
