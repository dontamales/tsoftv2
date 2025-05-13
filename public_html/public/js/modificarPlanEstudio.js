document.getElementById("formularioModificarPlanEstudio").addEventListener("submit", function (event) {
    event.preventDefault();

    const idPlanEstudio = document.getElementById("modificarPlanEstudio").value;

    const periodoPlanEstudio = document.getElementById("modificarPeriodoGeneracionInput").value;   
    const descripcionPlanEstudio = document.getElementById("modificarDescripcionPlanAnioInput").value;   



    fetch("../php/modificarPlanEstudio.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        idPlanEstudio: idPlanEstudio,
        periodoPlanEstudio: periodoPlanEstudio,
        descripcionPlanEstudio: descripcionPlanEstudio,
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
        alert("Ocurrió un error al modificar el plan de estudio, verifique que los datos no estén repetidos en la base de datos.");
      });
  });
