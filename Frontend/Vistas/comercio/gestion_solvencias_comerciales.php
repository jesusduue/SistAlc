<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/topbar.php';
restringirAcceso('solvencias_comerciales');

require_once '../../../Backend/clases/SolvenciaComercial.php';
require_once '../../../Backend/clases/Patente.php';

$solvenciaModel = new SolvenciaComercial();
$patenteModel = new Patente();

$solvencias = $solvenciaModel->consultar_todas();
$patentes = $patenteModel->consultar_todas();
$mapPatentes = [];
foreach ($patentes as $pat) { $mapPatentes[$pat['id_pat']] = $pat['num_pat'] . ' - ' . $pat['nom_pat']; }
?>
<link rel="stylesheet" href="/SistAlc/Frontend/estilos/css/bootstrap.min.css">
<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Solvencias comerciales</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalSolvenciaComercial">
        <i class="fas fa-plus"></i> Nueva solvencia comercial
    </button>

    <div class="card">
        <div class="card-header">Listado</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Numero</th>
                        <th>Patente</th>
                        <th>Expedicion</th>
                        <th>Vigencia</th>
                        <th>Estado tramite</th>
                        <th>Estado registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $labels = ['S' => 'Solicitada', 'P' => 'Pagada', 'V' => 'Vencida']; ?>
                    <?php foreach ($solvencias as $sol): ?>
                        <tr>
                            <td><?= htmlspecialchars($sol['num_sol_com']); ?></td>
                            <td><?= htmlspecialchars($mapPatentes[$sol['fky_pat']] ?? 'N/D'); ?></td>
                            <td><?= htmlspecialchars($sol['fec_exp_sol_com']); ?></td>
                            <td><?= htmlspecialchars($sol['vig_sol_com']); ?></td>
                            <td><span class="badge bg-info"><?= $labels[$sol['est_sol_com']] ?? 'Desconocido'; ?></span></td>
                            <td>
                                <span class="badge bg-<?= ($sol['est_registro'] === 'A') ? 'success' : 'secondary'; ?>">
                                    <?= ($sol['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSolvenciaComercial"
                                        data-id="<?= htmlspecialchars($sol['id_sol_com']); ?>"
                                        data-num="<?= htmlspecialchars($sol['num_sol_com']); ?>"
                                        data-patente="<?= htmlspecialchars($sol['fky_pat']); ?>"
                                        data-expedicion="<?= htmlspecialchars($sol['fec_exp_sol_com']); ?>"
                                        data-vigencia="<?= htmlspecialchars($sol['vig_sol_com']); ?>"
                                        data-estado="<?= htmlspecialchars($sol['est_sol_com']); ?>">
                                    Editar
                                </button>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-outline-success" href="../../../Backend/controlador/solvenciaComercialController.php?accion=actualizar_estado&id=<?= $sol['id_sol_com']; ?>&estado=P"><i class="fas fa-check"></i></a>
                                    <a class="btn btn-outline-secondary" href="../../../Backend/controlador/solvenciaComercialController.php?accion=actualizar_estado&id=<?= $sol['id_sol_com']; ?>&estado=S"><i class="fas fa-hourglass-half"></i></a>
                                    <a class="btn btn-outline-danger" href="../../../Backend/controlador/solvenciaComercialController.php?accion=actualizar_estado&id=<?= $sol['id_sol_com']; ?>&estado=V"><i class="fas fa-ban"></i></a>
                                </div>
                                <a class="btn btn-sm btn-<?= ($sol['est_registro'] === 'A') ? 'success' : 'secondary'; ?>" href="../../../Backend/controlador/solvenciaComercialController.php?accion=cambiar_estado&id=<?= $sol['id_sol_com']; ?>&nuevo_estado=<?= ($sol['est_registro'] === 'A') ? 'I' : 'A'; ?>">
                                    <i class="fas fa-toggle-<?= ($sol['est_registro'] === 'A') ? 'off' : 'on'; ?>"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSolvenciaComercial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../../Backend/controlador/solvenciaComercialController.php" method="POST" id="solvenciaComercialForm">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Solvencia Comercial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionSolvenciaComercial" value="registrar">
                    <input type="hidden" name="id_sol_com" id="id_sol_com" value="">
                    <div class="mb-3">
                        <label for="num_sol_com" class="form-label">Numero *</label>
                        <input type="text" name="num_sol_com" id="num_sol_com" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="fky_pat" class="form-label">Patente *</label>
                        <select name="fky_pat" id="patente_sol_com" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($patentes as $pat): ?>
                                <option value="<?= $pat['id_pat']; ?>"><?= htmlspecialchars($pat['num_pat'] . ' - ' . $pat['nom_pat']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fec_exp_sol_com" class="form-label">Fecha de expedicion *</label>
                        <input type="date" name="fec_exp_sol_com" id="fec_exp_sol_com" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="vig_sol_com" class="form-label">Vigencia *</label>
                        <input type="date" name="vig_sol_com" id="vig_sol_com" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="est_sol_com" class="form-label">Estado</label>
                        <select name="est_sol_com" id="est_sol_com" class="form-select">
                            <option value="S">Solicitada</option>
                            <option value="P">Pagada</option>
                            <option value="V">Vencida</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarSolvenciaComercial">
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
    const modal = document.getElementById('modalSolvenciaComercial');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget || {};
        const form = document.getElementById('solvenciaComercialForm');
        const accion = document.getElementById('accionSolvenciaComercial');
        const idInput = document.getElementById('id_sol_com');
        const btn = document.getElementById('btnGuardarSolvenciaComercial');

        if (button.dataset && button.dataset.id) {
            accion.value = 'modificar';
            idInput.value = button.dataset.id;
            document.getElementById('num_sol_com').value = button.dataset.num || '';
            document.getElementById('patente_sol_com').value = button.dataset.patente || '';
            document.getElementById('fec_exp_sol_com').value = button.dataset.expedicion || '';
            document.getElementById('vig_sol_com').value = button.dataset.vigencia || '';
            document.getElementById('est_sol_com').value = button.dataset.estado || 'S';
            btn.querySelector('i').className = 'fas fa-save';
            btn.querySelector('span').textContent = 'Guardar Cambios';
            modal.querySelector('.modal-title').textContent = 'Editar Solvencia Comercial';
        } else {
            form.reset();
            accion.value = 'registrar';
            idInput.value = '';
            document.getElementById('est_sol_com').value = 'S';
            btn.querySelector('i').className = 'fas fa-plus';
            btn.querySelector('span').textContent = 'Registrar';
            modal.querySelector('.modal-title').textContent = 'Registrar Solvencia Comercial';
        }
    });
});
</script>
