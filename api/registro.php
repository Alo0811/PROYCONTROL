<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
include("../config/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $salon = $_POST['salon'] ?? '';
    $carrera = $_POST['carrera'] ?? '';
    $correo = $_POST['correo'] ?? '';

    if (!isset($_FILES['credencial']) || $_FILES['credencial']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "Falta subir la imagen de la credencial"]);
        exit();
    }

    $nombreImagen = $_FILES['credencial']['name'];
    $tmpImagen = $_FILES['credencial']['tmp_name'];

    $carpetaDestino = "../uploads/credenciales/";
    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    $rutaRelativa = "uploads/credenciales/" . time() . "_" . basename($nombreImagen);
    $rutaFisica = "../" . $rutaRelativa;

    if (move_uploaded_file($tmpImagen, $rutaFisica)) {
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, rol, salon, carrera, correo, credencial) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $rol, $salon, $carrera, $correo, $rutaRelativa);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Registro exitoso",
                "usuario_id" => $conexion->insert_id,
                "nombre" => $nombre,
                "rol" => $rol
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error BD: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo guardar la imagen en el servidor"]);
    }
}
$conexion->close();
?>
