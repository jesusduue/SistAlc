<?php
// Iniciar sesión para verificar si el usuario ya está autenticado
session_start();
require_once __DIR__ . '/paths.php';

// Comprueba si el usuario ha iniciado sesión, de lo contrario redirige a la página de inicio.
if (!isset($_SESSION['id_usu'])) {
    $_SESSION['mensaje_logout'] = "Su sesión ha expirado o no ha iniciado sesión.";
    header('Location: ../../index.php');
    exit;
}

$nombre_usuario = $_SESSION['nom_usu'] ?? '';
$nombre_rol = $_SESSION['nom_rol'] ?? '';

// Incluir rutas base
require_once __DIR__ . '/paths.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Alcaldía</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="<?= ASSETS_CSS ?>/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f0f2f5;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
        }
    </style>
</head>
<body>
