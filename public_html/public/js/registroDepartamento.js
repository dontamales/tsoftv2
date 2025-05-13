document.getElementById("formularioDepartamento").addEventListener("submit", function (event) {
    event.preventDefault();

    const nombreDepartamento = document.getElementById("nombreDepartamentoInput").value.trim();
    const jefeDepartamento = document.getElementById("jefeDepartamentoInput").value.trim();
    const correoJefatura = document.getElementById("correoJefaturaInput").value.trim();
    const correoProyecto = document.getElementById("correoProyectoInput").value.trim();

    // Realizar la solicitud fetch
    fetch("../php/registroDepartamento.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            nombreDepartamento: nombreDepartamento,
            jefeDepartamento: jefeDepartamento,
            correoJefatura: correoJefatura,
            correoProyecto: correoProyecto,
        }),
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            // Verificar la respuesta y mostrar mensaje de éxito o error
            if (data.message === 'Registro exitoso') {
                alert("El departamento se ha registrado exitosamente.");
                window.location.reload();
            } else  {
                alert(data.message);
            }
        })
        .catch(function (error) {
            // Manejar el error
            //console.error(error);
            alert("Ha ocurrido un error al registrar el departamento.");
            
        });
});

function clearForm() {
    document.getElementById("nombreDepartamentoInput").value = "";
    document.getElementById("jefeDepartamentoInput").value = "";
    document.getElementById("correoJefaturaInput").value = "";
    document.getElementById("correoProyectoInput").value = "";
    
}