<?php
error_reporting(0);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json; charset=utf-8');
include("../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? 0;

    // 1. Buscar la solicitud pendiente del usuario
    $stmtBusq = $conexion->prepare("SELECT id, proyector_id FROM solicitudes WHERE usuario_id = ? AND estado = 'Pendiente' ORDER BY id DESC LIMIT 1");
    $stmtBusq->bind_param("i", $usuario_id);
    $stmtBusq->execute();
    $res = $stmtBusq->get_result();

    if ($fila = $res->fetch_assoc()) {
        $solicitud_id = (int)$fila['id'];
        $proyector_id = (int)$fila['proyector_id'];
        $fecha_dev = date("Y-m-d");
        $hora_dev = date("H:i:s");

        // 2. Actualizar solicitud a Devuelto con fecha y hora de México
        $stmtUpd = $conexion->prepare("UPDATE solicitudes SET estado = 'Devuelto', fecha_devolucion = ?, hora_devolucion = ? WHERE id = ?");
        $stmtUpd->bind_param("ssi", $fecha_dev, $hora_dev, $solicitud_id);
        $stmtUpd->execute();
        $stmtUpd->close();

        // 3. Liberar ese proyector específico
        $conexion->query("UPDATE proyectores SET estado = 'Disponible' WHERE id = $proyector_id");

        echo json_encode([
            "status" => "success",
            "message" => "Proyector #$proyector_id devuelto correctamente"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "No tienes proyectores pendientes por devolver"]);
    }
    $stmtBusq->close();
}
$conexion->close();
?>
