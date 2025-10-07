<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/sidebar.php';
restringirAcceso('Administrador');

require_once '../../../Backend/clases/Factura.php';
require_once '../../../Backend/clases/DetalleImpuesto.php';
require_once '../../../Backend/clases/TipoImpuesto.php';
require_once '../../../Backend/clases/Contribuyente.php';
require_once '../../../Backend/clases/CedulaCatastral.php';
require_once '../../../Backend/clases/SolvenciaMunicipal.php';
require_once '../../../Backend/clases/licencia.php';
require_once '../../../Backend/clases/SolvenciaComercial.php';

$facturaModel = new Factura();
$detalleModel = new DetalleImpuesto();
$tipoModel = new TipoImpuesto();
$contribuyenteModel = new Contribuyente();
$cedulaModel = new CedulaCatastral();
$solMunModel = new SolvenciaMunicipal();
$licenciaModel = new Licencia();
$solComModel = new SolvenciaComercial();

$facturas = $facturaModel->consultar_todas();
$detallesPorFactura = [];
foreach ($facturas as $fac) {
    $detallesPorFactura[$fac['id_fac']] = $detalleModel->listar_por_factura((int)$fac['id_fac']);
}

$tiposImpuesto = $tipoModel->consultar_activos();
$contribuyentes = $contribuyenteModel->consultar_activos();
$cedulas = $cedulaModel->consultar_activas();
$solvMunicipales = $solMunModel->consultar_activas();
$licencias = $licenciaModel->consultar_activas();
$solvComerciales = $solComModel->consultar_activas();
?>
<link rel="stylesheet" href="../../estilos/css/bootstrap.min.css">
<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Gestion de Facturas</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Registrar / Editar Factura</div>
        <div class="card-body">
            <form id="facturaForm" action="../../../Backend/controlador/facturaController.php" method="POST">
                <input type="hidden" name="accion" id="formAccionFactura" value="registrar">
                <input type="hidden" name="id_fac" id="formIdFactura" value="">
                <input type="hidden" name="detalles" id="detallesFactura">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="num_fac" class="form-label">Numero *</label>
                        <input type="text" name="num_fac" id="num_fac" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="fec_fac" class="form-label">Fecha *</label>
                        <input type="datetime-local" name="fec_fac" id="fec_fac" class="form-control" value="<?= date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="fky_con" class="form-label">Contribuyente *</label>
                        <select name="fky_con" id="fky_con" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($contribuyentes as $con): ?>
                                <option value="<?= $con['id_con']; ?>"><?= htmlspecialchars($con['nom_con'] . ' - ' . $con['rif_con']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="des_fac" class="form-label">Descripcion</label>
                        <textarea name="des_fac" id="des_fac" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <hr>
                <h5>Detalle de Impuestos</h5>
                <div class="table-responsive">
                    <table class="table table-sm" id="tablaDetalle">
                        <thead>
                            <tr>
                                <th style="width: 30%">Tipo de Impuesto</th>
                                <th style="width: 15%">Monto</th>
                                <th style="width: 20%">Entidad Asociada</th>
                                <th style="width: 25%">Referencia</th>
                                <th style="width: 10%"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarDetalle">
                    <i class="fas fa-plus"></i> Agregar Detalle
                </button>

                <div class="row mt-3">
                    <div class="col-md-3 ms-auto">
                        <label for="tot_fac" class="form-label">Total (Bs)</label>
                        <input type="number" step="0.01" min="0" name="tot_fac" id="tot_fac" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="est_pago" class="form-label">Estado de pago</label>
                        <select name="est_pago" id="est_pago" class="form-select">
                            <option value="E">En espera</option>
                            <option value="P">Pagada</option>
                            <option value="A">Anulada</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="btnGuardarFactura">
                        <i class="fas fa-save"></i> <span>Registrar Factura</span>
                    </button>
                    <button type="button" class="btn btn-secondary" id="btnCancelarEdicion" hidden>Cancelar edicion</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Listado de Facturas</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Numero</th>
                        <th>Fecha</th>
                        <th>Descripcion</th>
                        <th>Total (Bs)</th>
                        <th>Pago</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facturas as $fac): ?>
                        <?php
                        $detalles = $detallesPorFactura[$fac['id_fac']] ?? [];
                        $detallesJson = htmlspecialchars(json_encode($detalles, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($fac['num_fac']); ?></td>
                            <td><?= htmlspecialchars($fac['fec_fac']); ?></td>
                            <td><?= htmlspecialchars($fac['des_fac']); ?></td>
                            <td><?= number_format((float)$fac['tot_fac'], 2, ',', '.'); ?></td>
                            <td>
                                <?php $labelsPago = ['E' => 'En espera', 'P' => 'Pagada', 'A' => 'Anulada']; ?>
                                <span class="badge bg-<?= ($fac['est_pago'] === 'P') ? 'success' : (($fac['est_pago'] === 'A') ? 'danger' : 'secondary'); ?>">
                                    <?= $labelsPago[$fac['est_pago']] ?? 'Desconocido'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($fac['est_registro'] === 'A') ? 'success' : 'secondary'; ?>">
                                    <?= ($fac['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-outline-info btn-detalles-factura" data-detalles="<?= $detallesJson; ?>">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning btn-editar-factura"
                                    data-id="<?= $fac['id_fac']; ?>"
                                    data-numero="<?= htmlspecialchars($fac['num_fac']); ?>"
                                    data-fecha="<?= date('Y-m-d\TH:i', strtotime($fac['fec_fac'])); ?>"
                                    data-contribuyente="<?= $fac['fky_con']; ?>"
                                    data-descripcion="<?= htmlspecialchars($fac['des_fac']); ?>"
                                    data-total="<?= $fac['tot_fac']; ?>"
                                    data-pago="<?= $fac['est_pago']; ?>"
                                    data-detalles="<?= $detallesJson; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-outline-success" href="../../../Backend/controlador/facturaController.php?accion=actualizar_pago&id=<?= $fac['id_fac']; ?>&estado=P"><i class="fas fa-check"></i></a>
                                    <a class="btn btn-outline-secondary" href="../../../Backend/controlador/facturaController.php?accion=actualizar_pago&id=<?= $fac['id_fac']; ?>&estado=E"><i class="fas fa-hourglass-half"></i></a>
                                    <a class="btn btn-outline-danger" href="../../../Backend/controlador/facturaController.php?accion=actualizar_pago&id=<?= $fac['id_fac']; ?>&estado=A"><i class="fas fa-ban"></i></a>
                                </div>
                                <a class="btn btn-sm btn-<?= ($fac['est_registro'] === 'A') ? 'success' : 'secondary'; ?>" href="../../../Backend/controlador/facturaController.php?accion=cambiar_estado&id=<?= $fac['id_fac']; ?>&nuevo_estado=<?= ($fac['est_registro'] === 'A') ? 'I' : 'A'; ?>">
                                    <i class="fas fa-toggle-<?= ($fac['est_registro'] === 'A') ? 'off' : 'on'; ?>"></i>
                                </a>
                                <a class="btn btn-sm btn-danger" href="../../../Backend/controlador/facturaController.php?accion=eliminar&id=<?= $fac['id_fac']; ?>"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalleFactura" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Entidad</th>
                            </tr>
                        </thead>
                        <tbody id="detalleFacturaCuerpo"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.catalogosFacturacion = {
    tipos: <?= json_encode($tiposImpuesto, JSON_UNESCAPED_UNICODE); ?>,
    cedulas: <?= json_encode($cedulas, JSON_UNESCAPED_UNICODE); ?>,
    solvenciasMunicipales: <?= json_encode($solvMunicipales, JSON_UNESCAPED_UNICODE); ?>,
    licencias: <?= json_encode($licencias, JSON_UNESCAPED_UNICODE); ?>,
    solvenciasComerciales: <?= json_encode($solvComerciales, JSON_UNESCAPED_UNICODE); ?>
};
</script>
<script src="../../estilos/js/bootstrap.bundle.min.js"></script>
<script src="../js/facturacion/factura.js"></script>
