document.addEventListener('DOMContentLoaded', function() {
    // Cargar estadísticas
    cargarEstadisticas();
    
    // Cargar tablas si existen
    if (document.getElementById('tablaDocumentosPendientes')) {
        cargarDocumentosPorCarrera();
    }
    
    if (document.getElementById('tablaSinodales')) {
        cargarSinodalesPorCarrera();
    }
    
    if (document.getElementById('calendarioEventos')) {
        cargarProximasCeremonias();
    }
    
    if (document.getElementById('listaTareasPendientes')) {
        cargarTareasPendientes();
    }
});

// === CARGAR ESTADÍSTICAS ===
async function cargarEstadisticas() {
    try {
        const response = await fetch('../php/dashboard/obtenerEstadisticasDashboard.php');
        const data = await response.json();
        
        // Trámites pendientes
        if (document.getElementById('tramitesPendientes')) {
            document.getElementById('tramitesPendientes').textContent = data.tramitesPendientes || 0;
            const tendenciaEl = document.getElementById('tramitesPendientes').parentElement.querySelector('.stats-trend');
            if (tendenciaEl) tendenciaEl.textContent = data.tramitesPendientesTendencia || '';
        }
        
        // Documentos pendientes
        if (document.getElementById('documentosPendientes')) {
            document.getElementById('documentosPendientes').textContent = data.documentosPendientes || 0;
            const tendenciaEl = document.getElementById('documentosPendientes').parentElement.querySelector('.stats-trend');
            if (tendenciaEl) tendenciaEl.textContent = data.documentosHoy || 'Sin documentos hoy';
        }
        
        // Ceremonias programadas
        if (document.getElementById('ceremoniasProgramadas')) {
            document.getElementById('ceremoniasProgramadas').textContent = data.ceremoniasProgramadas || 0;
            const tendenciaEl = document.getElementById('ceremoniasProgramadas').parentElement.querySelector('.stats-trend');
            if (tendenciaEl) tendenciaEl.textContent = data.proximaCeremonia || 'Sin ceremonias';
        }
        
        // Solo para Super Admin (rol 3)
        if (document.getElementById('tituladosSemestre')) {
            document.getElementById('tituladosSemestre').textContent = data.tituladosSemestre || 0;
            const tendenciaEl = document.getElementById('tituladosSemestre').parentElement.querySelector('.stats-trend');
            if (tendenciaEl) tendenciaEl.textContent = data.tituladosSemestre !== undefined ? `${data.tituladosSemestre} titulados` : 'Sin datos';
        }
        
        if (document.getElementById('eficienciaTerminal')) {
            document.getElementById('eficienciaTerminal').textContent = data.eficienciaTerminal || 'N/A';
            const tendenciaEl = document.getElementById('eficienciaTerminal').parentElement.querySelector('.stats-trend');
            if (tendenciaEl) tendenciaEl.textContent = data.eficienciaTerminal && data.eficienciaTerminal !== 'N/A' ? 'Indicador actual' : 'Sin datos';
        }
        
    } catch (error) {
        console.error('Error cargando estadísticas:', error);
    }
}

// === CARGAR DOCUMENTOS POR CARRERA ===
async function cargarDocumentosPorCarrera() {
    try {
        const response = await fetch('../php/dashboard/obtenerDocumentosPorCarrera.php');
        const data = await response.json();
        
        const tbody = document.querySelector('#tablaDocumentosPendientes tbody');
        tbody.innerHTML = '';
        
        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">No hay documentos pendientes</td></tr>';
            return;
        }
        
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.carrera}</td>
                <td>${item.pendientes}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="window.location.href='gestionDocumentos.php'">
                        Ver
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
    } catch (error) {
        console.error('Error cargando documentos por carrera:', error);
    }
}

// === CARGAR SINODALES POR CARRERA ===
async function cargarSinodalesPorCarrera() {
    try {
        const response = await fetch('../php/dashboard/obtenerSinodalesPorCarrera.php');
        const data = await response.json();
        
        const tbody = document.querySelector('#tablaSinodales tbody');
        tbody.innerHTML = '';
        
        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">No hay sinodales pendientes</td></tr>';
            return;
        }
        
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.carrera}</td>
                <td>
                    <span class="badge ${item.badge_class}">
                        ${item.asignados}/${item.total}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="window.location.href='gestionSinodal.php'">
                        Asignar
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
    } catch (error) {
        console.error('Error cargando sinodales por carrera:', error);
    }
}

// === CARGAR PRÓXIMAS CEREMONIAS ===
async function cargarProximasCeremonias() {
    try {
        const response = await fetch('../php/dashboard/obtenerProximasCeremonias.php');
        const data = await response.json();
        
        const container = document.getElementById('calendarioEventos');
        container.innerHTML = '';
        
        if (!data.length) {
            container.innerHTML = '<p class="text-center text-muted py-4">No hay ceremonias programadas</p>';
            return;
        }
        
        data.forEach(evento => {
            const eventDiv = document.createElement('div');
            eventDiv.className = 'event-item';
            eventDiv.innerHTML = `
                <div class="event-date">
                    <div class="event-day">${evento.dia}</div>
                    <div class="event-month">${evento.mes}</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Ceremonia de Titulación</div>
                    <div class="event-info">${evento.fecha_completa}</div>
                    <div class="event-description">
                        <span class="event-carrera">${evento.carreras}</span>
                        <span class="event-count">${evento.total_egresados} egresados</span>
                    </div>
                </div>
            `;
            container.appendChild(eventDiv);
        });
        
    } catch (error) {
        console.error('Error cargando próximas ceremonias:', error);
    }
}

// === CARGAR TAREAS PENDIENTES ===
async function cargarTareasPendientes() {
    try {
        const response = await fetch('../php/dashboard/obtenerTareasPendientes.php');
        const data = await response.json();
        
        const container = document.getElementById('listaTareasPendientes');
        const badge = document.getElementById('tareasPendientesBadge');
        
        container.innerHTML = '';
        badge.textContent = data.length;
        
        if (!data.length) {
            container.innerHTML = '<p class="text-center text-muted py-4">No hay tareas pendientes</p>';
            return;
        }
        
        data.forEach(tarea => {
            const taskDiv = document.createElement('div');
            taskDiv.className = 'task-item';
            taskDiv.innerHTML = `
                <div class="task-info">
                    <span class="task-badge ${tarea.badge}">${tarea.badge_text}</span>
                    <span>${tarea.descripcion}</span>
                </div>
                <button class="task-action" onclick="window.location.href='${tarea.url}'">
                    Ver
                </button>
            `;
            container.appendChild(taskDiv);
        });
        
    } catch (error) {
        console.error('Error cargando tareas pendientes:', error);
    }
}