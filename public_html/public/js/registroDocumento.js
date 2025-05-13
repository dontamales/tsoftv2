document.getElementById("formularioRegistroDocumento").addEventListener("submit", function (event) {
    event.preventDefault();

    const descripcionD = document.getElementById("documentoDescripcionInput").value.trim();

    // Realizar la solicitud fetch
    fetch("../php/registroDocumento.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            descripcionD: descripcionD,
        }),
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            // Verificar la respuesta y mostrar mensaje de éxito o error
            if (data.message === 'Registro exitoso') {
                alert("El documento se ha registrado exitosamente.");
                window.location.reload();
            } else  {
                alert(data.message);
            }
        })
        .catch(function (error) {
            // Manejar el error
            //console.error(error);
            alert("Ha ocurrido un error al registrar el documento.");
        });
});

function clearForm() {
    document.getElementById("documentoDescripcionInput").value = "";
    
}