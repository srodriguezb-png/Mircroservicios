
<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// Direcciones de los microservicios
$servicios = [
    "sprints" => "http://localhost/Mircroservicios/Gestor_Historias_Microservicios/servicios/sprints/public/",
    "historias" => "http://localhost/Mircroservicios/Gestor_Historias_Microservicios/servicios/historias/public/",
    "informes" => "http://localhost/Mircroservicios/Gestor_Historias_Microservicios/servicios/informes/public/"
];

// Identificar el servicio solicitado
$servicio = $_GET["servicio"] ?? "";

if (!array_key_exists($servicio, $servicios)) {
    http_response_code(404);
    echo json_encode([
        "error" => "Servicio no encontrado"
    ]);
    exit;
}

// Construir la URL del microservicio
$url = $servicios[$servicio];

// Conservar los parámetros adicionales
$parametros = $_GET;
unset($parametros["servicio"]);

if (!empty($parametros)) {
    $url .= "?" . http_build_query($parametros);
}

// Obtener el método HTTP y el cuerpo de la petición
$metodo = $_SERVER["REQUEST_METHOD"];
$datos = file_get_contents("php://input");

// Configurar la petición
$opciones = [
    "http" => [
        "method" => $metodo,
        "header" => "Content-Type: application/json\r\n",
        "content" => $datos,
        "ignore_errors" => true
    ]
];

$contexto = stream_context_create($opciones);

// Enviar petición al microservicio
$respuesta = file_get_contents($url, false, $contexto);

// Obtener el código HTTP de respuesta
if (isset($http_response_header[0])) {
    preg_match('/\s(\d{3})\s/', $http_response_header[0], $coincidencias);
    http_response_code((int) ($coincidencias[1] ?? 500));
}

echo $respuesta;
