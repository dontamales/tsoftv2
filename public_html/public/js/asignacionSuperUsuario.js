document.addEventListener('DOMContentLoaded', () => {
    fetch('../php/obtenerSuperAdmin.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('asignarSuperUsuario');
            data.forEach(usuario => {
                const option = document.createElement('option');
                option.value = usuario.id;
                option.textContent = usuario.nombre;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error:', error));
});
