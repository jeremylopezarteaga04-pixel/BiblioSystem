<?php

require_once __DIR__ . '/helpers.php';

exigir_post();

require_once __DIR__ . '/../config/database.php';

try {
    $cedula = dato('cedula');
    $nombres = dato('nombres');
    $apellidos = dato('apellidos');
    $correo = dato('correo');
    $telefono = dato('telefono');
    $direccion = dato('direccion');

    if ($cedula === '') {
        throw new Exception('La cédula es obligatoria.');
    }

    if ($nombres === '') {
        throw new Exception('Los nombres son obligatorios.');
    }

    if ($apellidos === '') {
        throw new Exception('Los apellidos son obligatorios.');
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ingresa un correo electrónico válido.');
    }

    $consulta = $conexion->prepare(
        'SELECT COUNT(*) FROM usuarios WHERE cedula = ?'
    );

    $consulta->execute([$cedula]);

    if ((int) $consulta->fetchColumn() > 0) {
        throw new Exception('Ya existe un lector registrado con esa cédula.');
    }

    $consulta = $conexion->prepare(
        'SELECT COUNT(*) FROM usuarios WHERE correo = ?'
    );

    $consulta->execute([$correo]);

    if ((int) $consulta->fetchColumn() > 0) {
        throw new Exception('Ya existe un lector registrado con ese correo.');
    }

    $sql = "
        INSERT INTO usuarios (
            cedula,
            nombres,
            apellidos,
            correo,
            telefono,
            direccion,
            estado,
            fecha_registro
        )
        VALUES (?, ?, ?, ?, ?, ?, 'ACTIVO', CURRENT_TIMESTAMP)
    ";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        $cedula,
        $nombres,
        $apellidos,
        $correo,
        $telefono !== '' ? $telefono : null,
        $direccion !== '' ? $direccion : null
    ]);

    $id = (int) $conexion->lastInsertId();

    registrar_bitacora(
        $conexion,
        'REGISTRAR USUARIO',
        'Se registró el lector ' . $nombres . ' ' . $apellidos,
        'usuarios',
        $id
    );

    responder([
        'success' => true,
        'message' => 'Lector registrado correctamente.',
        'id' => $id
    ]);

} catch (Throwable $error) {
    responder([
        'success' => false,
        'message' => mensaje_error($error)
    ], 400);
}
