<?php
session_start();
include("config/conexion.php");

$usuario_id = $_SESSION['usuario_id'];

$sql = "
SELECT *
FROM solicitudes
WHERE usuario_id='$usuario_id'
AND estado='Aprobada'
ORDER BY id DESC
LIMIT 1
";

$resultado = $conexion->query($sql);

if($resultado->num_rows > 0){

    $solicitud = $resultado->fetch_assoc();

    $solicitud_id = $solicitud['id'];
    $proyector_id = $solicitud['proyector_id'];

    $conexion->query("
    UPDATE solicitudes
    SET
    estado='Devuelta',
    fecha_devolucion=CURDATE(),
    hora_devolucion=CURTIME()
    WHERE id='$solicitud_id'
    ");

    $conexion->query("
    UPDATE proyectores
    SET estado='Disponible'
    WHERE id='$proyector_id'
    ");

    echo "<script>
    alert('Proyector devuelto correctamente');
    window.location='dashboard.php';
    </script>";

}else{

    echo "<script>
    alert('No tienes proyectores prestados');
    window.location='dashboard.php';
    </script>";
}
?>