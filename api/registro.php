<?php
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo json_encode(["status" => "error", "message" => "Error PHP: " . $error['message']]);
    }
});

if (file_exists("../config/conexion.php")) {
    include("../config/conexion.php");
} elseif (file_exists("../conexion.php")) {
    include("../conexion.php");
} else {
    echo json_encode(["status" => "error", "message" => "No se encontró el archivo conexion.php"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit();
}

$nombre = $_POST['nombre'] ?? '';
$rol = $_POST['rol'] ?? '';
$salon = $_POST['salon'] ?? '';
$carrera = $_POST['carrera'] ?? '';
$correo = $_POST['correo'] ?? '';
$password = $_POST['password'] ?? '';

if (!isset($_FILES['credencial']) || $_FILES['credencial']['error'] !== UPLOAD_ERR_OK) {
    $errCode = $_FILES['credencial']['error'] ?? -1;
    echo json_encode(["status" => "error", "message" => "Error al subir archivo (código $errCode)"]);
    exit();
}

$carpetaDestino = "../uploads/credenciales/";
if (!is_dir($carpetaDestino)) {
    @mkdir($carpetaDestino, 0777, true);
}

$rutaRelativa = "uploads/credenciales/" . time() . "_" . basename($_FILES['credencial']['name']);
$rutaFisica = "../" . $rutaRelativa;

if (move_uploaded_file($_FILES['credencial']['tmp_name'], $rutaFisica)) {
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, rol, salon, carrera, correo, password, credencial) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Error SQL prepare: " . $conexion->error]);
        exit();
    }
    $stmt->bind_param("sssssss", $nombre, $rol, $salon, $carrera, $correo, $password, $rutaRelativa);

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
    echo json_encode(["status" => "error", "message" => "No se pudo mover la imagen a $rutaFisica"]);
}

$conexion->close();
?>
