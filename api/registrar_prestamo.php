<?php
require_once __DIR__ . '/helpers.php';
exigir_post();
require_once __DIR__ . '/../config/database.php';

try {
    $cedula = dato('cedula_usuario');
    $codigo = dato('codigo_libro');
    $fecha_limite = dato('fecha_limite');

    if (!$cedula || !$codigo || !$fecha_limite) {
        throw new Exception('Selecciona un lector, un libro y la fecha límite de devolución.');
    }
    $fecha = DateTime::createFromFormat('!Y-m-d', $fecha_limite);
    if (!$fecha || $fecha->format('Y-m-d') !== $fecha_limite || $fecha_limite < date('Y-m-d')) {
        throw new Exception('La fecha de devolución debe ser válida y no puede estar en el pasado.');
    }

    $conexion->beginTransaction();
    $consulta_usuario = $conexion->prepare("SELECT id_usuario, nombres, apellidos FROM usuarios WHERE cedula = ? AND estado = 'ACTIVO'");
    $consulta_usuario->execute([$cedula]);
    $usuario = $consulta_usuario->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        throw new Exception('El lector seleccionado no existe o no está activo.');
    }

    $consulta_libro = $conexion->prepare("SELECT id_libro, titulo, cantidad_disponible FROM libros WHERE codigo = ? AND estado = 'ACTIVO' FOR UPDATE");
    $consulta_libro->execute([$codigo]);
    $libro = $consulta_libro->fetch(PDO::FETCH_ASSOC);
    if (!$libro) {
        throw new Exception('El libro seleccionado no existe o no está activo.');
    }
    if ((int) $libro['cantidad_disponible'] < 1) {
        throw new Exception('No hay ejemplares disponibles para prestar este libro.');
    }

    $sql = "INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion_programada, estado, observacion, fecha_registro) VALUES (?, ?, CURDATE(), ?, 'ACTIVO', ?, NOW())";
    $conexion->prepare($sql)->execute([$usuario['id_usuario'], $libro['id_libro'], $fecha_limite, dato('observacion') ?: null]);
    $id = (int) $conexion->lastInsertId();
    $conexion->prepare('UPDATE libros SET cantidad_disponible = cantidad_disponible - 1 WHERE id_libro = ?')->execute([$libro['id_libro']]);
    $conexion->commit();
    registrar_bitacora($conexion, 'REGISTRAR PRÉSTAMO', 'Se prestó ' . $libro['titulo'] . ' a ' . $usuario['nombres'] . ' ' . $usuario['apellidos'], 'prestamos', $id);
    responder(['success' => true, 'message' => 'Préstamo registrado. La disponibilidad del libro fue actualizada.', 'id' => $id]);
} catch (Throwable $error) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder(['success' => false, 'message' => mensaje_error($error)], 400);
}
