<?php
// ============================================================
// INICIO APORTE ELOY ROJAS
// ============================================================
// api/get_libros.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $conexion->query("SELECT * FROM libros");
    $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $libros
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
// ============================================================
// FIN APORTE ELOY ROJAS
// ============================================================
?>
