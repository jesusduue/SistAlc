<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/topbar.php';
restringirAcceso('linderos');

require_once '../../../Backend/clases/Linderos.php';
$linderosModel = new Linderos();
$linderos = $linderosModel->consultar_todos();
?>
<link rel="stylesheet" href="/SistAlc/Frontend/estilos/css/bootstrap.min.css">
<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Linderos</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalLinderos">
        <i class="fas fa-plus"></i> Nuevo registro de linderos
    </button>

    <div class="card">
        <div class="card-header">Listado</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Norte</th>
                        <th>Sur</th>
                        <th>Este</th>
                        <th>Oeste</th>
                        <th>Metros totales</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($linderos as $lin): ?>
                        <tr>
                            <td><?= htmlspecialchars($lin['id_lin']); ?></td>
                            <td><?= htmlspecialchars($lin['lin_norte']); ?> (<?= htmlspecialchars($lin['mt2_norte']); ?> m2)</td>
                            <td><?= htmlspecialchars($lin['lin_sur']); ?> (<?= htmlspecialchars($lin['mt2_sur']); ?> m2)</td>
                            <td><?= htmlspecialchars($lin['lin_este']); ?> (<?= htmlspecialchars($lin['mt2_este']); ?> m2)</td>
                            <td><?= htmlspecialchars($lin['lin_oeste']); ?> (<?= htmlspecialchars($lin['mt2_oeste']); ?> m2)</td>
                            <td><?= htmlspecialchars($lin['mt2_total']); ?></td>
                            <td>
                                <span class="badge bg-<?= ($lin['est_registro'] === 'A') ? 'success' : 'secondary'; ?>">
                                    <?= ($lin['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalLinderos"
                                        data-id="<?= htmlspecialchars($lin['id_lin']); ?>"
                                        data-lin_norte="<?= htmlspecialchars($lin['lin_norte']); ?>"
                                        data-lin_sur="<?= htmlspecialchars($lin['lin_sur']); ?>"
                                        data-lin_este="<?= htmlspecialchars($lin['lin_este']); ?>"
                                        data-lin_oeste="<?= htmlspecialchars($lin['lin_oeste']); ?>"
                                        data-mt2_norte="<?= htmlspecialchars($lin['mt2_norte']); ?>"
                                        data-mt2_sur="<?= htmlspecialchars($lin['mt2_sur']); ?>"
                                        data-mt2_este="<?= htmlspecialchars($lin['mt2_este']); ?>"
                                        data-mt2_oeste="<?= htmlspecialchars($lin['mt2_oeste']); ?>"
                                        data-mt2_total="<?= htmlspecialchars($lin['mt2_total']); ?>">
                                    Editar
                                </button>
                                <a class="btn btn-sm btn-<?= ($lin['est_registro'] === 'A') ? 'success' : 'secondary'; ?>"
                                   href="../../../Backend/controlador/linderosController.php?accion=cambiar_estado&id=<?= $lin['id_lin']; ?>&nuevo_estado=<?= ($lin['est_registro'] === 'A') ? 'I' : 'A'; ?>">
                                    <i class="fas fa-toggle-<?= ($lin['est_registro'] === 'A') ? 'off' : 'on'; ?>"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLinderos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="../../../Backend/controlador/linderosController.php" method="POST" id="linderosForm">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Linderos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionLinderos" value="registrar">
                    <input type="hidden" name="id_lin" id="id_lin" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Norte *</label>
                            <input type="text" name="lin_norte" id="lin_norte" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Metros norte *</label>
                            <input type="number" step="0.01" name="mt2_norte" id="mt2_norte" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sur *</label>
                            <input type="text" name="lin_sur" id="lin_sur" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Metros sur *</label>
                            <input type="number" step="0.01" name="mt2_sur" id="mt2_sur" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Este *</label>
                            <input type="text" name="lin_este" id="lin_este" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Metros este *</label>
                            <input type="number" step="0.01" name="mt2_este" id="mt2_este" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Oeste *</label>
                            <input type="text" name="lin_oeste" id="lin_oeste" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Metros oeste *</label>
                            <input type="number" step="0.01" name="mt2_oeste" id="mt2_oeste" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Metros totales *</label>
                            <input type="number" step="0.01" name="mt2_total" id="mt2_total" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarLinderos">
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
    const modal = document.getElementById('modalLinderos');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget || {};
        const form = document.getElementById('linderosForm');
        const accion = document.getElementById('accionLinderos');
        const idInput = document.getElementById('id_lin');
        const campos = ['lin_norte','lin_sur','lin_este','lin_oeste','mt2_norte','mt2_sur','mt2_este','mt2_oeste','mt2_total'];
        const btn = document.getElementById('btnGuardarLinderos');

        if (button.dataset && button.dataset.id) {
            accion.value = 'modificar';
            idInput.value = button.dataset.id;
            campos.forEach(campo => {
                const elemento = document.getElementById(campo);
                elemento.value = button.dataset[campo] || '';
            });
            btn.querySelector('i').className = 'fas fa-save';
            btn.querySelector('span').textContent = 'Guardar Cambios';
            modal.querySelector('.modal-title').textContent = 'Editar Linderos';
        } else {
            form.reset();
            accion.value = 'registrar';
            idInput.value = '';
            btn.querySelector('i').className = 'fas fa-plus';
            btn.querySelector('span').textContent = 'Registrar';
            modal.querySelector('.modal-title').textContent = 'Registrar Linderos';
        }
    });
});
</script>
