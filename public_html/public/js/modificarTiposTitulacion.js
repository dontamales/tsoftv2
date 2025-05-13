document.getElementById("modificarFormularioTipoTitulacion").addEventListener("submit", function (event) {
    event.preventDefault();

    const idTipoTitulacion = document.getElementById("modificarTipoTitulacion").value.trim();

    const nombreTipoTitulacion = document.getElementById("modificarNombreTipoTitulacionInput").value.trim();
    const documentosSeleccionados = [...document.querySelectorAll('input[name="modificarDocumentos[]"]:checked')].map(input => input.value);
    const planEstudioSeleccionado = document.getElementById("modificarPlanEstudioTipoTitulacion").value;
  



    fetch("../php/modificarTiposTitulacion.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        idTipoTitulacion: idTipoTitulacion,
        nombreTipoTitulacion: nombreTipoTitulacion,
        documentos: documentosSeleccionados,
        planEstudio: planEstudioSeleccionado
      }),
    })
      .then(function (response) {
        if (response.ok) {
          return response.text().then(function (text) {
            try {
              return JSON.parse(text);
            } catch (error) {
              //console.error(error);
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
        alert("Ocurrió un error al modificar el documento, verifique que el nombre no esté repetido en la base de datos.");
      });
  });
