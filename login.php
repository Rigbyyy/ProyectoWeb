<?php
session_start();
include("conexion.php");

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $pass = trim($_POST['contraseña']);

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
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .login-container {
        background: #02253e;
        padding: 40px 30px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        width: 380px;
        color: #fff;
    }

    .login-container h3 {
        text-align: center;
        margin-bottom: 25px;
        color: #f5c505;
        font-weight: bold;
    }

    .login-container label {
        font-weight: bold;
    }

    .login-container input {
        border-radius: 8px;
        border: none;
        padding: 10px;
        margin-bottom: 15px;
    }

    .login-container input:focus {
        outline: 2px solid #f5c505;
        box-shadow: 0 0 6px #f5c505;
    }

    .btn-login {
        background-color: #f5c505;
        color: #02253e;
        font-weight: bold;
        border-radius: 8px;
        width: 100%;
        padding: 10px;
        transition: all 0.3s;
    }
    .btn-login:hover {
        background-color: #e0b500;
        color: #02253e;
    }

    .error-msg {
        background-color: #dc3545;
        color: #fff;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        text-align: center;
    }

    .nav-links {
        position: absolute;
        top: 20px;
        left: 20px;
    }

    .nav-links a {
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: bold;
        margin-left: 10px;
        transition: all 0.3s;
        color: #fff;
        background-color: #02253e;
    }

    .nav-links a:hover {
        background-color: #f5c505;
        color: #02253e;
    }
</style>
</head>
<body>

<div class="nav-links">
    <a href="index.php">Volver al inicio</a>
</div>

<div class="login-container">
    <h3>Iniciar Sesión</h3>
    <?php if($error): ?>
        <div class="error-msg"><?= $error ?></div>
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
        <button type="submit" class="btn-login">Ingresar</button>
    </form>
</div>

</body>
</html>
