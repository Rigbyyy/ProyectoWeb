<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("conexion.php");

// Obtener configuración actual
$result = $conect->query("SELECT * FROM configuracion LIMIT 1");
$config = $result->fetch_assoc();

// Valores por defecto si faltan
$color_principal = $config['color_tema'] ?? '#f5c505'; // color botones y acentos
$banner_texto    = $config['banner_texto'] ?? '';
$direccion       = $config['direccion'] ?? '';
$telefono        = $config['telefono'] ?? '';
$email           = $config['email'] ?? '';
$facebook        = $config['facebook'] ?? '';
$instagram       = $config['instagram'] ?? '';
$twitter         = $config['twitter'] ?? '';
$quienes_somos   = $config['quienes_somos'] ?? '';
$icono_principal = $config['icono_principal'] ?? '';
$icono_blanco    = $config['icono_blanco'] ?? '';
$banner_imagen   = $config['banner_imagen'] ?? '';
$quienes_imagen  = $config['quienes_imagen'] ?? '';

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    function subirArchivo($campo, $directorio = "uploads/") {
        if (!empty($_FILES[$campo]['name'])) {
            if (!is_dir($directorio)) mkdir($directorio, 0755, true);
            $nombre = time() . "_" . basename($_FILES[$campo]["name"]);
            $ruta = $directorio . $nombre;
            move_uploaded_file($_FILES[$campo]["tmp_name"], $ruta);
            return $ruta;
        }
        return null;
    }

    $color_principal = $_POST['color_principal'];
    $banner_texto    = $_POST['banner_texto'];
    $direccion       = $_POST['direccion'];
    $telefono        = $_POST['telefono'];
    $email           = $_POST['email'];
    $facebook        = $_POST['facebook'];
    $instagram       = $_POST['instagram'];
    $twitter         = $_POST['twitter'];
    $quienes_somos   = $_POST['quienes_somos'];

    $icono_principal_up = subirArchivo("icono_principal");
    $icono_blanco_up    = subirArchivo("icono_blanco");
    $banner_imagen_up   = subirArchivo("banner_imagen");
    $quienes_imagen_up  = subirArchivo("quienes_imagen");

    $sql = "UPDATE configuracion SET 
        color_tema=?, banner_texto=?, direccion=?, telefono=?, email=?, 
        facebook=?, instagram=?, twitter=?, quienes_somos=?";

    if($icono_principal_up) $sql .= ", icono_principal='$icono_principal_up'";
    if($icono_blanco_up) $sql .= ", icono_blanco='$icono_blanco_up'";
    if($banner_imagen_up) $sql .= ", banner_imagen='$banner_imagen_up'";
    if($quienes_imagen_up) $sql .= ", quienes_imagen='$quienes_imagen_up'";

    $sql .= " WHERE id=" . $config['id'];

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $color_principal, $banner_texto, $direccion, $telefono, $email,
                                    $facebook, $instagram, $twitter, $quienes_somos);
    $stmt->execute();

    header("Location: personalizar.php?ok=1");
    exit();
}

// Obtener propiedades para preview
$propiedades_res = $conect->query("SELECT * FROM propiedades ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Personalizar Página</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { font-family: 'Segoe UI', sans-serif; margin:20px; }
    .preview { margin-top:20px; border:1px solid #ccc; }

    /* HEADER */
    .header-top { display:flex; justify-content:space-between; align-items:center; padding:10px 30px; background:#fff; border-bottom:1px solid #ddd; }
    .header-left { display:flex; align-items:center; gap:15px; }
    .header-left img { height:60px; }
    .header-left .social-icons i { font-size:20px; margin-right:10px; color:#333; }
    .header-right { text-align:right; }
    .header-right img { height:50px; cursor:pointer; }
    .nav-links a { margin-left:15px; color:#333; text-decoration:none; font-weight:500; }
    .nav-links a:hover { text-decoration:underline; }

    /* BANNER */
    .banner { background-size:cover; background-position:center; height:350px; color:white; display:flex; align-items:center; justify-content:center; text-shadow: 2px 2px 6px #000; }

    /* SECCIONES */
    .destacadas, .enalquiler { background-color: #02253e; color:white; padding:30px 0; }
    .enventa { background-color:#fff; color:#000; padding:30px 0; }
    .prop-card { border-radius:10px; padding:15px; margin-bottom:30px; transition: transform 0.2s; position:relative; cursor:pointer; }
    .prop-card:hover { transform: scale(1.03); box-shadow:0 5px 15px rgba(0,0,0,0.2); }
    .prop-card img { width:100%; height:200px; object-fit:cover; border-radius:5px; }
    .prop-card h5, .prop-card p, .prop-card .precio { text-align:center; }
    .prop-card a.full-link { position:absolute; top:0; left:0; width:100%; height:100%; z-index:1; }
    .prop-card .btn { position:relative; z-index:2; }
    .destacadas .prop-card .precio, .enalquiler .prop-card .precio { color:#f5c505; font-weight:bold; font-size:1.2rem; text-shadow:0.5px 0.5px 0 #000; }
    .enventa .prop-card .precio { color:#02253e; font-weight:bold; font-size:1.2rem; text-shadow:0.5px 0.5px 0 #000; }

    /* QUIÉNES SOMOS */
    .qs-content { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; padding:30px; }
    .qs-content img { width:350px; border-radius:10px; }

    /* FOOTER */
    .footer-yellow { background-color:#f5c505; color:#333; padding:40px 0; display:flex; flex-wrap:wrap; justify-content:space-between; }
    .footer-yellow .footer-col { flex:1; padding:10px; }
    .footer-yellow .social-icons i { font-size:24px; margin:0 5px; }
    .contact-form { background-color:#f0f0f0; padding:20px; border-radius:10px; }
    .contact-form input, .contact-form textarea { margin-bottom:10px; }
    .contact-form button { background-color:#02253e; color:#f5c505; border:none; width:100%; padding:10px; font-weight:bold; }
</style>
</head>
<body>

<h2>Personalizar Página</h2>
<?php if(isset($_GET['ok'])): ?>
    <div class="alert alert-success">Cambios guardados correctamente</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="row g-3">
    <!-- Color principal -->
    <div class="col-md-4">
        <label>Color Principal (Botones y acentos)</label>
        <input type="color" id="colorPicker" value="<?= $color_principal ?>" onchange="cambiarColorPrincipal(this.value)">
        <input type="hidden" name="color_principal" id="color_hidden" value="<?= $color_principal ?>">
    </div>

    <!-- Banner -->
    <div class="col-md-6">
        <label>Imagen del Banner</label>
        <input type="file" name="banner_imagen" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Texto del Banner</label>
        <input type="text" name="banner_texto" class="form-control" value="<?= $banner_texto ?>">
    </div>

    <!-- Quiénes Somos -->
    <div class="col-md-12">
        <label>Texto Quiénes Somos</label>
        <textarea name="quienes_somos" class="form-control" rows="3"><?= $quienes_somos ?></textarea>
    </div>
    <div class="col-md-6">
        <label>Imagen Quiénes Somos</label>
        <input type="file" name="quienes_imagen" class="form-control">
    </div>

    <!-- Contacto -->
    <div class="col-md-4"><label>Facebook</label><input type="text" name="facebook" class="form-control" value="<?= $facebook ?>"></div>
    <div class="col-md-4"><label>Instagram</label><input type="text" name="instagram" class="form-control" value="<?= $instagram ?>"></div>
    <div class="col-md-4"><label>Twitter</label><input type="text" name="twitter" class="form-control" value="<?= $twitter ?>"></div>
    <div class="col-md-6"><label>Dirección</label><input type="text" name="direccion" class="form-control" value="<?= $direccion ?>"></div>
    <div class="col-md-3"><label>Teléfono</label><input type="text" name="telefono" class="form-control" value="<?= $telefono ?>"></div>
    <div class="col-md-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= $email ?>"></div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </div>
</form>

<!-- PREVIEW -->
<div class="preview" id="preview">

    <!-- HEADER -->
    <div class="header-top">
        <div class="header-left">
            <img id="logo_preview" src="<?= $icono_principal ?>" alt="Logo">
            <div class="social-icons">
                <i class="bi bi-facebook"></i>
                <i class="bi bi-instagram"></i>
                <i class="bi bi-twitter"></i>
            </div>
        </div>
        <div class="header-right">
            <img src="uploads/administracion.jpg" alt="Admin">
            <div class="nav-links">
                <a href="#inicio" class="text-warning me-2">Inicio</a>
                <a href="#quienes" class="text-warning me-2">Quiénes Somos</a>
                <a href="#alquileres" class="text-warning me-2">Alquileres</a>
                <a href="#ventas" class="text-warning me-2">Ventas</a>
                <a href="#contacto" class="text-warning">Contáctenos</a>
            </div>
        </div>
    </div>

    <!-- BANNER -->
    <header class="banner" id="inicio" style="background-image:url('<?= $banner_imagen ?>');">
        <div style="width:100%; background-color: rgba(0,0,0,0.5); text-align:center; padding:20px 0;">
            <h1 class="text-white" id="banner_preview"><?= $banner_texto ?></h1>
        </div>
    </header>

    <!-- QUIÉNES SOMOS -->
    <section class="container mt-5" id="quienes">
        <h2 class="section-title" style="color:#000;">Quiénes Somos</h2>
        <div class="qs-content">
            <div style="flex:1; min-width:250px; margin-right:20px;">
                <p id="quienes_text_preview"><?= $quienes_somos ?></p>
            </div>
            <div style="flex:1; min-width:250px; text-align:right;">
                <img id="quienes_img_preview" src="<?= $quienes_imagen ?>" alt="Equipo">
            </div>
        </div>
    </section>

    <!-- PROPIEDADES DESTACADAS -->
    <section class="destacadas">
        <div class="container">
            <h2 class="section-title">Propiedades Destacadas</h2>
            <div class="row">
                <?php
                mysqli_data_seek($propiedades_res, 0);
                while($prop = $propiedades_res->fetch_assoc()) {
                    if($prop['destacada'] == 1) { ?>
                    <div class="col-md-4">
                        <div class="prop-card position-relative">
                            <a href="detalle.php?id=<?= $prop['id'] ?>" class="full-link"></a>
                            <img src="<?= $prop['imagen_destacada'] ?>" alt="Imagen Propiedad">
                            <h5 class="mt-2"><?= $prop['titulo'] ?></h5>
                            <p><?= $prop['descripcion_breve'] ?></p>
                            <p class="precio">Precio: $<?= number_format($prop['precio'], 2) ?></p>
                        </div>
                    </div>
                <?php } } ?>
            </div>
        </div>
    </section>

    <!-- PROPIEDADES EN VENTA -->
    <section class="enventa" id="ventas">
        <div class="container">
            <h2 class="section-title">Propiedades en Venta</h2>
            <div class="row">
                <?php
                mysqli_data_seek($propiedades_res, 0);
                while($prop = $propiedades_res->fetch_assoc()) {
                    if($prop['tipo'] == 'venta') { ?>
                    <div class="col-md-4">
                        <div class="prop-card position-relative">
                            <a href="detalle.php?id=<?= $prop['id'] ?>" class="full-link"></a>
                            <img src="<?= $prop['imagen_destacada'] ?>" alt="Imagen Propiedad">
                            <h5 class="mt-2"><?= $prop['titulo'] ?></h5>
                            <p><?= $prop['descripcion_breve'] ?></p>
                            <p class="precio">Precio: $<?= number_format($prop['precio'], 2) ?></p>
                        </div>
                    </div>
                <?php } } ?>
            </div>
        </div>
    </section>

    <!-- PROPIEDADES EN ALQUILER -->
    <section class="enalquiler" id="alquileres">
        <div class="container">
            <h2 class="section-title">Propiedades en Alquiler</h2>
            <div class="row">
                <?php
                mysqli_data_seek($propiedades_res, 0);
                while($prop = $propiedades_res->fetch_assoc()) {
                    if($prop['tipo'] == 'alquiler') { ?>
                    <div class="col-md-4">
                        <div class="prop-card position-relative">
                            <a href="detalle.php?id=<?= $prop['id'] ?>" class="full-link"></a>
                            <img src="<?= $prop['imagen_destacada'] ?>" alt="Imagen Propiedad">
                            <h5 class="mt-2"><?= $prop['titulo'] ?></h5>
                            <p><?= $prop['descripcion_breve'] ?></p>
                            <p class="precio">Precio: $<?= number_format($prop['precio'], 2) ?></p>
                        </div>
                    </div>
                <?php } } ?>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <section class="footer-yellow" id="contacto">
        <div class="footer-col">
            <h5>Contacto</h5>
            <p><strong>Dirección:</strong> <span id="direccion_preview"><?= $direccion ?></span></p>
            <p><strong>Tel:</strong> <span id="telefono_preview"><?= $telefono ?></span></p>
            <p><strong>Email:</strong> <span id="email_preview"><?= $email ?></span></p>
        </div>
        <div class="footer-col text-center">
            <img id="icono_blanco_preview" src="<?= $icono_blanco ?>" alt="Logo" height="80">
            <div class="social-icons mt-2">
                <a href="<?= $facebook ?>" target="_blank"><i class="bi bi-facebook"></i></a>
                <a href="<?= $instagram ?>" target="_blank"><i class="bi bi-instagram"></i></a>
                <a href="<?= $twitter ?>" target="_blank"><i class="bi bi-twitter"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <div class="contact-form">
                <h5>Contáctanos</h5>
                <form>
                    <input type="text" class="form-control" placeholder="Nombre">
                    <input type="email" class="form-control" placeholder="Email">
                    <input type="text" class="form-control" placeholder="Teléfono">
                    <textarea class="form-control" rows="3" placeholder="Mensaje"></textarea>
                    <button type="submit">Enviar</button>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
// Inicializar colores al cargar
document.addEventListener("DOMContentLoaded", () => {
    const color = "<?= $color_principal ?>";
    document.getElementById('colorPicker').value = color;
    document.getElementById('color_hidden').value = color;
    cambiarColorPrincipal(color);
});

// Cambiar color principal en vivo
function cambiarColorPrincipal(nuevoColor){
    document.getElementById('color_hidden').value = nuevoColor;
    // Cambiar color de títulos y botones
    document.querySelectorAll('.btn, .section-title').forEach(el=>el.style.color=nuevoColor);
    document.querySelectorAll('.destacadas .precio, .enalquiler .precio').forEach(el=>el.style.color=nuevoColor);
}

// Banner en vivo
document.querySelector('input[name="banner_texto"]').addEventListener('input', e=>{
    document.getElementById('banner_preview').innerText = e.target.value;
});

// Quiénes Somos en vivo
document.querySelector('textarea[name="quienes_somos"]').addEventListener('input', e=>{
    document.getElementById('quienes_text_preview').innerText = e.target.value;
});

// Actualizar imagen del banner en vivo
document.querySelector('input[name="banner_imagen"]').addEventListener('change', function(e){
    const reader = new FileReader();
    reader.onload = function(){
        document.querySelector('.banner').style.backgroundImage = `url('${reader.result}')`;
    };
    reader.readAsDataURL(e.target.files[0]);
});

// Actualizar imagen Quiénes Somos en vivo
document.querySelector('input[name="quienes_imagen"]').addEventListener('change', function(e){
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('quienes_img_preview').src = reader.result;
    };
    reader.readAsDataURL(e.target.files[0]);
});

// Actualizar logos en vivo
document.querySelector('input[name="icono_principal"]').addEventListener('change', function(e){
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('logo_preview').src = reader.result;
    };
    reader.readAsDataURL(e.target.files[0]);
});
document.querySelector('input[name="icono_blanco"]').addEventListener('change', function(e){
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('icono_blanco_preview').src = reader.result;
    };
    reader.readAsDataURL(e.target.files[0]);
});

// Contacto en vivo
document.querySelector('input[name="direccion"]').addEventListener('input', e=>{
    document.getElementById('direccion_preview').innerText = e.target.value;
});
document.querySelector('input[name="telefono"]').addEventListener('input', e=>{
    document.getElementById('telefono_preview').innerText = e.target.value;
});
document.querySelector('input[name="email"]').addEventListener('input', e=>{
    document.getElementById('email_preview').innerText = e.target.value;
});
document.querySelector('input[name="facebook"]').addEventListener('input', e=>{
    document.querySelector('.footer-yellow .social-icons a:nth-child(1)').href = e.target.value;
});
document.querySelector('input[name="instagram"]').addEventListener('input', e=>{
    document.querySelector('.footer-yellow .social-icons a:nth-child(2)').href = e.target.value;
});
document.querySelector('input[name="twitter"]').addEventListener('input', e=>{
    document.querySelector('.footer-yellow .social-icons a:nth-child(3)').href = e.target.value;
});
</script>
