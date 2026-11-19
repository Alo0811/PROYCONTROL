<?php

include("../config/conexion.php");

$id = $_GET['id'];

$conexion->query("
DELETE FROM solicitudes
WHERE id='$id'
");

header("Location: panel_supervisor.php");
exit();
?>