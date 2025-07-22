<?php

// Se inicia la sesión para poder acceder a las variables de sesión.
session_start();

// Si el usuario ya tiene una sesión activa, se le redirige directamente al dashboard.
if (isset($_SESSION['id_usu'])) {
    header('Location: Frontend/Vistas/dashboard.php');
    exit;
}

// Se revisa si el controlador (login.php) dejó un mensaje en la sesión.
$mensaje_alerta = null; // Se inicializa la variable
if (isset($_SESSION['mensaje'])) {
    // Si hay un mensaje, se guarda en nuestra variable local.
    $mensaje_alerta = $_SESSION['mensaje'];
    // Inmediatamente después, se elimina de la sesión para que no se muestre de nuevo si se recarga la página.
    unset($_SESSION['mensaje']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Alcaldía</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="Frontend/estilos/css/bootstrap.min.css" rel="stylesheet">
    
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

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h3 class="mb-0">Sistema de Alcaldía</h3>
                    <p class="mb-0">Inicio de Sesión</p>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <!-- Puedes colocar el logo de la alcaldía aquí -->
                        <img src="Frontend/estilos/img/logo.png" alt="Logo Alcaldía" class="img-fluid" style="max-height: 100px;">
                    </div>

                    <?php
                    // Mostrar un mensaje de error si la validación falla
                    if (isset($_GET['error'])) {
                        echo '<div class="alert alert-danger" role="alert">
                                <strong>Error:</strong> Usuario o contraseña incorrectos, o el usuario está inactivo.
                              </div>';
                    }
                    ?>

                    <form action="Backend/controlador/UsuarioController.php" method="POST">
                        <!-- Campo oculto para indicar la acción a realizar -->
                        <input type="hidden" name="accion" value="login">
                        
                        <div class="mb-3">
                            <label for="nom_usu" class="form-label"><strong><i class="fas fa-user"></i> Usuario:</strong></label>
                            <input type="text" class="form-control" id="nom_usu" name="nom_usu" placeholder="Ingrese su nombre de usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="cla_usu" class="form-label"><strong><i class="fas fa-lock"></i> Contraseña:</strong></label>
                            <input type="password" class="form-control" id="cla_usu" name="cla_usu" placeholder="Ingrese su contraseña" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Ingresar</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>&copy; <?php echo date('Y'); ?> - Alcaldía del Municipio G. de Hevia</small>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap 5 JS Bundle (incluye Popper) -->
    <script src="../../estilos/js/bootstrap.bundle.min.js"></script>
</body>
</html>








