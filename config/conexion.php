<?php
$host = getenv("MYSQLHOST") ?: "localhost";
$user = getenv("MYSQLUSER") ?: "root";
$pass = getenv("MYSQLPASSWORD") ?: "";
$db   = getenv("MYSQLDATABASE") ?: "proycontrol";
$port = getenv("MYSQLPORT") ?: 3306;

$conexion = new mysqli($host, $user, $pass, $db, $port);
if ($conexion->connect_error) {
    die(json_encode(["status" => "error", "message" => "Error de conexión: " . $conexion->connect_error]));
}
$conexion->set_charset("utf8mb4");
?>