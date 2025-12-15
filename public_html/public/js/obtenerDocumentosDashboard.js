document.addEventListener("DOMContentLoaded", function () {
    fetch("../php/obtenerDocumentosDashboard.php")
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#tablaDocumentosEgresado tbody");
            tbody.innerHTML = "";

            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-4">No tienes documentos registrados aún.</td></tr>`;
                return;
            }

            data.forEach(doc => {
                const tr = document.createElement("tr");

                // Columna: Nombre del documento
                const tdDoc = document.createElement("td");
                tdDoc.textContent = doc.Descripcion_Documentos_Pendientes;
                tr.appendChild(tdDoc);

                // Columna: Estado con badge
                const tdEstado = document.createElement("td");
                const badge = document.createElement("span");
                badge.className = "task-badge";

                if (doc.Aceptado_Egresado_Documentos == 1) {
                    badge.classList.add("completed");
                    badge.textContent = "Aprobado";
                } else if (doc.Fecha_Documento_Subido_Egresado_Documentos) {
                    badge.classList.add("pending");
                    badge.textContent = "Pendiente de revisión";
                } else {
                    badge.classList.add("urgent");
                    badge.textContent = "No enviado";
                }

                tdEstado.appendChild(badge);
                tr.appendChild(tdEstado);

                // Columna: Fecha
                const tdFecha = document.createElement("td");
                tdFecha.textContent = doc.Fecha_Documento_Subido_Egresado_Documentos || "—";
                tr.appendChild(tdFecha);

                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            console.error("Error cargando documentos:", err);
            const tbody = document.querySelector("#tablaDocumentosEgresado tbody");
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger py-4">Error al cargar los documentos. Intente nuevamente.</td></tr>`;
        });
});