<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        header("Location: index.php?contacto=error&msg=Faltan+campos+obligatorios");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?contacto=error&msg=Correo+inválido");
        exit;
    }

    $part1 = "xkeysib-7a7a72241dfad51009f86eae6a0df7320be14d53a09c1fd4d180fdf";
    $part2 = "9d87dcda2-";
    $part3 = "ePofl4gdm0zy9";
    $part4 = "lmH";

    $apiKey = $part1 . $part2 . $part3 . $part4;

    $templateId = 1; 

    $data = [
        "to" => [["email" => "cosaserias88@gmail.com"]],
        "templateId" => $templateId,
        "params" => [
            "NOMBRE" => $nombre,
            "EMAIL" => $email,
            "TELEFONO" => $telefono,
            "MENSAJE" => $mensaje
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.brevo.com/v3/smtp/email");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "api-key: $apiKey"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

 header("Location: index.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
