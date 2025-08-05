<?php
include '../layouts/proteger_vista.php';

// Aplicar restricción de acceso a administradores
restringirAcceso('Administrador');

// Seguridad: Solo admins pueden entrar
/* if (!isset($_SESSION['nom_rol']) || $_SESSION['nom_rol'] !== 'Administrador') {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: "error",
                title: "Acceso denegado",
                text: "No tienes permisos para acceder a esta vista.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: "Volver al inicio"
            }).then(() => {
                window.location.href = "../dashboard.php";
            });
        });
    </script>';
    exit;
} */
// Incluir la clase Usuario
// Obtener roles
require_once '../../../Backend/clases/Rol.php';
require_once '../../../Backend/clases/Usuario.php';

$usuario = new Usuario();
$lista_usuarios = $usuario->consultar_todos_con_rol();

$usuario_editar = null;
$accion = 'registrar';

if (isset($_GET['editar_id'])) {
    $usuario_editar = $usuario->consultar_por_id((int)$_GET['editar_id']);
    $accion = 'modificar';
}

$rol = new Rol();
$roles = $rol->consultar_activos();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios</title>
    <link href="../../../Frontend/estilos/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include '../layouts/sidebar.php'; ?>
<div class="container py-4" style="margin-left: 250px; padding-top: 2rem;">
<h2>Gestión de Usuarios</h2>

<!-- Mensaje de estado cuando se realiza una acción -->
<?php if (isset($_SESSION['mensaje'])): ?>
<div class="alert alert-<?php echo $_SESSION['mensaje']['tipo']; ?>">
    <?php echo $_SESSION['mensaje']['texto']; ?>
</div>
<?php unset($_SESSION['mensaje']); endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <?php echo ($accion === 'modificar') ? 'Editar Usuario' : 'Registrar Nuevo Usuario'; ?>
    </div>
    <div class="card-body">
        <form action="../../../Backend/controlador/UsuarioController.php" method="POST">
            <input type="hidden" name="accion" value="<?php echo $accion; ?>">
            <?php if ($accion === 'modificar'): ?>
                <input type="hidden" name="id_usu" value="<?php echo $usuario_editar['id_usu']; ?>">
            <?php endif; ?>
            <div class="row">
                <div class="col-md-4">
                    <label>Usuario *</label>
                    <input type="text" name="nom_usu" class="form-control" value="<?php echo $usuario_editar['nom_usu'] ?? ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <label>Contraseña <?php echo ($accion === 'registrar') ? '*' : '(opcional)'; ?></label>
                    <input type="password" name="cla_usu" class="form-control" <?php echo ($accion === 'registrar') ? 'required' : ''; ?> >
                </div>
                <div class="col-md-2">
                    <label>Rol *</label>
                    <select name="fky_rol" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo $rol['id_rol']; ?>" <?php echo ($usuario_editar['fky_rol'] ?? '') == $rol['id_rol'] ? 'selected' : ''; ?>>
                                <?php echo $rol['nom_rol']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Estado *</label>
                    <select name="est_usu" class="form-select" required>
                        <option value="A" <?php echo ($usuario_editar['est_usu'] ?? '') == 'A' ? 'selected' : ''; ?>>Activo</option>
                        <option value="I" <?php echo ($usuario_editar['est_usu'] ?? '') == 'I' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-<?php echo ($accion === 'modificar') ? 'save' : 'plus'; ?>"></i>
                    <?php echo ($accion === 'modificar') ? 'Guardar Cambios' : 'Registrar Usuario'; ?>
                </button>
                <?php if ($accion === 'modificar'): ?>
                    <a href="gestion_usuarios.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Listado de Usuarios</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista_usuarios as $usu): ?>
                <tr>
                    <td><?php echo $usu['id_usu']; ?></td>
                    <td><?php echo $usu['nom_usu']; ?></td>
                    <td><?php echo $usu['nom_rol']; ?></td>
                    <td>
                        <span class="badge bg-<?php echo ($usu['est_usu'] === 'A') ? 'success' : 'secondary'; ?>">
                            <?php echo $usu['estado_descripcion']; ?>
                        </span>
                    </td>
                    <td>
                        <a href="gestion_usuarios.php?editar_id=<?php echo $usu['id_usu']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($usu['id_usu'] != $_SESSION['id_usu']): ?>
                            <a href="../../../Backend/controlador/UsuarioController.php?accion=cambiar_estado&id=<?php echo $usu['id_usu']; ?>&estado_nuevo=<?php echo ($usu['est_usu'] === 'A') ? 'I' : 'A'; ?>"
                               class="btn btn-sm btn-<?php echo ($usu['est_usu'] === 'A') ? 'success' : 'danger'; ?>"
                               onclick="return confirm('Confirma el cambio de estado del usuario?');">
                                <i class="fas fa-toggle-<?php echo ($usu['est_usu'] === 'A') ? 'off' : 'on'; ?>"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<script src="../estilos/js/bootstrap.bundle.min.js"></script>

</body>
</html>
