<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('inmuebles');

require_once '../../../Backend/clases/Inmueble.php';
$inmuebleModel = new Inmueble();
$inmuebles = $inmuebleModel->consultar_todos();
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/catastro/gestion_inmuebles.css">
<?php include '../layouts/topbar.php'; ?>

<div class="container-fluid alc-page inmuebles-page">
    <section class="alc-page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div>
            <h1 class="alc-page-title">Gestion de inmuebles</h1>
            <p class="alc-page-subtitle">Administra direccion, tipo y area de las propiedades registradas en catastro.</p>
        </div>
        <div class="alc-page-tools">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInmueble">
                <i class="fas fa-plus me-2"></i>Nuevo inmueble
            </button>
        </div>
    </section>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <section class="card alc-panel">
        <div class="card-header">Listado de inmuebles</div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Direccion</th>
                        <th>Tipo</th>
                        <th>Area</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inmuebles as $inm): ?>
                        <tr>
                            <td><?= htmlspecialchars($inm['id_inm']); ?></td>
                            <td><?= htmlspecialchars($inm['dir_inm']); ?></td>
                            <td><?= htmlspecialchars($inm['tip_inm']); ?></td>
                            <td><?= htmlspecialchars($inm['area_inm']); ?></td>
                            <td>
                                <span class="badge status-chip <?= ($inm['est_registro'] === 'A') ? 'status-a' : 'status-i'; ?>">
                                    <?= ($inm['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalInmueble"
                                    data-id="<?= htmlspecialchars($inm['id_inm']); ?>"
                                    data-direccion="<?= htmlspecialchars($inm['dir_inm']); ?>"
                                    data-tipo="<?= htmlspecialchars($inm['tip_inm']); ?>"
                                    data-area="<?= htmlspecialchars($inm['area_inm']); ?>">
                                    Editar
                                </button>
                                <a class="btn btn-sm btn-<?= ($inm['est_registro'] === 'A') ? 'success' : 'secondary'; ?>"
                                    href="../../../Backend/controlador/inmuebleController.php?accion=cambiar_estado&id=<?= $inm['id_inm']; ?>&nuevo_estado=<?= ($inm['est_registro'] === 'A') ? 'I' : 'A'; ?>">
                                    <i class="fas fa-toggle-<?= ($inm['est_registro'] === 'A') ? 'off' : 'on'; ?>"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="modal fade" id="modalInmueble" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../../Backend/controlador/inmuebleController.php" method="POST" id="inmuebleForm">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Inmueble</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionInmueble" value="registrar">
                    <input type="hidden" name="id_inm" id="id_inm" value="">
                    <div class="mb-3">
                        <label for="dir_inm" class="form-label">Direccion *</label>
                        <textarea name="dir_inm" id="dir_inm" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tip_inm" class="form-label">Tipo *</label>
                            <select name="tip_inm" id="tip_inm" class="form-select" required>
                                <option value="Terreno">Terreno</option>
                                <option value="Casa">Casa</option>
                                <option value="Local">Local</option>
                                <option value="Galpon">Galpon</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="area_inm" class="form-label">Area *</label>
                            <select name="area_inm" id="area_inm" class="form-select" required>
                                <option value="Urbano">Urbano</option>
                                <option value="Rural">Rural</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarInmueble">
                        <i class="fas fa-plus"></i> <span>Registrar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/SistAlc/Frontend/estilos/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalInmueble');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget || {};
        const form = document.getElementById('inmuebleForm');
        const accion = document.getElementById('accionInmueble');
        const idInput = document.getElementById('id_inm');
        const dirInput = document.getElementById('dir_inm');
        const tipoSelect = document.getElementById('tip_inm');
        const areaSelect = document.getElementById('area_inm');
        const btn = document.getElementById('btnGuardarInmueble');

        if (button.dataset && button.dataset.id) {
            accion.value = 'modificar';
            idInput.value = button.dataset.id;
            dirInput.value = button.dataset.direccion || '';
            tipoSelect.value = button.dataset.tipo || 'Terreno';
            areaSelect.value = button.dataset.area || 'Urbano';
            btn.querySelector('i').className = 'fas fa-save';
            btn.querySelector('span').textContent = 'Guardar Cambios';
            modal.querySelector('.modal-title').textContent = 'Editar Inmueble';
        } else {
            form.reset();
            accion.value = 'registrar';
            idInput.value = '';
            btn.querySelector('i').className = 'fas fa-plus';
            btn.querySelector('span').textContent = 'Registrar';
            modal.querySelector('.modal-title').textContent = 'Registrar Inmueble';
        }
    });
});
</script>
