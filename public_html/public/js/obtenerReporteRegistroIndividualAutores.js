$(document).ready(function () {
  $.ajax({
    url: '../php/obtenerReporteRegistroIndividualAutores.php',
    type: 'POST',
    dataType: 'json',
    success: function (data) {
      var tbody = $("#tabla-reporte-individual-autores tbody");
      tbody.empty();

      $.each(data, function (index, row) {
        var newRow = $("<tr>");

        newRow.append($("<td>").text(row.Id_Reporte_Registro_Individual_Autores));
        newRow.append($("<td>").text(row.Fecha_Creacion_Reporte_Registro_Individual_Autores));
        newRow.append($("<td>").text(row.Fecha_Ingreso_Reporte_Registro_Individual_Autores));
        newRow.append($("<td>").text(row.Fecha_Egreso_Reporte_Registro_Individual_Autores));
        newRow.append(
          $("<td>").html(
            '<a href="' + row.Direccion_Archivo_Reporte_Registro_Individual_Autores + '" download>Descargar</a>'
          )
        );

        tbody.append(newRow);
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Error al obtener los reportes individuales:", textStatus, errorThrown);
    }
  });
});
