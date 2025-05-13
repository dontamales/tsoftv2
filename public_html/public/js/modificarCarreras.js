document.getElementById("modificar-carrera-form").addEventListener("submit", function (event) {
    event.preventDefault();

    const idCarrera = document.getElementById("modificarCarrera").value;
    const nombreCarrera = document.getElementById("modificarNombreCarrera").value;
    const departamentoCarrera = document.getElementById("modificarDepartamentoCarrera").value;    
    const jefeCarrera = document.getElementById("modificarJefeCarrera").value;    
    const inicialesCarrera = document.getElementById("modificarInicialesCarrera").value;    
    const tipoCarrera = document.getElementById("modificarTipoCarrera").value;    



    fetch("../php/modificarCarreras.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        idCarrera: idCarrera,
        nombreCarrera: nombreCarrera,
        departamentoCarrera: departamentoCarrera,
        jefeCarrera: jefeCarrera,
        inicialesCarrera: inicialesCarrera,
        tipoCarrera: tipoCarrera,
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
        alert("Ocurrió un error al modificar la carrera, puede ser que el nombre o la iniciales de la carrera ya existan en la base de datos.");
      });
  });
