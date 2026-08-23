<?php
require_once __DIR__ . '/helpers.php';
exigir_post();
require_once __DIR__ . '/../config/database.php';

try {
    $id = (int) dato('id_libro');
    $codigo = dato('codigo');
    $titulo = dato('titulo');
    $autor = (int) dato('id_autor');
    $categoria = (int) dato('id_categoria');
    $cantidad_total = (int) dato('cantidad_total');

    if (!$id || !$codigo || !$titulo || !$autor || !$categoria || $cantidad_total < 1) {
        throw new Exception('Completa el código, título, autor, categoría y cantidad de ejemplares.');
    }

    $consulta = $conexion->prepare('SELECT cantidad_total, cantidad_disponible FROM libros WHERE id_libro = ?');
    $consulta->execute([$id]);
    $actual = $consulta->fetch(PDO::FETCH_ASSOC);
    if (!$actual) {
        throw new Exception('El libro seleccionado no existe.');
    }

    $prestados = max(0, (int) $actual['cantidad_total'] - (int) $actual['cantidad_disponible']);
    if ($cantidad_total < $prestados) {
        throw new Exception('La cantidad total no puede ser menor al número de ejemplares prestados.');
    }

    $sql = 'UPDATE libros SET codigo = ?, titulo = ?, id_autor = ?, id_categoria = ?, editorial = ?, anio_publicacion = ?, isbn = ?, cantidad_total = ?, cantidad_disponible = ?, descripcion = ? WHERE id_libro = ?';
    $conexion->prepare($sql)->execute([$codigo, $titulo, $autor, $categoria, dato('editorial') ?: null, dato('anio_publicacion') ?: null, dato('isbn') ?: null, $cantidad_total, $cantidad_total - $prestados, dato('descripcion') ?: null, $id]);
    registrar_bitacora($conexion, 'ACTUALIZAR LIBRO', 'Se actualizó el libro ' . $titulo, 'libros', $id);
    responder(['success' => true, 'message' => 'Libro actualizado correctamente.']);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 400);
}
