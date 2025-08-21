<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>UTW Solutions Real Estate</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }
    .banner {
      background-image: url('https://img.freepik.com/free-photo/real-estate-agent-presenting-house-model_23-2149396286.jpg');
      background-size: cover;
      background-position: center;
      height: 400px;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-shadow: 2px 2px 4px #000;
    }
    .section-title {
      margin-top: 40px;
      margin-bottom: 20px;
      text-align: center;
    }
    .property-card {
      border: 1px solid #ddd;
      padding: 15px;
      border-radius: 8px;
      background-color: white;
      margin-bottom: 20px;
    }
    .footer {
      background-color: #f5c505de;
      color: #333;
      padding: 30px 0;
    }
    .social-icons i {
      font-size: 24px;
      margin-right: 15px;
    }
    .destacadas {
      background-color: #3117E3;
      padding: 30px 0;
    }
    .venta {
      background-color: white;
      padding: 30px 0;
    }
    .alquiler {
      background-color: #02253e;
      padding: 30px 0;
    }
  </style>
</head>
<body style="background-color: <?= $config['color_tema'] ?>;">

    <!-- Encabezado -->
    <header>
        <img src="<?= $config['icono_principal'] ?>" alt="Logo" height="60">
        <h1><?= $config['banner_texto'] ?></h1>
        <img src="<?= $config['banner_imagen'] ?>" alt="Banner" height="120">
    </header>

    <!-- Menú -->
    <nav>
        <a href="index.php">Inicio</a>
        <a href="quienes.php">Quiénes Somos</a>
        <a href="contacto.php">Contacto</a>
        <?php if(isset($_SESSION['usuario'])): ?>
            <a href="logout.php">Cerrar Sesión (<?= $_SESSION['usuario'] ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>

    <!-- Propiedades destacadas -->
    <section>
        <h2>Propiedades Destacadas</h2>
        <div class="propiedades">
            <?php while($fila = $propiedades->fetch_assoc()): ?>
                <div class="propiedad">
                    <img src="<?= $fila['imagen_destacada'] ?>" alt="<?= $fila['titulo'] ?>" width="250">
                    <h3><?= $fila['titulo'] ?></h3>
                    <p><?= $fila['descripcion_breve'] ?></p>
                    <p><strong>Precio:</strong> $<?= number_format($fila['precio'], 2) ?></p>
                    <p><strong>Agente:</strong> <?= $fila['agente'] ?></p>
                    <a href="detalle.php?id=<?= $fila['id'] ?>">Ver Detalles</a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p><?= $config['direccion'] ?> | Tel: <?= $config['telefono'] ?> | Email: <?= $config['email'] ?></p>
        <a href="<?= $config['facebook'] ?>">Facebook</a> |
        <a href="<?= $config['instagram'] ?>">Instagram</a> |
        <a href="<?= $config['twitter'] ?>">Twitter</a>
    </footer>

</body>
</html>
