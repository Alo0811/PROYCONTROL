<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
include("../config/conexion.php");

$usuario_id = $_GET['usuario_id'] ?? 0;

// 1. Contar proyectores disponibles
$resDisp = $conexion->query("SELECT COUNT(*) AS total FROM proyectores WHERE estado = 'Disponible'");
$disponibles = 0;
if ($resDisp && $row = $resDisp->fetch_assoc()) {
    $disponibles = (int)$row['total'];
}

// 2. Obtener solicitudes del usuario
$solicitudes = [];
if ($usuario_id > 0) {
    $stmt = $conexion->prepare("SELECT id, fecha, hora, estado, devolucion FROM solicitudes WHERE usuario_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resSol = $stmt->get_result();
    while ($fila = $resSol->fetch_assoc()) {
        $solicitudes[] = $fila;
    }
    $stmt->close();
}

echo json_encode([
    "status" => "success",
    "disponibles" => $disponibles,
    "solicitudes" => $solicitudes
]);

$conexion->close();
?>
