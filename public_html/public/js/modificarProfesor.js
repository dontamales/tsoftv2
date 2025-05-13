document.getElementById("modificar-form_profesor").addEventListener("submit", function (event) {
    event.preventDefault();

    const idProfesor = document.getElementById("modificarProfesor").value;
    const nombresApellidos = document.getElementById("modificarNombresYApellidos_Profesor").value;
    const cedula = document.getElementById("modificarCedula_profesor").value;
    const grado = document.getElementById("modificarGrado_profesor").value;



    fetch("../php/modificarProfesor.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        idProfesor: idProfesor,
        nombresApellidos: nombresApellidos,
        cedula: cedula,
        grado: grado,
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
        alert("Ocurrió un error al modificar el profesor puede ser que el nombre o la cedula ya existan en la base de datos");
      });
  });
