<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/sidebar.php';
restringirAcceso('Administrador');

require_once '../../../Backend/clases/SolvenciaMunicipal.php';
require_once '../../../Backend/clases/Contribuyente.php';
require_once '../../../Backend/clases/CedulaCatastral.php';

$solvenciaModel = new SolvenciaMunicipal();
$contribuyenteModel = new Contribuyente();
$cedulaModel = new CedulaCatastral();

$solvencias = $solvenciaModel->consultar_todas();
$contribuyentes = $contribuyenteModel->consultar_activos();
$cedulas = $cedulaModel->consultar_activas();

$mapContribuyentes = [];
foreach ($contribuyentes as $item) { $mapContribuyentes[$item['id_con']] = $item['nom_con']; }
$mapCedulas = [];
foreach ($cedulas as $item) { $mapCedulas[$item['id_ced']] = $item['num_ced']; }
?>
<link rel="stylesheet" href="../../estilos/css/bootstrap.min.css">
<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Solvencias municipales</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalSolvenciaMunicipal">
        <i class="fas fa-plus"></i> Nueva solvencia
    </button>

    <div class="card">
        <div class="card-header">Listado</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Numero</th>
                        <th>Contribuyente</th>
                        <th>Cedula vinculada</th>
                        <th>Expedicion</th>
                        <th>Vigencia</th>
                        <th>Estado tramite</th>
                        <th>Estado registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($solvencias as $sol): ?>
                        <?php $labels = ['S' => 'Solicitada', 'P' => 'Pagada', 'V' => 'Vencida']; ?>
                        <tr>
                            <td><?= htmlspecialchars($sol['num_sol']); ?></td>
                            <td><?= htmlspecialchars($mapContribuyentes[$sol['fky_con']] ?? 'N/D'); ?></td>
                            <td><?= htmlspecialchars($mapCedulas[$sol['fky_ced']] ?? 'N/D'); ?></td>
                            <td><?= htmlspecialchars($sol['fec_exp_sol']); ?></td>
                            <td><?= htmlspecialchars($sol['vig_sol']); ?></td>
                            <td><span class="badge bg-info"><?= $labels[$sol['est_sol']] ?? 'Desconocido'; ?></span></td>
                            <td>
                                <span class="badge bg-<?= ($sol['est_registro'] === 'A') ? 'success' : 'secondary'; ?>">
                                    <?= ($sol['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalSolvenciaMunicipal"
                                        data-id="<?= htmlspecialchars($sol['id_sol']); ?>"
                                        data-num="<?= htmlspecialchars($sol['num_sol']); ?>"
                                        data-contribuyente="<?= htmlspecialchars($sol['fky_con']); ?>"
                                        data-cedula="<?= htmlspecialchars($sol['fky_ced']); ?>"
                                        data-solicitud="<?= htmlspecialchars($sol['sol_sol']); ?>"
                                        data-expedicion="<?= htmlspecialchars($sol['fec_exp_sol']); ?>"
                                        data-vigencia="<?= htmlspecialchars($sol['vig_sol']); ?>"
                                        data-estado="<?= htmlspecialchars($sol['est_sol']); ?>">
                                    Editar
                                </button>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-outline-success" href="../../../Backend/controlador/solvenciaMunicipalController.php?accion=actualizar_estado&id=<?= $sol['id_sol']; ?>&estado=P"><i class="fas fa-check"></i></a>
                                    <a class="btn btn-outline-secondary" href="../../../Backend/controlador/solvenciaMunicipalController.php?accion=actualizar_estado&id=<?= $sol['id_sol']; ?>&estado=S"><i class="fas fa-hourglass-half"></i></a>
                                    <a class="btn btn-outline-danger" href="../../../Backend/controlador/solvenciaMunicipalController.php?accion=actualizar_estado&id=<?= $sol['id_sol']; ?>&estado=V"><i class="fas fa-ban"></i></a>
                                </div>
                                <a class="btn btn-sm btn-<?= ($sol['est_registro'] === 'A') ? 'success' : 'secondary'; ?>" href="../../../Backend/controlador/solvenciaMunicipalController.php?accion=cambiar_estado&id=<?= $sol['id_sol']; ?>&nuevo_estado=<?= ($sol['est_registro'] === 'A') ? 'I' : 'A'; ?>">
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

<div class="modal fade" id="modalSolvenciaMunicipal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../../Backend/controlador/solvenciaMunicipalController.php" method="POST" id="solvenciaMunicipalForm">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Solvencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionSolvenciaMunicipal" value="registrar">
                    <input type="hidden" name="id_sol" id="id_sol" value="">
                    <div class="mb-3">
                        <label for="num_sol" class="form-label">Numero *</label>
                        <input type="text" name="num_sol" id="num_sol" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="fky_con" class="form-label">Contribuyente *</label>
                        <select name="fky_con" id="contribuyente_sol" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($contribuyentes as $con): ?>
                                <option value="<?= $con['id_con']; ?>"><?= htmlspecialchars($con['nom_con']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fky_ced" class="form-label">Cedula vinculada *</label>
                        <select name="fky_ced" id="cedula_sol" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($cedulas as $ced): ?>
                                <option value="<?= $ced['id_ced']; ?>"><?= htmlspecialchars($ced['num_ced']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="sol_sol" class="form-label">Solicitud *</label>
                        <textarea name="sol_sol" id="sol_sol" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fec_exp_sol" class="form-label">Fecha de expedicion *</label>
                        <input type="date" name="fec_exp_sol" id="fec_exp_sol" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="vig_sol" class="form-label">Vigencia *</label>
                        <input type="date" name="vig_sol" id="vig_sol" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="est_sol" class="form-label">Estado</label>
                        <select name="est_sol" id="est_sol" class="form-select">
                            <option value="S">Solicitada</option>
                            <option value="P">Pagada</option>
                            <option value="V">Vencida</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarSolvencia">
                        <i class="fas fa-plus"></i> <span>Registrar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../estilos/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalSolvenciaMunicipal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget || {};
        const form = document.getElementById('solvenciaMunicipalForm');
        const accion = document.getElementById('accionSolvenciaMunicipal');
        const idInput = document.getElementById('id_sol');
        const btn = document.getElementById('btnGuardarSolvencia');

        if (button.dataset && button.dataset.id) {
            accion.value = 'modificar';
            idInput.value = button.dataset.id;
            document.getElementById('num_sol').value = button.dataset.num || '';
            document.getElementById('contribuyente_sol').value = button.dataset.contribuyente || '';
            document.getElementById('cedula_sol').value = button.dataset.cedula || '';
            document.getElementById('sol_sol').value = button.dataset.solicitud || '';
            document.getElementById('fec_exp_sol').value = button.dataset.expedicion || '';
            document.getElementById('vig_sol').value = button.dataset.vigencia || '';
            document.getElementById('est_sol').value = button.dataset.estado || 'S';
            btn.querySelector('i').className = 'fas fa-save';
            btn.querySelector('span').textContent = 'Guardar Cambios';
            modal.querySelector('.modal-title').textContent = 'Editar Solvencia';
        } else {
            form.reset();
            accion.value = 'registrar';
            idInput.value = '';
            document.getElementById('est_sol').value = 'S';
            btn.querySelector('i').className = 'fas fa-plus';
            btn.querySelector('span').textContent = 'Registrar';
            modal.querySelector('.modal-title').textContent = 'Registrar Solvencia';
        }
    });
});
</script>
