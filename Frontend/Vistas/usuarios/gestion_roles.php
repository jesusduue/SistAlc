<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('roles');

require_once '../../../Backend/clases/Rol.php';

$rol = new Rol();
$roles = $rol->consultar_todos();

$rolesBaseSistema = [
    'administrador',
    'jefe de catastro',
    'jefe de patentes',
    'tesorero',
    'secretaria de catastro',
    'secretaria de patentes',
    'calculista',
];

$esRolBaseSistema = static function (string $nombreRol) use ($rolesBaseSistema): bool {
    return in_array(mb_strtolower(trim($nombreRol), 'UTF-8'), $rolesBaseSistema, true);
};
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/usuarios/gestion_roles.css">
<?php include '../layouts/topbar.php'; ?>

<main class="container-fluid alc-page roles-page">
    <section class="alc-page-header roles-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div>
            <h1 class="alc-page-title">Gestion de roles</h1>
            <p class="alc-page-subtitle">Administra el catalogo de roles y controla que usuarios pueden ser asignados a cada perfil institucional.</p>
        </div>
        <div class="alc-page-tools d-flex flex-wrap gap-2">
            <?php if (puedeRealizarAccion('usuarios', 'read')): ?>
                <a href="<?= BASE_URL ?>/Vistas/usuarios/gestion_usuarios.php" class="btn btn-outline-secondary">
                    <i class="fas fa-users me-2"></i>Gestionar usuarios
                </a>
            <?php endif; ?>
            <?php if (puedeRealizarAccion('roles', 'create')): ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rolModal">
                    <i class="fas fa-user-tag me-2"></i>Registrar nuevo rol
                </button>
            <?php endif; ?>
        </div>
    </section>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <section class="alert role-note" role="note">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-circle-info mt-1"></i>
            <div>
                <strong>Importante:</strong> Los permisos siguen definidos en <code>proteger_vista.php</code>. Los roles base no pueden renombrarse y los roles nuevos necesitan un mapeo de permisos en codigo para poder entrar a modulos protegidos.
            </div>
        </div>
    </section>

    <section class="card alc-panel">
        <div class="card-header">Listado de roles</div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Rol</th>
                        <th>Tipo</th>
                        <th>Usuarios asignados</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $item): ?>
                        <?php
                        $esBase = $esRolBaseSistema($item['nom_rol']);
                        $usuariosActivos = (int)($item['usuarios_activos'] ?? 0);
                        $totalUsuarios = (int)($item['total_usuarios'] ?? 0);
                        $bloquearInactivacion = $item['est_registro'] === 'A' && $usuariosActivos > 0;
                        ?>
                        <tr>
                            <td><?= (int)$item['id_rol']; ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($item['nom_rol']); ?></div>
                                <?php if ($esBase): ?>
                                    <small class="text-muted">Rol base con permisos estaticos.</small>
                                <?php else: ?>
                                    <small class="text-muted">Rol configurable a nivel de catalogo.</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge role-type <?= $esBase ? 'role-type-base' : 'role-type-custom'; ?>">
                                    <?= $esBase ? 'Base' : 'Personalizado'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="role-count"><?= $usuariosActivos; ?> activos</div>
                                <small class="text-muted"><?= $totalUsuarios; ?> totales</small>
                            </td>
                            <td>
                                <span class="badge status-chip <?= $item['est_registro'] === 'A' ? 'status-a' : 'status-i'; ?>">
                                    <?= $item['est_registro'] === 'A' ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex flex-wrap gap-2">
                                <?php if (puedeRealizarAccion('roles', 'update')): ?>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rolModal"
                                        data-id="<?= (int)$item['id_rol']; ?>"
                                        data-nombre="<?= htmlspecialchars($item['nom_rol']); ?>"
                                        data-estado="<?= htmlspecialchars($item['est_registro']); ?>"
                                        data-es-base="<?= $esBase ? '1' : '0'; ?>"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                <?php endif; ?>

                                <?php if (puedeRealizarAccion('roles', 'update')): ?>
                                    <a
                                        href="javascript:void(0);"
                                        class="btn btn-sm btn-<?= $item['est_registro'] === 'A' ? 'success' : 'secondary'; ?> btn-cambiar-estado-rol <?= $bloquearInactivacion ? 'disabled' : ''; ?>"
                                        <?= $bloquearInactivacion ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
                                        data-url="../../../Backend/controlador/RolController.php?accion=cambiar_estado&id=<?= (int)$item['id_rol']; ?>&nuevo_estado=<?= $item['est_registro'] === 'A' ? 'I' : 'A'; ?>"
                                        data-nombre="<?= htmlspecialchars($item['nom_rol']); ?>"
                                        data-accion="<?= $item['est_registro'] === 'A' ? 'inactivar' : 'activar'; ?>"
                                        title="<?= $bloquearInactivacion ? 'Debe reasignar o desactivar los usuarios activos antes de inactivar este rol.' : 'Cambiar estado'; ?>"
                                    >
                                        <i class="fas fa-toggle-<?= $item['est_registro'] === 'A' ? 'off' : 'on'; ?>"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<div class="modal fade" id="rolModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rolForm" action="../../../Backend/controlador/RolController.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="formAccionRol" value="registrar">
                    <input type="hidden" name="id_rol" id="formIdRol" value="">

                    <div class="mb-3">
                        <label for="nom_rol" class="form-label">Nombre del rol *</label>
                        <input type="text" name="nom_rol" id="nom_rol" class="form-control" maxlength="50" required>
                        <div class="form-text d-none" id="ayudaRolBase"></div>
                    </div>

                    <div class="mb-3">
                        <label for="est_registro" class="form-label">Estado *</label>
                        <select name="est_registro" id="est_registro" class="form-select" required>
                            <option value="A">Activo</option>
                            <option value="I">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitRol">
                        <i class="fas fa-plus"></i> <span>Registrar rol</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>/Vistas/js/usuarios/modalRol.js"></script>
<script src="<?= BASE_URL ?>/Vistas/js/usuarios/alertRol.js"></script>
<?php include '../layouts/footer.php'; ?>
