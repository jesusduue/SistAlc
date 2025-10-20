<?php
// Frontend/Vistas/usuarios/gestion_usuarios.php

// 1) Header común (hojas de estilo / metadatos)

include '../layouts/header.php';



// 2) Protección de vista y sesión

include '../layouts/proteger_vista.php';
include '../layouts/topbar.php';
restringirAcceso('usuarios');


// 3) Modelos necesarios
require_once '../../../Backend/clases/Rol.php';
require_once '../../../Backend/clases/Usuario.php';
require_once '../../../Backend/clases/Dashboard.php';

// 4) Obtener datos para la tabla de usuarios y roles
$usuario = new Usuario();
$lista_usuarios = $usuario->consultar_todos_con_rol();

$rol = new Rol();
$roles = $rol->consultar_activos();

// 5) Obtener actividad reciente desde el modelo Dashboard
$dash = new Dashboard();
$actividadReciente = $dash->actividadesRecientes(30);
?>




<div class="container py-4" style="margin-left: auto; margin-right: auto; padding-top: 2rem;">
    <h2>Gestión de Usuarios</h2>

    <?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?php echo $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['mensaje']['texto']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensaje']); endif; ?>

    <div class="mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#usuarioModal">
            <i class="fas fa-plus"></i> Registrar Nuevo Usuario
        </button>
    </div>

    <!-- Tabla lista de usuarios (separada en archivo para mantener limpio) -->
    <?php include 'listarUsuarios.php'; ?>
    <!-- Fin tabla lista de usuarios -->

    <!-- Actividad reciente (paginación nativa sin jQuery) -->
    <?php include 'actividadReciente.php'; ?>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
<!-- MODAL USUARIO REGISTRAR Y ACTUALIZAR -->
<?php include 'modalRegistroActualizar.php'; ?>
<!-- FIN MODAL -->

<!-- Scripts: bundle (para dropdowns), scripts locales -->

<script src="/SistAlc/Frontend/Vistas/js/modalUsuario.js"></script>

<!-- SweetAlert2 (si lo usas en esta vista) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/SistAlc/Frontend/Vistas/js/alertUsuario.js"></script>
<script src="/SistAlc/Frontend/Vistas/js/actRecUsuario.js"></script>

<script>
const actividadReciente = <?php echo json_encode($actividadReciente ?? [], JSON_UNESCAPED_UNICODE); ?>; // Actividad reciente 
//  inicializar paginación y cargar la actividad reciente
        renderPaginador();
        renderTabla();
</script>

<?php include '../layouts/footer.php'; ?>

