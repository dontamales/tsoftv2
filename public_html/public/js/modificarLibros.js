document.getElementById("formularioModificarLibro").addEventListener("submit", function (event) {
    event.preventDefault();

    const idLibro = document.getElementById("modificarLibro").value;
    const nombreLibro = document.getElementById("modificarNombreLibro").value;   



    fetch("../php/modificarLibro.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        idLibro: idLibro,
        nombreLibro: nombreLibro,
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
        alert("Ocurrió un error al modificar el libro, verifique que el nombre no esté repetido en la base de datos.");
      });
  });
