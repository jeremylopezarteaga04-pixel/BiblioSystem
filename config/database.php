<?php
// Puedes cambiar estas variables directamente o configurarlas en el servidor.
// Para Codespaces normalmente se utilizan localhost, un usuario de MySQL y su contraseña.
$host = getenv('DB_HOST') ?: 'devharbor.online';
$puerto = getenv('DB_PORT') ?: '3306';
$base_datos = getenv('DB_NAME') ?: 'devhara1f3f9_dh_jeloarte3b8f7e';
$usuario = getenv('DB_USER') ?: 'devhara1f3f9_dh_jeloarted2d728';
$contrasena = getenv('DB_PASSWORD');
if ($contrasena === false) {
    $contrasena = '896a528acf8ae9bb0e45bd31';
}

try {
    $conexion = new PDO(
        "mysql:host={$host};port={$puerto};dbname={$base_datos};charset=utf8mb4",
        $usuario,
        $contrasena,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
    );
} catch (PDOException $error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos: ' . $error->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
