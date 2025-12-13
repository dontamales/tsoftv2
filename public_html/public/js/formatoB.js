function debounce(func, delay) {
  let debounceTimer;
  return function () {
    const context = this;
    const args = arguments;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => func.apply(context, args), delay);
  };
}

function mayusculasNombre(nombre) {
  return nombre
    .toLowerCase()
    .split(" ")
    .map((palabra) => palabra.charAt(0).toUpperCase() + palabra.slice(1))
    .join(" ");
}

function mostrarError(elementId, message) {
    alert(message);
}

function campoVacio(campo, nombreCampo) {
  if (
    campo == null ||
    campo == undefined ||
    (typeof campo === "string" && campo.trim() == "")
  ) {
    mostrarError(nombreCampo, `El campo de "${nombreCampo}" es obligatorio.`);
    return true;
  }
  return false;
}

function validarFormatoB(
  nombres,
  apellidos,
  genero,
  edad,
  celular,
  telefono,
  codigo_postal,
  colonia,
  calle,
  num_ext,
  num_int,
  numero_control,
  carrera,
  promedio,
  proyecto,
  plan_estudio,
  tipo_titulacion,
  fecha_ingreso,
  fecha_egreso,
  asesor,
  equipo,
  numero_integrantes_equipo,
  numero_control_equipo_1,
  nombres_equipo_1,
  carrera_equipo_1,
  numero_control_equipo_2,
  nombres_equipo_2,
  carrera_equipo_2
) {
  if (num_int == null || num_int === undefined || num_int.trim() === "") {
    num_int = 0;
  }

  if (equipo == null || equipo === undefined || equipo.trim() === "") {
    equipo = 0;
  }

  if (campoVacio(nombres, "nombres")) return false;
  if (campoVacio(apellidos, "apellidos")) return false;
  if (campoVacio(genero, "género")) return false;
  if (campoVacio(edad, "edad")) return false;
  if (campoVacio(celular, "celular")) return false;
  if (campoVacio(telefono, "teléfono")) return false;
  if (campoVacio(codigo_postal, "código postal")) return false;
  if (campoVacio(colonia, "colonia")) return false;
  if (campoVacio(calle, "calle")) return false;
  if (campoVacio(num_ext, "número exterior")) return false;
  if (campoVacio(num_int, "número interior")) return false;
  if (campoVacio(numero_control, "número de control")) return false;
  if (campoVacio(carrera, "carrera")) return false;
  if (campoVacio(promedio, "promedio")) return false;
  if (campoVacio(plan_estudio, "plan de estudio")) return false;
  if (campoVacio(tipo_titulacion, "tipo de titulación")) return false;
  if (tipo_titulacion != 7 && tipo_titulacion != 8 && tipo_titulacion != 10 && tipo_titulacion != 11 && tipo_titulacion != 12) {
    if (campoVacio(proyecto, "proyecto")) return false;
    if (campoVacio(asesor, "asesor")) return false;
  }
  if (campoVacio(fecha_ingreso, "fecha de ingreso")) return false;
  if (campoVacio(fecha_egreso, "fecha de egreso")) return false;
  if (campoVacio(equipo, "equipo")) return false;
  if (campoVacio(numero_integrantes_equipo, "numero integrantes equipo"))
    return false;
  if (campoVacio(numero_control_equipo_1, "numero control equipo 1"))
    return false;
  if (campoVacio(nombres_equipo_1, "nombres equipo 1")) return false;
  if (campoVacio(carrera_equipo_1, "carrera equipo 1")) return false;
  if (campoVacio(numero_control_equipo_2, "numero control equipo 2"))
    return false;
  if (campoVacio(nombres_equipo_2, "nombres equipo 2")) return false;
  if (campoVacio(carrera_equipo_2, "carrera equipo 2")) return false;

  const patronNombre = /^[a-zA-ZÀ-ÿ\s]+$/;
  if (!patronNombre.test(nombres)) {
    alert("El nombre no debe contener números ni caracteres especiales.");
    return false;
  }
  if (!patronNombre.test(apellidos)) {
    alert("El apellido no debe contener números ni caracteres especiales.");
    return false;
  }

  nombres = mayusculasNombre(nombres.trim());
  apellidos = mayusculasNombre(apellidos.trim());

  const numeroControlPattern = /^[0-9]{8}$/;
  const numeroControlPattern2 = /^[a-zA-Z][0-9]{8}$/;
  if (
    !numeroControlPattern.test(numero_control) &&
    !numeroControlPattern2.test(numero_control)
  ) {
    alert(
      "El número de control debe ser de 8 números o una letra seguida de 8 números."
    );
    return false;
  }

  const regular = /^\d+$/;
  if (!regular.test(edad) || isNaN(edad) || edad < 0 || edad > 150) {
    alert("La debe ser un número entre 0 y 150.");
    return false;
  }

  const regularDouble = /^\d+(\.\d{1,2})?$/;
  if (
    !regularDouble.test(promedio) ||
    isNaN(promedio) ||
    promedio < 0 ||
    promedio > 100
  ) {
    alert("El promedio debe ser un número entre 0 y 100.");
    return false;
  }

  if (
    !regular.test(codigo_postal) ||
    isNaN(codigo_postal) ||
    codigo_postal.length < 4 ||
    codigo_postal.length > 10
  ) {
    alert(
      "El código postal debe ser un número con una longitud de 4 a 10 dígitos."
    );
    return false;
  }

  const patronFecha = /^\d{4}-\d{2}-\d{2}$/;
  if (!patronFecha.test(fecha_ingreso)) {
    alert(
      "El campo 'Fecha de ingreso' debe ser una fecha válida en el formato AAAA-MM-DD"
    );
  } else if (!patronFecha.test(fecha_egreso)) {
    alert(
      "El campo 'Fecha de egreso' debe ser una fecha válida en el formato AAAA-MM-DD"
    );
    return false;
  } else {
    const parteFechaIngreso = fecha_ingreso.split("-");
    const fechaIngreso = new Date(
      +parteFechaIngreso[0],
      parteFechaIngreso[1] - 1,
      +parteFechaIngreso[2]
    );

    const parteFechaEgreso = fecha_egreso.split("-");
    const fechaEgreso = new Date(
      +parteFechaEgreso[0],
      parteFechaEgreso[1] - 1,
      +parteFechaEgreso[2]
    );

    if (fechaIngreso >= fechaEgreso) {
      mostrarError(
        "fechas",
        "La fecha de ingreso debe ser anterior a la fecha de egreso."
      );
      alert("La fecha de ingreso debe ser anterior a la fecha de egreso.");
      return false;
    }
  }

  if (equipo > 1 || equipo < 0 || !regular.test(equipo)) {
    alert("El valor de proyecto en equipo está fuera de los parametros.");
    return false;
  }

  if (equipo != 0) {
    if (numero_integrantes_equipo != 2 && numero_integrantes_equipo != 3) {
      alert("El número de integrantes del equipo debe ser 2 o 3.");
      return false;
    }
    if (numero_integrantes_equipo == 2) {
      if (
        !numeroControlPattern.test(numero_control_equipo_1) &&
        !numeroControlPattern2.test(numero_control_equipo_1)
      ) {
        alert(
          "El número de control del integrante 1 debe ser de 8 números o una letra seguida de 8 números."
        );
        return false;
      }
      if (!patronNombre.test(nombres_equipo_1)) {
        alert(
          "El nombre del integrante 1 no debe contener números ni caracteres especiales."
        );
        return false;
      }
      if (numero_control === numero_control_equipo_1) {
        alert(
          "El número de control del sustentante y el integrante 1 no deben ser iguales."
        );
        return false;
      }
      if (nombres + " " + apellidos === nombres_equipo_1) {
        alert(
          "El nombre del sustentante y el integrante 1 no deben ser iguales."
        );
        return false;
      }
    }
    if (numero_integrantes_equipo == 3) {
      if (
        !numeroControlPattern.test(numero_control_equipo_1) &&
        !numeroControlPattern2.test(numero_control_equipo_1)
      ) {
        alert(
          "El número de control del integrante 1 debe ser de 8 números o una letra seguida de 8 números."
        );
        return false;
      }
      if (!patronNombre.test(nombres_equipo_1)) {
        alert(
          "El nombre del integrante 1 no debe contener números ni caracteres especiales."
        );
        return false;
      }
      if (
        !numeroControlPattern.test(numero_control_equipo_2) &&
        !numeroControlPattern2.test(numero_control_equipo_2)
      ) {
        alert(
          "El número de control del integrante 2 debe ser de 8 números o una letra seguida de 8 números."
        );
        return false;
      }
      if (!patronNombre.test(nombres_equipo_2)) {
        alert(
          "El nombre del integrante 2 no debe contener números ni caracteres especiales."
        );
        return false;
      }
      if (numero_control_equipo_1 == numero_control_equipo_2) {
        alert(
          "El número de control del integrante 1 y 2 no deben ser iguales."
        );
        return false;
      }
      if (nombres_equipo_1 === nombres_equipo_2) {
        alert("El nombre del integrante 1 y 2 no deben ser iguales.");
        return false;
      }
      if (numero_control === numero_control_equipo_1) {
        alert(
          "El número de control del sustentante y el integrante 1 no deben ser iguales."
        );
        return false;
      }
      if (numero_control === numero_control_equipo_2) {
        alert(
          "El número de control del sustentante y el integrante 2 no deben ser iguales."
        );
        return false;
      }
      if (nombres + " " + apellidos === nombres_equipo_1) {
        alert(
          "El nombre del sustentante y el integrante 1 no deben ser iguales."
        );
        return false;
      }
      if (nombres + " " + apellidos === nombres_equipo_2) {
        alert(
          "El nombre del sustentante y el integrante 2 no deben ser iguales."
        );
        return false;
      }
    }
  } else if (equipo == 0) {
    numero_integrantes_equipo = 1;
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
    carreraSelect.innerHTML = '<option value="">Seleccione su carrera</option>';

    for (const carrera of carreras) {
      const option = document.createElement("option");
      option.value = carrera.id;
      option.textContent = carrera.nombre;

      if (carrera.id == carreraSeleccionada) {
        option.selected = true;
      }

      carreraSelect.appendChild(option);
    }

    // Calcular el plan de estudio después de cargar las carreras
    if (idInputNumeroControl == "numero_controlFormatoB") {
      const plan_estudio = calcularPlanEstudio(
        numeroControl,
        carreraSeleccionada
      );
      document.getElementById("planEstudioFormatoB").value =
        plan_estudio.Descripcion_Del_Plan_De_Año_Plan_Estudio;
      document.getElementById("hiddenPlanEstudioFormatoB").value =
        plan_estudio.Id_PlanEstudio;
    }
  } catch (error) {
    alert("Error al cargar las carreras.");
  }
}

cargarCarreras("numero_controlFormatoB", "carreraFormatoB");

const numeroControlInput = document.getElementById("numero_controlFormatoB");

numeroControlInput.addEventListener(
  "input",
  debounce(
    () => cargarCarreras("numero_controlFormatoB", "carreraFormatoB"),
    300
  )
);

// Obtén una referencia al menú desplegable de carrera
const carreraSelect = document.getElementById("carreraFormatoB");

// Añade un escucha de evento para el evento 'change'
carreraSelect.addEventListener("change", (event) => {
  // Cuando el valor del menú desplegable cambie, obtén el nuevo valor
  const nuevaCarrera = event.target.value;
  const numeroControl = document.getElementById("numero_controlFormatoB").value;

  // Utiliza el nuevo valor para calcular el plan de estudio
  const plan_estudio = calcularPlanEstudio(numeroControl, nuevaCarrera);

  // Actualiza los elementos del plan de estudio con los nuevos valores
  document.getElementById("planEstudioFormatoB").value =
    plan_estudio.Descripcion_Del_Plan_De_Año_Plan_Estudio;
  document.getElementById("hiddenPlanEstudioFormatoB").value =
    plan_estudio.Id_PlanEstudio;
});

async function cargarPlanesEstudio() {
  try {
    const respuesta = await fetch("../../php/obtenerPlanesEstudio.php");
    if (!respuesta.ok) {
      throw new Error("Error al cargar los planes de estudio");
    }

    const planes_Estudio = await respuesta.json();
    const plan_EstudioSelect = document.getElementById("planEstudioFormatoB");
    const plan_EstudioSeleccionada = plan_EstudioSelect.dataset.selected;

    for (const plan_Estudio of planes_Estudio) {
      const option = document.createElement("option");
      option.value = plan_Estudio.id;
      option.textContent = plan_Estudio.nombre;

      if (plan_Estudio.id == plan_EstudioSeleccionada) {
        option.selected = true;
      }

      plan_EstudioSelect.appendChild(option);
    }
  } catch (error) {
    alert("Error al cargar los planes de estudio.");
  }
}

cargarPlanesEstudio();

async function cargarTipoTitulacion(Id_PlanEstudio) {
  try {
    const respuesta = await fetch(
      `../../php/obtenerTipoTitulacion.php?Id_PlanEstudio=${Id_PlanEstudio}`
    );

    if (!respuesta.ok) {
      throw new Error("Error al cargar los tipos de titulación");
    }

    const tipos_titulacion = await respuesta.json();
    const tipo_titulacionSelect = document.getElementById(
      "tipoTitulaciónFormatoB"
    );

    const selectedValue = tipo_titulacionSelect.getAttribute("data-selected");

    while (tipo_titulacionSelect.firstChild) {
      tipo_titulacionSelect.removeChild(tipo_titulacionSelect.firstChild);
    }

    for (const tipo_titulacion of tipos_titulacion) {
      const option = document.createElement("option");
      option.value = tipo_titulacion.id;
      option.textContent = tipo_titulacion.nombre;
      if (selectedValue && selectedValue == tipo_titulacion.id) {
        option.selected = true;
      }
      tipo_titulacionSelect.appendChild(option);
    }
  } catch (error) {
    alert("Error al cargar los tipos de titulación.");
  }
}


$(document).ready(function () {
  var timeout = null;

  $("#asesorFormatoB").on("input", function () {
    clearTimeout(timeout);

    timeout = setTimeout(function () {
      var busqueda = $("#asesorFormatoB").val();

      $.ajax({
        url: "../php/obtenerProfesores.php",
        data: { q: busqueda },
        dataType: "json",
        success: function (data) {
          // Eliminar resultados de búsqueda anteriores
          $("#resultado-busqueda-asesor").empty();

          // Mostrar los nuevos resultados de la búsqueda
          if (data.length > 0) {
            data.forEach(function (profesor) {
              $("#resultado-busqueda-asesor").append(
                '<div class="list-group-item resultado-busqueda" data-id="' +
                  profesor.id +
                  '">' +
                  profesor.nombre +
                  "</div>"
              );
            });
            mostrarListaDesplegable();
          } else {
            $("#resultado-busqueda-asesor").append(
              '<div class="list-group-item resultado-busqueda">No se encontraron resultados</div>'
            );
          }
        },
      });
    }, 500);
  });

  $(document).on("click", ".resultado-busqueda", function () {
    var id = $(this).data("id");
    var nombre = $(this).text();

    $("#hiddenAsesorFormatoB").val(id);
    $("#asesorFormatoB").val(nombre);

    // Eliminar resultados de búsqueda
    ocultarListaDesplegable();
  });

  // Ocultar la lista desplegable si no se selecciona nada
  $(document).on("click", function (e) {
    if (
      !$(e.target).hasClass("resultado-busqueda") &&
      !$(e.target).is("#asesorFormatoB")
    ) {
      ocultarListaDesplegable();
    }
  });

  function mostrarListaDesplegable() {
    $("#resultado-busqueda-asesor").show();
  }

  function ocultarListaDesplegable() {
    $("#resultado-busqueda-asesor").empty().hide();
  }
});

function calcularPlanEstudio(numeroControl, idCarrera) {
  if (typeof numeroControl === "string" && numeroControl.length === 9) {
    const añoInicio = parseInt(numeroControl.slice(1, 3), 10);
    let añoReal = añoInicio > 50 ? 1900 + añoInicio : 2000 + añoInicio;

    if (añoReal >= 2010 || (añoReal == 2009 && idCarrera == 8)) {
      return {
        Id_PlanEstudio: 1,
        Descripcion_Del_Plan_De_Año_Plan_Estudio:
          "Plan competencias (Número de control 10 o superior).",
      };
    } else if (añoReal >= 2004 && añoReal <= 2005) {
      return {
        Id_PlanEstudio: 2,
        Descripcion_Del_Plan_De_Año_Plan_Estudio:
          "Plan anterior (Número de control de 04 y 05).",
      };
    } else if (añoReal >= 2006 && añoReal <= 2009) {
      return {
        Id_PlanEstudio: 3,
        Descripcion_Del_Plan_De_Año_Plan_Estudio:
          "Plan anterior (Número de control del 06 al 09).",
      };
    } else {
      return {
        Id_PlanEstudio: 4,
        Descripcion_Del_Plan_De_Año_Plan_Estudio:
          "Plan anterior (Número de control del 03 hacia atrás).",
      };
    }
  } else {
    const añoInicio = parseInt(numeroControl.slice(0, 2), 10);
    let añoReal = añoInicio > 50 ? 1900 + añoInicio : 2000 + añoInicio;

    if (añoReal >= 2010 || (añoReal == 2009 && idCarrera == 8)) {
      return {
        Id_PlanEstudio: 1,
        Descripcion_Del_Plan_De_Año_Plan_Estudio:
          "Plan competencias (Número de control 10 o superior).",
      };
    } else if (añoReal >= 2004 && añoReal <= 2005) {
      return {
        Id_PlanEstudio: 2,
        Descripcion_Del_Plan_De_Año_Plan_Estudio:
          "Plan anterior (Número de control de 04 y 05).",
      };
    } else if (añoReal >= 2006 && añoReal <= 2009) {
      return {
        Id_PlanEstudio: 3,
        Descripcion_Del_Plan_De_Año_Plan_Estudio:
          "Plan anterior (Número de control del 06 al 09).",
      };
    } else {
      return {
        Id_PlanEstudio: 4,
        Descripcion_Del_Plan_De_Año_Plan_Estudio:
          "Plan anterior (Número de control del 03 hacia atrás).",
      };
    }
  }
}

// Función para modificar el valor que se envía a la base de datos conforme al checkbox
$(document).ready(function () {
  $("#equipoCheckboxFormatoB").on("change", function () {
    if ($(this).is(":checked")) {
      $('[name="equipoCheckboxFormatoB"]').val(1);
    } else {
      $('[name="equipoCheckboxFormatoB"]').val(0);
    }
  });
});

// Función para mostrar el grupo radio buttons de números de integrantes de equipo
$(document).ready(function () {
  $("#equipoCheckboxFormatoB").change(function () {
    if (this.checked) {
      $("#radioEquipoFormatoB").show();
    } else {
      $("#radioEquipoFormatoB, #equipoInputFormatoB").hide();
      $("#equipoInputFormatoB").empty();
    }
  });

  if ($("#equipoCheckboxFormatoB").is(":checked")) {
    $("#radioEquipoFormatoB").show();
    cargarCamposEquipo($('[name="radioEquipoFormatoB"]:checked').val());
  } else {
    $("#radioEquipoFormatoB, #equipoInputFormatoB").hide();
    $("#equipoInputFormatoB").empty();
  }

  //Función para crear los inputs de los integrantes del equipo
  $("input[type=radio][name=radioEquipoFormatoB]").change(async function () {
    cargarCamposEquipo(this.value);
  });
});

function cargarCamposEquipo(value) {
  let numInputs = value == 2 ? 3 : 6;
  let inputContainer = $("#equipoInputFormatoB");

  inputContainer.empty(); // Limpiar los inputs anteriores
  inputContainer.show(); // Asegurarse de que el contenedor de inputs se muestra

  for (let i = 0; i < numInputs; i++) {
    let newInputDiv = $('<div class="row mb-3"></div>');
    let newInputContainer = $('<div class="col-sm-10"></div>');
    let newInput;
    let newInputLabel;
    if (i == 0) {
      newInputLabel = $(
        `<label for="equipoInput${i}FormatoB" class="col-sm-2 col-form-label">Número de control de integrante 1:</label>`
      );
      newInput = $(
        `<input type="text" id="equipoInput${i}FormatoB" name="equipoInput${i}FormatoB" class="form-control" value="${datos.numeroControlEquipo1}" required>`
      );
    } else if (i == 1) {
      newInputLabel = $(
        `<label for="equipoInput${i}FormatoB" class="col-sm-2 col-form-label">Nombre y apellidos de integrante 1:</label>`
      );
      newInput = $(
        `<input type="text" id="equipoInput${i}FormatoB" name="equipoInput${i}FormatoB" class="form-control" value="${datos.nombresEquipo1}" required>`
      );
    } else if (i == 2) {
      newInputLabel = $(
        `<label for="equipoInput${i}FormatoB" class="col-sm-2 col-form-label">Carrera de integrante 1:</label>`
      );
      newInput = $(
        `<select id="equipoInput${i}FormatoB" name="equipoInput${i}FormatoB" class="form-control"  data-selected="${datos.carreraEquipo1}">`
      );
    } else if (i == 3) {
      newInputLabel = $(
        `<label for="equipoInput${i}FormatoB" class="col-sm-2 col-form-label">Número de control de integrante 2:</label>`
      );
      newInput = $(
        `<input type="text" id="equipoInput${i}FormatoB" name="equipoInput${i}FormatoB" class="form-control" value="${datos.numeroControlEquipo2}" required>`
      );
    } else if (i == 4) {
      newInputLabel = $(
        `<label for="equipoInput${i}FormatoB" class="col-sm-2 col-form-label">Nombre y apellidos de integrante 2:</label>`
      );
      newInput = $(
        `<input type="text" id="equipoInput${i}FormatoB" name="equipoInput${i}FormatoB" class="form-control" value="${datos.nombresEquipo2}" required>`
      );
    } else if (i == 5) {
      newInputLabel = $(
        `<label for="equipoInput${i}FormatoB" class="col-sm-2 col-form-label">Carrera de integrante 2:</label>`
      );
      newInput = $(
        `<select id="equipoInput${i}FormatoB" name="equipoInput${i}FormatoB" class="form-control"  data-selected="${datos.carreraEquipo2}">`
      );
    }
    newInput.appendTo(newInputContainer);
    newInputLabel.appendTo(newInputDiv);
    newInputContainer.appendTo(newInputDiv);
    newInputDiv.appendTo(inputContainer);
    if (i == 2) {
      cargarCarreras("equipoInput0FormatoB", "equipoInput2FormatoB");
      const numeroControlInput1 = document.getElementById(
        "equipoInput0FormatoB"
      );
      numeroControlInput1.addEventListener(
        "input",
        debounce(
          () => cargarCarreras("equipoInput0FormatoB", "equipoInput2FormatoB"),
          300
        )
      );
    } else if (i == 5) {
      cargarCarreras("equipoInput3FormatoB", "equipoInput5FormatoB");
      const numeroControlInput2 = document.getElementById(
        "equipoInput3FormatoB",
        "equipoInput5FormatoB"
      );
      numeroControlInput2.addEventListener(
        "input",
        debounce(
          () => cargarCarreras("equipoInput3FormatoB", "equipoInput5FormatoB"),
          300
        )
      );
    }
  }
}

// Carga de el plan de estudio y el tipo de titulación al cargar la página
document.addEventListener("DOMContentLoaded", (event) => {
  const numero_controlInput = document.getElementById("numero_controlFormatoB");
  const carreraInput = document.getElementById("carreraFormatoB");
  if (numero_controlInput.value.trim() !== "") {
    const numero_control = numero_controlInput.value.trim();
    const idCarrera = carreraInput.value;
    const plan_estudio = calcularPlanEstudio(numero_control, idCarrera);

    document.getElementById("planEstudioFormatoB").value =
      plan_estudio.Descripcion_Del_Plan_De_Año_Plan_Estudio;
    document.getElementById("hiddenPlanEstudioFormatoB").value =
      plan_estudio.Id_PlanEstudio;

    cargarTipoTitulacion(plan_estudio.Id_PlanEstudio);
  }
});

// Carga de el plan de estudio y el tipo de titulación al cambiar el número de control
document
  .getElementById("numero_controlFormatoB")
  .addEventListener("input", function (event) {
    const numero_control = event.target.value.trim();
    const idCarrera = document.getElementById("carreraFormatoB").value;
    const plan_estudio = calcularPlanEstudio(numero_control, idCarrera);

    document.getElementById("planEstudioFormatoB").value =
      plan_estudio.Descripcion_Del_Plan_De_Año_Plan_Estudio;
    document.getElementById("hiddenPlanEstudioFormatoB").value =
      plan_estudio.Id_PlanEstudio;

    cargarTipoTitulacion(plan_estudio.Id_PlanEstudio);
  });

document
  .getElementById("formato_B")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    const nombres = mayusculasNombre(
      document.getElementById("nombresFormatoB").value.trim()
    );
    const apellidos = mayusculasNombre(
      document.getElementById("apellidosFormatoB").value.trim()
    );

    let genero;
    let aux;
    let radios = document.getElementsByName("generoFormatoB");
    for (let i = 0, length = radios.length; i < length; i++) {
      if (radios[i].checked) {
        aux = i + 1;
        genero = aux;
        break;
      }
    }

    const edad = document.getElementById("edadFormatoB").value.trim();
    const celular = document.getElementById("celularFormatoB").value.trim();
    const telefono = document.getElementById("telefonoFormatoB").value.trim();
    const codigo_postal = document
      .getElementById("codigo_postalFormatoB")
      .value.trim();
    const colonia = document.getElementById("coloniaFormatoB").value.trim();
    const calle = document.getElementById("calleFormatoB").value.trim();
    const num_ext = document.getElementById("num_extFormatoB").value.trim();
    const num_int = document.getElementById("num_intFormatoB").value.trim();
    const numero_control = document
      .getElementById("numero_controlFormatoB")
      .value.trim();
    const carrera = document.getElementById("carreraFormatoB").value;
    const promedio = document.getElementById("promedioFormatoB").value;
    const proyecto = document.getElementById("proyectoFormatoB").value;
    const plan_estudio = document.getElementById(
      "hiddenPlanEstudioFormatoB"
    ).value;
    const tipo_titulacion = document.getElementById(
      "tipoTitulaciónFormatoB"
    ).value;
    const fecha_ingreso = document.getElementById("fechaIngresoFormatoB").value;
    const fecha_egreso = document.getElementById("fechaEgresoFormatoB").value;
    const asesor = document.getElementById("hiddenAsesorFormatoB").value;
    const equipo = document.getElementById("equipoCheckboxFormatoB").value;

    const radioSeleccionado = document.querySelector(
      'input[name="radioEquipoFormatoB"]:checked'
    );
    let numero_integrantes_equipo = radioSeleccionado
      ? parseInt(radioSeleccionado.value)
      : 1;

    let numero_control_equipo_1;
    let nombres_equipo_1;
    let carrera_equipo_1;
    let numero_control_equipo_2;
    let nombres_equipo_2;
    let carrera_equipo_2;

    function verificarValor(id) {
      let elemento = document.getElementById(id);
      if (elemento) {
        let valor = elemento.value;
        return valor != null && valor != undefined && valor.trim() != "";
      } else {
        return false;
      }
    }

    if (
      $("#equipoInput0FormatoB").length &&
      $("#equipoInput1FormatoB").length &&
      $("#equipoInput2FormatoB").length
    ) {
      if (
        verificarValor("equipoInput0FormatoB") &&
        verificarValor("equipoInput1FormatoB") &&
        verificarValor("equipoInput2FormatoB")
      ) {
        numero_control_equipo_1 = document.getElementById(
          "equipoInput0FormatoB"
        ).value;
        nombres_equipo_1 = document.getElementById(
          "equipoInput1FormatoB"
        ).value;
        carrera_equipo_1 = document.getElementById(
          "equipoInput2FormatoB"
        ).value;
      }
    } else {
      numero_control_equipo_1 = "No existe";
      nombres_equipo_1 = "No existe";
      carrera_equipo_1 = 1;
    }

    if (
      $("#equipoInput3FormatoB").length &&
      $("#equipoInput4FormatoB").length &&
      $("#equipoInput5FormatoB").length
    ) {
      if (
        verificarValor("equipoInput3FormatoB") &&
        verificarValor("equipoInput4FormatoB") &&
        verificarValor("equipoInput5FormatoB")
      ) {
        numero_control_equipo_2 = document.getElementById(
          "equipoInput3FormatoB"
        ).value;
        nombres_equipo_2 = document.getElementById(
          "equipoInput4FormatoB"
        ).value;
        carrera_equipo_2 = document.getElementById(
          "equipoInput5FormatoB"
        ).value;
      }
    } else {
      numero_control_equipo_2 = "No existe";
      nombres_equipo_2 = "No existe";
      carrera_equipo_2 = 1;
    }

    if (equipo == 0) {
      numero_integrantes_equipo = 1;
      numero_control_equipo_1 = "No existe";
      nombres_equipo_1 = "No existe";
      carrera_equipo_1 = 1;
      numero_control_equipo_2 = "No existe";
      nombres_equipo_2 = "No existe";
      carrera_equipo_2 = 1;
    }

    if (
      !validarFormatoB(
        nombres,
        apellidos,
        genero,
        edad,
        celular,
        telefono,
        codigo_postal,
        colonia,
        calle,
        num_ext,
        num_int,
        numero_control,
        carrera,
        promedio,
        proyecto,
        plan_estudio,
        tipo_titulacion,
        fecha_ingreso,
        fecha_egreso,
        asesor,
        equipo,
        numero_integrantes_equipo,
        numero_control_equipo_1,
        nombres_equipo_1,
        carrera_equipo_1,
        numero_control_equipo_2,
        nombres_equipo_2,
        carrera_equipo_2
      )
    ) {
      return;
    }

    fetch("../php/formatoB.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        nombres: nombres,
        apellidos: apellidos,
        genero: genero,
        edad: edad,
        celular: celular,
        telefono: telefono,
        codigo_postal: codigo_postal,
        colonia: colonia,
        calle: calle,
        num_ext: num_ext,
        num_int: num_int,
        numero_control: numero_control,
        carrera: carrera,
        promedio: promedio,
        proyecto: proyecto,
        plan_estudio: plan_estudio,
        tipo_titulacion: tipo_titulacion,
        fecha_ingreso: fecha_ingreso,
        fecha_egreso: fecha_egreso,
        asesor: asesor,
        equipo: equipo,
        numero_integrantes_equipo: numero_integrantes_equipo,
        numero_control_equipo_1: numero_control_equipo_1,
        nombres_equipo_1: nombres_equipo_1,
        carrera_equipo_1: carrera_equipo_1,
        numero_control_equipo_2: numero_control_equipo_2,
        nombres_equipo_2: nombres_equipo_2,
        carrera_equipo_2: carrera_equipo_2,
      }),
    })
      .then(function (respuesta) {
        if (respuesta.ok) {
          return respuesta.text().then(function (text) {
            try {
              return JSON.parse(text);
            } catch (error) {
              //console.error("Error al analizar JSON:", error, ".");
              //console.error("Respuesta recibida:", text, ".");
              throw new Error("Error al analizar JSON.");
            }
          });
        } else {
          throw new Error("Error en la petición: " + respuesta.statusText);
        }
      })
      .then(async function (data) {
        alert(data.message);
          window.location.href = 'userDashboard.php';
      })
      .catch(function (error) {
        alert("Error al guardar los nuevos datos.");
      });
  });
