<?php
// api/index.php
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    "success" => true,
    "message" => "Bienvenido a la API de BiblioSystem",
    "version" => "1.0",
    "endpoints_disponibles" => [
        "GET" => [
            "obtener_usuarios" => "api/obtener_usuarios.php",
            "obtener_prestamos" => "api/obtener_prestamos.php"
        ],
        "POST" => [
            "registrar_usuario" => "api/registrar_usuario.php",
            "registrar_libro" => "api/registrar_libro.php",
            "registrar_prestamo" => "api/registrar_prestamo.php"
        ]
    ]
], JSON_UNESCAPED_UNICODE);
?>