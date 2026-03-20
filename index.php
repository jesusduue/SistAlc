<?php
session_start();

if (isset($_SESSION['id_usu'])) {
    header('Location: Frontend/Vistas/dashboard.php');
    exit;
}

$mensaje_alerta = null;
$tipo_alerta = 'danger';
if (isset($_SESSION['mensaje'])) {
    $mensaje_alerta = $_SESSION['mensaje']['texto'] ?? '';
    $tipo_alerta = $_SESSION['mensaje']['tipo'] ?? 'danger';
    unset($_SESSION['mensaje']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Alcaldia</title>
    <link href="/SistAlc/Frontend/estilos/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/SistAlc/Frontend/estilos/css/login/login.css">
</head>
<body class="login-page">
    <?php if (!empty($mensaje_alerta)): ?>
        <div class="login-toast-wrap">
            <div id="mensajeToast" class="toast align-items-center text-white bg-<?php echo $tipo_alerta; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo htmlspecialchars($mensaje_alerta); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <main class="container login-shell">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-8 col-lg-5">
                <section class="card login-card">
                    <header class="login-card-header text-center">
                        <span class="login-chip">Municipio Garcia de Hevia</span>
                        <h1>Sistema de Alcaldia</h1>
                        <p>Acceso institucional</p>
                    </header>

                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <img src="Frontend/estilos/img/logo_alcaldia_la_fria.png" alt="Logo Alcaldia" class="login-logo">
                        </div>

                        <form id="loginForm" action="Backend/controlador/UsuarioController.php" method="POST" novalidate>
                            <input type="hidden" name="accion" value="login">

                            <div class="mb-3">
                                <label for="nom_usu" class="form-label"><i class="fas fa-user me-2"></i>Usuario</label>
                                <input type="text" class="form-control" autocomplete="username" id="nom_usu" name="nom_usu" placeholder="Ingrese su nombre de usuario" required>
                                <span class="invalid-feedback d-block"></span>
                            </div>

                            <div class="mb-4">
                                <label for="cla_usu" class="form-label"><i class="fas fa-lock me-2"></i>Contrasena</label>
                                <input type="password" autocomplete="current-password" class="form-control" id="cla_usu" name="cla_usu" placeholder="Ingrese su contrasena" required>
                                <span class="invalid-feedback d-block"></span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-right-to-bracket me-2"></i>Ingresar al sistema
                            </button>
                        </form>
                    </div>

                    <footer class="card-footer text-center">
                        <small>&copy; <?php echo date('Y'); ?> Alcaldia del Municipio G. de Hevia</small>
                    </footer>
                </section>
            </div>
        </div>
    </main>

    <script src="/SistAlc/Frontend/estilos/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function (toastEl) {
                return new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
            });
            toastList.forEach(function (toast) { toast.show(); });
        });
    </script>
    <script src="Frontend/Vistas/js/validarInicioSesion.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
