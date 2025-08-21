<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

echo "<h2>Bienvenido " . $_SESSION['usuario'] . "</h2>";

if ($_SESSION['privilegio'] == 'admin') {
    echo "<a href='personalizar.php'>Personalizar Página</a> | ";
    echo "<a href='usuarios.php'>Gestión de Usuarios</a> | ";
}
echo "<a href='propiedades.php'>Gestión de Propiedades</a> | ";
echo "<a href='logout.php'>Cerrar Sesión</a>";
?>
