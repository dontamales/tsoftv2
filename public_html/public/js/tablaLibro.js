$(document).ready(function() {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: '../php/tablaLibro.php', // Asegúrate de que esta ruta sea la correcta
    type: 'POST',
    dataType: 'json',
    success: function(data) {
      var tbody = $("#libro-table tbody");
      
      // Vacía el tbody por si acaso
      tbody.empty();
      
      // Llena la tabla con los datos obtenidos
      $.each(data, function(index, row) {
        var newRow = $("<tr>");
        var checkbox = $("<input>").attr("type", "checkbox").addClass("selectUser");
        newRow.append($("<td>").append(checkbox));
        newRow.append($("<td>").text(row.Id_Libro));
        newRow.append($("<td>").text(row.Descripcion_Libro));
        tbody.append(newRow);
      });
    },
    error: function(jqXHR, textStatus, errorThrown) {
      //console.log('Error:', textStatus, errorThrown);
    }
  });
});

$("#buscarLibroBtn").click(function() {
  var searchTerm = $("#searchLibro").val().toLowerCase();
  $("#libro-table tbody tr").each(function() {
      var lineStr = $(this).text().toLowerCase();
      if (lineStr.indexOf(searchTerm) === -1) {
          $(this).hide();
      } else {
          $(this).show();
      }
  });
});

$("#borrarBtnLibro").click(function() {
  // Recolectar los IDs de los usuarios seleccionados
  var selectedIds = [];
  $(".selectUser:checked").each(function() {
      var row = $(this).closest('tr');
      var id = row.find('td:nth-child(2)').text(); // Asume que el ID es la segunda columna
      selectedIds.push(id);
  });

  // Si no se selecciona ningún usuario, simplemente regresa
  if (selectedIds.length === 0) {
      alert("Por favor, selecciona al menos un libro para borrar.");
      return;
  }

  // Confirmar acción
  if (!confirm("¿Estás seguro de que quieres borrar los libros seleccionados?")) {
      return;
  }

  // Realizar la solicitud AJAX para borrar los usuarios
  $.ajax({
      url: '../php/borrarLibro.php',
      type: 'POST',
      data: {
          ids: selectedIds
      },
      dataType: 'json',
      success: function(response) {
          if (response.success) {
              alert(response.message);
              // Actualizar la tabla o recargar la página
              window.location.reload();
          } else {
              alert(response.message);
          }
      },
      error: function() {
          alert("Error al realizar la solicitud. Inténtalo de nuevo.");
      }
  });
});