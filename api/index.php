<?php
require_once __DIR__ . '/helpers.php';

responder([
    'success' => true,
    'message' => 'Bienvenido a la API de BiblioSystem',
    'version' => '2.0',
    'endpoints_disponibles' => [
        'GET' => [
            'libros' => 'get_libros.php',
            'usuarios' => 'get_usuarios.php',
            'prestamos' => 'get_prestamos.php',
            'categorias' => 'get_categorias.php',
            'autores' => 'get_autores.php',
            'bitacora' => 'get_bitacora.php',
        ],
        'POST' => [
            'registrar_libro' => 'registrar_libro.php',
            'actualizar_libro' => 'actualizar_libro.php',
            'eliminar_libro' => 'eliminar_libro.php',
            'registrar_usuario' => 'registrar_usuario.php',
            'actualizar_usuario' => 'actualizar_usuario.php',
            'registrar_prestamo' => 'registrar_prestamo.php',
            'devolver_prestamo' => 'devolver_prestamo.php',
            'registrar_categoria' => 'registrar_categoria.php',
            'registrar_autor' => 'registrar_autor.php',
        ],
    ],
]);
