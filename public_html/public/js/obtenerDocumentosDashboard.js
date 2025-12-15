document.addEventListener("DOMContentLoaded", function () {
    fetch("../php/obtenerDocumentosDashboard.php")
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#tablaDocumentosEgresado tbody");
            tbody.innerHTML = "";

            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center">No tienes documentos registrados aún.</td></tr>`;
                return;
            }

            data.forEach(doc => {
                const tr = document.createElement("tr");

                const tdDoc = document.createElement("td");
                tdDoc.textContent = doc.Descripcion_Documentos_Pendientes;
                tr.appendChild(tdDoc);

                const tdEstado = document.createElement("td");
                if (doc.Aceptado_Egresado_Documentos == 1) {
                    tdEstado.textContent = "Aprobado";
                    tdEstado.classList.add("text-success");
                } else if (doc.Fecha_Documento_Subido_Egresado_Documentos) {
                    tdEstado.textContent = "Pendiente de revisión";
                    tdEstado.classList.add("text-warning");
                } else {
                    tdEstado.textContent = "No enviado";
                    tdEstado.classList.add("text-danger");
                }
                tr.appendChild(tdEstado);

                const tdFecha = document.createElement("td");
                tdFecha.textContent = doc.Fecha_Documento_Subido_Egresado_Documentos || "—";
                tr.appendChild(tdFecha);

                tbody.appendChild(tr);
            });
        })
        .catch(err => console.error("Error cargando documentos:", err));
});