<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Usuario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f0f2f5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 50px 20px;
}

h2 {
    color: #02253e;
    margin-bottom: 40px;
    text-align: center;
}

/* Botones de navegación */
.nav-btn {
    display: block;
    width: 200px;
    text-align: center;
    padding: 15px 0;
    margin: 15px 0;
    font-weight: bold;
    text-decoration: none;
    color: #fff;
    background-color: #02253e; /* azul oscuro */
    border-radius: 12px;
    transition: all 0.3s;
}

.nav-btn:hover {
    background-color: #f5c505; /* amarillo */
    color: #02253e;
    transform: scale(1.05);
}

.container-btns {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 300px;
}
</style>
</head>
<body>

<h2>Bienvenido <?= $_SESSION['usuario'] ?></h2>

<div class="container-btns">
    <?php if ($_SESSION['privilegio'] == 'admin'): ?>
        <a href="personalizar.php" class="nav-btn">Personalizar Página</a>
        <a href="usuarios.php" class="nav-btn">Gestión de Usuarios</a>
    <?php endif; ?>
    
    <a href="propiedades.php" class="nav-btn">Gestión de Propiedades</a>
    <a href="logout.php" class="nav-btn">Cerrar Sesión</a>
</div>

</body>
</html>
