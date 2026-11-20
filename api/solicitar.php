<?php
ini_set('display_errors', 0);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(200);
        echo json_encode(["status" => "error", "message" => "Error PHP: " . $error['message']]);
    }
});

if (file_exists("../config/conexion.php")) {
    include("../config/conexion.php");
} else {
    include("../conexion.php");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit();
}

$usuario_id = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;

if ($usuario_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Usuario inválido ($usuario_id)"]);
    exit();
}

$resProy = $conexion->query("SELECT id FROM proyectores WHERE estado = 'Disponible' ORDER BY id ASC LIMIT 1");
if (!$resProy || $resProy->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "No hay proyectores disponibles"]);
    exit();
}

$filaProy = $resProy->fetch_assoc();
$proyector_id = (int)$filaProy['id'];
$fecha = date("Y-m-d");
$hora = date("H:i:s");

// Si tu tabla solicitudes usa 'Pendiente' por defecto al pedir, cámbialo aquí si es necesario
$estadoInicial = 'Pendiente';

$stmt = $conexion->prepare("INSERT INTO solicitudes (usuario_id, proyector_id, fecha, hora, estado) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Error SQL: " . $conexion->error]);
    exit();
}

$stmt->bind_param("iisss", $usuario_id, $proyector_id, $fecha, $hora, $estadoInicial);

if ($stmt->execute()) {
    $conexion->query("UPDATE proyectores SET estado = 'Ocupado' WHERE id = $proyector_id");
    echo json_encode([
        "status" => "success",
        "message" => "Solicitud enviada"
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Error BD: " . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>
