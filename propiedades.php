<?php
session_start();
include("conexion.php");

// Verificar sesión
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id'];
$privilegio = $_SESSION['privilegio'];
$msg = "";

// Agregar nueva propiedad
if(isset($_POST['agregar'])){
    $tipo = $_POST['tipo'];
    $destacada = isset($_POST['destacada']) ? 1 : 0;
    $titulo = $_POST['titulo'];
    $descripcion_breve = $_POST['descripcion_breve'];
    $precio = $_POST['precio'];
    $descripcion_larga = $_POST['descripcion_larga'];
    $mapa = $_POST['mapa'];
    $ubicacion = $_POST['ubicacion'];

    $imagen_destacada = "";
    if(!empty($_FILES['imagen_destacada']['name'])){
        $nombre = time() . "_" . basename($_FILES['imagen_destacada']['name']);
        $directorio = "uploads/";
        move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $directorio.$nombre);
        $imagen_destacada = $directorio.$nombre;
    }

    $sql = "INSERT INTO propiedades 
            (tipo, destacada, titulo, descripcion_breve, precio, agente_id, imagen_destacada, descripcion_larga, mapa, ubicacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param("sissdiisss", $tipo, $destacada, $titulo, $descripcion_breve, $precio, $usuario_id, $imagen_destacada, $descripcion_larga, $mapa, $ubicacion);
    $stmt->execute();
    $msg = "Propiedad agregada correctamente.";
}

// Eliminar propiedad
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    if($privilegio == 'admin'){
        $conect->query("DELETE FROM propiedades WHERE id=$id");
    } else {
        $conect->query("DELETE FROM propiedades WHERE id=$id AND agente_id=$usuario_id");
    }
    $msg = "Propiedad eliminada correctamente.";
}

// Obtener propiedades
if($privilegio == 'admin'){
    $propiedades = $conect->query("SELECT p.*, u.usuario as agente FROM propiedades p JOIN usuarios u ON p.agente_id=u.id");
} else {
    $propiedades = $conect->query("SELECT * FROM propiedades WHERE agente_id=$usuario_id");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Propiedades</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f0f2f5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Formulario central */
.form-card {
    background-color: #02253e;
    color: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.2);
    margin-bottom: 40px;
}
.form-card label {
    font-weight: bold;
}
.form-card input, 
.form-card textarea, 
.form-card select {
    border-radius: 8px;
    border: none;
    padding: 8px 12px;
}
.form-card input:focus,
.form-card textarea:focus,
.form-card select:focus {
    outline: 2px solid #f5c505;
    box-shadow: 0 0 6px #f5c505;
}

.button-agregar {
    background-color: #f5c505;
    color: #02253e;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    width: 100%;
    transition: all 0.3s;
}
.button-agregar:hover {
    background-color: #55470dff;
    color: #011f33;
}

/* Cards de propiedades */
.destacadas {
    background-color: #02253e;
    color: white;
    padding: 40px 0;
}
.prop-card {
    background-color: #021c33;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 30px;
    transition: transform 0.25s, box-shadow 0.25s;
    text-align: center;
    position: relative;
}
.prop-card:hover {
    transform: scale(1.03);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}
.prop-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}
.prop-card h5 {
    margin-bottom: 8px;
    font-size: 1.2rem;
}
.prop-card p {
    margin-bottom: 8px;
}
.prop-card .precio {
    color: #f5c505;
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 12px;
    text-shadow: 0.5px 0.5px 0 #000;
}

/* Botones de acciones dentro de la card */
.prop-card .acciones {
    display: flex;
    justify-content: center;
    gap: 10px;
}
.prop-card .acciones a {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: bold;
    transition: all 0.2s;
}
.prop-card .acciones .editar {
    background-color: #f5c505;
    color: #02253e;
}
.prop-card .acciones .editar:hover {
    background-color: #e0b500;
}
.prop-card .acciones .eliminar {
    background-color: #dc3545;
    color: #fff;
}
.prop-card .acciones .eliminar:hover {
    background-color: #b52d3a;
}

/* Títulos secciones */
.section-title {
    text-align: center;
    margin-bottom: 30px;
    font-size: 1.8rem;
    font-weight: bold;
}

/* Ajustes responsive */
@media (max-width: 768px){
    .prop-card img { height: 180px; }
}
</style>


</style>
</head>
<body class="container mt-4">

<h2 class="mb-4 section-title">Administrar Propiedades</h2>

<!-- Formulario -->
<div class="form-card">
<form method="POST" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-3">
        <label>Tipo</label>
        <select name="tipo" class="form-control" required>
            <option value="alquiler">Alquiler</option>
            <option value="venta">Venta</option>
        </select>
    </div>
    <div class="col-md-2">
        <label>Destacada</label>
        <input type="checkbox" name="destacada">
    </div>
    <div class="col-md-4">
        <label>Título</label>
        <input type="text" name="titulo" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label>Precio</label>
        <input type="number" step="0.01" name="precio" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Descripción Breve</label>
        <input type="text" name="descripcion_breve" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Imagen Destacada</label>
        <input type="file" name="imagen_destacada" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Descripción Larga</label>
        <textarea name="descripcion_larga" class="form-control"></textarea>
    </div>
    <div class="col-md-3">
        <label>Mapa</label>
        <input type="text" name="mapa" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Ubicación</label>
        <input type="text" name="ubicacion" class="form-control">
    </div>
    <div class="col-12">
        <button type="submit" name="agregar" class="button-agregar">Agregar Propiedad</button>
    </div>
</form>
</div>

<!-- Cards visualización -->
<section class="destacadas">
    <div class="container">
        <h2 class="section-title">Mis Propiedades</h2>
        <div class="row">
        <?php
        $propiedades->data_seek(0);
        while($prop = $propiedades->fetch_assoc()): ?>
            <div class="col-md-4">
             <div class="prop-card">
              <a href="detalle.php?id=<?= $prop['id'] ?>" class="full-link"></a>
              <img src="<?= $prop['imagen_destacada'] ?>" alt="Imagen Propiedad">
              <h5><?= $prop['titulo'] ?></h5>
              <p><?= $prop['descripcion_breve'] ?></p>
              <p class="precio">Precio: $<?= number_format($prop['precio'], 2) ?></p>
              <div class="acciones">
                 <a href="editar_propiedad.php?id=<?= $prop['id'] ?>" class="editar">Editar</a>
                 <a href="propiedades.php?eliminar=<?= $prop['id'] ?>" class="eliminar" onclick="return confirm('¿Eliminar propiedad?')">Eliminar</a>
              </div>
             </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
</section>
</body>
</html>