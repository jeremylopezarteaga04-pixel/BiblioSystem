<?php
// ============================================================
// INICIO APORTE MARIO CUEVA
// ============================================================
// api/registrar_prestamo.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $cedula_usuario = $_POST['cedula_usuario'] ?? '';
    $codigo_libro = $_POST['codigo_libro'] ?? '';
    $fecha_limite = $_POST['fecha_limite'] ?? '';

    if (empty($cedula_usuario) || empty($codigo_libro) || empty($fecha_limite)) {
        throw new Exception("Todos los campos son obligatorios.");
    }

    $stmtUser = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE cedula = ?");
    $stmtUser->execute([$cedula_usuario]);
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception("El usuario con cédula $cedula_usuario no está registrado.");
    }
    $id_usuario = $usuario['id_usuario'];

    $stmtBook = $conexion->prepare("SELECT id_libro FROM libros WHERE codigo = ?");
    $stmtBook->execute([$codigo_libro]);
    $libro = $stmtBook->fetch(PDO::FETCH_ASSOC);

    if (!$libro) {
        throw new Exception("El código de libro ingresado no existe.");
    }
    $id_libro = $libro['id_libro'];

    // Usamos 'ACTIVO' que es un valor válido del ENUM de tu tabla
    $sql = "INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion_programada, estado, fecha_registro) 
            VALUES (:id_usuario, :id_libro, NOW(), :fecha_limite, 'ACTIVO', NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':id_libro' => $id_libro,
        ':fecha_limite' => $fecha_limite
    ]);

    echo json_encode([
        "success" => true,
        "message" => "¡Préstamo registrado exitosamente!"
    ]);

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
