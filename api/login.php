<?php

session_start();

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/database.php';

exigir_post();

try {

    $correo = dato('correo');
    $password = dato('password');

    // ============================================================
    // VALIDACIONES
    // ============================================================

    if ($correo === '') {
        throw new Exception('Ingresa tu correo electrónico.');
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ingresa un correo electrónico válido.');
    }

    if ($password === '') {
        throw new Exception('Ingresa tu contraseña.');
    }

    // ============================================================
    // BUSCAR CUENTA
    // ============================================================

    $sql = "
        SELECT
            c.id_cuenta,
            c.id_usuario,
            c.password_hash,
            c.rol,
            c.estado_cuenta,

            u.nombres,
            u.apellidos,
            u.correo,
            u.cedula,
            u.telefono,
            u.direccion,
            u.estado

        FROM cuentas c

        INNER JOIN usuarios u
            ON u.id_usuario = c.id_usuario

        WHERE u.correo = ?
        LIMIT 1
    ";

    $consulta = $conexion->prepare($sql);
    $consulta->execute([$correo]);

    $cuenta = $consulta->fetch(PDO::FETCH_ASSOC);

    // ============================================================
    // CUENTA NO ENCONTRADA
    // ============================================================

    if (!$cuenta) {
        throw new Exception(
            'El correo o la contraseña son incorrectos.'
        );
    }

    // ============================================================
    // VERIFICAR ESTADO DEL USUARIO
    // ============================================================

    if ($cuenta['estado'] !== 'ACTIVO') {
        throw new Exception(
            'Tu usuario se encuentra inactivo.'
        );
    }

    // ============================================================
    // VERIFICAR ESTADO DE LA CUENTA
    // ============================================================

    if ($cuenta['estado_cuenta'] !== 'ACTIVADA') {
        throw new Exception(
            'Tu cuenta todavía está pendiente de activación.'
        );
    }

    // ============================================================
    // VERIFICAR CONTRASEÑA
    // ============================================================

    if (
        empty($cuenta['password_hash']) ||
        !password_verify(
            $password,
            $cuenta['password_hash']
        )
    ) {
        throw new Exception(
            'El correo o la contraseña son incorrectos.'
        );
    }

    // ============================================================
    // CREAR SESIÓN
    // ============================================================

    session_regenerate_id(true);

    $_SESSION['usuario'] = [
        'id_usuario' => (int) $cuenta['id_usuario'],
        'id_cuenta' => (int) $cuenta['id_cuenta'],
        'nombres' => $cuenta['nombres'],
        'apellidos' => $cuenta['apellidos'],
        'correo' => $cuenta['correo'],
        'cedula' => $cuenta['cedula'],
        'rol' => $cuenta['rol']
    ];

    // ============================================================
    // RESPUESTA
    // ============================================================

    responder([
        'success' => true,
        'message' => 'Inicio de sesión correcto.',
        'usuario' => $_SESSION['usuario']
    ]);

} catch (Throwable $error) {

    responder([
        'success' => false,
        'message' => mensaje_error($error)
    ], 401);
}