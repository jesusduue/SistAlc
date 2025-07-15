<?php
// Iniciar sesión para verificar si el usuario ya está autenticado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si ya existe una sesión activa, redirigir al dashboard
if (isset($_SESSION['id_usu'])) {
    header('Location: dashboard.php');
    exit();
}
/* verificamos el rol del usuario para mostrar el menu de acuerdo al rol     */
if (isset($_SESSION['id_usu'])) {
    $id_usu = $_SESSION['id_usu'];
    $rol = $_SESSION['rol'];
}
/* GUARDAMOS EL NOMBRE DEL USUARIO Y EL NOMBRE DEL ROL   */

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Alcaldía</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="../../estilos/css/bootstrap.min.css" rel="stylesheet">
    
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
