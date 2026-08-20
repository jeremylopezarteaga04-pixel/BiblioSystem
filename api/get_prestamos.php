<?php
// ============================================================
// INICIO APORTE MARIO CUEVA
// ============================================================
// api/obtener_prestamos.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

try {
    // Consulta para traer los préstamos con información legible del usuario y del libro si es posible
    $sql = "SELECT p.*, u.nombres, u.apellidos, u.cedula, l.titulo AS titulo_libro, l.codigo AS codigo_libro 
            FROM prestamos p
            LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
            LEFT JOIN libros l ON p.id_libro = l.id_libro";
            
    $stmt = $conexion->query($sql);
    $prestamos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $prestamos
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
// ============================================================
// FIN APORTE MARIO CUEVA
// ============================================================
?>
