<?php

session_start();

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/database.php';

try {

    // ============================================================
    // VERIFICAR SESIÓN
    // ============================================================

    if (!isset($_SESSION['usuario'])) {
        throw new Exception('No hay una sesión activa.');
    }

    $usuarioSesion = $_SESSION['usuario'];

    $idUsuario = (int) $usuarioSesion['id_usuario'];
    $rol = strtoupper($usuarioSesion['rol'] ?? 'USUARIO');

    // ============================================================
    // ACTUALIZAR PRÉSTAMOS ATRASADOS
    // ============================================================

    $conexion->exec("
        UPDATE prestamos
        SET estado = 'ATRASADO'
        WHERE estado = 'ACTIVO'
        AND fecha_devolucion_programada < date('now', 'localtime')
    ");

    // ============================================================
    // CONSULTAR PRÉSTAMOS
    // ============================================================

    if ($rol === 'ADMIN' || $rol === 'ADMINISTRADOR') {

        // --------------------------------------------------------
        // ADMINISTRADOR → TODOS LOS PRÉSTAMOS
        // --------------------------------------------------------

        $sql = "
            SELECT
                p.*,
                u.nombres,
                u.apellidos,
                u.cedula,
                u.correo,
                l.titulo AS titulo_libro,
                l.codigo AS codigo_libro

            FROM prestamos p

            LEFT JOIN usuarios u
                ON p.id_usuario = u.id_usuario

            LEFT JOIN libros l
                ON p.id_libro = l.id_libro

            ORDER BY
                p.fecha_registro DESC,
                p.id_prestamo DESC
        ";

        $consulta = $conexion->query($sql);

    } else {

        // --------------------------------------------------------
        // USUARIO → SOLO SUS PROPIOS PRÉSTAMOS
        // --------------------------------------------------------

        $sql = "
            SELECT
                p.*,
                u.nombres,
                u.apellidos,
                u.cedula,
                u.correo,
                l.titulo AS titulo_libro,
                l.codigo AS codigo_libro

            FROM prestamos p

            LEFT JOIN usuarios u
                ON p.id_usuario = u.id_usuario

            LEFT JOIN libros l
                ON p.id_libro = l.id_libro

            WHERE p.id_usuario = ?

            ORDER BY
                p.fecha_registro DESC,
                p.id_prestamo DESC
        ";

        $consulta = $conexion->prepare($sql);
        $consulta->execute([$idUsuario]);
    }

    // ============================================================
    // RESPUESTA
    // ============================================================

    responder([
        'success' => true,
        'data' => $consulta->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (Throwable $error) {

    responder([
        'success' => false,
        'message' => mensaje_error($error)
    ], 401);
}