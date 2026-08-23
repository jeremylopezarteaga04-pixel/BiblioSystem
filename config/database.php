<?php

$host = getenv('DB_HOST') ?: 'localhost';
$puerto = getenv('DB_PORT') ?: '3306';
$base_datos = getenv('DB_NAME') ?: 'bibliosystem';
$usuario = getenv('DB_USER') ?: 'bibliosystem';

$contrasena = getenv('DB_PASSWORD');

if ($contrasena === false) {
    $contrasena = 'Biblioteca2026';
}

try {
    $conexion = new PDO(
        "mysql:host={$host};port={$puerto};dbname={$base_datos};charset=utf8mb4",
        $usuario,
        $contrasena,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $error) {
    http_response_code(500);

    echo json_encode(
        [
            'success' => false,
            'message' => 'Error al conectar con la base de datos: '
                . $error->getMessage()
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}