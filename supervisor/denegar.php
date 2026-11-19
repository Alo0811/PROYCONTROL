<?php
session_start();
include("../config/conexion.php");

$id = $_GET['id'];

$sql = "SELECT proyector_id
        FROM solicitudes
        WHERE id='$id'";

$resultado = $conexion->query($sql);
$fila = $resultado->fetch_assoc();

$proyector_id = $fila['proyector_id'];

$conexion->query("
UPDATE solicitudes
SET estado='Denegada'
WHERE id='$id'
");

$conexion->query("
UPDATE proyectores
SET estado='Disponible'
WHERE id='$proyector_id'
");

header("Location: panel_supervisor.php");
exit();
?>