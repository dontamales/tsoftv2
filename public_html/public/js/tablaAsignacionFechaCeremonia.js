$(document).ready(function () {
  actualizarTablaEgresados();

  // Función para cargar los datos en la tabla
  function cargarDatosEnTabla(data) {
    var tabla = $("#ceremonia-table tbody");
    tabla.empty();

    // Ordenar el array de datos según la fecha más cercana
    data.sort(function (a, b) {
      if (a.Fecha_Hora_Ceremonia_Egresado == null) return 1;
      if (b.Fecha_Hora_Ceremonia_Egresado == null) return -1;
      return (
        new Date(a.Fecha_Hora_Ceremonia_Egresado) -
        new Date(b.Fecha_Hora_Ceremonia_Egresado)
      );
    });

    data.forEach(function (egresado) {
      var fechaHora = egresado.Fecha_Hora_Ceremonia_Egresado;
      if (fechaHora == null) {
        fechaHora = "Sin asignar";
      } else {
        var fechaObj = new Date(fechaHora + "Z"); // Añadir 'Z' para interpretar como UTC
        var dia = fechaObj.getUTCDate().toString().padStart(2, "0");
        var mes = (fechaObj.getUTCMonth() + 1).toString().padStart(2, "0"); // Los meses en JS son 0-indexados
        var año = fechaObj.getUTCFullYear();
        var hora = fechaObj.getUTCHours().toString().padStart(2, "0");
        var minutos = fechaObj.getUTCMinutes().toString().padStart(2, "0");

        fechaHora = `${dia}-${mes}-${año} ${hora}:${minutos}`;
      }
      var row = `<tr>
                        <td><input type="checkbox" class="select-row" data-num-control="${egresado.Num_Control}"></td>
                        <td>${egresado.Num_Control}</td>
                        <td>${egresado.Nombres_Usuario} ${egresado.Apellidos_Usuario}</td>
                        <td>${egresado.Nombre_Carrera}</td>
                        <td>${egresado.Nombre_Proyecto}</td>
                        <td>${fechaHora}</td>
                    </tr>`;
      tabla.append(row);
    });
  }

  // Agregar evento para la búsqueda en tiempo real
  $("#busqueda").on("input", function () {
    var term = $(this).val().toLowerCase();
    filtrarTabla(term);
  });

  // Función de filtrado de la tabla
  function filtrarTabla(term) {
    var filas = $("#ceremonia-table tbody tr");

    filas.each(function () {
      var numControl = $(this).find("td:eq(0)").text().toLowerCase();
      var nombres = $(this).find("td:eq(1)").text().toLowerCase();
      var carrera = $(this).find("td:eq(2)").text().toLowerCase();
      var proyecto = $(this).find("td:eq(3)").text().toLowerCase();
      var fechaHora = $(this).find("td:eq(4)").text().toLowerCase();

      if (
        numControl.includes(term) ||
        nombres.includes(term) ||
        carrera.includes(term) ||
        proyecto.includes(term) ||
        fechaHora.includes(term)
      ) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }

  // Botón para asignar la fecha a los registros seleccionados
  $("#asignar-fecha-seleccionados").click(function (e) {
    e.preventDefault();
    var seleccionados = [];
    $(".select-row:checked").each(function () {
      seleccionados.push($(this).data("num-control"));
    });

    if (seleccionados.length === 0) {
      alert("No se seleccionó ningún registro.");
      return;
    }

    var fechaHora = $("#fechaHora").val();

    $.ajax({
      url: "../php/registroFechaCeremoniaMultiple.php",
      method: "POST",
      data: {
        seleccionados: JSON.stringify(seleccionados),
        fechaHora: fechaHora,
      },
      dataType: "json",
      success: function (response) {
        if (response.error) {
          alert(response.error);
        } else {
          actualizarTablaEgresados();
          alert(response.message);
        }
      },
      error: function (xhr, status, error) {
        alert("Error en la solicitud a la base de datos");
      },
    });
  });
  
  //Desasginar fecha de ceremonia - SCS09072025
  $("#desasignar-fecha").click(function (e) {
  e.preventDefault();
  var seleccionados = [];
  $(".select-row:checked").each(function () {
    seleccionados.push($(this).data("num-control"));
  });

  if (seleccionados.length === 0) {
    alert("No se seleccionó ningún registro.");
    return;
  }

  $.ajax({
    url: "../php/desasignarFechaCeremoniaMultiple.php", // URL al archivo PHP de desasignación
    method: "POST",
    data: {
      seleccionados: JSON.stringify(seleccionados)
    },
    dataType: "json",
    success: function (response) {
      if (response.error) {
        alert(response.error);
      } else {
        actualizarTablaEgresados(); 
        alert(response.message);    
      }
    },
    error: function (xhr, status, error) {
      alert("Error en la solicitud a la base de datos");
    }
  });
});

  function actualizarTablaEgresados() {
    $.ajax({
      url: "../php/tablaAsignacionFechaCeremonia.php", // Cambia la URL al archivo PHP que obtiene los datos
      dataType: "json",
      success: function (data) {
        cargarDatosEnTabla(data);
      },
      error: function (xhr, status, error) {
        alert("Error al obtener los datos de la tabla");
      },
    });
  }

  // Seleccionar/deseleccionar todos los registros
  $("#select-all").click(function () {
    var isChecked = $(this).prop("checked");
    $(".select-row").prop("checked", isChecked);
  });

  // Actualiza el estado del checkbox "Seleccionar Todo" si alguno de los otros checkboxes cambia
  $(".table-responsive").on("click", ".select-row", function () {
    var total = $(".select-row").length;
    var selected = $(".select-row:checked").length;
    $("#select-all").prop("checked", total === selected);
  });
});
