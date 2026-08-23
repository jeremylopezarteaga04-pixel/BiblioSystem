# BiblioSystem

Sistema web para gestionar bibliotecas, lectores, libros, préstamos y devoluciones. Está desarrollado con PHP, JavaScript y SQLite.

## Integrantes

- Jeremy López
- Eloy Rojas
- Mario Cueva

## Inicio rápido

La base de datos ya está incluida en `database/bibliosystem.sqlite`. No necesitas MySQL, MariaDB, DevHarbor ni importar archivos SQL.

Desde la carpeta principal ejecuta:

```bash
php -S 0.0.0.0:8000
```

Abre el puerto 8000 en GitHub Codespaces o ingresa a `http://localhost:8000`.

Para confirmar que PHP tiene el controlador necesario:

```bash
php -m | grep -i sqlite
```

Debe aparecer `pdo_sqlite`.

## Funcionalidades

- Dashboard con indicadores y gráficos.
- Registro, búsqueda, edición y eliminación de libros.
- Portadas mediante imagen subida desde la computadora o URL externa.
- Autores y categorías preparados para registrar libros reales.
- Registro y edición de lectores.
- Préstamos y devoluciones con actualización automática del inventario.
- Identificación de préstamos atrasados.
- Historial por lector, bitácora y exportación de reportes a CSV.
- Diseño adaptable a celulares y computadoras.

## Portadas de libros

Al registrar o editar un libro puedes elegir cualquiera de estas opciones:

1. Subir una imagen JPG, PNG, WEBP o GIF desde tu computadora.
2. Pegar la URL completa de una imagen publicada en internet.

Si completas ambas opciones, tiene prioridad la imagen que subiste. Las imágenes locales se guardan en `img/portadas/` y pueden conservarse en GitHub junto con la base de datos.

## Guardar los cambios en GitHub

Para conservar libros nuevos, lectores, préstamos y portadas al crear otro Codespace, guarda los cambios del repositorio:

```bash
git add .
git commit -m "Actualizar biblioteca y registros"
git push
```

La base SQLite no requiere un servidor externo, pero los cambios solo estarán disponibles en otro Codespace después de subirlos a GitHub.

## Estructura

```text
BiblioSystem/
├── api/                         Endpoints PHP
├── config/database.php          Conexión SQLite
├── css/styles.css               Diseño de la interfaz
├── database/bibliosystem.sqlite Base de datos del proyecto
├── database/BiblioSystem_Base_de_Datos.sql
├── img/portadas/                Portadas subidas
├── js/app.js                    Lógica del frontend
├── index.html                   Aplicación principal
└── pruebas.html                 Panel de pruebas
```

## Endpoints

| Método | Archivo | Función |
| --- | --- | --- |
| GET | `api/get_libros.php` | Consultar libros con autores y categorías. |
| GET | `api/get_usuarios.php` | Consultar lectores. |
| GET | `api/get_prestamos.php` | Consultar préstamos y actualizar atrasados. |
| GET | `api/get_categorias.php` | Consultar categorías activas. |
| GET | `api/get_autores.php` | Consultar autores. |
| GET | `api/get_bitacora.php` | Consultar movimientos. |
| POST | `api/registrar_libro.php` | Registrar un libro y su portada. |
| POST | `api/actualizar_libro.php` | Editar un libro y cambiar su portada. |
| POST | `api/eliminar_libro.php` | Eliminar un libro sin préstamos. |
| POST | `api/registrar_usuario.php` | Registrar un lector. |
| POST | `api/actualizar_usuario.php` | Editar un lector. |
| POST | `api/registrar_prestamo.php` | Registrar un préstamo. |
| POST | `api/devolver_prestamo.php` | Registrar una devolución. |
| POST | `api/registrar_categoria.php` | Agregar una categoría. |
| POST | `api/registrar_autor.php` | Agregar un autor.
