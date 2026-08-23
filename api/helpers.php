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
    if ($error instanceof PDOException && ($error->getCode() === '23000' || strpos($error->getMessage(), 'Duplicate entry') !== false || strpos($error->getMessage(), 'UNIQUE constraint failed') !== false || strpos($error->getMessage(), 'FOREIGN KEY constraint failed') !== false)) {
        return 'El registro ya existe o está relacionado con otra operación. Revisa los datos ingresados.';
    }
    return $error->getMessage();
}

function procesar_portada($imagen_actual = null)
{
    $url = dato('imagen_url');
    $archivo = $_FILES['imagen_archivo'] ?? null;

    if ($archivo && $archivo['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No se pudo subir la imagen. Intenta nuevamente.');
        }
        if ($archivo['size'] > 5 * 1024 * 1024) {
            throw new Exception('La portada no puede superar los 5 MB.');
        }

        $informacion = @getimagesize($archivo['tmp_name']);
        $extensiones = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $tipo = $informacion['mime'] ?? '';
        if (!isset($extensiones[$tipo])) {
            throw new Exception('La portada debe ser una imagen JPG, PNG, WEBP o GIF.');
        }

        $directorio = __DIR__ . '/../img/portadas';
        if (!is_dir($directorio) && !mkdir($directorio, 0755, true)) {
            throw new Exception('No se pudo crear la carpeta de portadas.');
        }

        $nombre = 'libro_' . date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $extensiones[$tipo];
        if (!move_uploaded_file($archivo['tmp_name'], $directorio . '/' . $nombre)) {
            throw new Exception('No se pudo guardar la imagen dentro del proyecto.');
        }

        return 'img/portadas/' . $nombre;
    }

    if ($url !== '') {
        if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)) {
            throw new Exception('La URL de la portada debe comenzar con http:// o https://.');
        }
        return $url;
    }

    return $imagen_actual;
}
