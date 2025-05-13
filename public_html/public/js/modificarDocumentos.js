document.getElementById("formularioModificarDocumento").addEventListener("submit", function (event) {
    event.preventDefault();

    const idDocumento = document.getElementById("modificarDocumento").value;

    const nombreDocumento = document.getElementById("modificarDocumentoDescripcion").value;   



    fetch("../php/modificarDocumentos.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        idDocumento: idDocumento,
        nombreDocumento: nombreDocumento,
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
        alert("Ocurrió un error al modificar el documento verifique que el nombre no esté repetido.");
      });
  });
