<?php
include("conexion.php");
$id = $_GET['id'] ?? 0;

$stmt = $conect->prepare("SELECT p.*, u.nombre AS agente 
    FROM propiedades p 
    JOIN usuarios u ON p.agente_id = u.id 
    WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$propiedad = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $propiedad['titulo'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?= $propiedad['titulo'] ?></h1>
    <img src="<?= $propiedad['imagen_destacada'] ?>" width="400">
    <p><?= $propiedad['descripcion_larga'] ?></p>
    <p><strong>Precio:</strong> $<?= number_format($propiedad['precio'], 2) ?></p>
    <p><strong>Ubicación:</strong> <?= $propiedad['ubicacion'] ?></p>
    <p><strong>Agente:</strong> <?= $propiedad['agente'] ?></p>
    <div><?= $propiedad['mapa'] ?></div>
    <a href="index.php">Volver</a>
</body>
</html>
