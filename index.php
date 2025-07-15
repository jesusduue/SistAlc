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

// Incluir el encabezado de la página
require_once 'Frontend/Vistas/layouts/header.php';
?>

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

<?php
// Incluir el pie de página
require_once 'Frontend/Vistas/layouts/footer.php';
?>
