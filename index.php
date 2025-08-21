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
        body { font-family: 'Segoe UI', sans-serif; background-color: #f8f9fa; }

        /* HEADER */
        .header-top { display: flex; justify-content: space-between; align-items: center; padding: 10px 30px; background: #fff; border-bottom: 1px solid #ddd; }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .header-left img { height: 60px; }
        .header-left .social-icons i { font-size: 20px; margin-right: 10px; color: #333; }
        .header-right { text-align: right; }
        .header-right img { height: 50px; cursor: pointer; }
        .nav-links { text-align: right; margin-top: 5px; }
        .nav-links a { margin-left: 15px; color: #333; font-weight: 500; text-decoration: none; }
        .nav-links a:hover { text-decoration: underline; }

        /* BANNER */
        .banner { background-size: cover; background-position: center; height: 400px; color: white; display: flex; align-items: center; justify-content: center; text-shadow: 2px 2px 6px #000; }
        .section-title { margin: 50px 0 30px; text-align: center; color: white; }

        /* PROPIEDADES */
        .prop-card { border-radius: 10px; padding: 15px; margin-bottom: 30px; transition: transform 0.2s; position: relative; cursor: pointer; }
        .prop-card:hover { transform: scale(1.03); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

        .prop-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; }

        /* CENTRAR contenido de las tarjetas */
        .prop-card h5,
        .prop-card p,
        .prop-card .precio {
            text-align: center;
        }

        /* TODA LA TARJETA CLICKABLE */
        .prop-card a.full-link {
            position: absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            z-index:1;
        }

        /* Que los botones queden encima de todo */
        .prop-card .btn {
            position: relative;
            z-index:2;
        }

        /* PRECIOS DESTACADAS Y ALQUILER - AMARILLO */
        .destacadas .prop-card .precio,
        .enalquiler .prop-card .precio {
            color: #f5c505;  /* Amarillo */
            font-weight: bold;
            font-size: 1.2rem;
            text-shadow: 0.5px 0.5px 0 #000, -0.5px 0.5px 0 #000, 0.5px -0.5px 0 #000, -0.5px -0.5px 0 #000;
        }

        /* PRECIOS EN VENTA - AZUL MARINO */
        .enventa .prop-card .precio {
            color: #02253e;  /* Azul marino */
            font-weight: bold;
            font-size: 1.2rem;
            text-shadow: 0.5px 0.5px 0 #000;
        }

        /* SECCIONES Y TARJETAS */
        .destacadas { background-color: #02253e; color: white; padding: 30px 0; }
        .destacadas .prop-card { background-color: #02253e; color: white; border: none; }
        .destacadas .prop-card h5,
        .destacadas .prop-card p,
        .destacadas .prop-card i { color: white; }

        .enalquiler { background-color: #02253e; color: white; padding: 30px 0; }
        .enalquiler .prop-card { background-color: #02253e; color: white; border: none; }
        .enalquiler .prop-card h5,
        .enalquiler .prop-card p,
        .enalquiler .prop-card i { color: white; }

        .enventa { background-color: #fff; color: #000; padding: 30px 0; }
        .enventa .prop-card { background-color: #fff; color: #000; border: 1px solid #ddd; }
        .enventa .prop-card h5,
        .enventa .prop-card p,
        .enventa .prop-card i { color: #000; }

        /* Quiénes somos */
        .qs-content { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 30px; padding: 30px; }
        .qs-content img { width: 350px; border-radius: 10px; }

        /* Footer amarillo */
        .footer-yellow { background-color: #f5c505; color: #333; padding: 40px 0; }
        .footer-yellow .footer-col { flex: 1; padding: 10px; }
        .footer-yellow .social-icons i { font-size: 24px; margin: 0 5px; }
        .contact-form { background-color: #f0f0f0; padding: 20px; border-radius: 10px; }
        .contact-form input, .contact-form textarea { margin-bottom: 10px; }
        .contact-form button { background-color: #02253e; color: #f5c505; border: none; width: 100%; padding: 10px; font-weight: bold; }
    </style>
</head>
<body>

<div style="background-color:#02253e; padding:5px 30px; display:flex; justify-content:space-between; align-items:flex-start;">
    <!-- Izquierda: Logo principal -->
    <div>
        <img src="<?php echo $config['icono_principal']; ?>" alt="Logo" style="height:50px;">
    </div>
    <!-- Derecha: Admin y nav-links -->
    <div style="text-align:right;">
        <a href="dashboard.php"><img src="uploads/administracion.jpg" alt="Admin" style="height:30px; cursor:pointer;"></a>
        <div style="margin-top:5px;">
            <a href="#inicio" class="text-warning me-2">Inicio</a>
            <a href="#quienes" class="text-warning me-2">Quiénes Somos</a>
            <a href="#alquileres" class="text-warning me-2">Alquileres</a>
            <a href="#ventas" class="text-warning me-2">Ventas</a>
            <a href="#contacto" class="text-warning">Contáctenos</a>
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
            <a href="destacadas.php" class="btn btn-outline-light">Ver más</a>
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
            <a href="ventas.php" class="btn btn-outline-primary">Ver más</a>
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
            <a href="alquileres.php" class="btn btn-outline-light">Ver más</a>
        </div>
    </div>
</section>

<!-- FOOTER AMARILLO -->
<section class="footer-yellow" id="contacto">
    <div class="container d-flex flex-wrap justify-content-between">
        <div class="footer-col">
            <h5>Contacto</h5>
            <p><strong>Dirección:</strong> <?php echo $config['direccion']; ?></p>
            <p><strong>Tel:</strong> <?php echo $config['telefono']; ?></p>
            <p><strong>Email:</strong> <?php echo $config['email']; ?></p>
        </div>
        <div class="footer-col text-center">
            <img src="<?php echo $config['icono_blanco']; ?>" alt="Logo" height="80">
            <div class="social-icons mt-2">
                <a href="<?php echo $config['facebook']; ?>" target="_blank"><i class="bi bi-facebook"></i></a>
                <a href="<?php echo $config['instagram']; ?>" target="_blank"><i class="bi bi-instagram"></i></a>
                <a href="<?php echo $config['twitter']; ?>" target="_blank"><i class="bi bi-twitter"></i></a>
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
    </div>
</section>

<div style="background-color:#02253e; color:white; text-align:center; padding:10px 0; font-size:14px;">
    &copy; Derechos Reservados 2025
</div>

</body>
</html>
