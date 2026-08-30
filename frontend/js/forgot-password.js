'use strict';

let usuarioVerificado = null;

const API_RECUPERACION = '../../api/recuperar_password.php';

const form = document.getElementById('forgot-form');
const message = document.getElementById('message');

const stepEmail = document.getElementById('step-email');
const stepCedula = document.getElementById('step-cedula');
const stepPassword = document.getElementById('step-password');
const stepSuccess = document.getElementById('step-success');

const formTitle = document.getElementById('form-title');
const formDescription = document.getElementById('form-description');


// ============================================================
// MOSTRAR / OCULTAR CONTRASEÑA
// ============================================================

document.querySelectorAll('.toggle-password').forEach((button) => {

  button.addEventListener('click', () => {

    const input = document.getElementById(
      button.dataset.target
    );

    if (!input) return;

    input.type =
      input.type === 'password'
        ? 'text'
        : 'password';

  });

});


// ============================================================
// MOSTRAR MENSAJE
// ============================================================

function mostrarMensaje(texto, error = true) {

  message.textContent = texto;

  message.className =
    `auth-message${error ? ' error' : ' success'}`;

}


// ============================================================
// OCULTAR MENSAJE
// ============================================================

function ocultarMensaje() {

  message.textContent = '';

  message.className =
    'auth-message hidden';

}


// ============================================================
// CAMBIAR PASO
// ============================================================

function mostrarPaso(paso) {

  stepEmail.classList.add('hidden');
  stepCedula.classList.add('hidden');
  stepPassword.classList.add('hidden');
  stepSuccess.classList.add('hidden');


  if (paso === 'email') {

    stepEmail.classList.remove('hidden');

    formTitle.textContent =
      'Recuperar contraseña';

    formDescription.textContent =
      'Ingresa el correo asociado a tu cuenta.';

  }


  if (paso === 'cedula') {

    stepCedula.classList.remove('hidden');

    formTitle.textContent =
      'Verificar identidad';

    formDescription.textContent =
      'Ingresa los últimos 4 dígitos de tu cédula.';

  }


  if (paso === 'password') {

    stepPassword.classList.remove('hidden');

    formTitle.textContent =
      'Nueva contraseña';

    formDescription.textContent =
      'Crea una nueva contraseña para tu cuenta.';

  }


  if (paso === 'success') {

    stepSuccess.classList.remove('hidden');

    formTitle.textContent =
      'Todo listo';

    formDescription.textContent =
      'Tu cuenta ya está protegida con tu nueva contraseña.';

  }

}


// ============================================================
// PASO 1 - VERIFICAR CORREO
// ============================================================

form.addEventListener('submit', async (event) => {

  event.preventDefault();

  ocultarMensaje();

  const correo =
    document.getElementById('correo').value.trim();


  if (!correo) {

    mostrarMensaje(
      'Ingresa tu correo electrónico.'
    );

    return;
  }


  try {

    const body = new FormData();

    body.append(
      'accion',
      'verificar_correo'
    );

    body.append(
      'correo',
      correo
    );


    const response = await fetch(
      API_RECUPERACION,
      {
        method: 'POST',
        body
      }
    );


    const data =
      await response.json();


    if (
      !response.ok ||
      data.success === false
    ) {

      throw new Error(
        data.message ||
        'No se encontró un usuario con ese correo.'
      );

    }


    usuarioVerificado =
      data.data;


    mostrarPaso('cedula');


  } catch (error) {

    mostrarMensaje(
      error.message
    );

  }

});


// ============================================================
// PASO 2 - VERIFICAR CÉDULA
// ============================================================

document
  .getElementById('verify-button')
  .addEventListener('click', async () => {

    ocultarMensaje();

    const digitos =
      document
        .getElementById('ultimos_digitos')
        .value
        .trim();


    if (!/^[0-9]{4}$/.test(digitos)) {

      mostrarMensaje(
        'Ingresa exactamente los últimos 4 dígitos de tu cédula.'
      );

      return;
    }


    if (!usuarioVerificado) {

      mostrarMensaje(
        'Primero debes ingresar y verificar tu correo.'
      );

      mostrarPaso('email');

      return;
    }


    try {

      const body = new FormData();

      body.append(
        'accion',
        'verificar_cedula'
      );

      body.append(
        'id_usuario',
        usuarioVerificado.id_usuario
      );

      body.append(
        'ultimos_digitos',
        digitos
      );


      const response = await fetch(
        API_RECUPERACION,
        {
          method: 'POST',
          body
        }
      );


      const data =
        await response.json();


      if (
        !response.ok ||
        data.success === false
      ) {

        throw new Error(
          data.message ||
          'Los datos de verificación no son correctos.'
        );

      }


      mostrarPaso('password');


    } catch (error) {

      mostrarMensaje(
        error.message
      );

    }

  });


// ============================================================
// PASO 3 - CAMBIAR CONTRASEÑA
// ============================================================

document
  .getElementById('change-button')
  .addEventListener('click', async () => {

    ocultarMensaje();

    const password =
      document
        .getElementById('nueva_password')
        .value;

    const confirmar =
      document
        .getElementById('confirmar_password')
        .value;


    if (password.length < 6) {

      mostrarMensaje(
        'La contraseña debe tener al menos 6 caracteres.'
      );

      return;
    }


    if (password !== confirmar) {

      mostrarMensaje(
        'Las contraseñas no coinciden.'
      );

      return;
    }


    if (!usuarioVerificado) {

      mostrarMensaje(
        'No se pudo identificar al usuario.'
      );

      mostrarPaso('email');

      return;
    }


    try {

      const body = new FormData();

      body.append(
        'accion',
        'cambiar_password'
      );

      body.append(
        'id_usuario',
        usuarioVerificado.id_usuario
      );

      body.append(
        'nueva_password',
        password
      );


      const response = await fetch(
        API_RECUPERACION,
        {
          method: 'POST',
          body
        }
      );


      const data =
        await response.json();


      if (
        !response.ok ||
        data.success === false
      ) {

        throw new Error(
          data.message ||
          'No se pudo cambiar la contraseña.'
        );

      }


      mostrarPaso('success');


    } catch (error) {

      mostrarMensaje(
        error.message
      );

    }

  });