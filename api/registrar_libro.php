<?php
require_once __DIR__ . '/helpers.php';
exigir_post();
require_once __DIR__ . '/../config/database.php';

try {
    $codigo = dato('codigo') ?: dato('isbn');
    $titulo = dato('titulo');
    $autor = (int) (dato('id_autor') ?: dato('autor'));
    $categoria = (int) (dato('id_categoria') ?: dato('categoria'));
    $cantidad = (int) (dato('cantidad_total') ?: dato('cantidad_disponible') ?: dato('stock') ?: dato('cantidad'));

    if (!$codigo || !$titulo || !$autor || !$categoria || $cantidad < 1) {
        throw new Exception('Completa el código, título, autor, categoría y una cantidad válida de ejemplares.');
    }

    $sql = 'INSERT INTO libros (codigo, titulo, id_autor, id_categoria, editorial, anio_publicacion, isbn, cantidad_total, cantidad_disponible, descripcion, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
    $conexion->prepare($sql)->execute([$codigo, $titulo, $autor, $categoria, dato('editorial') ?: null, dato('anio_publicacion') ?: null, dato('isbn') ?: null, $cantidad, $cantidad, dato('descripcion') ?: null]);
    $id = (int) $conexion->lastInsertId();
    registrar_bitacora($conexion, 'REGISTRAR LIBRO', 'Se registró el libro ' . $titulo . ' con ' . $cantidad . ' ejemplares', 'libros', $id);
    responder(['success' => true, 'message' => 'Libro registrado exitosamente.', 'id' => $id]);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 400);
}
