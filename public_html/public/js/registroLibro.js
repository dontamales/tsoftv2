document.getElementById("formularioRegistroLibro").addEventListener("submit", function (event) {
    event.preventDefault();

    const descripcion = document.getElementById("nombreLibroInput").value.trim();

    // Realizar la solicitud fetch
    fetch("../php/registroLibro.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            descripcion: descripcion,
        }),
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            // Verificar la respuesta y mostrar mensaje de éxito o error
            if (data.message === 'Registro exitoso') {
                alert("El libro se ha registrado exitosamente.");
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(function (error) {
            // Manejar el error
            //console.error(error);
            alert("Ha ocurrido un error al registrar el libro, verifique que la descripción no esté repetida en la base de datos.");
        });
});

function clearForm() {
    document.getElementById("nombreLibroInput").value = "";
}
