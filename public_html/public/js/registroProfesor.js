document.getElementById("register-form_profesor").addEventListener("submit", function (event) {
    event.preventDefault();

    const nombreCompleto = document.getElementById("nombresyapellidos_profesor").value.trim();
    const cedula = document.getElementById("cedula_profesor").value.trim();
    const grado = document.getElementById("grado_profesor").value.trim();

    // Realizar la solicitud fetch
    fetch("../php/registroProfesor.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            nombreCompleto: nombreCompleto,
            cedula: cedula,
            grado: grado,
        }),
    })
        .then(function (response) {
            // Verificar la respuesta y llamar a clearForm() si es exitosa
            if (response.ok) {
                alert("Los datos se han guardado exitosamente.");
                window.location.reload();
            } else {
                alert("Los datos no se guardaron exitosamente.");
            }
        })
        .catch(function (error) {
            // Manejar el error
        });
});

function clearForm() {
    document.getElementById("nombresyapellidos_profesor").value = "";
    document.getElementById("cedula_profesor").value = "";
    document.getElementById("grado_profesor").value = "";
    
}