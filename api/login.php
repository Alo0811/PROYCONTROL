<?php
header('Content-Type: application/json; charset=utf-8');
include("../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';

    $stmt = $conexion->prepare("SELECT id, nombre, rol, salon, carrera FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "message" => "Bienvenido " . $usuario['nombre'],
            "usuario" => $usuario
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Usuario no encontrado"
        ]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
$conexion->close();
?>