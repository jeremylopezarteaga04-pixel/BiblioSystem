<?php

require_once __DIR__ . '/helpers.php';

exigir_post();

require_once __DIR__ . '/../config/database.php';

try {

    // ============================================================
    // DATOS DEL USUARIO
    // ============================================================

    $cedula = dato('cedula');
    $nombres = dato('nombres');
    $apellidos = dato('apellidos');
    $correo = dato('correo');
    $telefono = dato('telefono');
    $direccion = dato('direccion');

    // ============================================================
    // DATOS DE CUENTA
    // ============================================================

    $password = dato('password');
    $confirmPassword = dato('confirm_password');

    // ============================================================
    // VALIDACIONES DEL USUARIO
    // ============================================================

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

    // ============================================================
    // VERIFICAR CÉDULA
    // ============================================================

    $consulta = $conexion->prepare(
        'SELECT COUNT(*) FROM usuarios WHERE cedula = ?'
    );

    $consulta->execute([$cedula]);

    if ((int) $consulta->fetchColumn() > 0) {
        throw new Exception(
            'Ya existe un lector registrado con esa cédula.'
        );
    }

    // ============================================================
    // VERIFICAR CORREO
    // ============================================================

    $consulta = $conexion->prepare(
        'SELECT COUNT(*) FROM usuarios WHERE correo = ?'
    );

    $consulta->execute([$correo]);

    if ((int) $consulta->fetchColumn() > 0) {
        throw new Exception(
            'Ya existe un lector registrado con ese correo.'
        );
    }

    // ============================================================
    // DETERMINAR TIPO DE REGISTRO
    // ============================================================
    //
    // Si viene contraseña:
    //
    //   → Registro realizado por el propio usuario
    //   → Contraseña obligatoria
    //   → Cuenta ACTIVADA
    //
    // Si NO viene contraseña:
    //
    //   → Usuario creado por administrador
    //   → Sin contraseña inicialmente
    //   → Cuenta PENDIENTE
    //
    // ============================================================

    if ($password !== '') {

        // --------------------------------------------------------
        // REGISTRO PROPIO
        // --------------------------------------------------------

        if (strlen($password) < 6) {
            throw new Exception(
                'La contraseña debe tener al menos 6 caracteres.'
            );
        }

        if ($confirmPassword === '') {
            throw new Exception(
                'Debes confirmar tu contraseña.'
            );
        }

        if ($password !== $confirmPassword) {
            throw new Exception(
                'Las contraseñas no coinciden.'
            );
        }

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if ($passwordHash === false) {
            throw new Exception(
                'No se pudo procesar la contraseña.'
            );
        }

        $estadoCuenta = 'ACTIVADA';

    } else {

        // --------------------------------------------------------
        // USUARIO CREADO POR ADMINISTRADOR
        // --------------------------------------------------------

        $passwordHash = null;
        $estadoCuenta = 'PENDIENTE';
    }

    // ============================================================
    // INICIAR TRANSACCIÓN
    // ============================================================

    $conexion->beginTransaction();

    // ============================================================
    // CREAR USUARIO
    // ============================================================

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

    $idUsuario = (int) $conexion->lastInsertId();

    // ============================================================
    // CREAR CUENTA
    // ============================================================

    $sqlCuenta = "
        INSERT INTO cuentas (
            id_usuario,
            password_hash,
            rol,
            estado_cuenta,
            token_activacion,
            fecha_activacion
        )
        VALUES (?, ?, 'USUARIO', ?, NULL, ?)
    ";

    $fechaActivacion = (
        $estadoCuenta === 'ACTIVADA'
            ? date('Y-m-d H:i:s')
            : null
    );

    $consultaCuenta = $conexion->prepare($sqlCuenta);

    $consultaCuenta->execute([
        $idUsuario,
        $passwordHash,
        $estadoCuenta,
        $fechaActivacion
    ]);

    // ============================================================
    // BITÁCORA
    // ============================================================

    registrar_bitacora(
        $conexion,
        'REGISTRAR USUARIO',
        'Se registró el lector ' . $nombres . ' ' . $apellidos,
        'usuarios',
        $idUsuario
    );

    // ============================================================
    // CONFIRMAR TRANSACCIÓN
    // ============================================================

    $conexion->commit();

    // ============================================================
    // RESPUESTA
    // ============================================================

    if ($estadoCuenta === 'ACTIVADA') {

        $mensaje = 'Cuenta registrada correctamente.';

    } else {

        $mensaje = 'Lector registrado correctamente. La cuenta queda pendiente de activación.';
    }

    responder([
        'success' => true,
        'message' => $mensaje,
        'id' => $idUsuario,
        'estado_cuenta' => $estadoCuenta
    ]);

} catch (Throwable $error) {

    // ============================================================
    // DESHACER TRANSACCIÓN SI OCURRIÓ UN ERROR
    // ============================================================

    if (
        isset($conexion) &&
        $conexion->inTransaction()
    ) {
        $conexion->rollBack();
    }

    responder([
        'success' => false,
        'message' => mensaje_error($error)
    ], 400);
}