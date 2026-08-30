<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar contraseña | BiblioSystem</title>
  <link rel="stylesheet" href="../css/forgot-password.css">
</head>
<body>
  <main class="auth-page">
    <section class="auth-showcase">
      <div class="auth-brand">
        <span class="auth-brand-mark">B</span>
        <div>
          <strong>BiblioSystem</strong>
          <small>Biblioteca digital</small>
        </div>
      </div>

      <div class="auth-showcase-content">
        <span class="auth-eyebrow">BIBLIOTECA DIGITAL</span>
        <h1>Recupera el acceso a tu cuenta.</h1>
        <p>Verifica tus datos y establece una nueva contraseña para continuar disfrutando de BiblioSystem.</p>
      </div>

      <div class="auth-books">
        <span class="book book-one"></span>
        <span class="book book-two"></span>
        <span class="book book-three"></span>
      </div>
    </section>

    <section class="auth-panel">
      <div class="auth-card">
        <div class="auth-card-header">
          <span class="auth-eyebrow">RECUPERACIÓN DE CUENTA</span>
          <h2 id="form-title">Recuperar contraseña</h2>
          <p id="form-description">Ingresa el correo asociado a tu cuenta.</p>
        </div>

        <div id="message" class="auth-message hidden"></div>

        <form id="forgot-form">

          <div id="step-email">
            <div class="auth-field">
              <label for="correo">Correo electrónico</label>

              <div class="auth-input">
                <span>@</span>

                <input
                  type="email"
                  id="correo"
                  name="correo"
                  placeholder="correo@ejemplo.com"
                  required
                >
              </div>
            </div>

            <button type="submit" class="auth-button">
              Continuar
              <span>→</span>
            </button>
          </div>

          <div id="step-cedula" class="hidden">
            <div class="auth-field">
              <label for="ultimos_digitos">
                Últimos 4 dígitos de tu cédula
              </label>

              <div class="auth-input">
                <span>•</span>

                <input
                  type="text"
                  id="ultimos_digitos"
                  name="ultimos_digitos"
                  maxlength="4"
                  minlength="4"
                  inputmode="numeric"
                  pattern="[0-9]{4}"
                  placeholder="0000"
                >
              </div>
            </div>

            <button type="button" id="verify-button" class="auth-button">
              Verificar identidad
              <span>→</span>
            </button>
          </div>

          <div id="step-password" class="hidden">

            <div class="auth-field">
              <label for="nueva_password">Nueva contraseña</label>

              <div class="auth-input">
                <span>•</span>

                <input
                  type="password"
                  id="nueva_password"
                  name="nueva_password"
                  placeholder="Ingresa tu nueva contraseña"
                  minlength="6"
                >

                <button
                  type="button"
                  class="toggle-password"
                  data-target="nueva_password"
                >
                  ◉
                </button>
              </div>
            </div>

            <div class="auth-field">
              <label for="confirmar_password">
                Confirmar contraseña
              </label>

              <div class="auth-input">
                <span>•</span>

                <input
                  type="password"
                  id="confirmar_password"
                  name="confirmar_password"
                  placeholder="Repite tu nueva contraseña"
                  minlength="6"
                >

                <button
                  type="button"
                  class="toggle-password"
                  data-target="confirmar_password"
                >
                  ◉
                </button>
              </div>
            </div>

            <button type="button" id="change-button" class="auth-button">
              Cambiar contraseña
              <span>→</span>
            </button>

          </div>

          <div id="step-success" class="hidden">

            <div class="auth-success">
              <div class="auth-success-icon">✓</div>

              <h3>Contraseña actualizada</h3>

              <p>
                Tu contraseña se cambió correctamente.
                Ya puedes iniciar sesión con tu nueva contraseña.
              </p>
            </div>

            <a href="login.php" class="auth-button auth-button-link">
              Volver a iniciar sesión
              <span>→</span>
            </a>

          </div>

        </form>

        <div class="auth-footer">
          <span>¿Recordaste tu contraseña?</span>
          <a href="login.php">
            Volver al inicio de sesión
          </a>
        </div>

      </div>

      <p class="auth-copyright">© 2026 BiblioSystem</p>
    </section>
  </main>

  <script src="../js/forgot-password.js"></script>
</body>
</html>