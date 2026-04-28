<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('patentes_vehiculos');

require_once '../../../Backend/clases/PatenteVehiculoFiscal.php';
require_once '../../../Backend/clases/VehiculoFiscal.php';

$patenteVehiculoModel = new PatenteVehiculoFiscal();
$vehiculoModel = new VehiculoFiscal();

$patentesVehiculares = $patenteVehiculoModel->consultar_todas();
$vehiculos = $vehiculoModel->consultar_activos();
$catalogoVehiculos = [];
foreach ($vehiculos as $veh) {
    $catalogoVehiculos[$veh['id_veh_fis']] = [
        'placa' => $veh['pla_veh'] ?? '',
        'vehiculo' => trim(($veh['mar_veh'] ?? '') . ' ' . ($veh['mod_veh'] ?? '')),
        'contribuyente' => $veh['nom_con'] ?? '',
        'rif' => $veh['rif_con'] ?? '',
        'tipo' => $veh['tip_veh'] ?? '',
        'ano' => $veh['ano_veh'] ?? '',
    ];
}
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/vehiculos/vehiculos.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?php include '../layouts/topbar.php'; ?>

<main class="container-fluid alc-page patentes-vehiculos-page">
    <section class="alc-page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div>
            <h1 class="alc-page-title">Patentes de vehiculos fiscales</h1>
            <p class="alc-page-subtitle">Control anual de emision, vigencia y estado de las patentes asociadas a vehiculos fiscales.</p>
        </div>
        <div class="alc-page-tools">
            <a href="<?= BASE_URL ?>/Vistas/vehiculos/gestion_vehiculos.php" class="btn btn-outline-secondary">
                <i class="fas fa-car-side me-2"></i>Ver vehiculos
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPatenteVehiculo">
                <i class="fas fa-file-circle-plus me-2"></i>Registrar patente vehicular
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

    <section class="card alc-panel mb-3">
        <div class="card-body">
            <label for="buscadorPatenteVehiculo" class="form-label fw-semibold mb-2">Busqueda rapida</label>
            <input type="text" id="buscadorPatenteVehiculo" class="form-control" placeholder="Buscar por numero, placa, contribuyente o estado...">
        </div>
    </section>

    <section class="card alc-panel">
        <div class="card-header">Listado de patentes vehiculares</div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle" id="tablaPatentesVehiculos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Numero</th>
                        <th>Vehiculo</th>
                        <th>Contribuyente</th>
                        <th>Emision</th>
                        <th>Vencimiento</th>
                        <th>Monto</th>
                        <th>Estado tramite</th>
                        <th>Estado registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patentesVehiculares as $pat): ?>
                        <tr>
                            <td><?= (int)$pat['id_pat_vf']; ?></td>
                            <td><?= htmlspecialchars($pat['num_pat_vf']); ?></td>
                            <td>
                                <div><?= htmlspecialchars($pat['pla_veh']); ?></div>
                                <small class="text-muted"><?= htmlspecialchars(trim(($pat['mar_veh'] ?? '') . ' ' . ($pat['mod_veh'] ?? '') . ' / ' . ($pat['ano_veh'] ?? ''))); ?></small>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($pat['nom_con']); ?></div>
                                <small class="text-muted"><?= htmlspecialchars($pat['rif_con']); ?></small>
                            </td>
                            <td><?= htmlspecialchars($pat['fec_emi_pat']); ?></td>
                            <td><?= htmlspecialchars($pat['fec_ven_pat']); ?></td>
                            <td>Bs. <?= number_format((float)$pat['mon_pat'], 2, ',', '.'); ?></td>
                            <td>
                                <span class="badge status-chip <?= ['e' => 'status-s', 'v' => 'status-v', 'a' => 'status-i'][$pat['est_pat']] ?? 'status-s'; ?>">
                                    <?= htmlspecialchars($pat['estado_patente']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge status-chip <?= $pat['est_registro'] === 'a' ? 'status-a' : 'status-i'; ?>">
                                    <?= htmlspecialchars($pat['estado_registro']); ?>
                                </span>
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalPatenteVehiculo"
                                    data-id="<?= (int)$pat['id_pat_vf']; ?>"
                                    data-numero="<?= htmlspecialchars($pat['num_pat_vf']); ?>"
                                    data-vehiculo="<?= (int)$pat['fky_veh_fis']; ?>"
                                    data-emision="<?= htmlspecialchars($pat['fec_emi_pat']); ?>"
                                    data-vencimiento="<?= htmlspecialchars($pat['fec_ven_pat']); ?>"
                                    data-monto="<?= htmlspecialchars($pat['mon_pat']); ?>"
                                    data-estado="<?= htmlspecialchars($pat['est_pat']); ?>">
                                    Editar
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                        Estado tramite
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item btn-actualizar-estado-patente-vehiculo" href="javascript:void(0);" data-url="../../../Backend/controlador/patenteVehiculoFiscalController.php?accion=actualizar_estado&id=<?= (int)$pat['id_pat_vf']; ?>&nuevo_estado=e" data-numero="<?= htmlspecialchars($pat['num_pat_vf']); ?>" data-estado="Emitida">Emitida</a></li>
                                        <li><a class="dropdown-item btn-actualizar-estado-patente-vehiculo" href="javascript:void(0);" data-url="../../../Backend/controlador/patenteVehiculoFiscalController.php?accion=actualizar_estado&id=<?= (int)$pat['id_pat_vf']; ?>&nuevo_estado=v" data-numero="<?= htmlspecialchars($pat['num_pat_vf']); ?>" data-estado="Vencida">Vencida</a></li>
                                        <li><a class="dropdown-item btn-actualizar-estado-patente-vehiculo" href="javascript:void(0);" data-url="../../../Backend/controlador/patenteVehiculoFiscalController.php?accion=actualizar_estado&id=<?= (int)$pat['id_pat_vf']; ?>&nuevo_estado=a" data-numero="<?= htmlspecialchars($pat['num_pat_vf']); ?>" data-estado="Anulada">Anulada</a></li>
                                    </ul>
                                </div>
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-sm btn-<?= $pat['est_registro'] === 'a' ? 'success' : 'secondary'; ?> btn-cambiar-estado-patente-vehiculo"
                                    data-url="../../../Backend/controlador/patenteVehiculoFiscalController.php?accion=cambiar_estado&id=<?= (int)$pat['id_pat_vf']; ?>&nuevo_estado=<?= $pat['est_registro'] === 'a' ? 'i' : 'a'; ?>"
                                    data-numero="<?= htmlspecialchars($pat['num_pat_vf']); ?>">
                                    <i class="fas fa-toggle-<?= $pat['est_registro'] === 'a' ? 'off' : 'on'; ?>"></i>
                                </a>
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-sm btn-danger btn-eliminar-patente-vehiculo"
                                    data-url="../../../Backend/controlador/patenteVehiculoFiscalController.php?accion=eliminar&id=<?= (int)$pat['id_pat_vf']; ?>"
                                    data-numero="<?= htmlspecialchars($pat['num_pat_vf']); ?>">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<div class="modal fade" id="modalPatenteVehiculo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="patenteVehiculoForm" action="../../../Backend/controlador/patenteVehiculoFiscalController.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar patente vehicular</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="formAccionPatenteVehiculo" value="registrar">
                    <input type="hidden" name="id_pat_vf" id="formIdPatenteVehiculo" value="">

                    <div class="mb-3">
                        <label for="num_pat_vf" class="form-label">Numero de patente *</label>
                        <input type="text" name="num_pat_vf" id="num_pat_vf" class="form-control" maxlength="20" required>
                    </div>

                    <div class="mb-3">
                        <label for="buscarVehiculoPatente" class="form-label">Buscar vehiculo</label>
                        <input type="text" id="buscarVehiculoPatente" class="form-control" placeholder="Filtrar por placa o contribuyente...">
                    </div>

                    <div class="mb-3">
                        <label for="fky_veh_fis" class="form-label">Vehiculo fiscal *</label>
                        <select name="fky_veh_fis" id="fky_veh_fis" class="form-select" required>
                            <option value="">Seleccione un vehiculo</option>
                            <?php foreach ($vehiculos as $veh): ?>
                                <option value="<?= (int)$veh['id_veh_fis']; ?>">
                                    <?= htmlspecialchars(($veh['pla_veh'] ?? '') . ' - ' . ($veh['nom_con'] ?? '') . ' - ' . trim(($veh['mar_veh'] ?? '') . ' ' . ($veh['mod_veh'] ?? ''))); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text text-danger d-none" id="sinCoincidenciasVehiculoPatente">No se encontraron vehiculos con ese criterio.</div>
                    </div>

                    <div class="vehiculo-resumen" id="resumenVehiculoPatente">
                        Selecciona un vehiculo para ver un resumen rapido del propietario y la unidad.
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="fec_emi_pat" class="form-label">Fecha de emision *</label>
                            <input type="date" name="fec_emi_pat" id="fec_emi_pat" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fec_ven_pat" class="form-label">Fecha de vencimiento *</label>
                            <input type="date" name="fec_ven_pat" id="fec_ven_pat" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mon_pat" class="form-label">Monto *</label>
                            <input type="number" name="mon_pat" id="mon_pat" class="form-control" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="est_pat_vf" class="form-label">Estado del tramite *</label>
                            <select name="est_pat" id="est_pat_vf" class="form-select" required>
                                <option value="e">Emitida</option>
                                <option value="v">Vencida</option>
                                <option value="a">Anulada</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitPatenteVehiculo">
                        <i class="fas fa-plus"></i> <span>Registrar patente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/SistAlc/Frontend/estilos/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.catalogosVehiculosPatentes = <?= json_encode($catalogoVehiculos, JSON_UNESCAPED_UNICODE); ?>;
</script>
<script src="../js/vehiculos/modalVehiculo.js"></script>
<script src="../js/vehiculos/alertVehiculo.js"></script>
<script>
    $(function() {
        const table = $('#tablaPatentesVehiculos').DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            language: {
                paginate: { first: 'Primero', last: 'Ultimo', next: 'Siguiente', previous: 'Anterior' },
                info: 'Mostrando _START_ a _END_ de _TOTAL_ patentes vehiculares',
                infoEmpty: 'Mostrando 0 a 0 de 0 patentes vehiculares',
                infoFiltered: '(filtrado de _MAX_ patentes vehiculares totales)',
                zeroRecords: 'No se encontraron patentes vehiculares',
                search: ''
            },
            dom: 'rt<"d-flex justify-content-between align-items-center"ip>'
        });

        $('#buscadorPatenteVehiculo').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
