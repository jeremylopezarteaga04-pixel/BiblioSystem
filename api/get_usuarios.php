<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/database.php';

try {

    $sql = "
        SELECT
            u.id_usuario,
            u.cedula,
            u.nombres,
            u.apellidos,
            u.correo,
            u.telefono,
            u.direccion,
            u.estado,
            u.fecha_registro,

            c.id_cuenta,
            c.rol,
            c.estado_cuenta

        FROM usuarios u

        LEFT JOIN cuentas c
            ON u.id_usuario = c.id_usuario

        ORDER BY
            u.fecha_registro DESC,
            u.id_usuario DESC
    ";

    $consulta = $conexion->query($sql);

    responder([
        'success' => true,
        'data' => $consulta->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (Throwable $error) {

    responder([
        'success' => false,
        'message' => mensaje_error($error)
    ], 500);
}