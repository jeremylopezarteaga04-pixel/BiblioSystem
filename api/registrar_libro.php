<?php
// ============================================================
// INICIO APORTE ELOY ROJAS
// ============================================================
// api/registrar_libro.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    // Capturamos las variables aceptando múltiples alternativas por si el input se llama diferente
    $codigo = $_POST['codigo'] ?? $_POST['isbn'] ?? '';
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? $_POST['id_autor'] ?? '';
    $categoria = $_POST['categoria'] ?? $_POST['id_categoria'] ?? '';
    $cantidad = $_POST['cantidad_disponible'] ?? $_POST['stock'] ?? $_POST['cantidad'] ?? '';

    // Validamos que los campos obligatorios vengan llenos
    if (empty($codigo) || empty($titulo) || empty($autor) || empty($categoria) || $cantidad === '') {
        throw new Exception("Faltan datos obligatorios. Asegúrate de llenar todos los campos.");
    }

    // Inserción en la tabla libros (ajustando los nombres reales de tus columnas)
    $sql = "INSERT INTO libros (codigo, titulo, id_autor, id_categoria, cantidad_disponible, fecha_registro) 
            VALUES (:codigo, :titulo, :autor, :categoria, :cantidad, NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':codigo' => $codigo,
        ':titulo' => $titulo,
        ':autor' => $autor,
        ':categoria' => $categoria,
        ':cantidad' => $cantidad
    ]);

    echo json_encode([
        "success" => true,
        "message" => "¡Libro registrado exitosamente!"
    ]);

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