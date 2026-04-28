<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('perfil');

require_once '../../../Backend/clases/Usuario.php';

$usuario = new Usuario();
$perfil = $usuario->consultar_perfil((int)($_SESSION['id_usu'] ?? 0));
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/usuarios/perfil.css">
<?php include '../layouts/topbar.php'; ?>

<main class="container-fluid alc-page perfil-page">
    <section class="alc-page-header perfil-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div>
            <h1 class="alc-page-title">Perfil de usuario</h1>
            <p class="alc-page-subtitle">Consulta tus datos de acceso y actualiza tu contrasena institucional de forma segura.</p>
        </div>
        <div class="perfil-meta">
            <span><i class="fas fa-user-shield"></i>Sesion activa</span>
            <span><i class="fas fa-lock"></i>Cambio de clave protegido</span>
        </div>
    </section>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <section class="row g-3">
        <div class="col-12 col-xl-5">
            <article class="card alc-panel h-100 perfil-card">
                <div class="card-body">
                    <div class="perfil-identidad">
                        <div class="perfil-avatar">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <h2><?= htmlspecialchars($perfil['nom_usu'] ?? $_SESSION['nom_usu'] ?? 'Usuario'); ?></h2>
                            <p><?= htmlspecialchars($perfil['nom_rol'] ?? $_SESSION['nom_rol'] ?? 'Sin rol'); ?></p>
                        </div>
                    </div>

                    <dl class="perfil-detalle-lista">
                        <div>
                            <dt>ID de usuario</dt>
                            <dd><?= (int)($perfil['id_usu'] ?? 0); ?></dd>
                        </div>
                        <div>
                            <dt>Nombre de usuario</dt>
                            <dd><?= htmlspecialchars($perfil['nom_usu'] ?? ''); ?></dd>
                        </div>
                        <div>
                            <dt>Rol asignado</dt>
                            <dd><?= htmlspecialchars($perfil['nom_rol'] ?? ''); ?></dd>
                        </div>
                        <div>
                            <dt>Estado</dt>
                            <dd>
                                <span class="badge status-chip <?= (($perfil['est_registro'] ?? 'I') === 'A') ? 'status-a' : 'status-i'; ?>">
                                    <?= htmlspecialchars($perfil['estado_descripcion'] ?? 'Sin estado'); ?>
                                </span>
                            </dd>
                        </div>
                    </dl>

                    <div class="perfil-nota">
                        <i class="fas fa-circle-info"></i>
                        <span>Si administracion cambia tu rol o estado, esos datos se reflejan aqui automaticamente.</span>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-12 col-xl-7">
            <article class="card alc-panel h-100 seguridad-card">
                <div class="card-header">Cambiar contrasena</div>
                <div class="card-body">
                    <form id="perfilClaveForm" action="../../../Backend/controlador/UsuarioController.php" method="POST" novalidate>
                        <input type="hidden" name="accion" value="cambiar_clave">

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="cla_actual" class="form-label">Contrasena actual *</label>
                                <input type="password" name="cla_actual" id="cla_actual" class="form-control" autocomplete="current-password" required>
                                <div class="invalid-feedback">Debes indicar tu contrasena actual.</div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label for="cla_nueva" class="form-label">Nueva contrasena *</label>
                                <input type="password" name="cla_nueva" id="cla_nueva" class="form-control" minlength="8" autocomplete="new-password" required>
                                <div class="form-text">Usa al menos 8 caracteres y evita repetir la clave actual.</div>
                                <div class="invalid-feedback">La nueva contrasena debe tener al menos 8 caracteres.</div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label for="cla_confirmar" class="form-label">Confirmar nueva contrasena *</label>
                                <input type="password" name="cla_confirmar" id="cla_confirmar" class="form-control" minlength="8" autocomplete="new-password" required>
                                <div class="invalid-feedback" id="feedbackConfirmacion">Debes confirmar la nueva contrasena.</div>
                            </div>
                        </div>

                        <div class="seguridad-tips">
                            <div><i class="fas fa-shield-halved"></i>La validacion final siempre se ejecuta en servidor.</div>
                            <div><i class="fas fa-key"></i>La contrasena actual es obligatoria para autorizar el cambio.</div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i>Actualizar contrasena
                            </button>
                            <a href="<?= BASE_URL ?>/Vistas/dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al inicio
                            </a>
                        </div>
                    </form>
                </div>
            </article>
        </div>
    </section>
</main>

<script src="<?= BASE_URL ?>/Vistas/js/usuarios/perfilUsuario.js"></script>
<?php include '../layouts/footer.php'; ?>
