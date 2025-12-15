// Creamos un arreglo para almacenar los libros y titulaciones disponibles de manera global JH20250701
// Esto nos permitirá acceder a ellos en diferentes partes del código sin necesidad de volver a hacer solicitudes
var librosDisponibles = [];
var titulacionesDisponibles = [];

$(document).ready(function () {
    // Variables para controlar la paginación
    var pageSize = 7;
    var currentPage = 1;
    var maxVisibleButtons = 5; // Número máximo de botones visibles

    // Realiza una solicitud AJAX para obtener los datos
    $.ajax({
        url: '../php/tablaLibroTipoTitulacion.php',
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            var tbody = $("#Libro-Documentos tbody");
            var pagination = $("#pagination");

            // Vacía el tbody y la paginación por si acaso
            tbody.empty();
            pagination.empty();

            // Calcula el número total de páginas
            var totalPages = Math.ceil(data.length / pageSize);

            // Muestra los registros de la página actual
            displayData(data, tbody, pageSize, currentPage);

            // Calcula los índices para mostrar solo 5 botones
            var startButton = Math.max(1, currentPage - Math.floor(maxVisibleButtons / 2));
            var endButton = Math.min(totalPages, startButton + maxVisibleButtons - 1);

            // Agrega botones de paginación
            for (var i = startButton; i <= endButton; i++) {
                var li = $("<li class='page-item'><a class='page-link' href='#'>" + i + "</a></li>");
                li.on('click', { page: i }, function (event) {
                    currentPage = event.data.page;
                    displayData(data, tbody, pageSize, currentPage);
                    updatePagination(data, pagination, pageSize, currentPage, maxVisibleButtons);
                });
                pagination.append(li);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Manejo de errores
            console.log('Error:', textStatus, errorThrown);
        }
    });
});

// Función para mostrar los datos de una página específica
function displayData(data, tbody, pageSize, currentPage) {
    var startIndex = (currentPage - 1) * pageSize;
    var endIndex = startIndex + pageSize;
    var pageData = data.slice(startIndex, endIndex);

    tbody.empty();

    $.each(pageData, function (index, row) {
        var newRow = $("<tr>");
        newRow.append($("<td>").text(row.Descripcion_Libro));
        newRow.append($("<td>").text(row.Tipo_Producto_Titulacion));
        tbody.append(newRow);
    });
}

// Función para actualizar la paginación
function updatePagination(data, pagination, pageSize, currentPage, maxVisibleButtons) {
    pagination.empty();
    var totalPages = Math.ceil(data.length / pageSize);

    // Calcula los índices para mostrar solo 5 botones
    var startButton = Math.max(1, currentPage - Math.floor(maxVisibleButtons / 2));
    var endButton = Math.min(totalPages, startButton + maxVisibleButtons - 1);

    // Agrega botones de paginación
    for (var i = startButton; i <= endButton; i++) {
        var li = $("<li class='page-item'><a class='page-link' href='#'>" + i + "</a></li>");
        li.on('click', { page: i }, function (event) {
            currentPage = event.data.page;
            displayData(data, $("#Libro-Documentos tbody"), pageSize, currentPage);
            updatePagination(data, pagination, pageSize, currentPage, maxVisibleButtons);
        });
        pagination.append(li);
    }
}

//Combobox Libro
$(document).ready(function () {
    // Realiza una solicitud AJAX para obtener los datos
    $.ajax({
        url: '../php/obtenerLibro.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            librosDisponibles = data; // Almacena los datos de libros disponibles JH20250701
            var select = $("#libroSelect");

            // Agrega la opción "Selecciona una" al principio
            select.append($("<option>").text("[SELECCIONAR LIBRO]").attr("value", ""));

            // Llena el combo box con los datos obtenidos
            $.each(data, function (index, row) {
                var option = $("<option>").attr("value", row.idL).text(row.nombreL);
                select.append(option);
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error:', textStatus, errorThrown);
        }
    });
});

//Combobox Tipo Titulacion
$(document).ready(function () {
    // Realiza una solicitud AJAX para obtener los datos
    $.ajax({
        url: '../php/obtenerTipoTitulacionValidacionLibro.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            titulacionesDisponibles = data; // Almacena los datos de titulaciones disponibles JH20250701
            var select = $("#titulacionSelect");

            // Agrega la opción "Selecciona una" al principio
            select.append($("<option>").text("[SELECCIONAR TIPO DE TITULACION]").attr("value", ""));

            // Llena el combo box con los datos obtenidos
            $.each(data, function (index, row) {
                var option = $("<option>").attr("value", row.idL).text(row.nombreL);
                select.append(option);
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error:', textStatus, errorThrown);
        }
    });
});

// Función para convertir los valores a enteros
function convertirAEnteros(resultados) {
    return resultados.map(function(resultado) {
        // Asegurarse de que titulaciones sea un array
        var titulaciones = Array.isArray(resultado.titulaciones) ? resultado.titulaciones : [resultado.titulaciones];

        return {
            libro: parseInt(resultado.libro),
            titulaciones: titulaciones.map(function(titulacion) {
                return parseInt(titulacion);
            })
        };
    });
}


// Función para guardar los datos en la base de datos
function guardarEnBaseDeDatos() {
    // Realizar una solicitud AJAX para guardar los datos
    $.ajax({
        url: '../php/guardarLibroDocumentos.php',
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(resultados), // Asumiendo que 'resultados' es tu arreglo de datos
        success: function (data) {
            if (data && data.success) {
                // Éxito al guardar los datos, puedes realizar alguna acción adicional si es necesario
                console.log('Success');
                
                // Después de realizar la operación exitosa
                $("#mensaje").show(); // Muestra el mensaje
                
                // Oculta el mensaje después de 5000 milisegundos (5 segundos)
                setTimeout(function () {
                    $("#mensaje").hide();
                }, 5000);
                
                // Después de realizar la operación exitosa
                $("#mensaje").show(); // Muestra el mensaje
                
                // Oculta el mensaje después de 5000 milisegundos (5 segundos)
                setTimeout(function () {
                    $("#mensaje").hide();
                }, 5000);
            } else {
                // Error al guardar los datos
                console.error('Error al guardar los datos en la tabla libro_documentos:', data);
                alert("Error al guardar los datos. Consulta la consola para más detalles.");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Manejar el error si es necesario
            console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            alert("Error en la solicitud AJAX. Consulta la consola para más detalles.");
        }
    });
}

// Arreglo para almacenar los resultados
var resultados = [];

//Lista Opciones
$(document).ready(function () {

    // Manejar el clic en el botón "Agregar Tipo a Libro"
    $("#AgregarTipoALibro").on("click", function () {
        // Obtener el valor seleccionado en #libroSelect
        var libroSeleccionado = $("#libroSelect").val();

        // Verificar si se ha seleccionado un libro
        if (libroSeleccionado) {
            // Obtener todas las opciones seleccionadas en #titulacionSelect
            var titulacionesSeleccionadas = $("#titulacionSelect").val();

            // Verificar si se han seleccionado titulaciones
            if (titulacionesSeleccionadas && titulacionesSeleccionadas.length > 0) {
                // Realizar la validación en el arreglo para evitar duplicados
                var existeDuplicado = resultados.some(function (resultado) {
                    return (
                        resultado.libro === libroSeleccionado &&
                        compararArrays(resultado.titulaciones, titulacionesSeleccionadas)
                    );
                });

                if (!existeDuplicado) {
                    // Agregar el resultado al arreglo
                    var resultado = {
                        libro: libroSeleccionado,
                        titulaciones: titulacionesSeleccionadas
                    };

                    // Agregar el resultado al arreglo
                    resultados.push(resultado);

                    // Limpiar la tabla y volver a llenarla con los resultados actuales
                    actualizarTabla();
                } else {
                    // Mostrar mensaje de que ya existe el registro
                    alert("Ya existe un registro en la tabla con esas características.");
                }
            } else {
                alert("Selecciona al menos una titulación.");
            }
        } else {
            alert("Selecciona un libro antes de agregar.");
        }
    });

    // Manejar el clic en el botón "Regresar Tipo a Libro"
    $("#RegresarTipoALibro").on("click", function () {
        // Verificar si hay elementos en el arreglo antes de eliminar
        if (resultados.length > 0) {
            // Eliminar el último resultado del arreglo
            resultados.pop();

            // Actualizar la tabla con los resultados actuales
            actualizarTabla();
        } else {
            alert("No hay resultados para eliminar.");
        }
    });

    // Manejar el clic en el botón "Guardar"
    $("#AgregarTipoALibroF").on("click", function () {
        // Llamar a la función para convertir los valores a enteros
        resultados = convertirAEnteros(resultados);

        // Llamar a la función para guardar los datos en la tabla libro_documentos
        guardarEnBaseDeDatos();
    });

    // Función para actualizar la tabla con los resultados actuales
    function actualizarTabla() {
        // Limpiar el cuerpo de la tabla
        $("#resultadoTabla tbody").empty();

        // Llenar la tabla con los resultados actuales
        $.each(resultados, function (index, resultado) {
            var fila = $("<tr>");
            
            // fila.append($("<td>").text(resultado.libro));
            
            // Obtener nombre del libro usando su ID y asignar data-id JH20250701
            var libro = librosDisponibles.find(l => l.idL == resultado.libro);
            var nombreLibro = libro ? libro.nombreL : resultado.libro;
            fila.append($("<td>").text(nombreLibro).attr("data-id", resultado.libro));

            // Obtener nombres de titulaciones
            var titulos = resultado.titulaciones;
            
            // --- Versión anterior ---
            // Verificar si resultado.titulaciones es un array antes de usar join
            // if (Array.isArray(resultado.titulaciones)) {
            //     fila.append($("<td>").text(resultado.titulaciones.join(', ')));
            // } else {
                 // Si no es un array, mostrar el valor directamente
            //     fila.append($("<td>").text(resultado.titulaciones));
            // }
            
            // Mostrar los nombres legibles y guardar el primer ID como data-id JH20250701
            if (Array.isArray(titulos)) {
                var nombresTitulaciones = titulos.map(id => {
                    var t = titulacionesDisponibles.find(tt => tt.idL == id);
                    return t ? t.nombreL : id;
                });
                fila.append($("<td>").text(nombresTitulaciones.join(', ')).attr("data-id", titulos[0]));
            } else {
                var t = titulacionesDisponibles.find(tt => tt.idL == titulos);
                fila.append($("<td>").text(t ? t.nombreL : titulos).attr("data-id", titulos));
            }

            $("#resultadoTabla tbody").append(fila);
        });
    }

    // Función para comparar dos arrays, maneja casos donde uno o ambos no son arrays
    function compararArrays(arr1, arr2) {
        if (Array.isArray(arr1) && Array.isArray(arr2)) {
            // Si ambos son arrays, compararlos
            return JSON.stringify(arr1.sort()) === JSON.stringify(arr2.sort());
        } else {
            // Si al menos uno no es un array, comparar los valores directamente
            return arr1 === arr2;
        }
    }
});

$(document).ready(function () {
    // Evento para obtener datos al hacer clic en una celda en Libro Seleccionado
    $('#resultadoTabla tbody').on('click', 'td:first-child', function () {
        // var idLibro = $(this).text();
        var idLibro = $(this).data('id'); // Obtener el id del libro desde el atributo data-id JH20250701
        obtenerInformacionLibro(idLibro);
    });

    // Evento para obtener datos al hacer clic en una celda en Titulación a Asignar
    $('#resultadoTabla tbody').on('click', 'td:nth-child(2)', function () {
        // var idTitulacion = $(this).text();
        var idTitulacion = $(this).data('id'); // Obtener el id de la titulación desde el atributo data-id JH20250701
        obtenerInformacionTitulacion(idTitulacion);
    });
});

function obtenerInformacionLibro(idLibro) {
    $.ajax({
        url: '../php/script_para_obtener_libro.php',
        type: 'POST',
        data: { idLibro: idLibro },
        dataType: 'json',
        success: function (data) {
            mostrarModal(data.nombreL);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error en la solicitud AJAX para Libro Seleccionado:', textStatus, errorThrown);
        }
    });
}

function obtenerInformacionTitulacion(idTitulacion) {
    $.ajax({
        url: '../php/script_para_obtener_titulacion.php',
        type: 'POST',
        data: { idTitulacion: idTitulacion },
        dataType: 'json',
        success: function (data) {
            mostrarModal(data.nombreL);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error en la solicitud AJAX para Titulación a Asignar:', textStatus, errorThrown);
        }
    });
}

function mostrarModal(informacion) {
    // Limpia el contenido anterior del modal
    $('#informacionModalBody').empty();

    // Agrega la nueva información al cuerpo del modal
    $('#informacionModalBody').append('<p>' + informacion + '</p>');

    // Cierra cualquier modal abierto y abre el nuevo modal
    $('#informacionModal').modal('hide').modal('show');

    // Agrega un evento para cerrar el modal cuando se oculta
    $('#informacionModal').on('hidden.bs.modal', function () {
        // Código a ejecutar cuando el modal se cierra
        console.log('Modal cerrado');
    });
}
