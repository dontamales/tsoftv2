document.getElementById("carrera-form").addEventListener("submit", function (event) {
    event.preventDefault();

    const departamento = document.getElementById("departamentoCarrera").value.trim();
    const jefe = document.getElementById("jefeCarrera").value.trim();
    const nombreCarrera = document.getElementById("nombreCarrera").value.trim();
    const iniciales = document.getElementById("inicialesCarrera").value.trim();
    const tipo = document.getElementById("tipoCarrera").value.trim();

    // Realizar la solicitud fetch
    fetch("../php/registroCarrera.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            departamento: departamento,
            jefe: jefe,
            nombreCarrera: nombreCarrera,
            iniciales: iniciales,
            tipo: tipo,
        }),
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            // Verificar la respuesta y llamar a clearForm() si es exitosa
            if (data.message === 'Error: Todos los campos son obligatorios.') {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(function (error) {
            // Manejar el error
            //console.error(error);
            alert("Ha ocurrido un error al guardar los datos.");
        });
});

function clearForm() {
    document.getElementById("nombreCarrera").value = "";
    document.getElementById("departamentoCarrera").value = "";
    document.getElementById("jefeCarrera").value = "";
    document.getElementById("inicialesCarrera").value = "";
    document.getElementById("tipoCarrera").value = "";
    
}
