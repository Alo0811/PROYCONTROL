<?php
date_default_timezone_set('America/Mexico_City');
session_start();
include("config/conexion.php");

if(!isset($_SESSION['usuario_id'])){
    header("Location:index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$sqlProyector = "
SELECT * 
FROM proyectores
WHERE estado='Disponible'
LIMIT 1
";

$resultado = $conexion->query($sqlProyector);

if($resultado->num_rows > 0){

    $proyector = $resultado->fetch_assoc();
    $proyector_id = $proyector['id'];

    $sql = "
    INSERT INTO solicitudes
    (usuario_id, proyector_id, fecha, hora, estado)
    VALUES
    (
    '$usuario_id',
    '$proyector_id',
    CURDATE(),
    CURTIME(),
    'Pendiente'
    )
    ";

    if($conexion->query($sql)){

        echo "<script>
        alert('Solicitud enviada correctamente');
        window.location='dashboard.php';
        </script>";

    }else{

        echo "<script>
        alert('Error al guardar la solicitud');
        window.location='dashboard.php';
        </script>";
    }

}else{

    echo "<script>
    alert('No hay proyectores disponibles');
    window.location='dashboard.php';
    </script>";
}
?>
