<?php
require_once __DIR__ . '/helpers.php';
exigir_post();
require_once __DIR__ . '/../config/database.php';

try {
    $nombre = dato('nombre');
    if (!$nombre) {
        throw new Exception('El nombre de la categoría es obligatorio.');
    }

    $conexion->prepare("INSERT INTO categorias (nombre, descripcion, estado) VALUES (?, ?, 'ACTIVO')")->execute([$nombre, dato('descripcion') ?: null]);
    $id = (int) $conexion->lastInsertId();
    registrar_bitacora($conexion, 'REGISTRAR CATEGORÍA', 'Se registró la categoría ' . $nombre, 'categorias', $id);
    responder(['success' => true, 'message' => 'Categoría registrada correctamente.', 'id' => $id]);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 400);
}
