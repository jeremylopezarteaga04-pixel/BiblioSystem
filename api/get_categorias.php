<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/database.php';

try {
    $consulta = $conexion->query("SELECT * FROM categorias WHERE estado = 'ACTIVO' ORDER BY nombre");
    responder(['success' => true, 'data' => $consulta->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Throwable $error) {
    responder(['success' => false, 'message' => mensaje_error($error)], 500);
}
