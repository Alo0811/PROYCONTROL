<?php

session_start();

include("config/conexion.php");

if(isset($_POST['registrar'])){

    $nombre = $_POST['nombre'];
    $rol = $_POST['rol'];
    $salon = $_POST['salon'];
    $carrera = $_POST['carrera'];
    $correo = $_POST['correo'];

    $nombreImagen = $_FILES['credencial']['name'];
    $tmpImagen = $_FILES['credencial']['tmp_name'];
    
    $tipoArchivo = $_FILES['credencial']['type'];

    $permitidos = [
    "image/jpeg",
    "image/jpg",
    "image/png"
    ];

if(!in_array($tipoArchivo, $permitidos)){
    echo "<script>
    alert('Solo se permiten imágenes JPG, JPEG o PNG');
    </script>";
    exit();
}
    

    $rutaImagen = "uploads/credenciales/" . time() . "_" . $nombreImagen;

    move_uploaded_file($tmpImagen, $rutaImagen);

    $sql = "INSERT INTO usuarios
    (nombre, rol, salon, carrera, correo, credencial)
    VALUES
    (
    '$nombre',
    '$rol',
    '$salon',
    '$carrera',
    '$correo',
    '$rutaImagen'
    )";

    if($conexion->query($sql)){

    session_start();

    $_SESSION['usuario_id'] = $conexion->insert_id;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['rol'] = $rol;

    header("Location: dashboard.php");
    exit();

    }else{
    echo "<script>
    alert('Error al registrar');
    </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Proy Control</title>

<link rel="stylesheet" href="css/estilo.css">

</head>

<body>

<a href="logout.php" class="salir">
    Salir
</a>

<div style="text-align:center;padding-top:30px;">

    <img src="img/Logo1.png" class="Logo1">

    <h1 class="titulo">
        PROY CONTROL
    </h1>

</div>

<div class="contenedor">

<div class="tarjeta">

<h2 class="form-title">
    Solicitud de Registro
</h2>

<form
action=""
method="POST"
enctype="multipart/form-data"
onsubmit="return validarFormulario()"
>

<div class="form-group">

<label>
👤 Nombre Completo
</label>

<input
type="text"
id="nombre"
name="nombre"
required
>

</div>

<div class="form-group">

<label>
🛡️ Rol
</label>

<select
id="rol"
name="rol"
required
>

<option value="">
Seleccione un rol
</option>

<option>
Jefe
</option>

<option>
Subjefe
</option>

<option>
Tercero
</option>

</select>

</div>

<div class="form-group">

<label>
🏫 Salón
</label>

<input
type="text"
name="salon"
required
>

</div>

<div class="form-group">

<label>
🎓 Carrera
</label>

<select
name="carrera"
required
>

<option value="">
Seleccione una carrera
</option>

<option>
Licenciatura en Contaduría Pública
</option>

<option>
Licenciatura en Administración
</option>

<option>
Licenciatura en Informática
</option>

<option>
Licenciatura en Administración Pública
</option>

<option>
Licenciatura en Economía
</option>

</select>

</div>

<div class="form-group">

<label>
📧 Correo Institucional
</label>

<input
type="email"
id="correo"
name="correo"
required
>

</div>

<div class="form-group">

<label>
📷 Credencial
</label>

<input
type="file"
name="credencial"
accept=".jpg,.jpeg,.png"
onchange="mostrarImagen(event)"
required
>

<div class="preview">

<img
id="vista"
src="img/subir.png"
alt="Vista previa"
>

</div>

</div>

<br>

<button
type="submit"
name="registrar"
class="btn"
>

ENTRAR

</button>

</form>

</div>

</div>

<script src="js/validaciones.js"></script>

</body>
</html>