# BiblioSystem

Sistema web para la gestión de bibliotecas y préstamo de libros, desarrollado con PHP, JavaScript y MySQL.

## Integrantes

- Jeremy López
- Eloy Rojas
- Mario Cueva

## Funcionalidades

- Dashboard con indicadores y gráficos calculados con datos reales.
- Registro, consulta, búsqueda, edición y eliminación de libros.
- Administración de autores y categorías.
- Registro, consulta y actualización de lectores.
- Registro de préstamos con actualización automática del inventario.
- Registro de devoluciones con reintegro automático del ejemplar.
- Identificación automática de préstamos atrasados.
- Consulta del historial de préstamos por lector.
- Reportes de actividad, libros solicitados y disponibilidad.
- Exportación del catálogo y sus indicadores a CSV.
- Bitácora de operaciones.
- Interfaz adaptable a computadoras y celulares.

## Tecnologías

- Backend: PHP con PDO y consultas preparadas.
- Frontend: HTML, CSS y JavaScript.
- Base de datos: MySQL o MariaDB.
- Arquitectura: cliente-servidor con endpoints JSON.

## Instalación

1. Descarga o clona este repositorio.
2. Importa `database/BiblioSystem_Base_de_Datos.sql` en MySQL.
3. Revisa los datos de acceso en `config/database.php`.
4. Ejecuta el proyecto desde la carpeta principal:

```bash
php -S 0.0.0.0:8000
```

5. Abre `http://localhost:8000` o la URL del puerto 8000 si utilizas GitHub Codespaces.

### Configuración mediante variables de entorno

La conexión también acepta estas variables: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` y `DB_PASSWORD`.

Ejemplo para un servidor local:

```bash
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_NAME=bibliosystem
export DB_USER=bibliosystem
export DB_PASSWORD=tu_clave
php -S 0.0.0.0:8000
```

## Estructura

```text
BiblioSystem/
├── api/          Endpoints del backend
├── config/       Conexión con la base de datos
├── css/          Estilos de la interfaz
├── database/     Respaldo SQL
├── js/           Lógica del frontend
├── pages/        Carpeta para futuras pantallas
├── index.html    Aplicación principal
└── pruebas.html  Panel original de pruebas
```

## Endpoints principales

| Método | Archivo | Función |
| --- | --- | --- |
| GET | `api/get_libros.php` | Consultar libros, autores y categorías. |
| GET | `api/get_usuarios.php` | Consultar lectores registrados. |
| GET | `api/get_prestamos.php` | Consultar préstamos y actualizar atrasados. |
| GET | `api/get_categorias.php` | Consultar categorías activas. |
| GET | `api/get_autores.php` | Consultar autores registrados. |
| GET | `api/get_bitacora.php` | Consultar movimientos del sistema. |
| POST | `api/registrar_libro.php` | Registrar un libro y su inventario. |
| POST | `api/actualizar_libro.php` | Editar los datos de un libro. |
| POST | `api/eliminar_libro.php` | Eliminar un libro sin préstamos. |
| POST | `api/registrar_usuario.php` | Registrar un lector. |
| POST | `api/actualizar_usuario.php` | Editar un lector. |
| POST | `api/registrar_prestamo.php` | Prestar un ejemplar. |
| POST | `api/devolver_prestamo.php` | Registrar una devolución. |
| POST | `api/registrar_categoria.php` | Registrar una categoría. |
| POST | `api/registrar_autor.php` | Registrar un autor. |

## Nota sobre GitHub Codespaces

El entorno debe tener PHP, la extensión `pdo_mysql` y acceso a una instancia de MySQL. Si aparece `could not find driver`, instala o habilita `php-mysql`. Si aparece `Access denied`, revisa el usuario y la contraseña configurados para MySQL.
