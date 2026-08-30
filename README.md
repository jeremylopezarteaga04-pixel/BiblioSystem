# BiblioSystem

Sistema web para gestionar bibliotecas, lectores, libros, préstamos y devoluciones, con inicio de sesión y roles diferenciados (Administrador / Usuario). Desarrollado con PHP, JavaScript y SQLite.

## Integrantes

- Jeremy López
- Eloy Rojas
- Mario Cueva

## Requisitos previos

- PHP 8+ con la extensión `pdo_sqlite` habilitada.
- No se necesita instalar MySQL, MariaDB ni ningún servidor externo — la base de datos SQLite ya viene incluida en el repositorio.

## Inicio rápido

La base de datos ya está incluida en `database/bibliosystem.sqlite`, con categorías, autores y dos cuentas de administrador de ejemplo precargadas.

Desde la carpeta principal del proyecto, ejecuta:

```bash
php -S 0.0.0.0:8000
```

Abre el puerto 8000 en GitHub Codespaces (pestaña "Puertos") o ingresa a `http://localhost:8000` si lo corres en tu computadora.

Para confirmar que PHP tiene el controlador necesario:

```bash
php -m | grep -i sqlite
```

Debe aparecer `pdo_sqlite` en el resultado.

### Regenerar la base de datos (si hace falta)

Si el archivo `database/bibliosystem.sqlite` no existe o está dañado, se puede regenerar desde el script SQL incluido:

```bash
python3 -c "import sqlite3,pathlib; c=sqlite3.connect('database/bibliosystem.sqlite'); c.executescript(pathlib.Path('database/BiblioSystem_Base_de_Datos.sql').read_text(encoding='utf-8')); c.commit(); c.close(); print('Base SQLite creada correctamente.')"
```

## Cuentas de prueba

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | admin@bibliosystem.com | _(completar)_ |
| Administrador | admin2@bibliosystem.com | _(completar)_ |

También se puede crear una cuenta nueva desde la pantalla de registro (`frontend/pages/register.php`), que queda con rol `USUARIO` por defecto.

## Funcionalidades

- Inicio de sesión con roles (Administrador / Usuario) y recuperación de contraseña verificando correo + últimos 4 dígitos de la cédula.
- Dashboard con indicadores y gráficos, adaptado según el rol de la cuenta.
- Registro, búsqueda, edición y eliminación de libros, con portada por imagen subida o URL externa.
- Autores y categorías para organizar el catálogo.
- Registro y edición de lectores.
- Préstamos y devoluciones con actualización automática del inventario disponible (operación transaccional).
- Identificación automática de préstamos atrasados.
- Historial por lector, bitácora de auditoría y exportación de reportes a CSV.
- Diseño adaptable a celulares y computadoras.

## Portadas de libros

Al registrar o editar un libro se puede elegir cualquiera de estas opciones:

1. Subir una imagen JPG, PNG, WEBP o GIF desde la computadora (máximo 5 MB).
2. Pegar la URL completa de una imagen publicada en internet.

Si se completan ambas opciones, tiene prioridad la imagen subida. Las imágenes locales se guardan en `img/portadas/` y se conservan en el repositorio junto con la base de datos.

## Guardar los cambios en GitHub

Para conservar libros nuevos, lectores, préstamos y portadas al crear otro Codespace, hay que subir los cambios al repositorio:

```bash
git add .
git commit -m "Actualizar biblioteca y registros"
git push
```

La base SQLite no requiere un servidor externo, pero los cambios solo estarán disponibles en otro Codespace después de subirlos a GitHub.

## Estructura del proyecto

```text
BiblioSystem/
├── api/                              Endpoints PHP (backend)
│   ├── login.php, logout.php, recuperar_password.php
│   ├── registrar_usuario.php, registrar_libro.php, registrar_prestamo.php, ...
│   └── get_usuarios.php, get_libros.php, get_prestamos.php, ...
├── config/database.php               Conexión a SQLite
├── database/
│   ├── bibliosystem.sqlite           Base de datos del proyecto
│   └── BiblioSystem_Base_de_Datos.sql  Script para regenerarla
├── frontend/
│   ├── css/                          Estilos (interfaz principal y auth)
│   ├── js/                           Lógica del frontend (app.js, auth.js, register.js, forgot-password.js)
│   ├── index.php                     Redirige a la pantalla de login
│   └── pages/
│       ├── login.php, register.php, forgot-password.php
│       ├── admin/dashboard.php       Panel para rol ADMIN
│       └── user/dashboard.php        Panel para rol USUARIO
├── img/portadas/                     Portadas de libros subidas
└── index.html                        Redirige a frontend/
```

## Endpoints principales

| Método | Archivo | Función |
| --- | --- | --- |
| POST | `api/login.php` | Iniciar sesión. |
| POST | `api/logout.php` | Cerrar sesión. |
| POST | `api/recuperar_password.php` | Recuperar contraseña (verificar correo, verificar cédula, cambiar contraseña). |
| GET | `api/get_libros.php` | Consultar libros con autores y categorías. |
| GET | `api/get_usuarios.php` | Consultar lectores. |
| GET | `api/get_prestamos.php` | Consultar préstamos y actualizar atrasados. |
| GET | `api/get_categorias.php` | Consultar categorías activas. |
| GET | `api/get_autores.php` | Consultar autores. |
| GET | `api/get_bitacora.php` | Consultar movimientos registrados. |
| POST | `api/registrar_libro.php` | Registrar un libro y su portada. |
| POST | `api/actualizar_libro.php` | Editar un libro y cambiar su portada. |
| POST | `api/eliminar_libro.php` | Eliminar un libro sin préstamos activos. |
| POST | `api/registrar_usuario.php` | Registrar un lector (y su cuenta, si aplica). |
| POST | `api/actualizar_usuario.php` | Editar un lector. |
| POST | `api/registrar_prestamo.php` | Registrar un préstamo (valida disponibilidad y descuenta inventario). |
| POST | `api/devolver_prestamo.php` | Registrar una devolución (repone inventario). |
| POST | `api/registrar_categoria.php` | Agregar una categoría. |
| POST | `api/registrar_autor.php` | Agregar un autor. |
