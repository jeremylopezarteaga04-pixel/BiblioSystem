<?php
require_once __DIR__ . '/helpers.php';
exigir_post();
require_once __DIR__ . '/../config/database.php';

try {
    $cedula = dato('cedula');
    $nombres = dato('nombres');
    $apellidos = dato('apellidos');
    $correo = dato('correo');

    if (!$cedula || !$nombres || !$apellidos || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ingresa cédula, nombres, apellidos y un correo electrónico válido.');
    }

    $sql = 'INSERT INTO usuarios (cedula, nombres, apellidos, correo, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?)';
    $conexion->prepare($sql)->execute([$cedula, $nombres, $apellidos, $correo, dato('telefono') ?: null, dato('direccion') ?: null]);
    $id = (int) $conexion->lastInsertId();
    registrar_bitacora($conexion, 'REGISTRAR USUARIO', 'Se registró el lector ' . $nombres . ' ' . $apellidos, 'usuarios', $id);
    responder(['success' => true, 'message' => 'Lector registrado exitosamente.', 'id' => $id]);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 400);
}
