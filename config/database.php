<?php
// config/database.php

$host = "devharbor.online";
$dbname = "devhara1f3f9_dh_jeloarte3b8f7e";
$username = "devhara1f3f9_dh_jeloarted2d728";
$password = "896a528acf8ae9bb0e45bd31";

try {
    $conexion = new PDO(
        "mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    die(json_encode([
        "success" => false,
        "message" => "Error al conectar: " . $e->getMessage()
    ]));
}
?>