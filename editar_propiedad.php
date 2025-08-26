<?php
session_start();
include("conexion.php");


if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: propiedades.php");
    exit();
}
$propiedad_id = $_GET['id'];
$usuario_id = $_SESSION['id'];
$privilegio = $_SESSION['privilegio'];

if($privilegio == 'admin'){
    $sql = "SELECT * FROM propiedades WHERE id=?";
} else {
    $sql = "SELECT * FROM propiedades WHERE id=? AND agente_id=?";
}
$stmt = $conect->prepare($sql);
if($privilegio == 'admin'){
    $stmt->bind_param("i", $propiedad_id);
} else {
    $stmt->bind_param("ii", $propiedad_id, $usuario_id);
}
$stmt->execute();
$result = $stmt->get_result();
$propiedad = $result->fetch_assoc();

if(!$propiedad){
    die("Propiedad no encontrada o no tienes permisos.");
}

$msg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $tipo = $_POST['tipo'];
    $destacada = isset($_POST['destacada']) ? 1 : 0;
    $titulo = $_POST['titulo'];
    $descripcion_breve = $_POST['descripcion_breve'];
    $precio = $_POST['precio'];
    $descripcion_larga = $_POST['descripcion_larga'];
    $mapa = $_POST['mapa'];
    $ubicacion = $_POST['ubicacion'];

    $imagen_destacada = $propiedad['imagen_destacada'];
    if(!empty($_FILES['imagen_destacada']['name'])){
        $nombre = time() . "_" . basename($_FILES['imagen_destacada']['name']);
        $directorio = "uploads/";
        move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $directorio.$nombre);
        $imagen_destacada = $directorio.$nombre;
    }

    $sql = "UPDATE propiedades SET tipo=?, destacada=?, titulo=?, descripcion_breve=?, precio=?, imagen_destacada=?, descripcion_larga=?, mapa=?, ubicacion=? WHERE id=?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param("sissdssssi", $tipo, $destacada, $titulo, $descripcion_breve, $precio, $imagen_destacada, $descripcion_larga, $mapa, $ubicacion, $propiedad_id);
    $stmt->execute();

    $msg = "Propiedad actualizada correctamente.";

    header("Location: propiedades.php");
    exit();

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Propiedad</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h2>Editar Propiedad</h2>
<?php if($msg): ?>
    <div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-3">
        <label>Tipo</label>
        <select name="tipo" class="form-control" required>
            <option value="alquiler" <?= $propiedad['tipo']=='alquiler'?'selected':'' ?>>Alquiler</option>
            <option value="venta" <?= $propiedad['tipo']=='venta'?'selected':'' ?>>Venta</option>
        </select>
    </div>
    <div class="col-md-2">
        <label>Destacada</label>
        <input type="checkbox" name="destacada" <?= $propiedad['destacada']?'checked':'' ?>>
    </div>
    <div class="col-md-4">
        <label>Título</label>
        <input type="text" name="titulo" class="form-control" value="<?= $propiedad['titulo'] ?>" required>
    </div>
    <div class="col-md-3">
        <label>Precio</label>
        <input type="number" step="0.01" name="precio" class="form-control" value="<?= $propiedad['precio'] ?>" required>
    </div>
    <div class="col-md-6">
        <label>Descripción Breve</label>
        <input type="text" name="descripcion_breve" class="form-control" value="<?= $propiedad['descripcion_breve'] ?>" required>
    </div>
    <div class="col-md-6">
        <label>Imagen Destacada</label>
        <input type="file" name="imagen_destacada" class="form-control">
        <?php if($propiedad['imagen_destacada']): ?>
            <img src="<?= $propiedad['imagen_destacada'] ?>" height="80" class="mt-2">
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label>Descripción Larga</label>
        <textarea name="descripcion_larga" class="form-control"><?= $propiedad['descripcion_larga'] ?></textarea>
    </div>
    <div class="col-md-3">
        <label>Mapa</label>
        <input type="text" name="mapa" class="form-control" value="<?= $propiedad['mapa'] ?>">
    </div>
    <div class="col-md-3">
        <label>Ubicación</label>
        <input type="text" name="ubicacion" class="form-control" value="<?= $propiedad['ubicacion'] ?>">
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-success ">Guardar Cambios</button>
        <a href="propiedades.php" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

</body>
</html>
