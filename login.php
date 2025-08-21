<?php
session_start();
session_unset();
session_destroy();
session_start();

include("conexion.php");

// Si ya hay sesión iniciada, redirigir según privilegio
if(isset($_SESSION['usuario'])){
    if($_SESSION['privilegio'] == 'admin'){
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: propiedades.php");
        exit();
    }
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $pass = trim($_POST['contraseña']);

    // Validación simple
    if(empty($usuario) || empty($pass)){
        $error = "Por favor, complete todos los campos.";
    } else {
        $sql = "SELECT * FROM usuarios WHERE usuario=? AND contraseña=MD5(?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param("ss", $usuario, $pass);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['privilegio'] = $user['privilegio'];
            $_SESSION['id'] = $user['id'];

            // Redirigir según privilegio
            if($user['privilegio'] == 'admin'){
                header("Location: dashboard.php");
            } else {
                header("Location: propiedades.php");
            }
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login - Inmobiliaria</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; display:flex; justify-content:center; align-items:center; height:100vh; font-family: 'Segoe UI', sans-serif; }
    .login-box { background:white; padding:30px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.2); width:350px; }
</style>
</head>
<body>
<div class="login-box">
    <h3 class="text-center mb-4">Iniciar Sesión</h3>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Usuario</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="contraseña" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>
</div>
</body>
</html>
