<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/sidebar.php';
restringirAcceso('cedulas');

require_once '../../../Backend/clases/CedulaCatastral.php';
require_once '../../../Backend/clases/Contribuyente.php';
require_once '../../../Backend/clases/Inmueble.php';
require_once '../../../Backend/clases/Linderos.php';
require_once '../../../Backend/clases/DocumentoLegal.php';

$cedulaModel = new CedulaCatastral();
$contribuyenteModel = new Contribuyente();
$inmuebleModel = new Inmueble();
$linderosModel = new Linderos();
$documentoModel = new DocumentoLegal();

$cedulas = $cedulaModel->consultar_todas();
$contribuyentes = $contribuyenteModel->consultar_activos();
$inmuebles = $inmuebleModel->consultar_activos();
$linderos = $linderosModel->consultar_activos();
$documentos = $documentoModel->consultar_activos();

$mapContribuyentes = [];
foreach ($contribuyentes as $item) { $mapContribuyentes[$item['id_con']] = $item['nom_con']; }
$mapInmuebles = [];
foreach ($inmuebles as $item) { $mapInmuebles[$item['id_inm']] = $item['dir_inm']; }
$mapLinderos = [];
foreach ($linderos as $item) { $mapLinderos[$item['id_lin']] = $item['lin_norte']; }
$mapDocumentos = [];
foreach ($documentos as $item) { $mapDocumentos[$item['id_doc']] = $item['num_doc']; }
?>
<link rel="stylesheet" href="../../estilos/css/bootstrap.min.css">
<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Cedulas catastrales</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCedula">
        <i class="fas fa-plus"></i> Nueva cedula
    </button>

    <div class="card">
        <div class="card-header">Listado</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Numero</th>
                        <th>Contribuyente</th>
                        <th>Inmueble</th>
                        <th>Valor</th>
                        <th>Vigencia</th>
                        <th>Estado tramite</th>
                        <th>Estado registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cedulas as $ced): ?>
                        <tr>
                            <td><?= htmlspecialchars($ced['num_ced']); ?></td>
                            <td><?= htmlspecialchars($mapContribuyentes[$ced['fky_con']] ?? 'N/D'); ?></td>
                            <td><?= htmlspecialchars($mapInmuebles[$ced['fky_inm']] ?? 'N/D'); ?></td>
                            <td><?= number_format((float)$ced['val_ced'], 2, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($ced['vig_ced']); ?></td>
                            <td>
                                <?php $labels = ['S' => 'Solicitada', 'P' => 'Pagada', 'V' => 'Vencida']; ?>
                                <span class="badge bg-info">
                                    <?= $labels[$ced['est_ced']] ?? 'Desconocido'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($ced['est_registro'] === 'A') ? 'success' : 'secondary'; ?>">
                                    <?= ($ced['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalCedula"
                                        data-id="<?= htmlspecialchars($ced['id_ced']); ?>"
                                        data-num="<?= htmlspecialchars($ced['num_ced']); ?>"
                                        data-exp="<?= htmlspecialchars($ced['num_exp']); ?>"
                                        data-contribuyente="<?= htmlspecialchars($ced['fky_con']); ?>"
                                        data-inmueble="<?= htmlspecialchars($ced['fky_inm']); ?>"
                                        data-linderos="<?= htmlspecialchars($ced['fky_lin']); ?>"
                                        data-documento="<?= htmlspecialchars($ced['fky_doc']); ?>"
                                        data-valor="<?= htmlspecialchars($ced['val_ced']); ?>"
                                        data-expedicion="<?= htmlspecialchars($ced['fec_exp_ced']); ?>"
                                        data-vigencia="<?= htmlspecialchars($ced['vig_ced']); ?>"
                                        data-solicitud="<?= htmlspecialchars($ced['sol_ced']); ?>"
                                        data-estado="<?= htmlspecialchars($ced['est_ced']); ?>">
                                    Editar
                                </button>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-outline-success" href="../../../Backend/controlador/cedulaCatastralController.php?accion=actualizar_estado&id=<?= $ced['id_ced']; ?>&estado=P"><i class="fas fa-check"></i></a>
                                    <a class="btn btn-outline-secondary" href="../../../Backend/controlador/cedulaCatastralController.php?accion=actualizar_estado&id=<?= $ced['id_ced']; ?>&estado=S"><i class="fas fa-hourglass-half"></i></a>
                                    <a class="btn btn-outline-danger" href="../../../Backend/controlador/cedulaCatastralController.php?accion=actualizar_estado&id=<?= $ced['id_ced']; ?>&estado=V"><i class="fas fa-ban"></i></a>
                                </div>
                                <a class="btn btn-sm btn-<?= ($ced['est_registro'] === 'A') ? 'success' : 'secondary'; ?>" href="../../../Backend/controlador/cedulaCatastralController.php?accion=cambiar_estado&id=<?= $ced['id_ced']; ?>&nuevo_estado=<?= ($ced['est_registro'] === 'A') ? 'I' : 'A'; ?>">
                                    <i class="fas fa-toggle-<?= ($ced['est_registro'] === 'A') ? 'off' : 'on'; ?>"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCedula" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="../../../Backend/controlador/cedulaCatastralController.php" method="POST" id="cedulaForm">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Cedula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionCedula" value="registrar">
                    <input type="hidden" name="id_ced" id="id_ced" value="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="num_ced" class="form-label">Numero *</label>
                            <input type="text" name="num_ced" id="num_ced" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="num_exp" class="form-label">Expediente *</label>
                            <input type="text" name="num_exp" id="num_exp" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="fky_con" class="form-label">Contribuyente *</label>
                            <select name="fky_con" id="fky_con" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($contribuyentes as $con): ?>
                                    <option value="<?= $con['id_con']; ?>"><?= htmlspecialchars($con['nom_con']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fky_inm" class="form-label">Inmueble *</label>
                            <select name="fky_inm" id="fky_inm" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($inmuebles as $inm): ?>
                                    <option value="<?= $inm['id_inm']; ?>"><?= htmlspecialchars($inm['dir_inm']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fky_lin" class="form-label">Linderos *</label>
                            <select name="fky_lin" id="fky_lin" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($linderos as $lin): ?>
                                    <option value="<?= $lin['id_lin']; ?>"><?= htmlspecialchars($lin['lin_norte']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fky_doc" class="form-label">Documento *</label>
                            <select name="fky_doc" id="fky_doc" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($documentos as $doc): ?>
                                    <option value="<?= $doc['id_doc']; ?>"><?= htmlspecialchars($doc['num_doc']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="val_ced" class="form-label">Valor (Bs) *</label>
                            <input type="number" step="0.01" name="val_ced" id="val_ced" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="fec_exp_ced" class="form-label">Fecha de expedicion *</label>
                            <input type="date" name="fec_exp_ced" id="fec_exp_ced" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="vig_ced" class="form-label">Vencimiento *</label>
                            <input type="date" name="vig_ced" id="vig_ced" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="sol_ced" class="form-label">Solicitud *</label>
                            <textarea name="sol_ced" id="sol_ced" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="est_ced" class="form-label">Estado</label>
                            <select name="est_ced" id="est_ced" class="form-select">
                                <option value="S">Solicitada</option>
                                <option value="P">Pagada</option>
                                <option value="V">Vencida</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarCedula">
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
    const modal = document.getElementById('modalCedula');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget || {};
        const form = document.getElementById('cedulaForm');
        const accion = document.getElementById('accionCedula');
        const idInput = document.getElementById('id_ced');
        const btn = document.getElementById('btnGuardarCedula');

        if (button.dataset && button.dataset.id) {
            accion.value = 'modificar';
            idInput.value = button.dataset.id;
            document.getElementById('num_ced').value = button.dataset.num || '';
            document.getElementById('num_exp').value = button.dataset.exp || '';
            document.getElementById('fky_con').value = button.dataset.contribuyente || '';
            document.getElementById('fky_inm').value = button.dataset.inmueble || '';
            document.getElementById('fky_lin').value = button.dataset.linderos || '';
            document.getElementById('fky_doc').value = button.dataset.documento || '';
            document.getElementById('val_ced').value = button.dataset.valor || '';
            document.getElementById('fec_exp_ced').value = button.dataset.expedicion || '';
            document.getElementById('vig_ced').value = button.dataset.vigencia || '';
            document.getElementById('sol_ced').value = button.dataset.solicitud || '';
            document.getElementById('est_ced').value = button.dataset.estado || 'S';
            btn.querySelector('i').className = 'fas fa-save';
            btn.querySelector('span').textContent = 'Guardar Cambios';
            modal.querySelector('.modal-title').textContent = 'Editar Cedula';
        } else {
            form.reset();
            accion.value = 'registrar';
            idInput.value = '';
            document.getElementById('est_ced').value = 'S';
            btn.querySelector('i').className = 'fas fa-plus';
            btn.querySelector('span').textContent = 'Registrar';
            modal.querySelector('.modal-title').textContent = 'Registrar Cedula';
        }
    });
});
</script>
