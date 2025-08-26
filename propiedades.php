<?php
session_start();
include("conexion.php");


if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id'];
$privilegio = $_SESSION['privilegio'];
$msg = "";


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

        if(move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $directorio.$nombre)){
            $imagen_destacada = $directorio.$nombre; 
        } else {
            $msg = "Error al subir la imagen. Intenta nuevamente.";
        }
    } else {
        $msg = "Debe subir una imagen de la propiedad.";
    }

    
    if(!empty($imagen_destacada)){
        $sql = "INSERT INTO propiedades 
                (tipo, destacada, titulo, descripcion_breve, precio, agente_id, imagen_destacada, descripcion_larga, mapa, ubicacion)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param("sissdissss", $tipo, $destacada, $titulo, $descripcion_breve, $precio, $usuario_id, $imagen_destacada, $descripcion_larga, $mapa, $ubicacion);

        if($stmt->execute()){
            $msg = "Propiedad agregada correctamente.";
        } else {
            $msg = "Error al guardar la propiedad en la base de datos.";
        }
    }
}



if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];

      
    if($privilegio == 'admin'){
        $resultado = $conect->query("SELECT imagen_destacada FROM propiedades WHERE id=$id");
    } else {
        $resultado = $conect->query("SELECT imagen_destacada FROM propiedades WHERE id=$id AND agente_id=$usuario_id");
    }
    if($resultado && $resultado->num_rows > 0){
        $propiedad = $resultado->fetch_assoc();
        $imagen_path = $propiedad['imagen_destacada'];

        if(file_exists($imagen_path) && !empty($imagen_path)){
            unlink($imagen_path);
        }
    }

   
    if($privilegio == 'admin'){
        $conect->query("DELETE FROM propiedades WHERE id=$id");
    } else {
        $conect->query("DELETE FROM propiedades WHERE id=$id AND agente_id=$usuario_id");
    }
    $msg = "Propiedad eliminada correctamente.";
}


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
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.form-card { background-color: #02253e; color: white; padding: 25px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); margin-bottom: 40px; }
.form-card label { font-weight: bold; }
.form-card input, .form-card textarea, .form-card select { border-radius: 8px; border: none; padding: 8px 12px; }
.form-card input:focus, .form-card textarea:focus, .form-card select:focus { outline: 2px solid #f5c505; box-shadow: 0 0 6px #f5c505; }
.button-agregar { background-color: #f5c505; color: #02253e; border: none; padding: 12px; border-radius: 8px; font-weight: bold; width: 100%; transition: all 0.3s; }
.button-agregar:hover { background-color: #55470dff; color: #011f33; }
.destacadas { background-color: #02253e; color: white; padding: 40px 0; }
.prop-card { background-color: #021c33; border-radius: 12px; padding: 15px; margin-bottom: 30px; transition: transform 0.25s, box-shadow 0.25s; text-align: center; position: relative; }
.prop-card:hover { transform: scale(1.03); box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
.prop-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
.prop-card h5 { margin-bottom: 8px; font-size: 1.2rem; }
.prop-card p { margin-bottom: 8px; }
.prop-card .precio { color: #f5c505; font-weight: bold; font-size: 1.1rem; margin-bottom: 12px; text-shadow: 0.5px 0.5px 0 #000; }
.prop-card .acciones { display: flex; justify-content: center; gap: 10px; }
.prop-card .acciones a { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: bold; transition: all 0.2s; }
.prop-card .acciones .editar { background-color: #f5c505; color: #02253e; }
.prop-card .acciones .editar:hover { background-color: #e0b500; }
.prop-card .acciones .eliminar { background-color: #dc3545; color: #fff; }
.prop-card .acciones .eliminar:hover { background-color: #b52d3a; }
.section-title { text-align: center; margin-bottom: 30px; font-size: 1.8rem; font-weight: bold; }
.full-link { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
@media (max-width: 768px){ .prop-card img { height: 180px; } }
#preview { margin-top:10px; max-width:100%; max-height:200px; border-radius:8px; }

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
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body class="container mt-4">

<div class="header d-flex justify-content-between align-items-center mb-4">
<h2 class="mb-4 section-title m-0">Administrar Propiedades</h2>
    <div class="nav-links d-flex align-items-center justify-content-end flex-grow-1 ">
        <a href="index.php" class="nav-link me-2">Inicio</a>
        <a href="logout.php" class="nav-link me-2">Cerrar Sesion</a>
    </div>

</div>


<?php if($msg): ?>
<div class="alert alert-info"><?= $msg ?></div>
<?php endif; ?>


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
        <input type="file" name="imagen_destacada" class="form-control" accept="image/*" onchange="previewImage(event)" required>
        <img id="preview" src="#" alt="Preview" style="display:none;">
    </div>
    <div class="col-md-6">
        <label>Descripción Larga</label>
        <textarea name="descripcion_larga" class="form-control"></textarea>
    </div>
    <div class="col-md-6">
        <label>Ubicación en Mapa</label>
        <div id="map" style="height: 300px; border:1px solid #ccc; border-radius:8px;"></div>
        <input type="hidden" name="mapa" id="mapa">
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


<section class="destacadas">
    <div class="container">
        <h2 class="section-title">Mis Propiedades</h2>
        <div class="row">
        <?php
        $propiedades->data_seek(0);
        while($prop = $propiedades->fetch_assoc()): ?>
            <div class="col-md-4">
            <div class="prop-card position-relative">
                 <a href="detalle.php?id=<?= $prop['id'] ?>&from=propiedades" class="full-link"></a>
                 <?php if($prop['imagen_destacada']): ?>
                     <img src="<?= $prop['imagen_destacada'] ?>" alt="Imagen Propiedad">
                 <?php else: ?>
                     <img src="uploads/default.png" alt="Sin imagen">
                 <?php endif; ?>
                 <h5 class="mt-2"><?= $prop['titulo'] ?></h5>
                 <p><?= $prop['descripcion_breve'] ?></p>
                 <p class="precio">Precio: $<?= number_format($prop['precio'], 2) ?></p>
                 <div class="acciones" style="position: relative; z-index: 2;">
                    <a href="editar_propiedad.php?id=<?= $prop['id'] ?>" class="editar">Editar</a>
                    <a href="propiedades.php?eliminar=<?= $prop['id'] ?>" class="eliminar" onclick="return confirm('¿Eliminar propiedad?')">Eliminar</a>
                 </div>
              </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
</section>

<script>

function previewImage(event){
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('preview');
        output.src = reader.result;
        output.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}


var map = L.map('map').setView([9.9780, -84.6660], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
var marker = L.marker([9.9780, -84.6660], {draggable:true}).addTo(map);

function generarIframe(lat, lng){
    var iframe = '<iframe src="https://maps.google.com/?q=' + lat + ',' + lng + '&output=embed" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
    document.getElementById('mapa').value = iframe;
}

generarIframe(9.9780, -84.6660);

marker.on('dragend', function(e){
    var pos = marker.getLatLng();
    generarIframe(pos.lat, pos.lng);
});

map.on('click', function(e){
    marker.setLatLng(e.latlng);
    generarIframe(e.latlng.lat, e.latlng.lng);
});
</script>

</body>
</html>
