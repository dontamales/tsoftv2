import { generarDocumento } from "./anexos1y2.js";

$(document).ready(function () {
  updateStudentCount();
  $("#buscarEgresadoBtn").click(function () {
    var num_control = $("#buscarEgresado").val();
    $.ajax({
      type: "POST",
      url: "../php/buscarFormatoPendiente.php",
      data: { num_control: num_control },
      success: function (data) {
        var formatos = JSON.parse(data);
        var tbody = "";

        // Construir las filas de la tabla
        formatos.forEach(function (formato) {
          tbody += "<tr id='formatoB-" + formato.Num_Control + "'>";
          tbody += "<td>" + formato.Num_Control + "</td>";
          tbody +=
            "<td>" +
            formato.Nombres_Usuario +
            " " +
            formato.Apellidos_Usuario +
            "</td>";
          tbody += "<td>" + formato.Fecha_Envio_Formato_B_Egresado + "</td>";
          tbody += "<td>";
          tbody +=
            "<a href='#formato_BRev' class='view-button btn btn-primary btn-sm' data-id='" +
            formato.Id_Usuario +
            "'>Ver</a> ";
          tbody +=
            "<a href='#' class='approve-button btn btn-success btn-sm' data-id='" +
            formato.Id_Usuario +
            "'>Aprobar</a> ";
          tbody +=
            "<a href='#' class='reject-button btn btn-danger btn-sm' data-id='" +
            formato.Id_Usuario +
            "'>Rechazar</a>";
          tbody += "</td></tr>";
        });

        // Reemplazar el contenido de la tabla con las nuevas filas
        $(".table tbody").html(tbody);

        // Reasignar los eventos de clic
        assignClickEvents();
      },
      error: function (error) {
        //console.log(error);
      },
    });
  });
  assignClickEvents();
});

var idEstudiante = null; // Guarda el id del estudiante a nivel global

function updateStudentStatus(idEstudiante, nuevoEstatus, observaciones) {
  $.ajax({
    url: "../php/actualizarEstatusFormatoB.php",
    type: "POST",
    data: {
      user_id: idEstudiante,
      status: nuevoEstatus,
      observaciones: observaciones,
    },
    success: function (response) {
      var data = JSON.parse(response);

      // Recargar la tabla si la actualización fue exitosa
      if (data.success) {
        limpiarFormulario();
        reloadTable(true);
        updateStudentCount();
      }
    },
    error: function (error) {
      //console.log(error);
    },
  });
}

function assignClickEvents() {
  // Evento de clic para el botón "Ver"
  $(".view-button")
    .off("click")
    .click(function () {
      idEstudiante = $(this).attr("data-id");

      $.ajax({
        url: "../php/formatoBRev.php",
        type: "POST",
        data: {
          user_id: idEstudiante,
        },
        success: function (response) {
          var datosEstudiante = JSON.parse(response);

          $("#nombresFormatoBRev").val(datosEstudiante.Nombres_Usuario);
          $("#apellidosFormatoBRev").val(datosEstudiante.Apellidos_Usuario);
          switch (datosEstudiante.Id_Sexo_Genero) {
            case 1:
              $("#generoHombreFormatoBRev").prop("checked", true);
              break;
            case 2:
              $("#generoMujerFormatoBRev").prop("checked", true);
              break;
            case 3:
              $("#generoIndefinidoFormatoBRev").prop("checked", true);
              break;
          }
          $("#edadFormatoBRev").val(datosEstudiante.Edad_Egresado);
          $("#celularFormatoBRev").val(datosEstudiante.Celular_Egresado);
          $("#telefonoFormatoBRev").val(datosEstudiante.Telefono_Egresado);
          $("#codigo_postalFormatoBRev").val(
            datosEstudiante.Codigo_Postal_Direccion
          );
          $("#coloniaFormatoBRev").val(datosEstudiante.Colonia_Direccion);
          $("#calleFormatoBRev").val(datosEstudiante.Calle_Direccion);
          $("#num_extFormatoBRev").val(datosEstudiante.Num_Exterior_Direccion);
          $("#num_intFormatoBRev").val(datosEstudiante.Num_Interior_Direccion);
          $("#numero_controlFormatoBRev").val(datosEstudiante.Num_Control);
          $("#carreraFormatoBRev").val(datosEstudiante.Nombre_Carrera);
          $("#promedioFormatoBRev").val(datosEstudiante.Promedio_Egresado);
          $("#proyectoFormatoBRev").val(datosEstudiante.Nombre_Proyecto);
          $("#planEstudioFormatoBRev").val(
            datosEstudiante.Descripcion_Del_Plan_De_Año_Plan_Estudio
          );
          $("#tipoTitulaciónFormatoBRev").val(
            datosEstudiante.Tipo_Producto_Titulacion
          );
          $("#fechaIngresoFormatoBRev").val(
            datosEstudiante.Fecha_Ingreso_Egresado
          );
          $("#fechaEgresoFormatoBRev").val(
            datosEstudiante.Fecha_Egresar_Egresado
          );
          $("#asesorFormatoBRev").val(datosEstudiante.Nombre_Profesor);
          switch (datosEstudiante.Proyecto_Equipo_Egresado) {
            case 0:
              $("#equipoCheckboxFormatoBRev").prop("checked", false);
              break;
            case 1:
              $("#equipoCheckboxFormatoBRev").prop("checked", true);
              break;
          }
          switch (datosEstudiante.Numero_Equipo_Egresados) {
            case 2:
              $("#radioEquipo2FormatoBRev").prop("checked", true);
              break;
            case 3:
              $("#radioEquipo3FormatoBRev").prop("checked", true);
              break;
          }
          $("#equipoInput0FormatoBRev").val(
            datosEstudiante.NumeroControl_Equipo_Egresado1
          );
          $("#equipoInput1FormatoBRev").val(
            datosEstudiante.Nombre_Equipo_Egresado1
          );
          $("#equipoInput2FormatoBRev").val(
            datosEstudiante.Nombre_Carrera_1
          );
          $("#equipoInput3FormatoBRev").val(
            datosEstudiante.NumeroControl_Equipo_Egresado2
          );
          $("#equipoInput4FormatoBRev").val(
            datosEstudiante.Nombre_Equipo_Egresado2
          );
          $("#equipoInput5FormatoBRev").val(
            datosEstudiante.Nombre_Carrera_2
          );
        },
        error: function (error) {
          //console.log(error);
        },
      });
    });

  $("#aprobarFormatoBRev")
    .off("click")
    .click(function () {
      $.ajax({
        url: "../php/formatoBRev.php",
        type: "POST",
        data: {
          user_id: idEstudiante,
        },
        success: function (response) {
          var datosEstudiante = JSON.parse(response);
          updateStudentStatus(idEstudiante, 4, null);
          generarDocumento(datosEstudiante.Num_Control);
        },
        error: function (error) {
          //console.log(error);
        },
      });
    });

  // Para el botón de "Rechazar"
  $("#rechazarFormatoBRev")
    .off("click")
    .click(function () {
      $.ajax({
        url: "../php/formatoBRev.php",
        type: "POST",
        data: {
          user_id: idEstudiante,
        },
        success: function (response) {
          var datosEstudiante = JSON.parse(response);
          var observaciones = $("#comentariosFormatoBRev").val();
          updateStudentStatus(idEstudiante, 3, observaciones);
          sendEmail(
            datosEstudiante.Correo_Usuario,

            datosEstudiante.Nombres_Usuario +
              " " +
              datosEstudiante.Apellidos_Usuario,

            "Rechazo de Formato B",

            "Su formato B ha sido rechazado, observaciones: ''" +
              observaciones +
              ".'' Revise su formato B y corrija lo necesario.",

            "<strong>Su formato B ha sido rechazado</strong>, <br /> Observaciones: " +
              observaciones +
              ". Revise su formato B y corrija lo necesario."
          );
        },
        error: function (error) {
          //console.log(error);
        },
      });
    });

  // Evento de clic para el botón "Aprobar"
  $(".approve-button")
    .off("click")
    .click(function () {
      var idEstudiante = $(this).attr("data-id");
      $.ajax({
        url: "../php/formatoBRev.php",
        type: "POST",
        data: {
          user_id: idEstudiante,
        },
        success: function (response) {
          var datosEstudiante = JSON.parse(response);
          updateStudentStatus(idEstudiante, 4, null);
          generarDocumento(datosEstudiante.Num_Control);
          reloadTable(true);
        },
        error: function (error) {
          //console.log(error);
        },
      });
    });

  // Evento de clic para el botón "Rechazar"
  $(".reject-button")
    .off("click")
    .click(function () {
      var idEstudiante = $(this).attr("data-id");
      $.ajax({
        url: "../php/formatoBRev.php",
        type: "POST",
        data: {
          user_id: idEstudiante,
        },
        success: function (response) {
          var datosEstudiante = JSON.parse(response);
          var observaciones = $("#comentariosFormatoBRev").val();
          updateStudentStatus(idEstudiante, 3, observaciones);
          sendEmail(
            datosEstudiante.Correo_Usuario,

            datosEstudiante.Nombres_Usuario +
              " " +
              datosEstudiante.Apellidos_Usuario,

            "Rechazo de Formato B",

            "Su formato B ha sido rechazado. Revise su formato B y corrija lo necesario.",

            "<strong>Su formato B ha sido rechazado.</strong> Revise su formato B y corrija lo necesario."
          );
          reloadTable(true);
        },
        error: function (error) {
          //console.log(error);
        },
      });
    });

  // Maneja el evento de clic del botón "Ver todos"
  $("#verTodosFormatoBRev")
    .off("click")
    .click(function (e) {
      e.preventDefault();
      updateStudentCount();
      reloadTable(false);
    });

  // Maneja el evento de clic del botón "Ver sólo 10"
  $("#verDiezFormatoBRev")
    .off("click")
    .click(function (e) {
      e.preventDefault();
      updateStudentCount();
      reloadTable(true);
    });
}

function reloadTable(limit) {
  var script = limit
    ? "../php/obtenerFormatosPendientes.php"
    : "../php/obtenerTodosFormatosPendientes.php";
  $.ajax({
    url: script,
    type: "POST",
    success: function (response) {
      var formatos = JSON.parse(response);
      var tbody = "";

      // Construir las filas de la tabla
      formatos.forEach(function (formato) {
        tbody += "<tr id='formatoB-" + formato.Num_Control + "'>";
        tbody += "<td>" + formato.Num_Control + "</td>";
        tbody +=
          "<td>" +
          formato.Nombres_Usuario +
          " " +
          formato.Apellidos_Usuario +
          "</td>";
        tbody += "<td>" + formato.Fecha_Envio_Formato_B_Egresado + "</td>";
        tbody += "<td>";
        tbody +=
          "<a href='#formato_BRev' class='view-button btn btn-primary btn-sm' data-id='" +
          formato.Id_Usuario +
          "'>Ver</a> ";
        tbody +=
          "<a href='#' class='approve-button btn btn-success btn-sm' data-id='" +
          formato.Id_Usuario +
          "'>Aprobar</a> ";
        tbody +=
          "<a href='#' class='reject-button btn btn-danger btn-sm' data-id='" +
          formato.Id_Usuario +
          "'>Rechazar</a>";
        tbody += "</td></tr>";
      });

      // Reemplazar el contenido de la tabla con las nuevas filas
      $(".table tbody").html(tbody);

      // Reasignar los eventos de clic
      assignClickEvents();
    },
    error: function (error) {
      //console.log(error);
    },
  });
}

function updateStudentCount() {
  $.ajax({
    url: "../php/obtenerTotalFormatosPendientes.php",
    type: "POST",
    success: function (response) {
      var data = JSON.parse(response);

      // Actualizar la página con la nueva cantidad
      $("#studentCount").text(data.total);
    },
    error: function (error) {
      //console.log(error);
    },
  });
}

function limpiarFormulario() {
  document.getElementById("formato_BRev").reset();
}

function sendEmail(
  correoPara,
  nombrePara,
  asunto,
  textoPlanoCorreo,
  textoHtmlCorreo
) {
  return $.ajax({
    url: "../php/enviarNotificacion.php",
    type: "POST",
    data: {
      correoPara: correoPara,
      nombrePara: nombrePara,
      asunto: asunto,
      textoPlanoCorreo: textoPlanoCorreo,
      textoHtmlCorreo: textoHtmlCorreo,
    },
  });
}
