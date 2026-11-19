<?php
include("../config/conexion.php");
session_start();

if(isset($_POST['login'])){

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM supervisor 
    WHERE usuario='$usuario' 
    AND password='$password'";
    
    $resultado = mysqli_query($conexion, $sql);

    if(mysqli_num_rows($resultado) > 0){

        $_SESSION['supervisor'] = $usuario;

        header("Location: panel_supervisor.php");
        exit();

    } else {
        echo "<script>alert('Datos incorrectos');</script>";
    }
}
?>

<form action="login_supervisor.php" method="POST">
    <h2>Login Supervisor</h2>

    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>

    <button type="submit" name="login">Entrar</button>
</form>