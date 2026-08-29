/* ============================================================
   BIBLIOSYSTEM - AUTH.JS
   Login / autenticación - comportamiento del frontend
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

  // ==========================================================
  // ELEMENTOS
  // ==========================================================

  const loginForm = document.getElementById('login-form');
  const emailInput = document.getElementById('login-email');
  const passwordInput = document.getElementById('login-password');
  const togglePassword = document.getElementById('toggle-password');
  const loginSubmit = document.getElementById('login-submit');
  const loginError = document.getElementById('login-error');
  const rememberMe = document.getElementById('remember-me');
  const forgotPassword = document.getElementById('forgot-password');


  // ==========================================================
  // MOSTRAR / OCULTAR CONTRASEÑA
  // ==========================================================

  if (togglePassword && passwordInput) {

    togglePassword.addEventListener('click', () => {

      const isPassword = passwordInput.type === 'password';

      passwordInput.type = isPassword
        ? 'text'
        : 'password';

      togglePassword.textContent = isPassword
        ? '◉'
        : '◉';

      togglePassword.setAttribute(
        'aria-label',
        isPassword
          ? 'Ocultar contraseña'
          : 'Mostrar contraseña'
      );

    });

  }


  // ==========================================================
  // OCULTAR ERROR AL ESCRIBIR
  // ==========================================================

  function hideError() {

    if (!loginError) return;

    loginError.classList.add('hidden');
    loginError.textContent = '';

  }


  if (emailInput) {
    emailInput.addEventListener('input', hideError);
  }

  if (passwordInput) {
    passwordInput.addEventListener('input', hideError);
  }


  // ==========================================================
  // MOSTRAR ERROR
  // ==========================================================

  function showError(message) {

    if (!loginError) return;

    loginError.textContent = message;
    loginError.classList.remove('hidden');

  }


  // ==========================================================
  // VALIDACIÓN DEL FORMULARIO
  // ==========================================================

  function validateLogin() {

    const correo = emailInput?.value.trim() || '';
    const password = passwordInput?.value || '';

    if (!correo) {

      showError(
        'Ingresa tu correo electrónico.'
      );

      emailInput?.focus();

      return false;
    }


    if (!isValidEmail(correo)) {

      showError(
        'Ingresa un correo electrónico válido.'
      );

      emailInput?.focus();

      return false;
    }


    if (!password) {

      showError(
        'Ingresa tu contraseña.'
      );

      passwordInput?.focus();

      return false;
    }


    if (password.length < 6) {

      showError(
        'La contraseña debe tener al menos 6 caracteres.'
      );

      passwordInput?.focus();

      return false;
    }


    return true;
  }


  // ==========================================================
  // VALIDAR CORREO
  // ==========================================================

  function isValidEmail(email) {

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

  }


  // ==========================================================
  // ESTADO DEL BOTÓN
  // ==========================================================

  function setLoading(loading) {

    if (!loginSubmit) return;

    loginSubmit.disabled = loading;

    const text = loginSubmit.querySelector('span:first-child');

    if (text) {

      text.textContent = loading
        ? 'Iniciando sesión...'
        : 'Iniciar sesión';

    }

    if (loading) {

      loginSubmit.style.cursor = 'wait';

    } else {

      loginSubmit.style.cursor = '';

    }

  }

  // ==========================================================
  // LOGIN
  // ==========================================================

  if (loginForm) {

    loginForm.addEventListener('submit', async (event) => {

      event.preventDefault();

      hideError();

      // ------------------------------------------------------
      // VALIDACIÓN
      // ------------------------------------------------------

      if (!validateLogin()) {
        return;
      }

      const correo = emailInput.value.trim();
      const password = passwordInput.value;

      setLoading(true);

      try {

        // ----------------------------------------------------
        // DATOS DEL LOGIN
        // ----------------------------------------------------

        const datos = new FormData();

        datos.append('correo', correo);
        datos.append('password', password);

        // ----------------------------------------------------
        // ENVIAR AL BACKEND
        // ----------------------------------------------------

        const respuesta = await fetch(
          '../../api/login.php',
          {
            method: 'POST',
            body: datos,
            credentials: 'include'
          }
        );

        const textoRespuesta = await respuesta.text();

        console.log(
          'Respuesta del servidor:',
          textoRespuesta
        );

        // ----------------------------------------------------
        // CONVERTIR RESPUESTA A JSON
        // ----------------------------------------------------

        let resultado;

        try {

          resultado = JSON.parse(textoRespuesta);

        } catch (error) {

          console.error(
            'El servidor no devolvió JSON válido:',
            textoRespuesta
          );

          throw new Error(
            'El servidor devolvió una respuesta inesperada.'
          );
        }

        // ----------------------------------------------------
        // ERROR DEL LOGIN
        // ----------------------------------------------------

        if (!respuesta.ok || !resultado.success) {

          throw new Error(
            resultado.message ||
            'Correo o contraseña incorrectos.'
          );

        }

        // ----------------------------------------------------
        // GUARDAR INFORMACIÓN DE SESIÓN EN EL NAVEGADOR
        // ----------------------------------------------------

        if (resultado.usuario) {

          sessionStorage.setItem(
            'bibliosystem_usuario',
            JSON.stringify(resultado.usuario)
          );

        }

        if (resultado.rol) {

          sessionStorage.setItem(
          'bibliosystem_rol',
          resultado.usuario.rol
        );

        }

        // ----------------------------------------------------
        // RECORDAR CORREO
        // ----------------------------------------------------

        if (rememberMe && rememberMe.checked) {

          localStorage.setItem(
            'bibliosystem_remember_email',
            correo
          );

        }

        // ----------------------------------------------------
        // REDIRECCIÓN SEGÚN EL ROL
        // ----------------------------------------------------

        const rol =
          String(resultado.usuario?.rol || '').toUpperCase();

        if (
          rol === 'ADMIN' ||
          rol === 'ADMINISTRADOR'
        ) {

          window.location.href =
            '../pages/admin/dashboard.html';

        } else if (
          rol === 'USUARIO'
        ) {

          window.location.href =
            '../pages/user/dashboard.html';

        } else {

          throw new Error(
            'El usuario no tiene un rol válido.'
          );

        }

      } catch (error) {

        console.error(
          'Error al iniciar sesión:',
          error
        );

        showError(
          error.message ||
          'No se pudo iniciar sesión.'
        );

      } finally {

        setLoading(false);

      }

    });

  }

  // ==========================================================
  // RECORDAR SESIÓN
  // ==========================================================

  if (rememberMe) {

    const savedEmail = localStorage.getItem(
      'bibliosystem_remember_email'
    );

    if (savedEmail) {

      emailInput.value = savedEmail;

      rememberMe.checked = true;

    }


    rememberMe.addEventListener('change', () => {

      if (rememberMe.checked) {

        const email = emailInput.value.trim();

        if (email) {

          localStorage.setItem(
            'bibliosystem_remember_email',
            email
          );

        }

      } else {

        localStorage.removeItem(
          'bibliosystem_remember_email'
        );

      }

    });

  }


  // ==========================================================
  // ACTUALIZAR CORREO RECORDADO
  // ==========================================================

  if (emailInput && rememberMe) {

    emailInput.addEventListener('change', () => {

      if (!rememberMe.checked) {
        return;
      }

      const email = emailInput.value.trim();

      if (email) {

        localStorage.setItem(
          'bibliosystem_remember_email',
          email
        );

      }

    });

  }


  // ==========================================================
  // ¿OLVIDASTE TU CONTRASEÑA?
  // ==========================================================

  if (forgotPassword) {

    forgotPassword.addEventListener('click', (event) => {

      event.preventDefault();

      showError(
        'La recuperación de contraseña estará disponible próximamente.'
      );

    });

  }

});