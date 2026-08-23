<?php

require_once __DIR__ . '/helpers.php';

exigir_post();

require_once __DIR__ . '/../config/database.php';

try {
    $titulo = dato('titulo');

    $autor = (int) (
        dato('id_autor') ?: dato('autor')
    );

    $categoria = (int) (
        dato('id_categoria') ?: dato('categoria')
    );

    $cantidad = (int) (
        dato('cantidad_total')
        ?: dato('cantidad_disponible')
        ?: dato('stock')
        ?: dato('cantidad')
    );

    if (!$titulo || !$autor || !$categoria || $cantidad < 1) {
        throw new Exception(
            'Completa el título, autor, categoría y cantidad de ejemplares.'
        );
    }

    $consulta = $conexion->query(
        "SELECT MAX(CAST(SUBSTR(codigo, 5) AS INTEGER)) FROM libros"
    );

    $ultimo_numero = (int) $consulta->fetchColumn();

    $codigo = 'LIB-' . str_pad(
        (string) ($ultimo_numero + 1),
        3,
        '0',
        STR_PAD_LEFT
    );

    $imagen = procesar_portada();

    $sql = "
        INSERT INTO libros (
            codigo,
            titulo,
            id_autor,
            id_categoria,
            editorial,
            anio_publicacion,
            isbn,
            cantidad_total,
            cantidad_disponible,
            descripcion,
            imagen,
            fecha_registro
        )
        VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            CURRENT_TIMESTAMP
        )
    ";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        $codigo,
        $titulo,
        $autor,
        $categoria,
        dato('editorial') ?: null,
        dato('anio_publicacion') ?: null,
        dato('isbn') ?: null,
        $cantidad,
        $cantidad,
        dato('descripcion') ?: null,
        $imagen
    ]);

    $id = (int) $conexion->lastInsertId();

    registrar_bitacora(
        $conexion,
        'REGISTRAR LIBRO',
        'Se registró el libro ' . $titulo . ' con código ' . $codigo,
        'libros',
        $id
    );

    responder([
        'success' => true,
        'message' => 'Libro registrado correctamente con código ' . $codigo,
        'id' => $id,
        'codigo' => $codigo
    ]);

} catch (Throwable $error) {
    responder([
        'success' => false,
        'message' => mensaje_error($error)
    ], 400);
}
