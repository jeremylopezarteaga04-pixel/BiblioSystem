<?php
// ============================================================
// INICIO APORTE JEREMY LÓPEZ
// ============================================================
// api/obtener_usuarios.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $conexion->query("SELECT * FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $usuarios
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
// ============================================================
// FIN APORTE JEREMY LÓPEZ
// ============================================================
?>