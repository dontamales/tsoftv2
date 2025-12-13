$(document).ready(function () {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: '../php/obtenerReportesCohorteAnual.php',
    type: 'POST',
    dataType: 'json',
    success: function (data) {
      var tbody = $("#tabla-reporte-anual tbody");

      // Vacía el tbody por si acaso
      tbody.empty();

      // Llena la tabla con los datos obtenidos
      $.each(data, function (index, row) {
        var newRow = $("<tr>");

        newRow.append($("<td>").text(row.Id_Reporte_Cohorte_Anual));
        newRow.append($("<td>").text(row.Fecha_Creacion_Reporte_Cohorte_Anual));
        newRow.append($("<td>").text(row.Anio_Ingreso_Reporte_Cohorte_Anual));
        newRow.append($("<td>").html('<a href="' + row.Direccion_Archivo_Reporte_Cohorte_Anual + '" download>Descargar</a>'));

        tbody.append(newRow);
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Error al obtener los reportes:", textStatus, errorThrown);
    }
  });
});
