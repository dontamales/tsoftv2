let currentEgresadoData = null;

export function obtenerDocumentos() {
  return fetch(`../php/obtenerEgresadoDocumentos.php?`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en la petición AJAX");
      }
      return response.json();
    })
    .catch((error) => {
      //console.error("Error al obtener datos:", error);
      // Puedes agregar más detalles sobre el error o mostrar un mensaje de error en la página.
    });
}

export function generarTabla(data) {
  const egresadoTableElement = document.getElementById(
    "tabla-egresadosDocumentos"
  );

  // Si el elemento no existe, simplemente salimos de la función
  if (!egresadoTableElement) {
    console.warn(
      "El elemento 'tabla-egresadosDocumentos' no se encontró en el DOM. La función 'generarTabla()' no se ejecutó."
    );
    return;
  }

  const tbody = egresadoTableElement.getElementsByTagName("tbody")[0];

  // Limpia el cuerpo de la tabla antes de llenarlo
  tbody.innerHTML = "";

  data.forEach((row) => {
    const tr = document.createElement("tr");

    const tdNombre = document.createElement("td");
    tdNombre.textContent = row.Nombres_Usuario + " " + row.Apellidos_Usuario;
    tr.appendChild(tdNombre);

    const tdCorreo = document.createElement("td");
    tdCorreo.textContent = row.Correo_Usuario;
    tr.appendChild(tdCorreo);

    const tdNumControl = document.createElement("td");
    tdNumControl.textContent = row.Num_Control;
    tr.appendChild(tdNumControl);

    const tdTipoTitulacion = document.createElement("td");
    tdTipoTitulacion.textContent = row.Tipo_Producto_Titulacion;
    tr.appendChild(tdTipoTitulacion);

    const tdProyecto = document.createElement("td");
    tdProyecto.textContent = row.Nombre_Proyecto;
    tr.appendChild(tdProyecto);

    const tdCarrera = document.createElement("td");
    tdCarrera.textContent = row.Nombre_Carrera;
    tr.appendChild(tdCarrera);

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

    // Ahora, sólo agregarás un botón general en la columna de acciones
    const tdAcciones = document.createElement("td");
    const buttonMostrarDocs = document.createElement("button");
    buttonMostrarDocs.classList.add("btn", "btn-primary", "btn-sm", "m-1");
    buttonMostrarDocs.textContent = "Mostrar documentos";

    // Almacenar toda la fila del egresado en el botón
    buttonMostrarDocs.dataset.egresadoData = JSON.stringify(row);

    // Agregar el evento de clic al botón
    buttonMostrarDocs.addEventListener("click", (event) => {
      const egresadoData = JSON.parse(event.target.dataset.egresadoData);
      currentEgresadoData = egresadoData;
      generarTablaDocumentos(egresadoData.DocumentosTotales);
    });

    tdAcciones.appendChild(buttonMostrarDocs);
    tr.appendChild(tdAcciones);

    tbody.appendChild(tr);
  });
}

function generarTablaDocumentos(documentos) {
  const documentosTableElement = document.getElementById("tabla-documentos");
  const tbody = documentosTableElement.getElementsByTagName("tbody")[0];

  // Limpia el cuerpo de la tabla antes de llenarlo
  tbody.innerHTML = "";

  documentos.forEach((doc) => {
    const tr = document.createElement("tr");

    const tdDocumento = document.createElement("td");

    // Crear un elemento de enlace para la descripción del documento
    const aLink = document.createElement("a");
    aLink.href = doc.Direccion_Archivo_Egresados_Documentos;
    aLink.textContent = doc.Descripcion_Documentos_Pendientes;
    aLink.target = "_blank"; // Agregar el atributo target
    tdDocumento.appendChild(aLink);
    tr.appendChild(tdDocumento);

    const tdEstado = document.createElement("td");
    if (doc.Aceptado_Egresado_Documentos == 0) {
      tdEstado.textContent = "Revisión pendiente";
      tdEstado.style.color = "red"; // Texto en rojo
    } else {
      tdEstado.textContent = "Documento aprobado";
      tdEstado.style.color = "green"; // Texto en verde
    }
    tr.appendChild(tdEstado);

    const tdAccion = document.createElement("td");

    // Crear botón de Aprobar
    const btnAprobar = document.createElement("button");
    btnAprobar.textContent = "Aprobar";
    btnAprobar.classList.add("btn", "btn-success", "btn-sm", "m-1");
    btnAprobar.addEventListener("click", () => aprobarDocumento(doc));
    tdAccion.appendChild(btnAprobar);

    // Crear botón de Rechazar
    const btnRechazar = document.createElement("button");
    btnRechazar.textContent = "Rechazar";
    btnRechazar.classList.add("btn", "btn-danger", "btn-sm", "m-1");
    btnRechazar.addEventListener("click", () => rechazarDocumento(doc));
    tdAccion.appendChild(btnRechazar);

    tr.appendChild(tdAccion);

    const tdFecha = document.createElement("td");
    if (doc.Fecha_Documento_Subido_Egresado_Documentos == null) {
      tdFecha.textContent = "Documento no subido";
    } else {
      tdFecha.textContent = doc.Fecha_Documento_Subido_Egresado_Documentos;
    }
    tr.appendChild(tdFecha);

    // Creación del elemento td
    const tdObservaciones = document.createElement("td");

    // Creación del elemento input
    const inputObservaciones = document.createElement("input");

    // Configuración del elemento input
    inputObservaciones.type = "text";
    inputObservaciones.className = "form-control";
    inputObservaciones.name = "observaciones";
    inputObservaciones.id = "observaciones-" + doc.Id_Documentos_Pendientes;
    tdObservaciones.appendChild(inputObservaciones);
    tr.appendChild(tdObservaciones);

    tbody.appendChild(tr);
  });
}

function aprobarDocumento(documento) {
  const idDocumento = documento.Id_Documentos_Pendientes;
  const idEgresado = documento.Num_Control;

  // Retornar la promesa para poder encadenarla más tarde
  return fetch(
    `../php/aprobarDocumento.php?idDocumento=${idDocumento}&idEgresado=${idEgresado}`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        //alert("Documento aprobado exitosamente. ");
        obtenerDocumentos().then((data) => {
          //Limpiar la tabla de documentos
          limpiarTabla();

          // Actualizar la primera tabla
          generarTabla(data);

          // Buscar y actualizar la fila del egresado actual
          const updatedEgresadoData = data.find(
            (egresado) =>
              egresado.Num_Control === currentEgresadoData.Num_Control
          );

          if (updatedEgresadoData) {
            generarTablaDocumentos(updatedEgresadoData.DocumentosTotales);
          }

          // Actualizar el contador de correos restantes
          fetch("../php/obtenerCorreosRestantes.php")
            .then((response) => response.json())
            .then((data) => {
              const correosRestantesElement =
                document.getElementById("correos-restantes");
              correosRestantesElement.textContent = `Correos restantes de hoy: ${data.correosRestantes}`;
            });
        });
      } else {
        alert("Error al aprobar el documento. ");
      }
    });
}

function rechazarDocumento(documento) {
  const idDocumento = documento.Id_Documentos_Pendientes;
  const idEgresado = documento.Num_Control;

  // Obtener el contenido del input de observaciones
  const observacionesInput = document.getElementById("observaciones-" + idDocumento);
  const observaciones = observacionesInput.value;

  // Crear el cuerpo de la petición con los datos necesarios
  const params = new URLSearchParams();
  params.append('idDocumento', idDocumento);
  params.append('idEgresado', idEgresado);
  params.append('observaciones', observaciones);  // Agregar las observaciones

  fetch('../php/rechazarDocumento.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: params,
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
        //alert("Documento rechazado exitosamente. ");
        obtenerDocumentos().then((data) => {
          //Limpiar la tabla de documentos
          limpiarTabla();

          // Actualizar la primera tabla
          generarTabla(data);

          // Buscar y actualizar la fila del egresado actual
          const updatedEgresadoData = data.find(
            (egresado) =>
              egresado.Num_Control === currentEgresadoData.Num_Control
          );

          if (updatedEgresadoData) {
            generarTablaDocumentos(updatedEgresadoData.DocumentosTotales);
          }

          // Actualizar el contador de correos restantes
          fetch("../php/obtenerCorreosRestantes.php")
            .then((response) => response.json())
            .then((data) => {
              const correosRestantesElement =
                document.getElementById("correos-restantes");
              correosRestantesElement.textContent = `Correos restantes de hoy: ${data.correosRestantes}`;
            });
        });
      } else {
        alert("Error al rechazar el documento. ");
      }
    });
}

// Función para filtrar la tabla basada en la consulta de búsqueda
function filtrarTabla(query) {
  // Seleccionar todas las filas de la tabla excepto la primera (encabezado)
  const filas = $("#tabla-egresadosDocumentos tbody tr");

  if (query === "") {
    // Si la consulta está vacía, mostrar todas las filas
    filas.show();
  } else {
    // Ocultar todas las filas primero
    filas.hide();

    // Convertir la consulta a minúsculas para la búsqueda sin distinción de mayúsculas/minúsculas
    const queryLower = query.toLowerCase();

    // Filtrar las filas basadas en la consulta
    filas
      .filter(function () {
        const fila = $(this);
        // Obtener todos los td de la fila
        const tds = fila.find("td");
        // Iterar a través de todos los td y buscar la consulta en el contenido
        for (let i = 0; i < tds.length; i++) {
          const contenido = $(tds[i]).text().toLowerCase();
          if (contenido.indexOf(queryLower) > -1) {
            return true; // La consulta se encontró en este td, así que mostrar esta fila
          }
        }
        return false; // La consulta no se encontró en esta fila
      })
      .show(); // Mostrar las filas que coinciden
  }
}

// Agregar un evento de escucha al input para detectar cambios
$("#filtro-documentos").on("input", function () {
  const query = $(this).val();
  filtrarTabla(query);
});

// Función para limpiar la tabla de generarTablaDocumentos
function limpiarTabla() {
  const documentosTableElement = document.getElementById("tabla-documentos");
  const tbody = documentosTableElement.getElementsByTagName("tbody")[0];
  tbody.innerHTML = "";
}
