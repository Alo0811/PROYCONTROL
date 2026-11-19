<?php
header('Content-Type: application/json; charset=utf-8');
include("../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? '';

    if (empty($usuario_id)) {
        echo json_encode(["status" => "error", "message" => "Falta el ID de usuario"]);
        exit();
    }

    $sqlProyector = "SELECT id FROM proyectores WHERE estado='Disponible' LIMIT 1";
    $resultado = $conexion->query($sqlProyector);

    if ($resultado && $resultado->num_rows > 0) {
        $proyector = $resultado->fetch_assoc();
        $proyector_id = $proyector['id'];

        $stmt = $conexion->prepare("INSERT INTO solicitudes (usuario_id, proyector_id, fecha, hora, estado) VALUES (?, ?, CURDATE(), CURTIME(), 'Pendiente')");
        $stmt->bind_param("ii", $usuario_id, $proyector_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Solicitud enviada correctamente"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al guardar la solicitud"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "No hay proyectores disponibles"]);
    }
}
$conexion->close();
?>
