document.addEventListener("DOMContentLoaded", () => {
    const libroSelect = document.getElementById("libro");
    const subcarpetaSelect = document.getElementById("subcarpeta");

    fetch("../php/obtenerLibro.php")
        .then(response => response.json())
        .then(data => {
            data.forEach(libro => {
                const option = document.createElement("option");
                option.value = libro.nombreL; // Cambiar aquí para asignar el nombre del libro
                option.textContent = libro.nombreL;
                libroSelect.appendChild(option);
            });
        })
        .catch(error => {
            //console.error("Error fetching data:", error);
        });

    subcarpetaSelect.addEventListener("change", () => {
        const selectedSubcarpeta = subcarpetaSelect.value;
        document.getElementById("subcarpeta").value = selectedSubcarpeta;
    });
});

