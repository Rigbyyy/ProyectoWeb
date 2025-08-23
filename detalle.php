<?php
include("conexion.php");
$id = $_GET['id'] ?? 0;
$from = isset($_GET['from']) ? $_GET['from'] : 'index';

$stmt = $conect->prepare("SELECT p.*, u.nombre AS agente 
    FROM propiedades p 
    JOIN usuarios u ON p.agente_id = u.id 
    WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$propiedad = $stmt->get_result()->fetch_assoc();

$sqlConfig = "SELECT * FROM configuracion LIMIT 1";
$resConfig = $conect->query($sqlConfig);
$config = $resConfig->fetch_assoc();
$color1 = $config['color_tema1']; 
$color2 = $config['color_tema2']; 
$color3 = $config['color_tema3'];
$color4 = $config['color_tema4']; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $propiedad['titulo'] ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: <?= $color1 ?>;
            color: #333;
        }

        .header {
            background-color: <?= $color1 ?>;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid <?= $color3 ?>;
        }
        .header img {
            height: 55px;
        }
        .header .nav-links a {
            color: <?= $color3 ?>;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
        }

        .container {
            max-width: 950px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
            text-align: center;
        }

    .container h1 {
    color: <?= $color3 ?>;
    font-size: 32px;
    margin-bottom: 20px;
    text-shadow: 1px 1px 2px rgba(0,0,0,3); 
}

        .propiedad-img {
            width: 100%;
            max-height: 450px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .propiedad-info {
            margin: 20px 0;
            font-size: 16px;
            line-height: 1.6;
        }
        .propiedad-info strong {
            color: <?= $color2 ?>;
        }

        /* Precio destacado */
        .precio {
            display: inline-block;
            margin: 20px 0;
            padding: 15px 30px;
            font-size: 26px;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(135deg, <?= $color3 ?>, <?= $color4 ?>);
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.15);
        }

        .mapa {
            margin-top: 25px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-volver {
            display: inline-block;
            margin-top: 35px;
            padding: 12px 28px;
            border-radius: 10px;
            border: 2px solid <?= $color3 ?>;
            background: transparent;
            color: <?= $color3 ?>;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        .btn-volver:hover {
            background: <?= $color3 ?>;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <img src="<?php echo $config['icono_principal']; ?>" alt="Logo">
        </div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
        </div>
    </div>

    <div class="container">
        <h1><?= $propiedad['titulo'] ?></h1>
        <img src="<?= $propiedad['imagen_destacada'] ?>" class="propiedad-img">
        <div class="propiedad-info">
            <p><?= $propiedad['descripcion_larga'] ?></p>
            <div class="precio">$<?= number_format($propiedad['precio'], 2) ?></div>
            <p><strong>Ubicación:</strong> <?= $propiedad['ubicacion'] ?></p>
            <p><strong>Agente:</strong> <?= $propiedad['agente'] ?></p>
        </div>
        <div class="mapa">
            <?= $propiedad['mapa'] ?>
        </div>
       <a href="<?= $from ?>.php" class="btn-volver">Volver</a>
    </div>
</body>
</html>
