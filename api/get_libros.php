<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/database.php';

try {
    $sql = 'SELECT l.*, a.nombre AS autor_nombre, a.nacionalidad AS autor_nacionalidad, c.nombre AS categoria_nombre FROM libros l LEFT JOIN autores a ON a.id_autor = l.id_autor LEFT JOIN categorias c ON c.id_categoria = l.id_categoria ORDER BY l.fecha_registro DESC, l.id_libro DESC';
    $consulta = $conexion->query($sql);
    responder(['success' => true, 'data' => $consulta->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 500);
}
