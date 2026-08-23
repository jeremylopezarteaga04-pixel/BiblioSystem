<?php
require_once __DIR__ . '/helpers.php';
exigir_post();
require_once __DIR__ . '/../config/database.php';

try {
    $id = (int) dato('id_usuario');
    $cedula = dato('cedula');
    $nombres = dato('nombres');
    $apellidos = dato('apellidos');
    $correo = dato('correo');
    if (!$id || !$cedula || !$nombres || !$apellidos || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ingresa cédula, nombres, apellidos y un correo electrónico válido.');
    }

    $sql = 'UPDATE usuarios SET cedula = ?, nombres = ?, apellidos = ?, correo = ?, telefono = ?, direccion = ? WHERE id_usuario = ?';
    $conexion->prepare($sql)->execute([$cedula, $nombres, $apellidos, $correo, dato('telefono') ?: null, dato('direccion') ?: null, $id]);
    registrar_bitacora($conexion, 'ACTUALIZAR USUARIO', 'Se actualizó el lector ' . $nombres . ' ' . $apellidos, 'usuarios', $id);
    responder(['success' => true, 'message' => 'Lector actualizado correctamente.']);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 400);
}
