document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector('form[action="../php/subirExcel.php"]');
  const fileInput = form.querySelector('input[type="file"]');
  const submitButton = form.querySelector('button[type="submit"]');

  submitButton.addEventListener("click", function (event) {
    if (!fileInput.value) {
      event.preventDefault();
      alert("Por favor, selecciona un archivo antes de enviar el formulario.");
    }
  });
});
