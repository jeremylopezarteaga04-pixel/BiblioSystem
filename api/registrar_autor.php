<?php
require_once __DIR__ . '/helpers.php';
exigir_post();
require_once __DIR__ . '/../config/database.php';

try {
    $nombre = dato('nombre');
    if (!$nombre) {
        throw new Exception('El nombre del autor es obligatorio.');
    }

    $conexion->prepare('INSERT INTO autores (nombre, nacionalidad, fecha_nacimiento) VALUES (?, ?, ?)')->execute([$nombre, dato('nacionalidad') ?: null, dato('fecha_nacimiento') ?: null]);
    $id = (int) $conexion->lastInsertId();
    registrar_bitacora($conexion, 'REGISTRAR AUTOR', 'Se registró el autor ' . $nombre, 'autores', $id);
    responder(['success' => true, 'message' => 'Autor registrado correctamente.', 'id' => $id]);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 400);
}
