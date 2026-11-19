<?php
session_start();
include("config/conexion.php");

if(isset($_POST['login'])){

    $correo = $_POST['correo'];

    $sql = "SELECT * FROM usuarios WHERE correo='$correo'";
    $resultado = $conexion->query($sql);

    if($resultado && $resultado->num_rows > 0){

        $usuario = $resultado->fetch_assoc();

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        header("Location: dashboard.php");
        exit();

    } else {
        echo "<script>alert('Usuario no encontrado');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Usuario</title>
</head>
<body>

<form action="login.php" method="POST">
    <h2>Login Usuario</h2>

    <input type="email" name="correo" placeholder="Correo institucional" required>

    <button type="submit" name="login">Entrar</button>
</form>

</body>
</html>