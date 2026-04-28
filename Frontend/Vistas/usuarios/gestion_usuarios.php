<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('usuarios');

require_once '../../../Backend/clases/Rol.php';
require_once '../../../Backend/clases/Usuario.php';
require_once '../../../Backend/clases/Dashboard.php';

$usuario = new Usuario();
$lista_usuarios = $usuario->consultar_todos_con_rol();

$rol = new Rol();
$roles = $rol->consultar_activos();

$dash = new Dashboard();
$actividadReciente = $dash->actividadesRecientes(30);
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/usuarios/gestion_usuarios.css">
<?php include '../layouts/topbar.php'; ?>

<main class="container-fluid alc-page usuarios-page">
    <section class="alc-page-header usuarios-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div>
            <h1 class="alc-page-title">Gestion de usuarios</h1>
            <p class="alc-page-subtitle">Administra cuentas, roles y estado de acceso del personal institucional.</p>
        </div>
        <div class="alc-page-tools d-flex flex-wrap gap-2">
            <?php if (puedeRealizarAccion('roles', 'read')): ?>
                <a href="<?= BASE_URL ?>/Vistas/usuarios/gestion_roles.php" class="btn btn-outline-secondary">
                    <i class="fas fa-user-tag me-2"></i>Gestionar roles
                </a>
            <?php endif; ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#usuarioModal">
                <i class="fas fa-user-plus me-2"></i>Registrar nuevo usuario
            </button>
        </div>
    </section>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php include 'listarUsuarios.php'; ?>
    <?php include 'actividadReciente.php'; ?>
</main>

<?php include 'modalRegistroActualizar.php'; ?>

<script src="/SistAlc/Frontend/Vistas/js/modalUsuario.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/SistAlc/Frontend/Vistas/js/alertUsuario.js"></script>
<script src="/SistAlc/Frontend/Vistas/js/actRecUsuario.js"></script>
<script>
const actividadReciente = <?php echo json_encode($actividadReciente ?? [], JSON_UNESCAPED_UNICODE); ?>;
renderPaginador();
renderTabla();
</script>

<?php include '../layouts/footer.php'; ?>
