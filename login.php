<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $pass = $_POST['contraseña'];

    $sql = "SELECT * FROM usuarios WHERE usuario=? AND contraseña=MD5(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['privilegio'] = $user['privilegio'];
        $_SESSION['id'] = $user['id'];
        header("Location: dashboard.php");
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
