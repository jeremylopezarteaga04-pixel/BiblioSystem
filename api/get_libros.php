<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/database.php';

try {
    $conexion->exec("UPDATE prestamos SET estado = 'ATRASADO' WHERE estado = 'ACTIVO' AND fecha_devolucion_programada < date('now', 'localtime')");
    $sql = 'SELECT p.*, u.nombres, u.apellidos, u.cedula, u.correo, l.titulo AS titulo_libro, l.codigo AS codigo_libro FROM prestamos p LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario LEFT JOIN libros l ON p.id_libro = l.id_libro ORDER BY p.fecha_registro DESC, p.id_prestamo DESC';
    $consulta = $conexion->query($sql);
    responder(['success' => true, 'data' => $consulta->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 500);
}
