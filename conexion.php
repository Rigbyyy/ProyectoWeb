<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inmobiliaria";

$conect = new mysqli($host, $user, $pass, $db);
if ($conect->connect_error) {
  die("Error de conexión: " . $conect->connect_error);
}
$conect->set_charset("utf8mb4");
