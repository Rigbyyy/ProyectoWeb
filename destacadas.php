<?php
session_start();
require_once "conexion.php";


$sqlConfig = "SELECT * FROM configuracion LIMIT 1";
$resConfig = $conect->query($sqlConfig);
$config = $resConfig->fetch_assoc();

$color1 = $config['color_tema1']; 
$color2 = $config['color_tema2'];
$color3 = $config['color_tema3']; 

$sqlPropiedades = "SELECT * FROM propiedades WHERE destacada = 1 ORDER BY id DESC";
$resPropiedades = $conect->query($sqlPropiedades);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Propiedades Destacadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin:0; padding:0; background-color: <?= $color2 ?>; }

        /* HEADER */
        .header { background-color: <?= $color1 ?>; padding: 15px 30px; display:flex; justify-content:space-between; align-items:center; }
        .header img { height:50px; }
        .nav-links a { color: <?= $color2 ?>; text-decoration:none; margin-left:15px; font-weight:500; }
        .nav-links a:hover { text-decoration:underline; }

        /* Título de sección */
        .section-title { text-align:center; font-size:32px; margin:30px 0; color: <?= $color1 ?>; text-shadow:1px 1px 2px rgba(0,0,0,0.3); }

        /* Propiedades */
        .prop-card { border-radius:10px; padding:15px; margin-bottom:30px; transition: transform 0.2s; position:relative; cursor:pointer; background-color:<?= $color2 ?>; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
        .prop-card:hover { transform:scale(1.03); box-shadow:0 6px 18px rgba(0,0,0,0.2); }
        .prop-card img { width:100%; height:250px; object-fit:cover; border-radius:8px; }
        .prop-card h5, .prop-card p { text-align:center; margin-top:10px; }
        .prop-card .precio { text-align:center; font-size:1.3rem; font-weight:bold; color: <?= $color3 ?>; text-shadow: 0.5px 0.5px 0 #000; }

        /* Botón Ver Más */
        .btn-vermas {
            border:2px solid <?= $color3 ?>;
            border-radius:12px;
            padding:10px 30px;
            text-decoration:none;
            font-weight:bold;
            color: <?= $color1 ?>;
            display:inline-block;
            margin-top:10px;
            transition: all 0.3s ease;
        }
        .btn-vermas:hover {
            background-color: <?= $color3 ?>;
            color: <?= $color2 ?>;
        }

        /* Contenedor general */
        .container-prop { max-width:1200px; margin:0 auto; padding:30px; }
        .text-center { text-align:center; }
    
    </style>
</head>
<body>
    <div class="header">
        <div><img src="<?= $config['icono_principal'] ?>" alt="Logo"></div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
        </div>
    </div>

    <div class="container-prop">
        <h2 class="section-title" id="destacadas">Propiedades Destacadas</h2>
        <div class="row">
            <?php while($prop = $resPropiedades->fetch_assoc()) { ?>
                <div class="col-md-4">
                    <div class="prop-card position-relative">
                        <a href="detalle.php?id=<?= $prop['id'] ?>" class="full-link"></a>
                        <img src="<?= $prop['imagen_destacada'] ?>" alt="Propiedad">
                        <h5><?= $prop['titulo'] ?></h5>
                        <p><?= $prop['descripcion_breve'] ?></p>
                        <p class="precio">$<?= number_format($prop['precio'], 2) ?></p>
                        <div class="text-center">
                            <a href="detalle.php?id=<?= $prop['id'] ?>" class="btn-vermas">Ver más</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
