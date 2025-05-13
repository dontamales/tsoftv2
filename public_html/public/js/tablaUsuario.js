$(document).ready(function() {
  // Realiza una solicitud AJAX para obtener los datos
  $.ajax({
    url: '../php/tablaUsuario.php', // Asegúrate de que esta ruta sea la correcta
    type: 'POST',
    dataType: 'json',
    success: function(data) {
      var tbody = $("#tabla-usuario-lista tbody");
      
      // Vacía el tbody por si acaso
      tbody.empty();
      
      // Llena la tabla con los datos obtenidos
      $.each(data, function(index, row) {
        var newRow = $("<tr>");
        var checkbox = $("<input>").attr("type", "checkbox").addClass("selectUser");
        if (row.FK_Estatus_Egresado <= 3) {
          checkbox.attr("disabled", false);
        } else {
          checkbox.attr("disabled", true);
        }
        newRow.append($("<td>").append(checkbox));
        newRow.append($("<td>").text(row.Id_Usuario));
        switch (row.Fk_Roles_Usuario) {
          case 1:
            rol = "Sustentante";
            break;
          case 2:
            rol = "Administrador";
            break;
          case 3:
            rol = "Super administrador";
            break;
          case 4:
            rol = "Secretario";
            break;
          case 5:
            rol = "Secretario auxiliar";
            break;
          case 6:
            rol = "Servicios escolares";
            break;
        
          default:
            break;
        }
        newRow.append($("<td>").text(rol));
        newRow.append($("<td>").text(row.Nombres_Usuario));
        newRow.append($("<td>").text(row.Apellidos_Usuario));
        newRow.append($("<td>").text(row.Correo_Usuario));
        newRow.append($("<td>").text(row.Fecha_Usuario));
        
        tbody.append(newRow);
      });
    },
    error: function(jqXHR, textStatus, errorThrown) {
      //console.log('Error:', textStatus, errorThrown);
    }
  });
});

$("#buscarBtn").click(function() {
  var searchTerm = $("#searchUsuario").val().toLowerCase();
  $("#tabla-usuario-lista tbody tr").each(function() {
      var lineStr = $(this).text().toLowerCase();
      if (lineStr.indexOf(searchTerm) === -1) {
          $(this).hide();
      } else {
          $(this).show();
      }
  });
});

$("#borrarBtnUsuarios").click(function() {
  // Recolectar los IDs de los usuarios seleccionados
  var selectedIds = [];
  $(".selectUser:checked").each(function() {
      var row = $(this).closest('tr');
      var id = row.find('td:nth-child(2)').text(); // Asume que el ID es la segunda columna
      selectedIds.push(id);
  });

  // Si no se selecciona ningún usuario, simplemente regresa
  if (selectedIds.length === 0) {
      alert("Por favor, selecciona al menos un usuario para borrar.");
      return;
  }

  // Confirmar acción
  if (!confirm("¿Estás seguro de que quieres borrar los usuarios seleccionados?")) {
      return;
  }

  // Realizar la solicitud AJAX para borrar los usuarios
  $.ajax({
      url: '../php/borrarUsuario.php',
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
