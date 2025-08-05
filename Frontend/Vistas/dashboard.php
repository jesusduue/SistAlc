<?php
include 'layouts/header.php';

// Verificar si el usuario tiene sesión
if (!isset($_SESSION['nom_usu']) || !isset($_SESSION['nom_rol'])) {
    header('Location: ../../index.php');
    exit;
}

$nombre_usuario = $_SESSION['nom_usu'];
$nombre_rol = $_SESSION['nom_rol'];
?>

<?php include 'layouts/sidebar.php'; ?>

<link rel="stylesheet" href="../estilos/css/bootstrap.min.css">

<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></h1>
    <p class="text-muted">Rol: <strong><?php echo htmlspecialchars($nombre_rol); ?></strong></p>

    <div class="row g-4 mt-4">
        <!-- Gestión de Usuarios -->
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text">Gestión de cuentas y roles de acceso.</p>
                    <a href="usuarios/gestion_usuarios.php" class="btn btn-outline-primary">Administrar</a>
                </div>
            </div>
        </div>

        <!-- Gestión de Contribuyentes -->
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-id-card fa-3x mb-3 text-success"></i>
                    <h5 class="card-title">Contribuyentes</h5>
                    <p class="card-text">Registro y control de contribuyentes.</p>
                    <a href="contribuyentes/gestion_contribuyentes.php" class="btn btn-outline-success">Administrar</a>
                </div>
            </div>
        </div>

        <!-- Gestión de Patentes -->
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-building fa-3x mb-3 text-warning"></i>
                    <h5 class="card-title">Patentes</h5>
                    <p class="card-text">Control de actividades económicas y licencias.</p>
                    <a href="#" class="btn btn-outline-warning">Administrar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>
