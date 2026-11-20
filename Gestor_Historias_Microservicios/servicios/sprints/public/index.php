
<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

$metodo = $_SERVER["REQUEST_METHOD"];

// OBTENER ID
$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;

// LISTAR SPRINTS
if ($metodo === "GET") {

    $sql = "SELECT * FROM sprints ORDER BY id DESC";
    $consulta = $conexion->query($sql);

    echo json_encode($consulta->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// CREAR SPRINT
if ($metodo === "POST") {

    $datos = json_decode(file_get_contents("php://input"), true);

    if (
        empty($datos["nombre"]) ||
        empty($datos["fecha_inicio"]) ||
        empty($datos["fecha_fin"])
    ) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos obligatorios"]);
        exit;
    }

    $sql = "INSERT INTO sprints
            (nombre, fecha_inicio, fecha_fin)
            VALUES (:nombre, :fecha_inicio, :fecha_fin)";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        ":nombre" => $datos["nombre"],
        ":fecha_inicio" => $datos["fecha_inicio"],
        ":fecha_fin" => $datos["fecha_fin"]
    ]);

    http_response_code(201);

    echo json_encode([
        "mensaje" => "Sprint creado correctamente",
        "id" => $conexion->lastInsertId()
    ]);
    exit;
}

// EDITAR SPRINT
if ($metodo === "PUT") {

    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el ID del sprint"]);
        exit;
    }

    $datos = json_decode(file_get_contents("php://input"), true);

    $sql = "UPDATE sprints
            SET nombre = :nombre,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin
            WHERE id = :id";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        ":nombre" => $datos["nombre"],
        ":fecha_inicio" => $datos["fecha_inicio"],
        ":fecha_fin" => $datos["fecha_fin"],
        ":id" => $id
    ]);

    echo json_encode([
        "mensaje" => "Sprint actualizado correctamente"
    ]);
    exit;
}

// ELIMINAR SPRINT
if ($metodo === "DELETE") {

    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el ID del sprint"]);
        exit;
    }

    $sql = "DELETE FROM sprints WHERE id = :id";

    $consulta = $conexion->prepare($sql);
    $consulta->execute([":id" => $id]);

    echo json_encode([
        "mensaje" => "Sprint eliminado correctamente"
    ]);
    exit;
}

// MÉTODO NO PERMITIDO
http_response_code(405);

echo json_encode([
    "error" => "Método no permitido"
]);

