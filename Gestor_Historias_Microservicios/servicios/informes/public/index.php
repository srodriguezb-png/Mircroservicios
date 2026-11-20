
<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

$metodo = $_SERVER["REQUEST_METHOD"];

if ($metodo !== "GET") {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// INFORME GENERAL
if (!isset($_GET["responsable"])) {
    $sql = "SELECT
                estado,
                COUNT(*) AS cantidad,
                SUM(puntos) AS puntos_totales
            FROM historias
            GROUP BY estado";

    $consulta = $conexion->query($sql);

    echo json_encode($consulta->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// INFORME POR RESPONSABLE
$responsable = $_GET["responsable"];

$sql = "SELECT
            responsable,
            estado,
            COUNT(*) AS cantidad,
            SUM(puntos) AS puntos_totales
        FROM historias
        WHERE responsable = :responsable
        GROUP BY responsable, estado";

$consulta = $conexion->prepare($sql);
$consulta->execute([":responsable" => $responsable]);

echo json_encode($consulta->fetchAll(PDO::FETCH_ASSOC));
exit;

