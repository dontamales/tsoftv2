document.addEventListener("DOMContentLoaded", function () {
    const selectLibro = document.getElementById("selectLibroDes");
    const selectFoja = document.getElementById("selectFojaDes");
    const btnDesasignar = document.getElementById("btnDesasignarFoja");

    // Cargar fojas al seleccionar un libro
    selectLibro.addEventListener("change", function () {
        let libroId = this.value;

        if (libroId) {
            fetch("../php/obtenerDesasignacionFoja.php?libroId=" + libroId)
                .then(response => response.json())
                .then(data => {
                    selectFoja.innerHTML = '<option value="">-- Elija la foja --</option>';
                    data.forEach(foja => {
                        let option = document.createElement("option");
                        option.value = foja.Fk_Formato_Foja_Asignado_Egresado;
                        option.textContent = foja.Nombre_Formato_Foja + " - " + foja.Num_Control;
                        selectFoja.appendChild(option);
                    });

                    console.log("Opciones agregadas:", selectFoja.innerHTML);
                })
                .catch(error => console.error("Error cargando fojas:", error));
        } else {
            selectFoja.innerHTML = '<option value="">-- Elija la foja --</option>';
        }
    });

    // Desasignar foja
    btnDesasignar.addEventListener("click", function () {
        const idLibro = selectLibro.value;
        const numeroFoja = selectFoja.value;

        if (!idLibro) {
            alert("Debe seleccionar un libro.");
            return;
        }
        if (!numeroFoja) {
            alert("Debe seleccionar una foja.");
            return;
        }

        const payload = {
            idLibro: parseInt(idLibro, 10),
            numeroFoja: parseInt(numeroFoja, 10)
        };

        fetch("../php/desasignarFoja.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json;charset=UTF-8"
            },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                console.log("Respuesta del servidor:", data);
                if (data.success) {
                    alert(data.message);
                    document.getElementById("formDesasignarFoja").reset();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => {
                console.error("Fetch error:", err);
                alert("Ocurrió un error al comunicarse con el servidor.");
            });
    });
});