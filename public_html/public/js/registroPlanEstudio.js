document.getElementById("formularioRegistroPlanEstudio").addEventListener("submit", function (event) {
    event.preventDefault();

    const periodoGeneracion = document.getElementById("periodoGeneracionInput").value.trim();
    const descripcionPlanAnio = document.getElementById("descripcionPlanAnioInput").value.trim();

    // Realizar la solicitud fetch
    fetch("../php/registroPlanEstudio.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            periodoGeneracion: periodoGeneracion,
            descripcionPlanAnio: descripcionPlanAnio,
        }),
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            // Verificar la respuesta y mostrar mensaje de éxito o error
            if (data.message === 'Registro exitoso') {
                alert("El plan de estudio se ha registrado exitosamente.");
                window.location.reload();
            } else  {
                alert(data.message);
            }
        })
        .catch(function (error) {
        alert("Ha ocurrido un error al registrar el plan de estudio.");
        });
});

function clearForm() {
    document.getElementById("periodoGeneracionInput").value = "";
    document.getElementById("descripcionPlanAnioInput").value = "";
    
}
