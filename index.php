<?php
session_start();
require_once "conexion.php";

// Configuración y propiedades
$sqlConfig = "SELECT * FROM configuracion LIMIT 1";
$resConfig = $conect->query($sqlConfig);
$config = $resConfig->fetch_assoc();

$sqlPropiedades = "SELECT * FROM propiedades ORDER BY id DESC";
$resPropiedades = $conect->query($sqlPropiedades);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>UTN Solutions Real Estate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    <?php
        $color1 = $config['color_tema1']; 
        $color2 = $config['color_tema2']; 
        $color3 = $config['color_tema3'];
        $color4 = $config['color_tema4']; 
    ?>

    body { 
        font-family: 'Segoe UI', sans-serif; 
        background-color: #f8f9fa; 
    }

    /* HEADER */
    .header-top { display: flex; justify-content: space-between; align-items: center; padding: 10px 30px; background: <?= $color2 ?>; border-bottom: 1px solid #ddd; }
    .header-left { display: flex; align-items: center; gap: 15px; }
    .header-left img { height: 60px; }
    .header-left .social-icons i { font-size: 20px; margin-right: 10px; color: #333; }
    .header-right { text-align: right; }
    .header-right img { height: 50px; cursor: pointer; }
    .nav-links { text-align: right; margin-top: 5px; }
    .nav-links a { margin-left: 15px; color: #333; font-weight: 500; text-decoration: none; }
    .nav-links a:hover { text-decoration: underline; }

    /* BANNER */
    .banner { background-size: cover; background-position: center; height: 400px; color: <?= $color2 ?>; display: flex; align-items: center; justify-content: center; text-shadow: 2px 2px 6px #000; }
    .section-title { margin: 50px 0 30px; text-align: center; color: <?= $color2 ?>; }

    /* PROPIEDADES */
    .prop-card { border-radius: 10px; padding: 15px; margin-bottom: 30px; transition: transform 0.2s; position: relative; cursor: pointer; }
    .prop-card:hover { transform: scale(1.03); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
    .prop-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; }

    /* Centrar contenido de las tarjetas */
    .prop-card h5,
    .prop-card p,
    .prop-card .precio { text-align: center; }

    /* Toda la tarjeta clickable */
    .prop-card a.full-link { position: absolute; top:0; left:0; width:100%; height:100%; z-index:1; }

    /* Que los botones queden encima de todo */
    .prop-card .btn { position: relative; z-index:2; }

    /* Precios destacadas y alquiler - color_tema3 */
    .destacadas .prop-card .precio,
    .enalquiler .prop-card .precio {
        color: <?= $color3 ?>;
        font-weight: bold;
        font-size: 1.2rem;
        text-shadow: 0.5px 0.5px 0 #000, -0.5px 0.5px 0 #000, 0.5px -0.5px 0 #000, -0.5px -0.5px 0 #000;
    }

    /* Precios en venta - color_tema1 */
    .enventa .prop-card .precio {
        color: <?= $color3 ?>;
        font-weight: bold;
        font-size: 1.2rem;
        text-shadow: 0.5px 0.5px 0 #000;
    }

    /* SECCIONES Y TARJETAS */
    .destacadas { background-color: <?= $color1 ?>; color: <?= $color2 ?>; padding: 30px 0; }
    .destacadas .prop-card { background-color: <?= $color1 ?>; color: <?= $color2 ?>; border: none; }
    .destacadas .prop-card h5,
    .destacadas .prop-card p,
    .destacadas .prop-card i { color: <?= $color2 ?>; }

    .enalquiler { background-color: <?= $color1 ?>; color: <?= $color2 ?>; padding: 30px 0; }
    .enalquiler .prop-card { background-color: <?= $color1 ?>; color: <?= $color2 ?>; border: none; }
    .enalquiler .prop-card h5,
    .enalquiler .prop-card p,
    .enalquiler .prop-card i { color: <?= $color2 ?>; }

    .enventa { background-color: <?= $color2 ?>; color: <?= $color1 ?>; padding: 30px 0; }
    .enventa .prop-card { background-color: <?= $color2 ?>; color: <?= $color1 ?>; border: 1px solid #ddd; }
    .enventa .prop-card h5,
    .enventa .prop-card p,
    .enventa .prop-card i { color: <?= $color1 ?>; }

    /* Quiénes somos */
    .qs-content { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 30px; padding: 30px; }
    .qs-content img { width: 350px; border-radius: 10px; }

    /* Footer amarillo */
    .footer-yellow { background-color: <?= $color3 ?>; color: <?= $color1 ?>; padding: 30px 20px; }
    .footer-yellow .footer-col { flex: 1; padding: 10px; }
    .footer-yellow .social-icons i { font-size: 28px; margin: 0 5px; }
    .contact-form { background-color: <?= $color2 ?>; padding: 15px; border-radius: 10px; max-width:300px; }
    .contact-form input, .contact-form textarea { margin-bottom: 10px; }
    .contact-form button { background-color: <?= $color1 ?>; color: <?= $color3 ?>; border: none; width: 100%; padding: 10px; font-weight: bold; }

    /* BOTONES VER MÁS */
.btn-vermas { 
    border: 2px solid <?= $color3 ?>;
    color: <?= $color2 ?>;
    font-weight: bold; 
    padding: 8px 30px;
    text-decoration: none; 
    display: inline-block; 
    border-radius: 8px; 
    background-color: transparent; 
}

/* Destacadas y Alquiler */
.btn-vermas-da {
    color: <?= $color2 ?>; /* texto color_tema2 */
    
}
    .btn-vermas-da:hover {
        background-color: <?= $color3 ?>;
        color: <?= $color2 ?>;
    }

    /* Ventas */
    .btn-vermas-venta {
        color: <?= $color1 ?>;
        border-color: <?= $color3 ?>;
        background-color: transparent;
    }
    .btn-vermas-venta:hover {
        background-color: <?= $color3 ?>;
        color: <?= $color1 ?>;
    }

    .nav-links a {
    color: <?= $color3 ?>; 
    text-decoration: none; 
    font-weight: 500;
    margin: 0 8px; 
}

.nav-links a:not(:last-child)::after {
    content: "|"; 
    margin-left: 8px;
    color: <?= $color3 ?>; 
}
</style>

</head>
<body>

<div style="background-color:<?= $color1 ?>; padding:5px 30px; display:flex; justify-content:space-between; align-items:flex-start;">
    <!-- Izquierda: Logo principal -->
    <div>
        <img src="<?php echo $config['icono_principal']; ?>" alt="Logo" style="height:50px;">
    </div>
    <!-- Derecha: Admin y nav-links -->
    <div style="text-align:right;">
        <a href="dashboard.php"><img src="uploads/administracion.jpg" alt="Admin" style="height:30px; cursor:pointer;"></a>
        <div class="nav-links" style="margin-top:5px;">
            <a href="#inicio" >Inicio</a>
            <a href="#quienes" >Quiénes Somos</a>
            <a href="#alquileres" >Alquileres</a>
            <a href="#ventas" >Ventas</a>
            <a href="#contacto" >Contáctenos</a>
        </div>
    </div>
</div>

<!-- REDES SOCIALES Y SEARCHBAR SOBRE EL BANNER -->
<div style="position: relative;">
    <div style="position: absolute; top:10px; left:30px; z-index:10;" class="d-flex align-items-center gap-2">
        <a href="<?php echo $config['facebook']; ?>" target="_blank"><i class="bi bi-facebook text-white"></i></a>
        <a href="<?php echo $config['instagram']; ?>" target="_blank"><i class="bi bi-instagram text-white"></i></a>
        <a href="<?php echo $config['twitter']; ?>" target="_blank"><i class="bi bi-twitter text-white"></i></a>
    </div>
    <div style="position: absolute; top:10px; right:30px; z-index:10;">
        <form class="d-flex" role="search" method="GET" action="index.php">
            <div class="input-group" style="width:250px;">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar propiedades..." style="height:35px;">
                <button class="btn btn-light" type="submit" style="height:35px;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    <header class="banner" id="inicio" style="background-image: url('<?php echo $config['banner_imagen']; ?>'); height:350px; background-size:cover; background-position:center; display:flex; align-items:center; justify-content:center;">
        <div style="width:100%; background-color: rgba(0,0,0,0.5); text-align:center; padding:20px 0;">
            <h1 class="text-white" style="margin:0; text-shadow: 2px 2px 6px #000;">
                <?php echo $config['banner_texto']; ?>
            </h1>
        </div>
    </header>
</div>

<!-- QUIÉNES SOMOS -->
<section class="container mt-5" id="quienes">
    <h2 class="section-title" style="color:#000;">Quiénes Somos</h2>
    <div class="qs-content d-flex flex-wrap align-items-center justify-content-between">
        <div style="flex:1; min-width:250px; margin-right:20px;">
            <p style="text-align:left;"><?php echo $config['quienes_somos']; ?></p>
        </div>
        <div style="flex:1; min-width:250px; text-align:right;">
            <img src="<?php echo $config['quienes_imagen']; ?>" alt="Equipo" style="max-width:100%; border-radius:10px;">
        </div>
    </div>
</section>

<!-- PROPIEDADES DESTACADAS -->
<section class="destacadas">
    <div class="container">
        <h2 class="section-title">Propiedades Destacadas</h2>
        <div class="row">
            <?php
            mysqli_data_seek($resPropiedades, 0);
            while($prop = $resPropiedades->fetch_assoc()) {
                if($prop['destacada'] == 1) { ?>
                <div class="col-md-4">
                    <div class="prop-card position-relative">
                        <a href="detalle.php?id=<?php echo $prop['id']; ?>" class="full-link"></a>
                        <img src="<?php echo $prop['imagen_destacada']; ?>" alt="Imagen Propiedad">
                        <h5 class="mt-2"><?php echo $prop['titulo']; ?></h5>
                        <p><?php echo $prop['descripcion_breve']; ?></p>
                        <p class="precio">Precio: $<?php echo number_format($prop['precio'], 2); ?></p>
                    </div>
                </div>
            <?php } } ?>
        </div>
     <div class="text-center mt-3">
    <a href="destacadas.php" class="btn-vermas btn-vermas-da">Ver más...</a>
</div>
    </div>
</section>

<!-- PROPIEDADES EN VENTA -->
<section class="enventa" id="ventas">
    <div class="container">
        <h2 class="section-title" style="color:#000;">Propiedades en Venta</h2>
        <div class="row">
            <?php
            mysqli_data_seek($resPropiedades, 0);
            while($prop = $resPropiedades->fetch_assoc()) {
                if($prop['tipo'] == 'venta') { ?>
                <div class="col-md-4">
                    <div class="prop-card position-relative">
                        <a href="detalle.php?id=<?php echo $prop['id']; ?>" class="full-link"></a>
                        <img src="<?php echo $prop['imagen_destacada']; ?>" alt="Imagen Propiedad">
                        <h5 class="mt-2"><?php echo $prop['titulo']; ?></h5>
                        <p><?php echo $prop['descripcion_breve']; ?></p>
                        <p class="precio">Precio: $<?php echo number_format($prop['precio'], 2); ?></p>
                    </div>
                </div>
            <?php } } ?>
        </div>
        <div class="text-center mt-3">
    <a href="ventas.php" class="btn-vermas btn-vermas-venta">Ver más...</a>
        </div>
    </div>
</section>

<!-- PROPIEDADES EN ALQUILER -->
<section class="enalquiler" id="alquileres">
    <div class="container">
        <h2 class="section-title">Propiedades en Alquiler</h2>
        <div class="row">
            <?php
            mysqli_data_seek($resPropiedades, 0);
            while($prop = $resPropiedades->fetch_assoc()) {
                if($prop['tipo'] == 'alquiler') { ?>
                <div class="col-md-4">
                    <div class="prop-card position-relative">
                        <a href="detalle.php?id=<?php echo $prop['id']; ?>" class="full-link"></a>
                        <img src="<?php echo $prop['imagen_destacada']; ?>" alt="Imagen Propiedad">
                        <h5 class="mt-2"><?php echo $prop['titulo']; ?></h5>
                        <p><?php echo $prop['descripcion_breve']; ?></p>
                        <p class="precio">Precio: $<?php echo number_format($prop['precio'], 2); ?></p>
                    </div>
                </div>
            <?php } } ?>
        </div>
        <div class="text-center mt-3">
    <a href="alquileres.php" class="btn-vermas btn-vermas-da">Ver más...</a>
</div>

    </div>
</section>

<!-- FOOTER AMARILLO -->
<section class="footer-yellow" id="contacto" style="padding: 20px 0;">
    <div class="container d-flex flex-wrap justify-content-between align-items-start">
        <!-- Contacto -->
        <div class="footer-col" style="flex:1; min-width:200px;">
         
            <p><strong>Dirección:</strong> <?php echo $config['direccion']; ?></p>
            <p><strong>Tel:</strong> <?php echo $config['telefono']; ?></p>
            <p><strong>Email:</strong> <?php echo $config['email']; ?></p>
        </div>

        <!-- Logo y redes sociales -->
        <div class="footer-col text-center" style="flex:1; min-width:200px;">
            <img src="<?php echo $config['icono_blanco']; ?>" alt="Logo" height="100">
            <div class="social-icons mt-2" style="font-size:28px;">
                <a href="<?php echo $config['facebook']; ?>" target="_blank"><i class="bi bi-facebook me-2"></i></a>
                <a href="<?php echo $config['instagram']; ?>" target="_blank"><i class="bi bi-instagram me-2"></i></a>
                <a href="<?php echo $config['twitter']; ?>" target="_blank"><i class="bi bi-twitter"></i></a>
            </div>
        </div>

        <!-- Formulario de contacto -->
      <div class="footer-col" style="flex:1; min-width:250px;">
    <div class="contact-form" style="padding:15px; background-color:<?= $color4 ?>; border-radius:8px;">
        <h5>Contáctanos</h5>
        <form method="POST" action="contacto.php">
            <input type="text" class="form-control mb-2" name="nombre" placeholder="Nombre" style="height:32px;">
            <input type="email" class="form-control mb-2" name="email" placeholder="Email" style="height:32px;">
            <input type="text" class="form-control mb-2" name="telefono" placeholder="Teléfono" style="height:32px;">
            <textarea class="form-control mb-2" rows="2" name="mensaje" placeholder="Mensaje"></textarea>
            <button type="submit" style="height:35px; font-weight:bold;">Enviar</button>
        </form>
    </div>
</div>

    </div>
</section>

<div style="background-color:<?= $color1 ?>; color:white; text-align:center; padding:8px 0; font-size:14px;">
    &copy; Derechos Reservados 2025
</div>

</body>
</html>
