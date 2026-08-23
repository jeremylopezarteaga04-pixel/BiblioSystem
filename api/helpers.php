<?php
header('Content-Type: application/json; charset=utf-8');

function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function exigir_post()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responder(['success' => false, 'message' => 'Método no permitido. Utiliza POST.'], 405);
    }
}

function dato($nombre, $predeterminado = '')
{
    return trim((string) ($_POST[$nombre] ?? $predeterminado));
}

function registrar_bitacora($conexion, $accion, $descripcion, $tabla, $id_registro = null)
{
    try {
        $consulta = $conexion->prepare('INSERT INTO bitacora (accion, descripcion, tabla_afectada, id_registro) VALUES (?, ?, ?, ?)');
        $consulta->execute([$accion, $descripcion, $tabla, $id_registro]);
    } catch (Throwable $error) {
        // La bitácora no debe impedir que la operación principal termine correctamente.
    }
}

function mensaje_error($error)
{
    if ($error instanceof PDOException && ($error->getCode() === '23000' || strpos($error->getMessage(), 'Duplicate entry') !== false)) {
        return 'El registro ya existe o está relacionado con otra operación. Revisa los datos ingresados.';
    }
    return $error->getMessage();
}
