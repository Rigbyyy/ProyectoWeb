<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("conexion.php");


function normalizarHex($color) {
    if (preg_match('/^#([a-fA-F0-9]{3})$/', $color)) {
       
        $color = '#' . $color[1] . $color[1] . $color[2] . $color[2] . $color[3] . $color[3];
    }
    return $color;
}



$result = $conect->query("SELECT * FROM configuracion LIMIT 1");
$config = $result->fetch_assoc();




$color_tema1 = normalizarHex($config['color_tema1'] ?? '#02253e');
$color_tema2 = normalizarHex($config['color_tema2'] ?? '#ffffff'); 
$color_tema3 = normalizarHex($config['color_tema3'] ?? '#f5c505');
$color_tema4 = normalizarHex($config['color_tema4'] ?? '#e3d1d1'); 

$icono_principal = $config['icono_principal'] ?? '';
$icono_blanco    = $config['icono_blanco'] ?? '';
$banner_imagen   = $config['banner_imagen'] ?? '';
$banner_texto    = $config['banner_texto'] ?? '';
$quienes_somos   = $config['quienes_somos'] ?? '';
$quienes_imagen  = $config['quienes_imagen'] ?? '';
$facebook        = $config['facebook'] ?? '';
$instagram       = $config['instagram'] ?? '';
$twitter         = $config['twitter'] ?? '';
$direccion       = $config['direccion'] ?? '';
$telefono        = $config['telefono'] ?? '';
$email           = $config['email'] ?? '';


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
    
  
    $color_tema1 =normalizarHex( $_POST['color_tema1'] ?? $color_tema1);
    $color_tema2 = normalizarHex($_POST['color_tema2'] ?? $color_tema2);
    $color_tema3 = normalizarHex($_POST['color_tema3'] ?? $color_tema3);
    $color_tema4 = normalizarHex($_POST['color_tema4'] ?? $color_tema4);

    // Textos / enlaces
    $banner_texto  = $_POST['banner_texto'] ?? '';
    $quienes_somos = $_POST['quienes_somos'] ?? '';
    $direccion     = $_POST['direccion'] ?? '';
    $telefono      = $_POST['telefono'] ?? '';
    $email         = $_POST['email'] ?? '';
    $facebook      = $_POST['facebook'] ?? '';
    $instagram     = $_POST['instagram'] ?? '';
    $twitter       = $_POST['twitter'] ?? '';

    // Archivos
    $icono_principal_up = subirArchivo("icono_principal");
    $icono_blanco_up    = subirArchivo("icono_blanco");
    $banner_imagen_up   = subirArchivo("banner_imagen");
    $quienes_imagen_up  = subirArchivo("quienes_imagen");

    $sql = "UPDATE configuracion SET 
        color_tema1=?, color_tema2=?, color_tema3=?, color_tema4=?,
        banner_texto=?, direccion=?, telefono=?, email=?,
        facebook=?, instagram=?, twitter=?, quienes_somos=?";

    if($icono_principal_up) $sql .= ", icono_principal='$icono_principal_up'";
    if($icono_blanco_up)    $sql .= ", icono_blanco='$icono_blanco_up'";
    if($banner_imagen_up)   $sql .= ", banner_imagen='$banner_imagen_up'";
    if($quienes_imagen_up)  $sql .= ", quienes_imagen='$quienes_imagen_up'";

    $sql .= " WHERE id=" . intval($config['id']);

    $stmt = $conect->prepare($sql);
    $stmt->bind_param(
        "ssssssssssss",
        $color_tema1, $color_tema2, $color_tema3, $color_tema4,
        $banner_texto, $direccion, $telefono, $email,
        $facebook, $instagram, $twitter, $quienes_somos
    );
    $stmt->execute();

    header("Location: personalizar.php?ok=1");
    exit();
}

// --- Propiedades para preview ---
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
    body { font-family: 'Segoe UI', sans-serif; background:#f8f9fa; padding:20px; }

    /* Contenedor de preview con CSS variables para cambios en vivo */
    #preview {
        --c1: <?= htmlspecialchars($color_tema1) ?>;
        --c2: <?= htmlspecialchars($color_tema2) ?>;
        --c3: <?= htmlspecialchars($color_tema3) ?>;
        --c4: <?= htmlspecialchars($color_tema4) ?>;
        border:1px solid #ddd; border-radius:10px; overflow:hidden; background:#fff; margin-top:20px;
    }

    /* HEADER SUPERIOR (igual a index) */
    .topbar { background-color: var(--c1); padding:6px 30px; display:flex; justify-content:space-between; align-items:flex-start; }
    .topbar img.logo { height:50px; }
    .nav-links { text-align:right; margin-top:5px; }
    .nav-links a { color: var(--c3); text-decoration:none; font-weight:500; margin: 0 8px; }
    .nav-links a:not(:last-child)::after { content:"|"; margin-left:8px; color: var(--c3); }

    /* BANNER con redes y buscador sobrepuesto */
    .banner-wrap { position:relative; }
    .banner-wrap .social { position:absolute; top:10px; left:30px; z-index:10; }
    .banner-wrap .social a { color:#fff; margin-right:8px; font-size:20px; }
    .banner-wrap .search { position:absolute; top:10px; right:30px; z-index:10; }
    .banner { background-size:cover; background-position:center; height:350px; display:flex; align-items:center; justify-content:center; }
    .banner .inner { width:100%; background:rgba(0,0,0,0.5); text-align:center; padding:20px 0; }
    .banner .inner h1 { color:#fff; margin:0; text-shadow: 2px 2px 6px #000; }

    /* SECCIONES */
    .section-title { margin: 40px 0 25px; text-align:center; color:#000; }
    .destacadas, .enalquiler { background-color: var(--c1); color: var(--c2); padding:30px 0; }
    .enventa { background-color: var(--c2); color: var(--c1); padding:30px 0; }
    .destacadas .section-title, .enalquiler .section-title { color: var(--c2); }
    .enventa .section-title { color: #000; }

    /* Tarjetas */
    .prop-card { background: transparent; border: none; border-radius:10px; padding:15px; margin-bottom:30px; transition: transform .2s; position:relative; cursor:pointer; }
    .prop-card:hover { transform: scale(1.03); box-shadow:0 5px 15px rgba(0,0,0,0.2); }
    .prop-card img { width:100%; height:200px; object-fit:cover; border-radius:5px; }
    .prop-card h5, .prop-card p, .prop-card .precio { text-align:center; }
    .prop-card a.full-link { position:absolute; inset:0; z-index:1; }
    .prop-card .btn { position:relative; z-index:2; }

    /* Precio (acento) */
    .destacadas .prop-card .precio,
    .enalquiler .prop-card .precio,
    .enventa .prop-card .precio {
        color: var(--c3); font-weight:bold; font-size:1.2rem;
        text-shadow: 0.5px 0.5px 0 #000;
    }

    /* Footer amarillo */
    .footer-yellow { background-color: var(--c3); color: var(--c1); padding: 20px 0; }
    .footer-yellow .footer-col { flex:1; padding:10px; min-width:200px; }
    .footer-yellow .social-icons i { font-size:28px; margin:0 5px; }
    .contact-form { background-color: var(--c4); padding:15px; border-radius:8px; max-width:320px; }
    .contact-form button { background-color: var(--c1); color: var(--c3); border:none; width:100%; padding:10px; font-weight:bold; }

    /* Botones "Ver más..." */
    .btn-vermas { border:2px solid var(--c3); font-weight:bold; padding:8px 30px; border-radius:8px; background:transparent; text-decoration:none; display:inline-block; }
    .btn-vermas-da { color: var(--c2); }
    .btn-vermas-da:hover { background: var(--c3); color: var(--c2); }
    .btn-vermas-venta { color: var(--c1); }
    .btn-vermas-venta:hover { background: var(--c3); color: var(--c1); }

    /* Caja del formulario de config */
    .config-card { background:#fff; border:1px solid #eaeaea; border-radius:12px; padding:20px; box-shadow:0 6px 16px rgba(0,0,0,0.06); }
    .config-card h5 { margin-bottom:12px; }
    .thumb { height:40px; border-radius:6px; object-fit:cover; border:1px solid #eee; }
</style>
</head>
<body>

<h2 class="mb-3">Personalizar Página</h2>
<?php if(isset($_GET['ok'])): ?>
    <div class="alert alert-success">Cambios guardados correctamente.</div>
<?php endif; ?>

<!-- ===== Formulario de configuración ===== -->
<form method="POST" enctype="multipart/form-data" class="config-card mb-4">
    <div class="row g-3">
        <!-- Colores -->
        <div class="col-12"><h5>Colores del sitio</h5></div>
        <div class="col-md-3">
            <label class="form-label d-block">Color 1 (oscuro)</label>
            <input type="color" class="form-control form-control-color" name="color_tema1" id="c1" value="<?= htmlspecialchars($color_tema1) ?>">
            <small class="text-muted">Header, Destacadas y Alquileres</small>
        </div>
        <div class="col-md-3">
            <label class="form-label d-block">Color 2 (claro)</label>
            <input type="color" class="form-control form-control-color" name="color_tema2" id="c2" value="<?= htmlspecialchars($color_tema2) ?>">
            <small class="text-muted">Ventas y textos claros</small>
        </div>
        <div class="col-md-3">
            <label class="form-label d-block">Color 3 (acento)</label>
            <input type="color" class="form-control form-control-color" name="color_tema3" id="c3" value="<?= htmlspecialchars($color_tema3) ?>">
            <small class="text-muted">Botones, precios y footer</small>
        </div>
        <div class="col-md-3">
            <label class="form-label d-block">Color 4 (secundario)</label>
            <input type="color" class="form-control form-control-color" name="color_tema4" id="c4" value="<?= htmlspecialchars($color_tema4) ?>">
            <small class="text-muted">Tarjeta de contacto</small>
        </div>

        <!-- Logos -->
        <div class="col-12"><hr><h5>Identidad</h5></div>
        <div class="col-md-6">
            <label class="form-label">Ícono principal (logo oscuro)</label>
            <input type="file" name="icono_principal" class="form-control">
            <?php if($icono_principal): ?>
                <div class="mt-2"><img class="thumb" src="<?= htmlspecialchars($icono_principal) ?>" alt="icono principal"></div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">Ícono blanco (para fondos oscuros)</label>
            <input type="file" name="icono_blanco" class="form-control">
            <?php if($icono_blanco): ?>
                <div class="mt-2"><img class="thumb" src="<?= htmlspecialchars($icono_blanco) ?>" alt="icono blanco"></div>
            <?php endif; ?>
        </div>

        <!-- Banner -->
        <div class="col-12"><hr><h5>Banner principal</h5></div>
        <div class="col-md-6">
            <label class="form-label">Imagen del banner</label>
            <input type="file" name="banner_imagen" class="form-control">
            <?php if($banner_imagen): ?>
                <div class="mt-2"><img class="thumb" src="<?= htmlspecialchars($banner_imagen) ?>" alt="banner"></div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">Texto del banner</label>
            <input type="text" name="banner_texto" class="form-control" value="<?= htmlspecialchars($banner_texto) ?>">
        </div>

        <!-- Quiénes somos -->
        <div class="col-12"><hr><h5>Quiénes somos</h5></div>
        <div class="col-md-8">
            <label class="form-label">Texto</label>
            <textarea name="quienes_somos" class="form-control" rows="5"><?= htmlspecialchars($quienes_somos) ?></textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label">Imagen</label>
            <input type="file" name="quienes_imagen" class="form-control">
            <?php if($quienes_imagen): ?>
                <div class="mt-2"><img class="thumb" src="<?= htmlspecialchars($quienes_imagen) ?>" alt="quienes"></div>
            <?php endif; ?>
        </div>

        <!-- Contacto / redes -->
        <div class="col-12"><hr><h5>Contacto y redes</h5></div>
        <div class="col-md-4"><label class="form-label">Facebook</label><input type="text" name="facebook" class="form-control" value="<?= htmlspecialchars($facebook) ?>"></div>
        <div class="col-md-4"><label class="form-label">Instagram</label><input type="text" name="instagram" class="form-control" value="<?= htmlspecialchars($instagram) ?>"></div>
        <div class="col-md-4"><label class="form-label">Twitter/X</label><input type="text" name="twitter" class="form-control" value="<?= htmlspecialchars($twitter) ?>"></div>
        <div class="col-md-6"><label class="form-label">Dirección</label><input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($direccion) ?>"></div>
        <div class="col-md-3"><label class="form-label">Teléfono</label><input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($telefono) ?>"></div>
        <div class="col-md-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>"></div>

        <div class="col-12 d-grid mt-2">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </div>
</form>

<!-- ===== PREVIEW (clona tu index con colores variables) ===== -->
<div id="preview">
    <!-- Header superior -->
    <div class="topbar">
        <div><img id="logo_preview" class="logo" src="<?= htmlspecialchars($icono_principal) ?>" alt="Logo"></div>
        <div style="text-align:right;">
            <a href="dashboard.php"><img src="uploads/administracion.jpg" alt="Admin" style="height:30px; cursor:pointer;"></a>
            <div class="nav-links">
                <a href="#inicio">Inicio</a>
                <a href="#quienes">Quiénes Somos</a>
                <a href="#alquileres">Alquileres</a>
                <a href="#ventas">Ventas</a>
                <a href="#contacto">Contáctenos</a>
            </div>
        </div>
    </div>

    <!-- Banner con redes y buscador -->
    <div class="banner-wrap">
        <div class="social">
            <a id="fb_link" href="<?= htmlspecialchars($facebook) ?>" target="_blank"><i class="bi bi-facebook"></i></a>
            <a id="ig_link" href="<?= htmlspecialchars($instagram) ?>" target="_blank"><i class="bi bi-instagram"></i></a>
            <a id="tw_link" href="<?= htmlspecialchars($twitter) ?>" target="_blank"><i class="bi bi-twitter"></i></a>
        </div>
        <div class="search">
            <form class="d-flex" role="search" method="GET" action="#">
                <div class="input-group" style="width:250px;">
                    <input type="text" class="form-control" placeholder="Buscar propiedades..." style="height:35px;">
                    <button class="btn btn-light" type="button" style="height:35px;"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <header class="banner" id="inicio" style="background-image:url('<?= htmlspecialchars($banner_imagen) ?>');">
            <div class="inner"><h1 id="banner_preview"><?= htmlspecialchars($banner_texto) ?></h1></div>
        </header>
    </div>

    <!-- Quiénes somos -->
    <section class="container mt-5" id="quienes">
        <h2 class="section-title" style="color:#000;">Quiénes Somos</h2>
        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:20px;">
            <div style="flex:1; min-width:250px;">
                <p id="quienes_text_preview" style="text-align:left;"><?= nl2br(htmlspecialchars($quienes_somos)) ?></p>
            </div>
            <div style="flex:1; min-width:250px; text-align:right;">
                <img id="quienes_img_preview" src="<?= htmlspecialchars($quienes_imagen) ?>" alt="Equipo" style="max-width:100%; border-radius:10px;">
            </div>
        </div>
    </section>

    <!-- Destacadas -->
    <section class="destacadas">
        <div class="container">
            <h2 class="section-title">Propiedades Destacadas</h2>
            <div class="row">
                <?php
                mysqli_data_seek($propiedades_res, 0);
                while($prop = $propiedades_res->fetch_assoc()):
                    if($prop['destacada'] == 1): ?>
                        <div class="col-md-4">
                            <div class="prop-card position-relative">
                                <a href="detalle.php?id=<?= $prop['id'] ?>" class="full-link"></a>
                                <img src="<?= htmlspecialchars($prop['imagen_destacada']) ?>" alt="Imagen Propiedad">
                                <h5 class="mt-2"><?= htmlspecialchars($prop['titulo']) ?></h5>
                                <p><?= htmlspecialchars($prop['descripcion_breve']) ?></p>
                                <p class="precio">Precio: $<?= number_format($prop['precio'], 2) ?></p>
                            </div>
                        </div>
                <?php endif; endwhile; ?>
            </div>
            <div class="text-center mt-2">
                <a href="#" class="btn-vermas btn-vermas-da">Ver más...</a>
            </div>
        </div>
    </section>

    <!-- Ventas -->
    <section class="enventa" id="ventas">
        <div class="container">
            <h2 class="section-title">Propiedades en Venta</h2>
            <div class="row">
                <?php
                mysqli_data_seek($propiedades_res, 0);
                while($prop = $propiedades_res->fetch_assoc()):
                    if($prop['tipo'] === 'venta'): ?>
                        <div class="col-md-4">
                            <div class="prop-card position-relative">
                                <a href="detalle.php?id=<?= $prop['id'] ?>" class="full-link"></a>
                                <img src="<?= htmlspecialchars($prop['imagen_destacada']) ?>" alt="Imagen Propiedad">
                                <h5 class="mt-2"><?= htmlspecialchars($prop['titulo']) ?></h5>
                                <p><?= htmlspecialchars($prop['descripcion_breve']) ?></p>
                                <p class="precio">Precio: $<?= number_format($prop['precio'], 2) ?></p>
                            </div>
                        </div>
                <?php endif; endwhile; ?>
            </div>
            <div class="text-center mt-2">
                <a href="#" class="btn-vermas btn-vermas-venta">Ver más...</a>
            </div>
        </div>
    </section>

    <!-- Alquileres -->
    <section class="enalquiler" id="alquileres">
        <div class="container">
            <h2 class="section-title">Propiedades en Alquiler</h2>
            <div class="row">
                <?php
                mysqli_data_seek($propiedades_res, 0);
                while($prop = $propiedades_res->fetch_assoc()):
                    if($prop['tipo'] === 'alquiler'): ?>
                        <div class="col-md-4">
                            <div class="prop-card position-relative">
                                <a href="detalle.php?id=<?= $prop['id'] ?>" class="full-link"></a>
                                <img src="<?= htmlspecialchars($prop['imagen_destacada']) ?>" alt="Imagen Propiedad">
                                <h5 class="mt-2"><?= htmlspecialchars($prop['titulo']) ?></h5>
                                <p><?= htmlspecialchars($prop['descripcion_breve']) ?></p>
                                <p class="precio">Precio: $<?= number_format($prop['precio'], 2) ?></p>
                            </div>
                        </div>
                <?php endif; endwhile; ?>
            </div>
            <div class="text-center mt-2">
                <a href="#" class="btn-vermas btn-vermas-da">Ver más...</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <section class="footer-yellow" id="contacto">
        <div class="container d-flex flex-wrap justify-content-between align-items-start">
            <div class="footer-col">
                <p><strong>Dirección:</strong> <span id="direccion_preview"><?= htmlspecialchars($direccion) ?></span></p>
                <p><strong>Tel:</strong> <span id="telefono_preview"><?= htmlspecialchars($telefono) ?></span></p>
                <p><strong>Email:</strong> <span id="email_preview"><?= htmlspecialchars($email) ?></span></p>
            </div>

            <div class="footer-col text-center">
                <img id="icono_blanco_preview" src="<?= htmlspecialchars($icono_blanco) ?>" alt="Logo" height="90">
                <div class="social-icons mt-2">
                    <a id="fb_link2" href="<?= htmlspecialchars($facebook) ?>" target="_blank"><i class="bi bi-facebook"></i></a>
                    <a id="ig_link2" href="<?= htmlspecialchars($instagram) ?>" target="_blank"><i class="bi bi-instagram"></i></a>
                    <a id="tw_link2" href="<?= htmlspecialchars($twitter) ?>" target="_blank"><i class="bi bi-twitter"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <div class="contact-form">
                    <h5>Contáctanos</h5>
                    <form>
                        <input type="text" class="form-control mb-2" placeholder="Nombre">
                        <input type="email" class="form-control mb-2" placeholder="Email">
                        <input type="text" class="form-control mb-2" placeholder="Teléfono">
                        <textarea class="form-control mb-2" rows="2" placeholder="Mensaje"></textarea>
                        <button type="button">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="text-center mt-3" style="color:var(--c1);">© Derechos Reservados 2025</div>
    </section>
</div>

<script>
// ===== Helpers =====
const $ = sel => document.querySelector(sel);
const preview = document.getElementById('preview');

// Colores en vivo (CSS variables)
['c1','c2','c3','c4'].forEach(id=>{
    const input = document.getElementById(id);
    input?.addEventListener('input', e=>{
        const map = { c1:'--c1', c2:'--c2', c3:'--c3', c4:'--c4' };
        preview.style.setProperty(map[id], e.target.value);
    });
});

// Texto banner en vivo
document.querySelector('input[name="banner_texto"]')?.addEventListener('input', e=>{
    document.getElementById('banner_preview').innerText = e.target.value;
});

// Quiénes somos en vivo
document.querySelector('textarea[name="quienes_somos"]')?.addEventListener('input', e=>{
    document.getElementById('quienes_text_preview').innerText = e.target.value;
});

// Contacto en vivo
document.querySelector('input[name="direccion"]')?.addEventListener('input', e=>{
    document.getElementById('direccion_preview').innerText = e.target.value;
});
document.querySelector('input[name="telefono"]')?.addEventListener('input', e=>{
    document.getElementById('telefono_preview').innerText = e.target.value;
});
document.querySelector('input[name="email"]')?.addEventListener('input', e=>{
    document.getElementById('email_preview').innerText = e.target.value;
});

// Redes en vivo (ambos sets de iconos)
document.querySelector('input[name="facebook"]')?.addEventListener('input', e=>{
    $('#fb_link')?.setAttribute('href', e.target.value);
    $('#fb_link2')?.setAttribute('href', e.target.value);
});
document.querySelector('input[name="instagram"]')?.addEventListener('input', e=>{
    $('#ig_link')?.setAttribute('href', e.target.value);
    $('#ig_link2')?.setAttribute('href', e.target.value);
});
document.querySelector('input[name="twitter"]')?.addEventListener('input', e=>{
    $('#tw_link')?.setAttribute('href', e.target.value);
    $('#tw_link2')?.setAttribute('href', e.target.value);
});

// Imágenes en vivo
function liveImage(inputSel, targetSel, asBackground=false) {
    const input = document.querySelector(inputSel);
    if(!input) return;
    input.addEventListener('change', e=>{
        if(!e.target.files?.length) return;
        const reader = new FileReader();
        reader.onload = () =>{
            if(asBackground) {
                document.querySelector(targetSel).style.backgroundImage = `url('${reader.result}')`;
            } else {
                document.querySelector(targetSel).src = reader.result;
            }
        };
        reader.readAsDataURL(e.target.files[0]);
    });
}

liveImage('input[name="icono_principal"]', '#logo_preview');
liveImage('input[name="icono_blanco"]', '#icono_blanco_preview');
liveImage('input[name="banner_imagen"]', '.banner', true);
liveImage('input[name="quienes_imagen"]', '#quienes_img_preview');
</script>
</body>
</html>
