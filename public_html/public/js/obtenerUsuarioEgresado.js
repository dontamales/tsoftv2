// Almacenar los egresados en un array
var egresados = [];

// Obtener los datos del egresado dinámicamente
function obtenerExpedienteEgresado() {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "../php/obtenerUsuariosEgresado.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      egresados = JSON.parse(xhr.responseText);
    }
  };
  xhr.send();
}

// Llamar a la función para obtener los datos de los egresados al cargar la página
window.addEventListener("load", obtenerExpedienteEgresado);

// Escuchar cambios en el input
var input = document.getElementById("inputUsuarioExpediente");
var hiddenInput = document.getElementById("selectedUsuarioId"); // Campo oculto

input.addEventListener("input", function() {
  var query = this.value.toLowerCase();
  var listContainer = document.getElementById("listContainer");
  listContainer.innerHTML = "";

  // Añade esta comprobación para evitar mostrar la lista completa cuando no hay entrada.
  if (query === "") {
    return;
  }

  egresados.forEach(function(egresado) {
    if (egresado.nombre.toLowerCase().includes(query) || egresado.numControl.toLowerCase().includes(query)) {
      var div = document.createElement("div");
      div.className = "list-group-item";
      div.textContent = egresado.numControl + " - " + egresado.nombre;
      div.onclick = function() {
        input.value = egresado.numControl + " - " + egresado.nombre;
        hiddenInput.value = egresado.id; // Guardamos el ID en el campo oculto
        listContainer.innerHTML = "";
      };
      listContainer.appendChild(div);
    }
  });
});

document
  .getElementById("btn_Expediente_Egresado")
  .addEventListener("click", cargarDatosExpediente);

function cargarDatosExpediente() {
  var usuarioId = document.getElementById("selectedUsuarioId").value;
  if (usuarioId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/obtenerDatosExpedienteEgresado.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        var datos = JSON.parse(xhr.responseText);
        rellenarFormatoB(datos);
      }
    };
    xhr.send("id=" + usuarioId);
  } else {
    alert("Por favor, selecciona un sustentante.");
  }
}

function cargarDatosDocumentosExpedienteEgresado() {
  var usuarioId = document.getElementById("selectedUsuarioId").value;
  if (usuarioId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/obtenerDocumentosExpedienteEgresado.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        var datos = JSON.parse(xhr.responseText);
        const egresadoTableElement = document.getElementById(
          "tabla-egresadosDocumentosListaExp"
        );
        const tbody = egresadoTableElement.getElementsByTagName("tbody")[0];
        tbody.innerHTML = "";

        datos.forEach((row) => {
          const tr = document.createElement("tr");
          const tdDocumentosPendientes = document.createElement("td");
          const ulPendientes = document.createElement("ul");
          row.DocumentosPendientes.forEach((descripcionDocumentoPendiente) => {
            const li = document.createElement("li");
            li.textContent = descripcionDocumentoPendiente;
            ulPendientes.appendChild(li);
          });
          tdDocumentosPendientes.appendChild(ulPendientes);
          tr.appendChild(tdDocumentosPendientes);

          const tdDocumentosAceptados = document.createElement("td");
          const ulAceptados = document.createElement("ul");
          row.DocumentosAprobados.forEach((doc) => {
            const li = document.createElement("li");
            li.textContent = doc;
            ulAceptados.appendChild(li);
          });
          tdDocumentosAceptados.appendChild(ulAceptados);
          tr.appendChild(tdDocumentosAceptados);

          const tdDocumentosPorRevisar = document.createElement("td");
          const ulPorRevisar = document.createElement("ul");
          row.DocumentosPorRevisar.forEach((doc) => {
            const li = document.createElement("li");
            li.textContent = doc.Descripcion_Documentos_Pendientes;
            ulPorRevisar.appendChild(li);
          });
          tdDocumentosPorRevisar.appendChild(ulPorRevisar);
          tr.appendChild(tdDocumentosPorRevisar);

          generarTablaDocumentos(row.DocumentosTotales);

          tbody.appendChild(tr);
        });
      }
    };
    xhr.send("id=" + usuarioId);
  }
}

function generarTablaDocumentos(documentosTotales) {
  const egresadoTableElement = document.getElementById(
    "tabla-egresadosDocumentosEntregadosExp"
  );
  const tbody = egresadoTableElement.getElementsByTagName("tbody")[0];
  tbody.innerHTML = "";
  documentosTotales.forEach((row) => {
    const tr = document.createElement("tr");

    const tdDocumento = document.createElement("td");

    // Crear un elemento de enlace para la descripción del documento
    const aLink = document.createElement("a");
    aLink.href = row.Direccion_Archivo_Egresados_Documentos;
    aLink.textContent = row.Descripcion_Documentos_Pendientes;
    tdDocumento.appendChild(aLink);
    aLink.target = "_blank"; // Abrir en una nueva pestaña JH20250707
    aLink.rel = "noopener noreferrer"; // Buena práctica de seguridad JH20250707
    tr.appendChild(tdDocumento);

    const tdEstado = document.createElement("td");
    if (row.Aceptado_Egresado_Documentos == 0) {
      tdEstado.textContent = "Revisión pendiente";
      tdEstado.style.color = "red"; // Texto en rojo
    } else {
      tdEstado.textContent = "Documento aprobado";
      tdEstado.style.color = "green"; // Texto en verde
    }
    tr.appendChild(tdEstado);

    const tdFecha = document.createElement("td");
    // Aquí puedes agregar cualquier acción específica que desees para cada documento
    if (row.Fecha_Documento_Subido_Egresado_Documentos == null) {
      tdFecha.textContent = "Documento no subido";
    } else {
      tdFecha.textContent = row.Fecha_Documento_Subido_Egresado_Documentos;
    }
    tr.appendChild(tdFecha);

    tbody.appendChild(tr);
  });
}

function rellenarFormatoB(datos) {
  var tbody1 = $("#tabla-egresadosPeticionSinodalesExp tbody");
  tbody1.empty();
  var tbody2 = $("#tabla-egresadosDocumentosListaExp tbody");
  tbody2.empty();
  var tbody3 = $("#tabla-egresadosDocumentosEntregadosExp tbody");
  tbody3.empty();
  var tbody4 = $("#tabla-egresadoSinodalesExp tbody");
  tbody4.empty();

  $("#estatusEgresadoExp").val(datos.Descripcion_Estatus);
  $("#selectModificarEstatusEgresadoExp").prop("disabled", false)
  $("#btnModificarEstatusEgresadoExp").prop("disabled", false)

  $("#nombresFormatoBExp").val(datos.Nombres_Usuario);
  $("#apellidosFormatoBExp").val(datos.Apellidos_Usuario);
  switch (datos.Id_Sexo_Genero) {
    case 1:
      $("#generoHombreFormatoBExp").prop("checked", true);
      break;
    case 2:
      $("#generoMujerFormatoBExp").prop("checked", true);
      break;
    case 3:
      $("#generoIndefinidoFormatoBExp").prop("checked", true);
      break;
  }
  $("#edadFormatoBExp").val(datos.Edad_Egresado);
  $("#celularFormatoBExp").val(datos.Celular_Egresado);
  $("#telefonoFormatoBExp").val(datos.Telefono_Egresado);
  $("#codigo_postalFormatoBExp").val(datos.Codigo_Postal_Direccion);
  $("#coloniaFormatoBExp").val(datos.Colonia_Direccion);
  $("#calleFormatoBExp").val(datos.Calle_Direccion);
  $("#num_extFormatoBExp").val(datos.Num_Exterior_Direccion);
  $("#num_intFormatoBExp").val(datos.Num_Interior_Direccion);
  $("#numero_controlFormatoBExp").val(datos.Num_Control);
  $("#carreraFormatoBExp").val(datos.Nombre_Carrera);
  $("#promedioFormatoBExp").val(datos.Promedio_Egresado);
  $("#proyectoFormatoBExp").val(datos.Nombre_Proyecto);
  $("#planEstudioFormatoBExp").val(
    datos.Descripcion_Del_Plan_De_Año_Plan_Estudio
  );
  $("#tipoTitulaciónFormatoBExp").val(datos.Tipo_Producto_Titulacion);
  $("#fechaIngresoFormatoBExp").val(datos.Fecha_Ingreso_Egresado);
  $("#fechaEgresoFormatoBExp").val(datos.Fecha_Egresar_Egresado);
  $("#asesorFormatoBExp").val(datos.Nombre_Asesor);
  switch (datos.Proyecto_Equipo_Egresado) {
    case 0:
      $("#equipoCheckboxFormatoBExp").prop("checked", false);
      break;
    case 1:
      $("#equipoCheckboxFormatoBExp").prop("checked", true);
      break;
  }
  switch (datos.Numero_Equipo_Egresados) {
    case 2:
      $("#radioEquipo2FormatoBExp").prop("checked", true);
      break;
    case 3:
      $("#radioEquipo3FormatoBExp").prop("checked", true);
      break;
  }
  $("#equipoInput0FormatoBExp").val(datos.NumeroControl_Equipo_Egresado1);
  $("#equipoInput1FormatoBExp").val(datos.Nombre_Equipo_Egresado1);
  $("#equipoInput2FormatoBExp").val(datos.Nombre_Carrera_1);
  $("#equipoInput3FormatoBExp").val(datos.NumeroControl_Equipo_Egresado2);
  $("#equipoInput4FormatoBExp").val(datos.Nombre_Equipo_Egresado2);
  $("#equipoInput5FormatoBExp").val(datos.Nombre_Carrera_2);

  var newRow1 = $("<tr>");
  if (datos.Direccion_De_Archivo_Anexo_I_II != null) {
    newRow1.append($("<td>").text(datos.Num_Control));
    newRow1.append(
      $("<td>").text(datos.Nombres_Usuario + " " + datos.Apellidos_Usuario)
    );
    newRow1.append($("<td>").text(datos.Correo_Usuario));
    newRow1.append(
      $("<td>").html(
        '<a href="' +
          datos.Direccion_De_Archivo_Anexo_I_II +
          '" download>Descargar</a>'
      )
    );
  } else {
    newRow1.append($("<td>").text(datos.Num_Control));
    newRow1.append(
      $("<td>").text(datos.Nombres_Usuario + " " + datos.Apellidos_Usuario)
    );
    newRow1.append($("<td>").text(datos.Correo_Usuario));
    newRow1.append($("<td>").text("No hay archivo"));
  }
  tbody1.append(newRow1);

  // Mostrar FOJA asignada JH20250707
  const fojaContainer = document.getElementById("fojaAsignadaExpediente");
  fojaContainer.innerHTML = "";

  if (datos.Direccion_Foja_Asignada && datos.Direccion_Foja_Asignada.trim() !== "") {
    const link = document.createElement("a");
    link.href = datos.Direccion_Foja_Asignada;
    link.target = "_blank";
    link.rel = "noopener noreferrer";
    link.textContent = datos.Nombre_Formato_Foja || "Ver FOJA";

    fojaContainer.appendChild(link);
    fojaContainer.style.color = "";
    fojaContainer.style.fontWeight = "normal";
    fojaContainer.style.fontStyle = "normal";
  } else {
    fojaContainer.textContent = "Sin asignación de FOJA";
    fojaContainer.style.color = "red";
    fojaContainer.style.fontWeight = "bold";
    fojaContainer.style.fontStyle = "italic";
  }

  cargarDatosDocumentosExpedienteEgresado();

  var newRow4 = $("<tr>");
  newRow4.append($("<td>").text(datos.Nombre_Proyecto));
  newRow4.append($("<td>").text(datos.Nombre_Sinodal_4));
  newRow4.append($("<td>").text(datos.Nombre_Sinodal_1));
  newRow4.append($("<td>").text(datos.Nombre_Sinodal_2));
  newRow4.append($("<td>").text(datos.Nombre_Sinodal_3));
  tbody4.append(newRow4);

  $("#fechaCeremoniaEgresadoExp").val(datos.Fecha_Hora_Ceremonia_Egresado);
}

function validateForm(estatus) {
  if (!estatus) {
    alert("Selecciona un estatus para continuar.");
    return false;
  }
}

document
  .getElementById("modificarEstatusEgresadoExp")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    var usuarioEstatus = document.getElementById("selectModificarEstatusEgresadoExp").value;
    
    if(validateForm(usuarioEstatus)){
      return;
    }

    var usuarioId = document.getElementById("selectedUsuarioId").value;
  
    fetch("../php/modificarEstatusUsuario.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        usuarioId: usuarioId,
        usuarioEstatus: usuarioEstatus,
      }),
    })
      .then(function (response) {
        if (response.ok) {
          return response.text().then(function (text) {
            try {
              return JSON.parse(text);
            } catch (error) {
              throw new Error("Error al analizar JSON.");
            }
          });
        } else {
          throw new Error("Error en la petición.");
        }
      })
    .then(async function (data) {
        if (data.message.includes("Error")) {
          throw new Error("Error del servidor.");
        }
        alert(data.message);

        window.location.reload();
      })
    .catch(function (error) {
        alert("Ocurrió un error al modificar el estatus del usuario");
      });
});