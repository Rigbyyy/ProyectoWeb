<?php
session_start();
include("conexion.php");

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id'];
$privilegio = $_SESSION['privilegio'];
$msg = "";

// Agregar nuevo usuario (solo admin)
if (isset($_POST['agregar']) && $privilegio == 'admin') {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $usuario = $_POST['usuario'];
    $contraseña = md5($_POST['contraseña']); // hash simple
    $priv = $_POST['privilegio'];

    // Verificar si el usuario ya existe
    $check = $conect->query("SELECT id FROM usuarios WHERE usuario='$usuario'");
    if ($check->num_rows > 0) {
        $msg = "El nombre de usuario ya existe, elige otro.";
    } else {
        $stmt = $conect->prepare("INSERT INTO usuarios (nombre, telefono, correo, usuario, contraseña, privilegio) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $telefono, $correo, $usuario, $contraseña, $priv);

        if ($stmt->execute()) {
            $msg = "Usuario agregado correctamente.";
        } else {
            $msg = "Error: " . $stmt->error;
        }
    }
}

// Eliminar usuario (solo admin)
if (isset($_GET['eliminar']) && $privilegio == 'admin') {
    $id = $_GET['eliminar'];
    $conect->query("DELETE FROM usuarios WHERE id=$id");
    $msg = "Usuario eliminado correctamente.";
}

// Obtener usuarios
if ($privilegio == 'admin') {
    $usuarios = $conect->query("SELECT * FROM usuarios");
} else {
    $usuarios = $conect->query("SELECT * FROM usuarios WHERE id=$usuario_id");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Usuarios</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { max-width: 1000px; margin-top: 40px; }
h2 { text-align: center; margin-bottom: 30px; color: #150d3e; }
.form-card { background-color: #02253e; color: white; padding: 25px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); margin-bottom: 40px; }
.form-card label { font-weight: bold; }
.form-card input, .form-card select { border-radius: 8px; border: none; padding: 8px 12px; margin-bottom: 10px; }
.form-card input:focus, .form-card select:focus { outline: 2px solid #f5c505; box-shadow: 0 0 6px #f5c505; }
.button-agregar { background-color: #f5c505; color: #02253e; border: none; padding: 12px; border-radius: 8px; font-weight: bold; width: 100%; transition: all 0.3s; }
.button-agregar:hover { background-color: #55470dff; color: #011f33; }
.table { background-color: #02253e; color: white; border-radius: 12px; overflow: hidden; }
.table th, .table td { text-align: center; vertical-align: middle; }
.table th { background-color: #150d3e; }
.btn-editar { background-color: #f5c505; color: #02253e; font-weight: bold; }
.btn-editar:hover { background-color: #e0b500; color: #02253e; }
.btn-eliminar { background-color: #dc3545; color: #fff; font-weight: bold; }
.btn-eliminar:hover { background-color: #b52d3a; }
.alert { margin-top: 20px; }

.nav-links a {
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: bold;
    margin-left: 10px;
    transition: all 0.3s;
    color: #fff;
    background-color: #02253e; /* azul oscuro */
}
.nav-links a:hover {
    background-color: #f5c505; /* amarillo al pasar el mouse */
    color: #02253e;
}
.logout-link {
    background-color: #dc3545; /* rojo */
}
.logout-link:hover {
    background-color: #b52d3a;
    color: #fff;
}

</style>
</head>
<body>
    
<div class="container">
<div class="header d-flex justify-content-between align-items-center mb-4">
<h2 class="mb-4 section-title m-0">Gestion de usuarios</h2>
    <div class="nav-links d-flex align-items-center justify-content-end flex-grow-1 ">
        <a href="index.php" class="nav-link me-2">Inicio</a>
        <a href="logout.php" class="nav-link me-2 logout-link">Cerrar Sesión</a>
    </div>

</div>

<?php if($msg): ?>
<div class="alert alert-info"><?= $msg ?></div>
<?php endif; ?>

<?php if($privilegio == 'admin'): ?>
<!-- Formulario agregar usuario -->
<div class="form-card">
<form method="POST" class="row g-3">
    <div class="col-md-6">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Correo</label>
        <input type="email" name="correo" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Usuario</label>
        <input type="text" name="usuario" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Contraseña</label>
        <input type="password" name="contraseña" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Privilegio</label>
        <select name="privilegio" class="form-control" required>
            <option value="admin">Administrador</option>
            <option value="agente">Agente de ventas</option>
        </select>
    </div>
    <div class="col-12">
        <button type="submit" name="agregar" class="button-agregar">Agregar Usuario</button>
    </div>
</form>
</div>
<?php endif; ?>

<!-- Tabla usuarios -->
<table class="table table-striped table-hover">
<thead>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Teléfono</th>
    <th>Correo</th>
    <th>Usuario</th>
    <th>Privilegio</th>
    <?php if($privilegio == 'admin') echo "<th>Acciones</th>"; ?>
</tr>
</thead>
<tbody>
<?php while($u = $usuarios->fetch_assoc()): ?>
<tr>
    <td><?= $u['id'] ?></td>
    <td><?= $u['nombre'] ?></td>
    <td><?= $u['telefono'] ?></td>
    <td><?= $u['correo'] ?></td>
    <td><?= $u['usuario'] ?></td>
    <td><?= ucfirst($u['privilegio']) ?></td>
    <?php if($privilegio == 'admin'): ?>
    <td>
        <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-editar btn-sm">Editar</a>
        <a href="usuarios.php?eliminar=<?= $u['id'] ?>" class="btn btn-eliminar btn-sm" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
    </td>
    <?php endif; ?>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</body>
</html>
