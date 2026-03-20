<?php
session_start();
require_once __DIR__ . '/paths.php';

if (!isset($_SESSION['id_usu'])) {
    $_SESSION['mensaje_logout'] = "Su sesion ha expirado o no ha iniciado sesion.";
    header('Location: ../../index.php');
    exit;
}

$nombre_usuario = $_SESSION['nom_usu'] ?? '';
$nombre_rol = $_SESSION['nom_rol'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Alcaldia</title>
    <link href="<?= ASSETS_CSS ?>/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= ASSETS_CSS ?>/layouts/topbar.css">
    <link rel="stylesheet" href="<?= ASSETS_CSS ?>/layouts/sidebar.css">
</head>
<body class="alc-app-body">
