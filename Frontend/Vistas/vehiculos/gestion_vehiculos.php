<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('vehiculos');

require_once '../../../Backend/clases/VehiculoFiscal.php';
require_once '../../../Backend/clases/Contribuyente.php';

$vehiculoModel = new VehiculoFiscal();
$contribuyenteModel = new Contribuyente();

$vehiculos = $vehiculoModel->consultar_todos();
$contribuyentes = $contribuyenteModel->consultar_todos();
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/vehiculos/vehiculos.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?php include '../layouts/topbar.php'; ?>

<main class="container-fluid alc-page vehiculos-page">
    <section class="alc-page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div>
            <h1 class="alc-page-title">Gestion de vehiculos fiscales</h1>
            <p class="alc-page-subtitle">Registro de vehiculos, propietario tributario y condicion administrativa de cada unidad.</p>
        </div>
        <div class="alc-page-tools">
            <a href="<?= BASE_URL ?>/Vistas/vehiculos/gestion_patentes_vehiculos.php" class="btn btn-outline-secondary">
                <i class="fas fa-file-signature me-2"></i>Ver patentes vehiculares
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVehiculoFiscal">
                <i class="fas fa-car-side me-2"></i>Registrar vehiculo
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
            <label for="buscadorVehiculo" class="form-label fw-semibold mb-2">Busqueda rapida</label>
            <input type="text" id="buscadorVehiculo" class="form-control" placeholder="Buscar por placa, contribuyente, marca, modelo o tipo...">
        </div>
    </section>

    <section class="card alc-panel">
        <div class="card-header">Listado de vehiculos fiscales</div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle" id="tablaVehiculosFiscales">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Contribuyente</th>
                        <th>Vehiculo</th>
                        <th>Tipo</th>
                        <th>Ano</th>
                        <th>Condicion</th>
                        <th>Patentes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehiculos as $veh): ?>
                        <tr>
                            <td><?= (int)$veh['id_veh_fis']; ?></td>
                            <td><?= htmlspecialchars($veh['pla_veh']); ?></td>
                            <td>
                                <div><?= htmlspecialchars($veh['nom_con']); ?></div>
                                <small class="text-muted"><?= htmlspecialchars($veh['rif_con']); ?></small>
                            </td>
                            <td>
                                <div><?= htmlspecialchars(trim(($veh['mar_veh'] ?? '') . ' ' . ($veh['mod_veh'] ?? ''))); ?></div>
                                <small class="text-muted"><?= htmlspecialchars($veh['num_ser_veh'] ?: 'Sin serial'); ?></small>
                            </td>
                            <td><?= htmlspecialchars($veh['tip_veh']); ?></td>
                            <td><?= (int)$veh['ano_veh']; ?></td>
                            <td>
                                <span class="badge status-chip <?= ['p' => 'status-p', 't' => 'status-s', 'e' => 'status-v'][$veh['est_veh']] ?? 'status-s'; ?>">
                                    <?= htmlspecialchars($veh['estado_vehiculo']); ?>
                                </span>
                            </td>
                            <td><?= (int)($veh['total_patentes'] ?? 0); ?></td>
                            <td>
                                <span class="badge status-chip <?= $veh['est_registro'] === 'a' ? 'status-a' : 'status-i'; ?>">
                                    <?= htmlspecialchars($veh['estado_registro']); ?>
                                </span>
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalVehiculoFiscal"
                                    data-id="<?= (int)$veh['id_veh_fis']; ?>"
                                    data-contribuyente="<?= (int)$veh['fky_con']; ?>"
                                    data-placa="<?= htmlspecialchars($veh['pla_veh']); ?>"
                                    data-marca="<?= htmlspecialchars($veh['mar_veh']); ?>"
                                    data-modelo="<?= htmlspecialchars($veh['mod_veh']); ?>"
                                    data-ano="<?= (int)$veh['ano_veh']; ?>"
                                    data-color="<?= htmlspecialchars($veh['col_veh'] ?? ''); ?>"
                                    data-tipo="<?= htmlspecialchars($veh['tip_veh']); ?>"
                                    data-capacidad="<?= htmlspecialchars($veh['cap_car_veh'] ?? ''); ?>"
                                    data-serial="<?= htmlspecialchars($veh['num_ser_veh'] ?? ''); ?>"
                                    data-condicion="<?= htmlspecialchars($veh['est_veh']); ?>">
                                    Editar
                                </button>
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-sm btn-<?= $veh['est_registro'] === 'a' ? 'success' : 'secondary'; ?> btn-cambiar-estado-vehiculo"
                                    data-url="../../../Backend/controlador/vehiculoFiscalController.php?accion=cambiar_estado&id=<?= (int)$veh['id_veh_fis']; ?>&nuevo_estado=<?= $veh['est_registro'] === 'a' ? 'i' : 'a'; ?>"
                                    data-placa="<?= htmlspecialchars($veh['pla_veh']); ?>">
                                    <i class="fas fa-toggle-<?= $veh['est_registro'] === 'a' ? 'off' : 'on'; ?>"></i>
                                </a>
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-sm btn-danger btn-eliminar-vehiculo"
                                    data-url="../../../Backend/controlador/vehiculoFiscalController.php?accion=eliminar&id=<?= (int)$veh['id_veh_fis']; ?>"
                                    data-placa="<?= htmlspecialchars($veh['pla_veh']); ?>">
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

<div class="modal fade" id="modalVehiculoFiscal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="vehiculoFiscalForm" action="../../../Backend/controlador/vehiculoFiscalController.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar vehiculo fiscal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="formAccionVehiculo" value="registrar">
                    <input type="hidden" name="id_veh_fis" id="formIdVehiculo" value="">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pla_veh" class="form-label">Placa *</label>
                            <input type="text" name="pla_veh" id="pla_veh" class="form-control text-uppercase" maxlength="20" required>
                        </div>
                        <div class="col-md-6">
                            <label for="buscarContribuyenteVehiculo" class="form-label">Buscar contribuyente</label>
                            <input type="text" id="buscarContribuyenteVehiculo" class="form-control" placeholder="Filtrar contribuyentes...">
                        </div>
                        <div class="col-md-6">
                            <label for="fky_con_vehiculo" class="form-label">Contribuyente *</label>
                            <select name="fky_con" id="fky_con_vehiculo" class="form-select" required>
                                <option value="">Seleccione un contribuyente</option>
                                <?php foreach ($contribuyentes as $con): ?>
                                    <option value="<?= (int)$con['id_con']; ?>">
                                        <?= htmlspecialchars(($con['nom_con'] ?? '') . ' - ' . ($con['rif_con'] ?? '')); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-danger d-none" id="sinCoincidenciasContribuyenteVehiculo">No se encontraron contribuyentes con ese criterio.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="tip_veh" class="form-label">Tipo de vehiculo *</label>
                            <input type="text" name="tip_veh" id="tip_veh" class="form-control" list="tiposVehiculoLista" required>
                            <datalist id="tiposVehiculoLista">
                                <option value="Automovil particular"></option>
                                <option value="Motocicleta"></option>
                                <option value="Camioneta"></option>
                                <option value="Transporte publico"></option>
                                <option value="Camion"></option>
                                <option value="Gandola"></option>
                                <option value="Chuto"></option>
                            </datalist>
                        </div>
                        <div class="col-md-4">
                            <label for="mar_veh" class="form-label">Marca *</label>
                            <input type="text" name="mar_veh" id="mar_veh" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="mod_veh" class="form-label">Modelo *</label>
                            <input type="text" name="mod_veh" id="mod_veh" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="ano_veh" class="form-label">Ano *</label>
                            <input type="number" name="ano_veh" id="ano_veh" class="form-control" min="1950" max="<?= date('Y') + 1; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="col_veh" class="form-label">Color</label>
                            <input type="text" name="col_veh" id="col_veh" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="cap_car_veh" class="form-label">Capacidad de carga</label>
                            <input type="text" name="cap_car_veh" id="cap_car_veh" class="form-control" placeholder="Ej. 3T">
                        </div>
                        <div class="col-md-4">
                            <label for="num_ser_veh" class="form-label">Numero serial</label>
                            <input type="text" name="num_ser_veh" id="num_ser_veh" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="est_veh" class="form-label">Condicion administrativa *</label>
                            <select name="est_veh" id="est_veh" class="form-select" required>
                                <option value="p">Propio</option>
                                <option value="t">Traspaso</option>
                                <option value="e">En venta</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitVehiculo">
                        <i class="fas fa-plus"></i> <span>Registrar vehiculo</span>
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
<script src="../js/vehiculos/modalVehiculo.js"></script>
<script src="../js/vehiculos/alertVehiculo.js"></script>
<script>
    $(function() {
        const table = $('#tablaVehiculosFiscales').DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            language: {
                paginate: { first: 'Primero', last: 'Ultimo', next: 'Siguiente', previous: 'Anterior' },
                info: 'Mostrando _START_ a _END_ de _TOTAL_ vehiculos',
                infoEmpty: 'Mostrando 0 a 0 de 0 vehiculos',
                infoFiltered: '(filtrado de _MAX_ vehiculos totales)',
                zeroRecords: 'No se encontraron vehiculos',
                search: ''
            },
            dom: 'rt<"d-flex justify-content-between align-items-center"ip>'
        });

        $('#buscadorVehiculo').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
