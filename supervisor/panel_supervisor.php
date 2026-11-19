<?php
session_start();

if(!isset($_SESSION['supervisor'])){
    header("Location: login_supervisor.php");
    exit();
}

include("../config/conexion.php");

$sql = "SELECT
s.id,
u.nombre,
u.rol,
u.salon,
u.carrera,
u.correo,
u.credencial,
p.nombre AS proyector,
s.fecha,
s.hora,
s.estado
FROM solicitudes s
INNER JOIN usuarios u
ON s.usuario_id = u.id
INNER JOIN proyectores p
ON s.proyector_id = p.id";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Panel Supervisor</title>

<style>

h1{
    text-align:center;
    margin-bottom:30px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th{
    background:#2c3e50;
    color:white;
    padding:12px;
}

td{
    border:1px solid #ccc;
    padding:10px;
    text-align:center;
}

.aprobar{
    background:#2ecc71;
    color:white;
    padding:8px 15px;
    border-radius:5px;
    text-decoration:none;
}

.denegar{
    background:#e74c3c;
    color:white;
    padding:8px 15px;
    border-radius:5px;
    text-decoration:none;
}

</style>

</head>

<link rel="stylesheet" href="../css/estilo.css">

<body>

<a href="../logout.php" class="salir">
    Salir
</a>

<div style="text-align:center;padding-top:30px;">

    <img src="../img/Logo1.png" class="Logo1">

    <h1 class="titulo">
        PROY CONTROL
    </h1>

</div>

<h2 style="
text-align:center;
color:white;
font-size:40px;
margin-bottom:40px;
">
Panel Supervisor
</h2>

<table>

<tr>
<th>Nombre</th>
<th>Rol</th>
<th>Salón</th>
<th>Carrera</th>
<th>Correo</th>
<th>Proyector</th>
<th>Credencial</th>
<th>Fecha</th>
<th>Hora</th>
<th>Estado</th>
<th>Aprobar</th>
<th>Denegar</th>
<th>Eliminar</th>
</tr>

<?php while($fila = $resultado->fetch_assoc()){ ?>

<tr>

<td><?php echo $fila['nombre']; ?></td>
<td><?php echo $fila['rol']; ?></td>
<td><?php echo $fila['salon']; ?></td>
<td><?php echo $fila['carrera']; ?></td>
<td><?php echo $fila['correo']; ?></td>
<td><?php echo $fila['proyector']; ?></td>
<td>
<a href="eliminar.php?id=<?php echo $fila['id']; ?>">
Eliminar
</a>
</td>
<td>

<img
src="../<?php echo $fila['credencial']; ?>"
width="120"
style="border-radius:10px;">
</td>

<td><?php echo $fila['fecha']; ?></td>
<td><?php echo $fila['hora']; ?></td>
<td><?php echo $fila['estado']; ?></td>


<td>
<a class="aprobar"
href="aprobar.php?id=<?php echo $fila['id']; ?>">
Aprobar
</a>
</td>

<td>
<a class="denegar"
href="denegar.php?id=<?php echo $fila['id']; ?>">
Denegar
</a>
</td>

</tr>

<?php } ?>

</table>

</body>
</html>