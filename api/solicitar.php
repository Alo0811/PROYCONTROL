<?php
error_reporting(0);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json; charset=utf-8');
include("../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? 0;

    if ($usuario_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Usuario no válido"]);
        exit();
    }

    // 1. Buscar el primer proyector disponible
    $resProy = $conexion->query("SELECT id FROM proyectores WHERE estado = 'Disponible' ORDER BY id ASC LIMIT 1");
    if (!$resProy || $resProy->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "No hay proyectores disponibles en este momento"]);
        exit();
    }

    $filaProy = $resProy->fetch_assoc();
    $proyector_id = (int)$filaProy['id'];

    // 2. Fecha y hora exacta de México
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");

    // 3. Registrar la solicitud con ese proyector_id real
    $stmt = $conexion->prepare("INSERT INTO solicitudes (usuario_id, proyector_id, fecha, hora, estado) VALUES (?, ?, ?, ?, 'Pendiente')");
    $stmt->bind_param("iiss", $usuario_id, $proyector_id, $fecha, $hora);

    if ($stmt->execute()) {
        // 4. Marcar ese proyector como Ocupado para que el siguiente usuario tome el #2, #3, etc.
        $conexion->query("UPDATE proyectores SET estado = 'Ocupado' WHERE id = $proyector_id");

        echo json_encode([
            "status" => "success",
            "message" => "Proyector #$proyector_id asignado correctamente"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al registrar solicitud: " . $stmt->error]);
    }
    $stmt->close();
}
$conexion->close();
?>
