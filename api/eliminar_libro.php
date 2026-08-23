<?php
require_once __DIR__ . '/helpers.php';
exigir_post();
require_once __DIR__ . '/../config/database.php';

try {
    $id = (int) dato('id_libro');
    if (!$id) {
        throw new Exception('Selecciona un libro válido.');
    }

    $consulta = $conexion->prepare('SELECT titulo FROM libros WHERE id_libro = ?');
    $consulta->execute([$id]);
    $libro = $consulta->fetch(PDO::FETCH_ASSOC);
    if (!$libro) {
        throw new Exception('El libro seleccionado no existe.');
    }

    $prestamos = $conexion->prepare('SELECT COUNT(*) FROM prestamos WHERE id_libro = ?');
    $prestamos->execute([$id]);
    if ((int) $prestamos->fetchColumn() > 0) {
        throw new Exception('No puedes eliminar un libro con préstamos registrados porque se perdería el historial.');
    }

    $conexion->prepare('DELETE FROM libros WHERE id_libro = ?')->execute([$id]);
    registrar_bitacora($conexion, 'ELIMINAR LIBRO', 'Se eliminó el libro ' . $libro['titulo'], 'libros', $id);
    responder(['success' => true, 'message' => 'Libro eliminado correctamente.']);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 400);
}
