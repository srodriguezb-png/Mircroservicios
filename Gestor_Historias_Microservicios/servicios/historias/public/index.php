
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

$id = isset($_GET["id"]) ? (int) $_GET["id"] : null;

// LISTAR HISTORIAS
if ($metodo === "GET") {

    // Consultar historias de un Sprint específico
    if (isset($_GET["sprint_id"])) {

        $sprint_id = (int) $_GET["sprint_id"];

        $sql = "SELECT * FROM historias
                WHERE sprint_id = :sprint_id
                ORDER BY id DESC";

        $consulta = $conexion->prepare($sql);

        $consulta->execute([
            ":sprint_id" => $sprint_id
        ]);

    } else {

        // Consultar todas las historias
        $sql = "SELECT * FROM historias ORDER BY id DESC";

        $consulta = $conexion->query($sql);
    }

    echo json_encode($consulta->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// CREAR HISTORIA
if ($metodo === "POST") {

    $datos = json_decode(file_get_contents("php://input"), true);

    if (
        empty($datos["titulo"]) ||
        empty($datos["descripcion"]) ||
        empty($datos["responsable"]) ||
        empty($datos["puntos"]) ||
        empty($datos["fecha_creacion"]) ||
        empty($datos["sprint_id"])
    ) {
        http_response_code(400);

        echo json_encode([
            "error" => "Faltan datos obligatorios"
        ]);

        exit;
    }

    $sql = "INSERT INTO historias
            (titulo, descripcion, responsable, estado, puntos,
             fecha_creacion, fecha_finalizacion, sprint_id)
            VALUES
            (:titulo, :descripcion, :responsable, :estado, :puntos,
             :fecha_creacion, :fecha_finalizacion, :sprint_id)";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        ":titulo" => $datos["titulo"],
        ":descripcion" => $datos["descripcion"],
        ":responsable" => $datos["responsable"],
        ":estado" => $datos["estado"] ?? "nueva",
        ":puntos" => $datos["puntos"],
        ":fecha_creacion" => $datos["fecha_creacion"],
        ":fecha_finalizacion" => $datos["fecha_finalizacion"] ?? null,
        ":sprint_id" => $datos["sprint_id"]
    ]);

    http_response_code(201);

    echo json_encode([
        "mensaje" => "Historia creada correctamente",
        "id" => $conexion->lastInsertId()
    ]);

    exit;
}

// EDITAR HISTORIA
if ($metodo === "PUT") {

    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el ID"]);
        exit;
    }

    $datos = json_decode(file_get_contents("php://input"), true);

    $sql = "UPDATE historias SET
            titulo = :titulo,
            descripcion = :descripcion,
            responsable = :responsable,
            estado = :estado,
            puntos = :puntos,
            fecha_finalizacion = :fecha_finalizacion,
            sprint_id = :sprint_id
            WHERE id = :id";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        ":titulo" => $datos["titulo"],
        ":descripcion" => $datos["descripcion"],
        ":responsable" => $datos["responsable"],
        ":estado" => $datos["estado"],
        ":puntos" => $datos["puntos"],
        ":fecha_finalizacion" => $datos["fecha_finalizacion"] ?? null,
        ":sprint_id" => $datos["sprint_id"],
        ":id" => $id
    ]);

    echo json_encode([
        "mensaje" => "Historia actualizada correctamente"
    ]);

    exit;
}

// ELIMINAR HISTORIA
if ($metodo === "DELETE") {

    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el ID"]);
        exit;
    }

    $sql = "DELETE FROM historias WHERE id = :id";

    $consulta = $conexion->prepare($sql);
    $consulta->execute([":id" => $id]);

    echo json_encode([
        "mensaje" => "Historia eliminada correctamente"
    ]);

    exit;
}

// MÉTODO NO PERMITIDO
http_response_code(405);

echo json_encode([
    "error" => "Método no permitido"
]);
