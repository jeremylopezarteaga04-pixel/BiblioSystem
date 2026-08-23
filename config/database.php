<?php
// La base de datos viaja con el repositorio y no requiere MySQL ni un servidor externo.
$ruta_base_datos = __DIR__ . '/../database/bibliosystem.sqlite';

try {
    if (!extension_loaded('pdo_sqlite')) {
        throw new RuntimeException('La extensión pdo_sqlite no está habilitada en PHP.');
    }

    if (!is_file($ruta_base_datos)) {
        throw new RuntimeException('No se encontró database/bibliosystem.sqlite. Verifica que el archivo esté incluido en el repositorio.');
    }

    $conexion = new PDO('sqlite:' . $ruta_base_datos);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conexion->exec('PRAGMA foreign_keys = ON');
    $conexion->exec('PRAGMA busy_timeout = 5000');
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al conectar con SQLite: ' . $error->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
