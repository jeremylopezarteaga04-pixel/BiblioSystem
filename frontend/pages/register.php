<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Crear cuenta | BiblioSystem</title>

  <!-- Fuentes -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap"
    rel="stylesheet"
  >

  <!-- CSS general -->
  <link rel="stylesheet" href="../css/styles.css">

  <!-- CSS exclusivo del registro -->
  <link rel="stylesheet" href="../css/register.css">
</head>

<body class="register-page">

  <main class="register-layout">

    <!-- =========================================================
         PANEL IZQUIERDO
         ========================================================= -->
    <section class="register-visual">

      <!-- Logo -->
      <a href="../index.html" class="register-brand">
        <span class="register-brand-mark">B</span>

        <span class="register-brand-text">
          <strong>BiblioSystem</strong>
          <small>Biblioteca digital</small>
        </span>
      </a>

      <!-- Contenido -->
      <div class="register-visual-content">

        <span class="register-eyebrow">
          ÚNETE A LA COMUNIDAD LECTORA
        </span>

        <h1>
          Una cuenta,<br>
          infinitas historias.
        </h1>

        <p>
          Crea tu cuenta en BiblioSystem y disfruta de una experiencia
          más sencilla para consultar tus libros y administrar tus préstamos.
        </p>

        <!-- Libros decorativos -->
        <div class="register-books" aria-hidden="true">

          <div class="register-book register-book-one">
          </div>

          <div class="register-book register-book-two">
          </div>

          <div class="register-book register-book-three">
          </div>

        </div>

        <!-- Frase inferior -->
        <div class="register-visual-footer">
          <span>LECTURA SIN LÍMITES</span>
          <span>•</span>
          <span>CONOCIMIENTO QUE SE COMPARTE</span>
        </div>

      </div>

    </section>


    <!-- =========================================================
         PANEL DERECHO
         ========================================================= -->
    <section class="register-form-panel">

      <div class="register-form-wrapper">

        <!-- Tarjeta -->
        <div class="register-card">

          <!-- Cabecera -->
          <div class="register-card-header">

            <span class="register-card-eyebrow">
              CREA TU CUENTA
            </span>

            <h2>Registrarse</h2>

            <p>
              Completa tus datos para crear una cuenta.
            </p>

          </div>


          <!-- =====================================================
               FORMULARIO
               ===================================================== -->
          <form id="register-form" class="register-form">

            <!-- Nombres + Apellidos -->
            <div class="register-form-row">

              <div class="register-field">
                <label for="nombres">
                  Nombres
                </label>

                <div class="register-input-wrapper">
                  <span class="register-input-icon">A</span>

                  <input
                    type="text"
                    id="nombres"
                    name="nombres"
                    placeholder="Tus nombres"
                    autocomplete="given-name"
                    required
                  >
                </div>
              </div>


              <div class="register-field">
                <label for="apellidos">
                  Apellidos
                </label>

                <div class="register-input-wrapper">
                  <span class="register-input-icon">A</span>

                  <input
                    type="text"
                    id="apellidos"
                    name="apellidos"
                    placeholder="Tus apellidos"
                    autocomplete="family-name"
                    required
                  >
                </div>
              </div>

            </div>


            <!-- Cédula -->
            <div class="register-field">

              <label for="cedula">
                Cédula
              </label>

              <div class="register-input-wrapper">
                <span class="register-input-icon">#</span>

                <input
                  type="text"
                  id="cedula"
                  name="cedula"
                  placeholder="Número de cédula"
                  inputmode="numeric"
                  autocomplete="off"
                  required
                >
              </div>

            </div>


            <!-- Correo -->
            <div class="register-field">

              <label for="correo">
                Correo electrónico
              </label>

              <div class="register-input-wrapper">
                <span class="register-input-icon">@</span>

                <input
                  type="email"
                  id="correo"
                  name="correo"
                  placeholder="correo@ejemplo.com"
                  autocomplete="email"
                  required
                >
              </div>

            </div>


            <!-- Teléfono -->
            <div class="register-field">

              <label for="telefono">
                Teléfono
                <span class="optional">(opcional)</span>
              </label>

              <div class="register-input-wrapper">
                <span class="register-input-icon">☎</span>

                <input
                  type="tel"
                  id="telefono"
                  name="telefono"
                  placeholder="Número de teléfono"
                  autocomplete="tel"
                >
              </div>

            </div>


            <!-- Dirección -->
            <div class="register-field">

              <label for="direccion">
                Dirección
                <span class="optional">(opcional)</span>
              </label>

              <div class="register-input-wrapper">
                <span class="register-input-icon">⌂</span>

                <input
                  type="text"
                  id="direccion"
                  name="direccion"
                  placeholder="Dirección de residencia"
                  autocomplete="street-address"
                >
              </div>

            </div>


            <!-- Contraseña -->
            <div class="register-field">

              <label for="password">
                Contraseña
              </label>

              <div class="register-input-wrapper">

                <span class="register-input-icon">•</span>

                <input
                  type="password"
                  id="password"
                  name="password"
                  placeholder="Crea una contraseña"
                  minlength="6"
                  autocomplete="new-password"
                  required
                >

                <button
                  type="button"
                  class="password-toggle"
                  data-target="password"
                  aria-label="Mostrar contraseña"
                >
                  ◉
                </button>

              </div>

              <small class="register-help">
                La contraseña debe tener al menos 6 caracteres.
              </small>

            </div>


            <!-- Confirmar contraseña -->
            <div class="register-field">

              <label for="confirm-password">
                Confirmar contraseña
              </label>

              <div class="register-input-wrapper">

                <span class="register-input-icon">•</span>

                <input
                  type="password"
                  id="confirm-password"
                  name="confirm_password"
                  placeholder="Repite tu contraseña"
                  minlength="6"
                  autocomplete="new-password"
                  required
                >

                <button
                  type="button"
                  class="password-toggle"
                  data-target="confirm-password"
                  aria-label="Mostrar contraseña"
                >
                  ◉
                </button>

              </div>

            </div>


            <!-- Términos -->
            <label class="register-terms">

              <input
                type="checkbox"
                id="terms"
                name="terms"
                required
              >

              <span>
                Acepto los
                <a href="#" onclick="return false;">
                  términos y condiciones
                </a>
                y la política de privacidad de BiblioSystem.
              </span>

            </label>


            <!-- Botón -->
            <button
              type="submit"
              class="register-submit"
            >
              Crear cuenta
              <span>→</span>
            </button>


            <!-- Separador -->
            <div class="register-divider">
              <span>o</span>
            </div>


            <!-- Login -->
            <p class="register-login">
              ¿Ya tienes una cuenta?
              <a href="login.php">
                Iniciar sesión
              </a>
            </p>

          </form>

        </div>


        <!-- Footer -->
        <div class="register-footer">
          <span>© 2026 BiblioSystem</span>
          <span>Sistema de gestión bibliotecaria</span>
        </div>

      </div>

    </section>

  </main>


  <!-- JS de autenticación -->
  <script src="../js/register.js"></script>

</body>
</html>