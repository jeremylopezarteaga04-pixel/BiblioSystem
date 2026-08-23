<?php
require_once __DIR__ . '/helpers.php';
exigir_post();
require_once __DIR__ . '/../config/database.php';

try {
    $id = (int) dato('id_prestamo');
    if (!$id) {
        throw new Exception('Selecciona un préstamo válido.');
    }

    $conexion->beginTransaction();
    $consulta = $conexion->prepare('SELECT p.*, l.titulo FROM prestamos p INNER JOIN libros l ON l.id_libro = p.id_libro WHERE p.id_prestamo = ? FOR UPDATE');
    $consulta->execute([$id]);
    $prestamo = $consulta->fetch(PDO::FETCH_ASSOC);
    if (!$prestamo) {
        throw new Exception('El préstamo seleccionado no existe.');
    }
    if ($prestamo['estado'] === 'DEVUELTO') {
        throw new Exception('Este préstamo ya fue devuelto anteriormente.');
    }

    $conexion->prepare("UPDATE prestamos SET estado = 'DEVUELTO', fecha_devolucion_real = CURDATE() WHERE id_prestamo = ?")->execute([$id]);
    $conexion->prepare('UPDATE libros SET cantidad_total = GREATEST(cantidad_total, cantidad_disponible + 1), cantidad_disponible = cantidad_disponible + 1 WHERE id_libro = ?')->execute([$prestamo['id_libro']]);
    $conexion->commit();
    registrar_bitacora($conexion, 'DEVOLVER PRÉSTAMO', 'Se recibió la devolución de ' . $prestamo['titulo'], 'prestamos', $id);
    responder(['success' => true, 'message' => 'Devolución registrada. El ejemplar vuelve a estar disponible.']);
} catch (Throwable $error) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    responder(['success' => false, 'message' => mensaje_error($error)], 400);
}
