<?php
session_start();

if(!isset($_SESSION['nombre']) || !isset($_SESSION['usuario_id'])){
    header("Location:index.php");
    exit();
}

include("config/conexion.php");

$rol = $_SESSION['rol'];
$usuario_id = $_SESSION['usuario_id'];

$sqlDisponibles = "
SELECT COUNT(*) AS total
FROM proyectores
WHERE estado='Disponible'
";

$resultadoDisponibles = $conexion->query($sqlDisponibles);
$datos = $resultadoDisponibles->fetch_assoc();

$totalDisponibles = $datos['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard - ProyControl</title>

<link rel="stylesheet" href="css/estilo.css">

<style>

.salir{
    position:absolute;
    top:20px;
    right:30px;
    background:red;
    color:white;
    padding:12px 25px;
    text-decoration:none;
    font-weight:bold;
    border-radius:10px;
    font-size:18px;
}

.titulo-dashboard{
    color:white;
    font-size:40px;
    margin-top:30px;
    font-weight:bold;
    letter-spacing:3px;
}

.dashboard{
    width:90%;
    max-width:1200px;
    margin:auto;
    padding-bottom:50px;
}

.bienvenida{
    text-align:center;
    color:white;
    font-size:45px;
    font-weight:bold;
    margin-top:20px;
    margin-bottom:40px;
}

.tarjetas{
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:35px; 
    margin-top:20px; 
}

.card{ 
    width:800px; 
    max-width:95%; 
    background:white; 
    border-radius:25px; 
    padding:35px; 
    text-align:center; 
    box-shadow:0 8px 20px rgba(0,0,0,.20); 
}

.btn-solicitar{
    background:#2ecc71;
    color:black;
    border:none;
    padding:22px 70px;
    border-radius:15px;
    font-size:28px;
    font-weight:bold;
    cursor:pointer;
}

.btn-devolver{
    background:#f1c40f;
    color:black;
    border:none;
    padding:22px 70px;
    border-radius:15px;
    font-size:28px;
    font-weight:bold;
    cursor:pointer;
}

.disponibles{
    font-size:120px !important;
    font-weight:bold;
    margin:20px 0;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
    font-size:18px;
}

table th, table td{
    border:1px solid #ccc;
    padding:12px;
}

table th{
    background:#2c3e50;
    color:white;
}

</style>

</head>

<body>

<a href="logout.php" class="salir">Salir</a>

<div style="text-align:center;padding-top:20px;">

    <img src="img/Logo1.png" class="Logo1">

    <h1 class="titulo-dashboard">PROY CONTROL</h1>

</div>

<div class="dashboard">

<h2 class="bienvenida">
    Bienvenido <?php echo $rol; ?>
</h2>

<div class="tarjetas">

<div class="card">
<h3 style="font-size:32px;margin-bottom:15px;">Solicitar Proyector</h3>

<form action="solicitar.php" method="POST">
    <button class="btn-solicitar" type="submit">Solicitar</button>
</form>
</div>

<div class="card">
<h3 style="font-size:32px;margin-bottom:15px;">Devolver Proyector</h3>

<form action="devolver.php" method="POST">
    <button class="btn-devolver" type="submit">Devolver</button>
</form>
</div>

<div class="card">
<h3 style="font-size:32px;">Proyectores Disponibles</h3>
<div class="disponibles">
    <?php echo $totalDisponibles; ?>
</div>
</div>

<div class="card">

<h3 style="font-size:32px;">Mis Solicitudes</h3>

<?php
$sql = "SELECT
fecha,
hora,
estado,
fecha_devolucion,
hora_devolucion
FROM solicitudes
WHERE usuario_id='$usuario_id'
ORDER BY id DESC";

$resultado = $conexion->query($sql);
?>

<table>
<tr>
    <th>Fecha</th>
    <th>Hora</th>
    <th>Estado</th>
    <th>Fecha devolución</th>
    <th>Hora devolución</th>
</tr>

<?php while($fila = $resultado->fetch_assoc()){ ?>
<tr>
    <td><?php echo $fila['fecha']; ?></td>
    <td><?php echo $fila['hora']; ?></td>
    <td><?php echo $fila['estado']; ?></td>
    <td><?php echo $fila['fecha_devolucion']; ?></td>
    <td><?php echo $fila['hora_devolucion']; ?></td>
</tr>
<?php } ?>

</table>

</div>

</div>

</div>

</body>
</html>
