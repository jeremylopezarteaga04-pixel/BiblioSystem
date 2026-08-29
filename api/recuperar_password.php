<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/database.php';

exigir_post();

try {

    $accion = dato('accion');

    // ============================================================
    // VERIFICAR CORREO
    // ============================================================

    if ($accion === 'verificar_correo') {

        $correo = dato('correo');

        if ($correo === '') {
            throw new Exception('Ingresa tu correo electrónico.');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Ingresa un correo electrónico válido.');
        }

        $sql = "
            SELECT
                c.id_cuenta,
                c.id_usuario,
                u.correo,
                u.cedula,
                u.nombres,
                u.apellidos
            FROM cuentas c
            INNER JOIN usuarios u
                ON u.id_usuario = c.id_usuario
            WHERE u.correo = ?
              AND u.estado = 'ACTIVO'
              AND c.estado_cuenta = 'ACTIVADA'
            LIMIT 1
        ";

        $consulta = $conexion->prepare($sql);
        $consulta->execute([$correo]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            throw new Exception(
                'No se encontró un usuario activo con ese correo.'
            );
        }

        responder([
            'success' => true,
            'message' => 'Correo encontrado.',
            'data' => [
                'id_usuario' => (int) $usuario['id_usuario'],
                'nombres' => $usuario['nombres'],
                'apellidos' => $usuario['apellidos']
            ]
        ]);
    }

    // ============================================================
    // VERIFICAR ÚLTIMOS 4 DÍGITOS DE CÉDULA
    // ============================================================

    elseif ($accion === 'verificar_cedula') {

        $id_usuario = dato('id_usuario');
        $ultimos_digitos = dato('ultimos_digitos');

        if ($id_usuario === '') {
            throw new Exception('Usuario no válido.');
        }

        if (!preg_match('/^[0-9]{4}$/', $ultimos_digitos)) {
            throw new Exception(
                'Ingresa exactamente los últimos 4 dígitos de tu cédula.'
            );
        }

        $sql = "
            SELECT cedula
            FROM usuarios
            WHERE id_usuario = ?
              AND estado = 'ACTIVO'
            LIMIT 1
        ";

        $consulta = $conexion->prepare($sql);
        $consulta->execute([$id_usuario]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            throw new Exception('No se encontró el usuario.');
        }

        $cedula = preg_replace('/\D/', '', $usuario['cedula']);

        if (strlen($cedula) < 4) {
            throw new Exception(
                'La cédula registrada no tiene suficientes dígitos.'
            );
        }

        $ultimos = substr($cedula, -4);

        if ($ultimos !== $ultimos_digitos) {
            throw new Exception(
                'Los datos de verificación no son correctos.'
            );
        }

        responder([
            'success' => true,
            'message' => 'Identidad verificada correctamente.'
        ]);
    }

    // ============================================================
    // CAMBIAR CONTRASEÑA
    // ============================================================

    elseif ($accion === 'cambiar_password') {

        $id_usuario = dato('id_usuario');
        $nueva_password = dato('nueva_password');

        if ($id_usuario === '') {
            throw new Exception('Usuario no válido.');
        }

        if ($nueva_password === '') {
            throw new Exception('Ingresa una nueva contraseña.');
        }

        if (strlen($nueva_password) < 6) {
            throw new Exception(
                'La contraseña debe tener al menos 6 caracteres.'
            );
        }

        // Verificar que exista la cuenta
        $sql = "
            SELECT id_cuenta
            FROM cuentas
            WHERE id_usuario = ?
              AND estado_cuenta = 'ACTIVADA'
            LIMIT 1
        ";

        $consulta = $conexion->prepare($sql);
        $consulta->execute([$id_usuario]);

        $cuenta = $consulta->fetch(PDO::FETCH_ASSOC);

        if (!$cuenta) {
            throw new Exception(
                'No se encontró una cuenta activa para este usuario.'
            );
        }

        // Generar hash seguro
        $password_hash = password_hash(
            $nueva_password,
            PASSWORD_DEFAULT
        );

        // Actualizar contraseña
        $sql = "
            UPDATE cuentas
            SET password_hash = ?
            WHERE id_cuenta = ?
        ";

        $consulta = $conexion->prepare($sql);
        $consulta->execute([
            $password_hash,
            $cuenta['id_cuenta']
        ]);

        responder([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.'
        ]);
    }

    // ============================================================
    // ACCIÓN NO VÁLIDA
    // ============================================================

    else {

        throw new Exception(
            'Acción de recuperación no válida.'
        );
    }

} catch (Throwable $error) {

    responder([
        'success' => false,
        'message' => mensaje_error($error)
    ], 400);
}