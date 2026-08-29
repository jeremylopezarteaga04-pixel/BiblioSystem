
document.addEventListener("DOMContentLoaded", () => {

  /* ============================================================
     ELEMENTOS DEL FORMULARIO
     ============================================================ */

  const registerForm = document.getElementById("register-form");

  if (!registerForm) {
    return;
  }

  const nombres = document.getElementById("nombres");
  const apellidos = document.getElementById("apellidos");
  const cedula = document.getElementById("cedula");
  const correo = document.getElementById("correo");
  const telefono = document.getElementById("telefono");
  const direccion = document.getElementById("direccion");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm-password");
  const terms = document.getElementById("terms");


  /* ============================================================
     MOSTRAR / OCULTAR CONTRASEÑAS
     ============================================================ */

  const passwordButtons =
    document.querySelectorAll(".password-toggle");

  passwordButtons.forEach((button) => {

    button.addEventListener("click", () => {

      const targetId = button.dataset.target;
      const input = document.getElementById(targetId);

      if (!input) {
        return;
      }

      if (input.type === "password") {

        input.type = "text";

        button.setAttribute(
          "aria-label",
          "Ocultar contraseña"
        );

      } else {

        input.type = "password";

        button.setAttribute(
          "aria-label",
          "Mostrar contraseña"
        );

      }

    });

  });


  /* ============================================================
     MENSAJES DE ERROR
     ============================================================ */

  function showError(input, message) {

    removeError(input);

    input.setAttribute("aria-invalid", "true");

    const error = document.createElement("small");

    error.className = "register-field-error";
    error.textContent = message;

    const field = input.closest(".register-field");

    if (field) {
      field.appendChild(error);
    }

  }


  function removeError(input) {

    input.removeAttribute("aria-invalid");

    const field = input.closest(".register-field");

    if (!field) {
      return;
    }

    const error =
      field.querySelector(".register-field-error");

    if (error) {
      error.remove();
    }

  }


  function clearErrors() {

    document
      .querySelectorAll(".register-field-error")
      .forEach((error) => error.remove());

    document
      .querySelectorAll("[aria-invalid='true']")
      .forEach((input) => {
        input.removeAttribute("aria-invalid");
      });

    const termsError =
      document.querySelector(".register-terms-error");

    if (termsError) {
      termsError.remove();
    }

    const formMessage =
      document.querySelector(".register-form-message");

    if (formMessage) {
      formMessage.remove();
    }

  }


  /* ============================================================
     MENSAJE GENERAL
     ============================================================ */

  function showFormMessage(message, type) {

    const oldMessage =
      registerForm.querySelector(".register-form-message");

    if (oldMessage) {
      oldMessage.remove();
    }

    const messageElement =
      document.createElement("div");

    messageElement.className =
      `register-form-message ${type}`;

    messageElement.textContent = message;

    registerForm.prepend(messageElement);

  }


  /* ============================================================
     VALIDAR NOMBRES
     ============================================================ */

  function validateNombres() {

    const value = nombres.value.trim();

    if (value === "") {

      showError(
        nombres,
        "Ingresa tus nombres."
      );

      return false;
    }

    if (value.length < 2) {

      showError(
        nombres,
        "Los nombres deben tener al menos 2 caracteres."
      );

      return false;
    }

    removeError(nombres);

    return true;

  }


  /* ============================================================
     VALIDAR APELLIDOS
     ============================================================ */

  function validateApellidos() {

    const value = apellidos.value.trim();

    if (value === "") {

      showError(
        apellidos,
        "Ingresa tus apellidos."
      );

      return false;
    }

    if (value.length < 2) {

      showError(
        apellidos,
        "Los apellidos deben tener al menos 2 caracteres."
      );

      return false;
    }

    removeError(apellidos);

    return true;

  }


  /* ============================================================
     VALIDAR CÉDULA
     ============================================================ */

  function validateCedula() {

    const value = cedula.value.trim();

    if (value === "") {

      showError(
        cedula,
        "Ingresa tu número de cédula."
      );

      return false;
    }

    if (!/^\d+$/.test(value)) {

      showError(
        cedula,
        "La cédula solo debe contener números."
      );

      return false;
    }

    if (value.length !== 10) {

      showError(
        cedula,
        "La cédula debe tener 10 dígitos."
      );

      return false;
    }

    removeError(cedula);

    return true;

  }


  /* ============================================================
     VALIDAR CORREO
     ============================================================ */

  function validateCorreo() {

    const value = correo.value.trim();

    if (value === "") {

      showError(
        correo,
        "Ingresa tu correo electrónico."
      );

      return false;
    }

    const emailRegex =
      /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(value)) {

      showError(
        correo,
        "Ingresa un correo electrónico válido."
      );

      return false;
    }

    removeError(correo);

    return true;

  }


  /* ============================================================
     VALIDAR TELÉFONO
     ============================================================ */

  function validateTelefono() {

    const value = telefono.value.trim();

    /*
     * Es opcional.
     */

    if (value === "") {

      removeError(telefono);

      return true;
    }

    if (!/^[0-9+\-()\s]+$/.test(value)) {

      showError(
        telefono,
        "Ingresa un número de teléfono válido."
      );

      return false;
    }

    removeError(telefono);

    return true;

  }


  /* ============================================================
     VALIDAR DIRECCIÓN
     ============================================================ */

  function validateDireccion() {

    const value = direccion.value.trim();

    /*
     * Es opcional.
     */

    if (value === "") {

      removeError(direccion);

      return true;
    }

    if (value.length < 3) {

      showError(
        direccion,
        "Ingresa una dirección válida."
      );

      return false;
    }

    removeError(direccion);

    return true;

  }


  /* ============================================================
     VALIDAR CONTRASEÑA
     ============================================================ */

  function validatePassword() {

    const value = password.value;

    if (value === "") {

      showError(
        password,
        "Ingresa una contraseña."
      );

      return false;
    }

    if (value.length < 6) {

      showError(
        password,
        "La contraseña debe tener al menos 6 caracteres."
      );

      return false;
    }

    removeError(password);

    return true;

  }


  /* ============================================================
     VALIDAR CONFIRMACIÓN
     ============================================================ */

  function validateConfirmPassword() {

    const value = confirmPassword.value;

    if (value === "") {

      showError(
        confirmPassword,
        "Confirma tu contraseña."
      );

      return false;
    }

    if (value !== password.value) {

      showError(
        confirmPassword,
        "Las contraseñas no coinciden."
      );

      return false;
    }

    removeError(confirmPassword);

    return true;

  }


  /* ============================================================
     VALIDAR TÉRMINOS
     ============================================================ */

  function validateTerms() {

    const oldError =
      document.querySelector(".register-terms-error");

    if (oldError) {
      oldError.remove();
    }

    if (!terms.checked) {

      const error =
        document.createElement("small");

      error.className =
        "register-terms-error";

      error.textContent =
        "Debes aceptar los términos y condiciones.";

      const termsContainer =
        terms.closest(".register-terms");

      if (termsContainer) {
        termsContainer.appendChild(error);
      }

      return false;
    }

    return true;

  }


  /* ============================================================
     CÉDULA: SOLO NÚMEROS
     ============================================================ */

  cedula.addEventListener("input", () => {

    cedula.value =
      cedula.value.replace(/\D/g, "");

  });


  /* ============================================================
     VALIDACIÓN DE CONTRASEÑA EN TIEMPO REAL
     ============================================================ */

  confirmPassword.addEventListener("input", () => {

    if (confirmPassword.value === "") {

      removeError(confirmPassword);

      return;
    }

    if (confirmPassword.value !== password.value) {

      showError(
        confirmPassword,
        "Las contraseñas no coinciden."
      );

    } else {

      removeError(confirmPassword);

    }

  });


  /* ============================================================
     VALIDACIÓN AL SALIR DEL CAMPO
     ============================================================ */

  nombres.addEventListener(
    "blur",
    validateNombres
  );

  apellidos.addEventListener(
    "blur",
    validateApellidos
  );

  cedula.addEventListener(
    "blur",
    validateCedula
  );

  correo.addEventListener(
    "blur",
    validateCorreo
  );

  telefono.addEventListener(
    "blur",
    validateTelefono
  );

  direccion.addEventListener(
    "blur",
    validateDireccion
  );

  password.addEventListener(
    "blur",
    validatePassword
  );

  confirmPassword.addEventListener(
    "blur",
    validateConfirmPassword
  );


  /* ============================================================
     ENVÍO DEL FORMULARIO
     ============================================================ */

  registerForm.addEventListener("submit", (event) => {

    /*
     * IMPORTANTE:
     * Evita que el navegador envíe el formulario.
     */

    event.preventDefault();

    clearErrors();


    /* ------------------------------------------------------------
       Ejecutar validaciones
       ------------------------------------------------------------ */

    const nombresValidos =
      validateNombres();

    const apellidosValidos =
      validateApellidos();

    const cedulaValida =
      validateCedula();

    const correoValido =
      validateCorreo();

    const telefonoValido =
      validateTelefono();

    const direccionValida =
      validateDireccion();

    const passwordValida =
      validatePassword();

    const confirmacionValida =
      validateConfirmPassword();

    const terminosValidos =
      validateTerms();


    /* ------------------------------------------------------------
       Resultado
       ------------------------------------------------------------ */

    const formularioValido =
      nombresValidos &&
      apellidosValidos &&
      cedulaValida &&
      correoValido &&
      telefonoValido &&
      direccionValida &&
      passwordValida &&
      confirmacionValida &&
      terminosValidos;


    if (!formularioValido) {

      showFormMessage(
        "Revisa los datos ingresados antes de continuar.",
        "error"
      );

      return;
    }


    /* ============================================================
       REGISTRO VÁLIDO
       ============================================================ */

    showFormMessage(
      "Los datos ingresados son correctos. El registro está listo para continuar.",
      "success"
    );


    console.log("Formulario de registro válido.");

  });

});