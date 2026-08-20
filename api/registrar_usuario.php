<?php
// ============================================================
// INICIO APORTE JEREMY LÓPEZ
// ============================================================
// api/registrar_usuario.php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $cedula = $_POST['cedula'] ?? '';
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';

    // Validación básica de campos vacíos
    if (empty($cedula) || empty($nombres) || empty($apellidos) || empty($correo)) {
        echo json_encode(["success" => false, "message" => "Cédula, nombres, apellidos y correo son obligatorios."]);
        exit;
    }

    try {
        $query = "INSERT INTO usuarios (cedula, nombres, apellidos, correo, telefono, direccion) 
                  VALUES (:cedula, :nombres, :apellidos, :correo, :telefono, :direccion)";
        
        $stmt = $conexion->prepare($query);
        
        // Ejecutamos pasando el arreglo de datos directamente
        $stmt->execute([
            ':cedula' => $cedula,
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':correo' => $correo,
            ':telefono' => $telefono,
            ':direccion' => $direccion
        ]);
        
        echo json_encode(["success" => true, "message" => "Usuario registrado exitosamente."]);
        
    } catch (PDOException $e) {
        // Si la cédula o el correo ya existen (asumiendo que en tu BD los pusiste como UNIQUE)
        echo json_encode(["success" => false, "message" => "Error al registrar: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido. Use POST."]);
}
// ============================================================
// FIN APORTE JEREMY LÓPEZ
// ============================================================
?>