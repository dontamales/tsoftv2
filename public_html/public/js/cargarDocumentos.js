document.addEventListener('DOMContentLoaded', function() {
    let formUploads = document.querySelectorAll("[id^=form_fileUpload]"); // selecciona todos los formularios cuyo id comienza con "form_fileUpload"
    
    formUploads.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // previene el envío estándar del formulario

            let formData = new FormData(form); // crea una instancia de FormData del formulario actual

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // aquí puedes manejar la respuesta, por ejemplo, mostrar una alerta
                alert(data.message); // suponiendo que la respuesta es un objeto JSON con un campo "message"
            })
            .catch(error => {
                //console.error('Error:', error);
                alert("Hubo un error al subir el archivo.");
            });
        });
    });
});
