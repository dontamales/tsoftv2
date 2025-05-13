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

  const promedio = parseFloat(document.getElementById("promedio").value);
  const numero_control = document.getElementById("numero_control").value.trim();
  const numeroControlPattern = /^[0-9]{8}$/;
  const numeroControlPattern2 = /^[a-zA-Z][0-9]{8}$/;
  const regularDouble = /^\d+(\.\d{1,2})?$/;

  if (fk_roles === "1") {
    if (
      !fk_roles ||
      !nombres ||
      !apellidos ||
      !correo ||
      !numero_control ||
      !carrera ||
      !promedio
    ) {
      alert("Todos los campos son obligatorios.");
      return false;
    }

    if (
      !numeroControlPattern.test(numero_control) &&
      !numeroControlPattern2.test(numero_control)
    ) {
      alert(
        "El número de control debe ser de 8 números o una letra seguida de 8 números."
      );
      return false;
    }

    if (
      !regularDouble.test(promedio) ||
      isNaN(promedio) ||
      promedio < 0 ||
      promedio > 100
    ) {
      alert("El promedio debe ser un número entre 0 y 100.");
      return false;
    }
  }

  return true;
}

function obtenerNivel(numeroControl) {
  const letra = numeroControl.charAt(0);
  switch (letra.toUpperCase()) {
    case "D":
      return ["Doctorado"];
    case "G":
      return ["Doctorado", "Maestria"];
    case "M":
      return ["Maestria"];
    case "A":
    case "T":
    case "L":
    default:
      return ["Licenciatura"];
  }
}

async function cargarCarreras(idInputNumeroControl, idInputCarrera) {
  try {
    const numeroControl = document.getElementById(idInputNumeroControl).value;
    const nivel = obtenerNivel(numeroControl);
    const respuesta = await fetch(
      `../../php/obtenerCarreras.php?nivel[]=${nivel.join("&nivel[]=")}`
    );

    const carreras = await respuesta.json();
    const carreraSelect = document.getElementById(idInputCarrera);
    const carreraSeleccionada = carreraSelect.dataset.selected;

    // Limpiar las opciones existentes
    carreraSelect.innerHTML = "";

    for (const carrera of carreras) {
      const option = document.createElement("option");
      option.value = carrera.id;
      option.textContent = carrera.nombre;

      if (carrera.id == carreraSeleccionada) {
        option.selected = true;
      }

      carreraSelect.appendChild(option);
    }
  } catch (error) {
    //console.error("Error al cargar las carreras:", error);
  }
}

cargarCarreras("numero_control", "carrera");

const numeroControlInput = document.getElementById("numero_control");

numeroControlInput.addEventListener(
  "input",
  debounce(() => cargarCarreras("numero_control", "carrera"), 300)
);

function debounce(func, delay) {
  let debounceTimer;
  return function () {
    const context = this;
    const args = arguments;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => func.apply(context, args), delay);
  };
}

function clearForm() {
  document.getElementById("fk_roles").value = "";
  document.getElementById("nombres").value = "";
  document.getElementById("apellidos").value = "";
  document.getElementById("correo").value = "";
  document.getElementById("numero_control").value = "";
  document.getElementById("carrera").value = "";
  document.getElementById("promedio").value = "";
  document.getElementById("telefonoRegistro").value = "";
  toggleSustentanteFields();
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
  .getElementById("register-form_usuario")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const fk_roles = document.getElementById("fk_roles").value;
    const nombres = capitalizeName(
      document.getElementById("nombres").value.trim()
    );
    const apellidos = capitalizeName(
      document.getElementById("apellidos").value.trim()
    );
    const correo = document.getElementById("correo").value.trim().toLowerCase();
    const numero_control = document
      .getElementById("numero_control")
      .value.trim();
    const carrera = document.getElementById("carrera").value;
    const promedio = document.getElementById("promedio").value;
    let telefono = document.getElementById("telefonoRegistro").value.trim();
    telefono = sanitizePhoneNumber(telefono);

    if (!validateForm(fk_roles, nombres, apellidos, correo)) {
      return;
    }

    if (fk_roles == "1") {
      if (!numero_control || !carrera || !promedio || !telefono) {
        alert("Todos los campos de sustentante son obligatorios.");
        return false;
      }
    }

    fetch("../php/registroUsuario.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        fk_roles: fk_roles,
        nombres: nombres,
        apellidos: apellidos,
        correo: correo,
        numero_control: numero_control,
        carrera: carrera,
        promedio: promedio,
        telefono: telefono,
      }),
    })
      .then(function (response) {
        if (response.ok) {
          return response.text().then(function (text) {
            try {
              return JSON.parse(text);
            } catch (error) {
              //console.error("Error al analizar JSON:", error, ".");
              //console.error("Respuesta recibida:", text, ".");
              throw new Error("Error al analizar JSON.");
            }
          });
        } else {
          throw new Error("Error en la petición.");
        }
      })
      .then(async function (data) {
        if (data.message.includes("Error")) {
          throw new Error("Error del servidor: " + data.message);
        }
        alert(data.message);
        window.location.reload();
      })
      .catch(function (error) {
        //console.error(error);
        alert("Error al registrar el usuario");
      });
  });

function toggleSustentanteFields() {
  const numeroControlContainer = document.getElementById(
    "numero_control_container"
  );
  const carreraContainer = document.getElementById("carrera_container");
  const promedioContainer = document.getElementById("promedio_container");
  const telefonoContainer = document.getElementById(
    "telefonoRegistro_container"
  );
  const roleSelector = document.getElementById("fk_roles");

  if (roleSelector.value == 1) {
    numeroControlContainer.style.display = "block";
    carreraContainer.style.display = "block";
    promedioContainer.style.display = "block";
    telefonoContainer.style.display = "block";

    numero_control.required = true;
    carrera.required = true;
    promedio.required = true;
    telefonoRegistro.required = true;
  } else {
    numeroControlContainer.style.display = "none";
    carreraContainer.style.display = "none";
    promedioContainer.style.display = "none";
    telefonoContainer.style.display = "none";

    numero_control.required = false;
    carrera.required = false;
    promedio.required = false;
    telefonoRegistro.required = false;
  }
}

document
  .getElementById("fk_roles")
  .addEventListener("change", toggleSustentanteFields);
