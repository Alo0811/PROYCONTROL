<?php
header('Content-Type: application/json; charset=utf-8');
include("../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? '';

    if (empty($usuario_id)) {
        echo json_encode(["status" => "error", "message" => "Falta el ID de usuario"]);
        exit();
    }

    $stmt = $conexion->prepare("SELECT id, proyector_id FROM solicitudes WHERE usuario_id = ? AND estado = 'Aprobada' ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $solicitud = $resultado->fetch_assoc();
        $solicitud_id = $solicitud['id'];
        $proyector_id = $solicitud['proyector_id'];

        $conexion->query("UPDATE solicitudes SET estado='Devuelta', fecha_devolucion=CURDATE(), hora_devolucion=CURTIME() WHERE id='$solicitud_id'");
        $conexion->query("UPDATE proyectores SET estado='Disponible' WHERE id='$proyector_id'");

        echo json_encode(["status" => "success", "message" => "Proyector devuelto correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No tienes proyectores prestados aprobados"]);
    }
    $stmt->close();
}
$conexion->close();
?>
