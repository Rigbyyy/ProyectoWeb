<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id'];
$privilegio = $_SESSION['privilegio'];
$msg = "";

if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$id = $_GET['id'];

if ($privilegio != 'admin' && $id != $usuario_id) {
    header("Location: usuarios.php");
    exit();
}

$result = $conect->query("SELECT * FROM usuarios WHERE id=$id");
if ($result->num_rows == 0) {
    header("Location: usuarios.php");
    exit();
}
$usuario = $result->fetch_assoc();

if (isset($_POST['actualizar'])) {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $usuario_txt = $_POST['usuario'];

    if (!empty($_POST['contraseña'])) {
        $contraseña = md5($_POST['contraseña']);
        $sql = "UPDATE usuarios SET nombre=?, telefono=?, correo=?, usuario=?, contraseña=? " . 
               ($privilegio == 'admin' ? ", privilegio=?" : "") . " WHERE id=?";
    } else {
        $sql = "UPDATE usuarios SET nombre=?, telefono=?, correo=?, usuario=? " . 
               ($privilegio == 'admin' ? ", privilegio=?" : "") . " WHERE id=?";
    }

    if ($stmt = $conect->prepare($sql)) {
        if (!empty($_POST['contraseña'])) {
            if ($privilegio == 'admin') {
                $stmt->bind_param("ssssssi", $nombre, $telefono, $correo, $usuario_txt, $contraseña, $_POST['privilegio'], $id);
            } else {
                $stmt->bind_param("sssssi", $nombre, $telefono, $correo, $usuario_txt, $contraseña, $id);
            }
        } else {
            if ($privilegio == 'admin') {
                $stmt->bind_param("sssssi", $nombre, $telefono, $correo, $usuario_txt, $_POST['privilegio'], $id);
            } else {
                $stmt->bind_param("ssssi", $nombre, $telefono, $correo, $usuario_txt, $id);
            }
        }

        if ($stmt->execute()) {
            $msg = "Usuario actualizado correctamente.";
            $usuario = $stmt->close() ? $usuario : $usuario;
        } else {
            $msg = "Error al actualizar: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Usuario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { max-width: 800px; margin-top: 40px; }
h2 { text-align: center; margin-bottom: 30px; color: #150d3e; }
.form-card { background-color: #02253e; color: white; padding: 25px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); margin-bottom: 40px; }
.form-card label { font-weight: bold; }
.form-card input, .form-card select { border-radius: 8px; border: none; padding: 8px 12px; margin-bottom: 10px; }
.form-card input:focus, .form-card select:focus { outline: 2px solid #f5c505; box-shadow: 0 0 6px #f5c505; }
.button-actualizar { background-color: #f5c505; color: #02253e; border: none; padding: 12px; border-radius: 8px; font-weight: bold; width: 100%; transition: all 0.3s; }
.button-actualizar:hover { background-color: #55470dff; color: #011f33; }
.alert { margin-top: 20px; }

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
.logout-link {
    background-color: #dc3545; 
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
    <h2 class="m-0">Editar Usuario</h2>
    <div class="nav-links d-flex gap-2">
        <a href="usuarios.php" class="nav-link">Volver</a>
        <a href="logout.php" class="nav-link logout-link">Cerrar Sesión</a>
    </div>
</div>



<?php if($msg): ?>
<div class="alert alert-info"><?= $msg ?></div>
<?php endif; ?>

<div class="form-card">
<form method="POST" class="row g-3">
    <div class="col-md-6">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= $usuario['nombre'] ?>" required>
    </div>
    <div class="col-md-6">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?= $usuario['telefono'] ?>">
    </div>
    <div class="col-md-6">
        <label>Correo</label>
        <input type="email" name="correo" class="form-control" value="<?= $usuario['correo'] ?>">
    </div>
    <div class="col-md-6">
        <label>Usuario</label>
        <input type="text" name="usuario" class="form-control" value="<?= $usuario['usuario'] ?>" required>
    </div>
    <div class="col-md-6">
        <label>Contraseña (dejar en blanco para no cambiar)</label>
        <input type="password" name="contraseña" class="form-control">
    </div>
    <?php if($privilegio == 'admin'): ?>
    <div class="col-md-6">
        <label>Privilegio</label>
        <select name="privilegio" class="form-control">
            <option value="admin" <?= $usuario['privilegio']=='admin'?'selected':'' ?>>Administrador</option>
            <option value="agente" <?= $usuario['privilegio']=='agente'?'selected':'' ?>>Agente de ventas</option>
        </select>
    </div>
    <?php endif; ?>
    <div class="col-12">
        <button type="submit" name="actualizar" class="button-actualizar">Actualizar Usuario</button>
    </div>
</form>
</div>
</div>
</body>
</html>
