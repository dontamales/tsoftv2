//Se utiliza en las páginas de anexos 1 y 2
export function obtenerEstatus(estatus) {
  return fetch(`../php/obtenerEgresadoEstatusAIyII.php?q=&estatus=${estatus}`)
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

// Se utiliza en las páginas de anexos 1 y 2
export function generarTabla(data) {
  const egresadoTableElement = document.getElementById("egresadoTable");

  // Si el elemento no existe, simplemente salimos de la función
  if (!egresadoTableElement) {
    // console.warn(
    //   "El elemento 'egresadoTable' no se encontró en el DOM. La función 'generarTabla()' no se ejecutó."
    // );
    return;
  }

  const tbody = egresadoTableElement.getElementsByTagName("tbody")[0];

  // Limpia el cuerpo de la tabla antes de llenarlo
  tbody.innerHTML = "";

  data.forEach((row) => {
    const tr = document.createElement("tr");

    const tdNombre = document.createElement("td");
    tdNombre.textContent = row.Nombres_Usuario;
    tr.appendChild(tdNombre);

    const tdApellido = document.createElement("td");
    tdApellido.textContent = row.Apellidos_Usuario;
    tr.appendChild(tdApellido);

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

    const tdAcciones = document.createElement("td");
    const button = document.createElement("button");
    button.classList.add("btn", "btn-primary", "btn-sm");
    button.textContent = "Generar y enviar documento a departamento académico";
    button.addEventListener("click", () => {
      generarDocumento(row.Num_Control);
    });
    tdAcciones.appendChild(button);
    tr.appendChild(tdAcciones);

    tbody.appendChild(tr);
  });
}

//Se utiliza en las páginas de anexos 1 y 2, y al aprobar el formato B de un egresado
export function generarDocumento(numControl) {
  fetch(`../php/generarAnexosIyII.php?numControl=${numControl}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en la petición AJAX");
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        alert(data.message);
        // Después de generar el documento con éxito, vuelve a obtener el estatus y a actualizar la tabla
        obtenerEstatus(4).then(generarTabla);
      } else {
        alert(data.message);
      }
    })
    .catch((error) => {
      alert(
        "Ha ocurrido un error al generar el documento. Por favor, verifique que no tenga el documento abierto o inténtalo de nuevo más tarde."
      );
    });
}
