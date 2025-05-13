document.getElementById("formularioTipoTitulacion").addEventListener("submit", function (event) {
    event.preventDefault();

    const nombreTipoTitulacion = document.getElementById("nombreTipoTitulacionInput").value.trim();
    const documentosSeleccionados = [...document.querySelectorAll('input[name="documentos[]"]:checked')].map(input => input.value);
    const planEstudioSeleccionado = document.getElementById("planEstudioTipoTitulacion").value;


    // Realizar la solicitud fetch
    fetch("../php/registroTipoTitulacion.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            nombreTipoTitulacion: nombreTipoTitulacion,
            documentos: documentosSeleccionados,
            planEstudio: planEstudioSeleccionado
        }),
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            // Verificar la respuesta y mostrar mensaje de éxito o error
            if (data.message === 'Registro exitoso') {
                alert("Registro de tipo de titulación exitoso.");
                window.location.reload();
            } else if (data.message === 'Registro de tipo de titulación exitoso, pero hubo un error al registrar los documentos'){  
            } else if (data.message === 'Registro de tipo de titulación exitoso, pero hubo un error al registrar el plan de estudio'){   
            } else {
                alert(data.message);
            }
        })
        .catch(function (error) {
            alert("Ha ocurrido un error al registrar el tipo de titulación.");
        });
});
