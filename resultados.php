
<?php
require 'conexion.php'; 
$busqueda = trim($_GET['buscar'] ?? '');
$color1 = "#004080"; 
$color2 = "#ffffff"; 
$color3 = "#f39c12";
$sqlConfig = "SELECT * FROM configuracion LIMIT 1";
$resConfig = $conect->query($sqlConfig);
$config = $resConfig->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> 
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin:0; padding:0; background-color: <?= $color2 ?>; }
        .header { background-color: <?= $color1 ?>; padding: 15px 30px; display:flex; justify-content:space-between; align-items:center; }
        .header img { height:50px; }
        .nav-links a { color: <?= $color2 ?>; text-decoration:none; margin-left:15px; font-weight:500; }
        .nav-links a:hover { text-decoration:underline; }
        .section-title { text-align:center; font-size:32px; margin:30px 0; color: <?= $color1 ?>; text-shadow:1px 1px 2px rgba(0,0,0,0.3); }
        .prop-card { border-radius:10px; padding:15px; margin-bottom:30px; transition: transform 0.2s; position:relative; cursor:pointer; background-color:<?= $color2 ?>; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
        .prop-card:hover { transform:scale(1.03); box-shadow:0 6px 18px rgba(0,0,0,0.2); }
        .prop-card img { width:100%; height:250px; object-fit:cover; border-radius:8px; }
        .prop-card h5, .prop-card p { text-align:center; margin-top:10px; }
        .prop-card .precio { text-align:center; font-size:1.3rem; font-weight:bold; color: <?= $color1 ?>; text-shadow: 0.5px 0.5px 0 #000; }
        .btn-vermas { border:2px solid <?= $color3 ?>; border-radius:12px; padding:10px 30px; text-decoration:none; font-weight:bold; color: <?= $color1 ?>; display:inline-block; margin-top:10px; transition: all 0.3s ease; }
        .btn-vermas:hover { background-color: <?= $color3 ?>; color: <?= $color2 ?>; }
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
        <h2 class="section-title">Resultados de búsqueda</h2>

        <?php
        if ($busqueda !== '') {
            $sql = "SELECT * FROM propiedades 
                    WHERE descripcion_larga LIKE ? 
                       OR descripcion_breve LIKE ?
                       OR titulo LIKE ?
                    ORDER BY id DESC";
            $stmt = $conect->prepare($sql);
            $param = "%$busqueda%";
            $stmt->bind_param("sss", $param, $param, $param);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                echo '<div class="row">';
                while ($prop = $res->fetch_assoc()) {
                    ?>
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
                    <?php
                }
                echo '</div>';
            } else {
                echo "<p>No se encontraron propiedades para <b>".htmlspecialchars($busqueda)."</b>.</p>";
            }
        } else {
            echo "<p>No ingresaste ningún término de búsqueda.</p>";
        }
        ?>
    </div>
</body>
</html>
