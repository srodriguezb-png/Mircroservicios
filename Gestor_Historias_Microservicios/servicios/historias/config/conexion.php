
<?php

$host = "localhost";
$bd = "gestor_historias_db";
$usuario = "root";
$password = "";

try {

    $conexion = new PDO(
        "mysql:host=$host;dbname=$bd;charset=utf8mb4",
        $usuario,
        $password
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "error" => "Error de conexión: " . $e->getMessage()
    ]);

    exit;
}
