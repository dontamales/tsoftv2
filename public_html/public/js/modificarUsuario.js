// Almacenar los usuarios en un array
var usuarios = [];

// Obtener los datos del usuario dinámicamente
function obtenerUsuarios() {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "../php/obtenerUsuarios.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      usuarios = JSON.parse(xhr.responseText);
    }
  };
  xhr.send();
}

// Llamar a la función para obtener los datos de los egresados al cargar la página
window.addEventListener("load", obtenerUsuarios);

// Escuchar cambios en el input
var input = document.getElementById("inputModificarUsuario");
var hiddenInput = document.getElementById("selectedModificarUsuarioId"); // Campo oculto

input.addEventListener("input", function() {
  var query = this.value.toLowerCase();
  var listContainer = document.getElementById("listContainer");
  listContainer.innerHTML = "";

  // Añade esta comprobación para evitar mostrar la lista completa cuando no hay entrada.
  if (query === "") {
    return;
  }

  usuarios.forEach(function(usuario) {
    if (usuario.nombre.toLowerCase().includes(query) || usuario.rol.toLowerCase().includes(query)) {
      var div = document.createElement("div");
      div.className = "list-group-item";
      div.textContent = usuario.rol + " - " + usuario.nombre;
      div.onclick = function() {
        input.value = usuario.rol + " - " + usuario.nombre;
        hiddenInput.value = usuario.id; // Guardamos el ID en el campo oculto
        listContainer.innerHTML = "";
      };
      listContainer.appendChild(div);
    }
  });
});

function capitalizeName(name) {
  return name
    .toLowerCase()
    .split(" ")
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(" ");
}

function sanitizePhoneNumber(phoneNumber) {
  // Quita todos los caracteres no numéricos.
  return phoneNumber.replace(/\D/g, "");
}

function validateForm(fk_roles, nombres, apellidos, correo) {
  if (!fk_roles || !nombres || !apellidos || !correo) {
    alert("Todos los campos son obligatorios.");
    return false;
  }

  const namePattern = /^[a-zA-ZÀ-ÿ\s]+$/; // Expresión regular para verificar que solo contiene letras y espacios
  if (!namePattern.test(nombres)) {
    alert("El nombre no debe contener números ni caracteres especiales.");
    return false;
  }
  if (!namePattern.test(apellidos)) {
    alert("El apellido no debe contener números ni caracteres especiales.");
    return false;
  }

  nombres = capitalizeName(nombres.trim());
  apellidos = capitalizeName(apellidos.trim());
  correo = correo.trim().toLowerCase();

  const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/i;
  if (!emailPattern.test(correo)) {
    alert("Por favor, introduce un correo electrónico válido.");
    return false;
  }

  if (!checkEmailDomain(correo)) {
    alert("Revisa si el dominio de correo electrónico es correcto.");
    return false;
  }

  return true;
}

function clearForm() {
  document.getElementById("modificarFk_roles").value = "";
  document.getElementById("modificarUsuarioNombres").value = "";
  document.getElementById("modificarUsuarioApellidos").value = "";
  document.getElementById("modificarUsuarioCorreo").value = "";
}

function checkEmailDomain(email) {
  const domains = [
    "gmail.com",
    "gmail.com.mx",
    "hotmail.com",
    "hotmail.es",
    "outlook.com",
    "outlook.es",
    "live.com",
    "live.com.mx",
    "msn.com",
    "yahoo.com",
    "yahoo.com.mx",
    "cdjuarez.tecnm.mx",
    "itcj.edu.mx",
  ];

  const domain = email.split("@")[1];

  for (let i = 0; i < domains.length; i++) {
    if (domain === domains[i]) {
      return true;
    }
    // Si la distancia de Levenshtein es 1, asumimos un error ortográfico.
    if (levenshteinDistance(domain, domains[i]) <= 1) {
      //VERIFICAR LA DISTANCIA DE LEVENSHTEIN
      return false;
    }
  }
  return true;
}

function levenshteinDistance(a, b) {
  const matrix = [];

  let i;
  for (i = 0; i <= b.length; i++) {
    matrix[i] = [i];
  }

  let j;
  for (j = 0; j <= a.length; j++) {
    matrix[0][j] = j;
  }

  for (i = 1; i <= b.length; i++) {
    for (j = 1; j <= a.length; j++) {
      if (b.charAt(i - 1) === a.charAt(j - 1)) {
        matrix[i][j] = matrix[i - 1][j - 1];
      } else {
        matrix[i][j] = Math.min(
          matrix[i - 1][j - 1] + 1,
          Math.min(matrix[i][j - 1] + 1, matrix[i - 1][j] + 1)
        );
      }
    }
  }

  return matrix[b.length][a.length];
}

document
  .getElementById("modificar-form_usuario")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const fk_roles = document.getElementById("modificarFk_roles").value;
    const nombres = capitalizeName(
      document.getElementById("modificarUsuarioNombres").value.trim()
    );
    const apellidos = capitalizeName(
      document.getElementById("modificarUsuarioApellidos").value.trim()
    );
    const correo = document
      .getElementById("modificarUsuarioCorreo")
      .value.trim()
      .toLowerCase();

    if (!validateForm(fk_roles, nombres, apellidos, correo)) {
      return;
    }

    const idUsuario = document.getElementById("selectedModificarUsuarioId").value;

    fetch("../php/modificarUsuario.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        idUsuario: idUsuario,
        fk_roles: fk_roles,
        nombres: nombres,
        apellidos: apellidos,
        correo: correo,
      }),
    })
      .then(function (response) {
        if (response.ok) {
          return response.text().then(function (text) {
            try {
              return JSON.parse(text);
            } catch (error) {
              throw new Error("Error al analizar JSON.");
            }
          });
        } else {
          throw new Error("Error en la petición.");
        }
      })
    .then(async function (data) {
        if (data.message.includes("Error")) {
          throw new Error("Error del servidor.");
        }
        alert(data.message);

        window.location.reload();
      })
    .catch(function (error) {
        alert("Ocurrio un error al registrar el usuario, puede ser que el correo ya exista en la base de datos");
      });
});
