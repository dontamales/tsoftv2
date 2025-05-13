document.getElementById("formularioRegistroSinodales").addEventListener("submit", function (event) {
    event.preventDefault();

    const sinodal1 = document.getElementById("nombre1").dataset.id;
    const rolSinodal1 = 1;
    const sinodal2 = document.getElementById("nombre2").dataset.id;
    const rolSinodal2 = 2;
    const sinodal3 = document.getElementById("nombre3").dataset.id;
    const rolSinodal3 = 3;
    const sinodal4 = document.getElementById("nombre4").dataset.id;
    const rolSinodal4 = 4;
    const egresadoProyecto = document.getElementById("egresado").dataset.id;

    const payload = {
        sinodal1: sinodal1,
        rolSinodal1: rolSinodal1,
        sinodal2: sinodal2,
        rolSinodal2: rolSinodal2,
        sinodal3: sinodal3,
        rolSinodal3: rolSinodal3,
        sinodal4: sinodal4,
        rolSinodal4: rolSinodal4,
        egresadoProyecto: egresadoProyecto,
    };
    
    // Realizar la solicitud fetch
    if(sinodal1 && sinodal2 && sinodal3 && sinodal4 && egresadoProyecto && rolSinodal1 && rolSinodal2 && rolSinodal3 && rolSinodal4){
    fetch("../php/registroSinodales.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(payload),
    })
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Ocurrió un error con la conexión al registrar los sinodales.");
            }
            return response.json();
        })
        .then(function (data) {
          //console.log(data);
            if (data.message === 'Actualización de asignaión de sinodales exitosa' || data.message === 'Asignación de sinodales exitoso') {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(function (error) {
            alert(data.error);
        });
    } else {
        alert("No se han recibido correctamente los datos.");
        }
});

function clearForm() {
    document.getElementById("nombre1").value = "";
    document.getElementById("nombre2").value = "";
    document.getElementById("nombre3").value = "";
    document.getElementById("nombre4").value = "";
    document.getElementById("egresado").value = "";
}

