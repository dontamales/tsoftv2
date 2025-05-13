document.getElementById("formularioModificarDepartamento").addEventListener("submit", function (event) {
    event.preventDefault();

    const idDepartamento = document.getElementById("modificarDepartamento").value;

    const nombreDepartamento = document.getElementById("modificarNombreDepartamento").value;
    const jefeDepartamento = document.getElementById("modificarJefeDepartamentoInput").value;    
    const correoJefatura = document.getElementById("modificarCorreoJefatura").value;    
    const correoProyecto = document.getElementById("modificarCorreoProyecto").value;    



    fetch("../php/modificarDepartamentos.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        idDepartamento: idDepartamento,
        nombreDepartamento: nombreDepartamento,
        jefeDepartamento: jefeDepartamento,
        correoJefatura: correoJefatura,
        correoProyecto: correoProyecto,
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
          throw new Error("Error.");
        }
        alert(data.message);
        window.location.reload();

      })
      .catch(function (error) {
        alert("Ocurrió un error al modificar el departamento, verifique que el nombre no esté repetido.");
      });
  });
