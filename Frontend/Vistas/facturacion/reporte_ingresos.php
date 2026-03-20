<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('facturacion');

require_once '../../../Backend/clases/Factura.php';
require_once '../../../Backend/clases/DetalleImpuesto.php';
require_once '../../../Backend/clases/TipoImpuesto.php';
require_once '../../../Backend/clases/Contribuyente.php';
require_once '../../../Backend/clases/CedulaCatastral.php';
require_once '../../../Backend/clases/SolvenciaMunicipal.php';
require_once '../../../Backend/clases/licencia.php';
require_once '../../../Backend/clases/SolvenciaComercial.php';
require_once '../../../Backend/clases/Patente.php';
require_once '../../../Backend/clases/ReporteFinanciero.php';

$facturaModel = new Factura();
$detalleModel = new DetalleImpuesto();
$tipoModel = new TipoImpuesto();
$contribuyenteModel = new Contribuyente();
$cedulaModel = new CedulaCatastral();
$solMunModel = new SolvenciaMunicipal();
$licenciaModel = new Licencia();
$solComModel = new SolvenciaComercial();
$patenteModel = new Patente();
$reporteFinanciero = new ReporteFinanciero();

$hoy = date('Y-m-d');
$resumenIngresosDia = $reporteFinanciero->resumenDiarioContribuyentes($hoy);
$resumenRubrosDia = $reporteFinanciero->resumenDiarioRubros($hoy);
$indicadoresFinancieros = $reporteFinanciero->indicadoresDashboard((int)date('Y'));
$rubrosMesActual = $reporteFinanciero->distribucionRubrosMensual((int)date('Y'), (int)date('n'));
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/facturacion/reporte_ingresos.css">
<?php include '../layouts/topbar.php'; ?>

<div class="container-fluid alc-page reporte-ingresos-page">
    <section class="alc-page-header">
        <h1 class="alc-page-title">Reporte de ingresos</h1>
        <p class="alc-page-subtitle">Monitorea movimientos diarios y consolidado mensual por rubro e indicadores netos.</p>
    </section>

    <section class="card alc-panel mb-4">
        <div class="card-header d-flex flex-column flex-lg-row gap-2 align-items-lg-center justify-content-lg-between">
            <span>Reporte diario de ingresos y rubros</span>
            <div class="d-flex gap-2 flex-column flex-sm-row">
                <input type="date" class="form-control form-control-sm" id="fechaReporteDiario" value="<?= $hoy; ?>">
                <button class="btn btn-sm btn-outline-primary" id="btnRefrescarReporteDiario"><i class="fas fa-sync"></i> Actualizar</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Movimientos por contribuyente</h5>
                        <div class="text-end d-flex gap-2 flex-wrap justify-content-end">
                            <span class="badge status-chip status-p" id="totalPagadoDia">+0,00</span>
                            <span class="badge status-chip status-v" id="totalAnuladoDia">-0,00</span>
                            <span class="badge status-chip status-s" id="totalNetoDia">0,00</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped" id="tablaIngresosDia">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Contribuyente</th>
                                    <th>Factura</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Resumen por rubros</h5>
                        <div class="text-end d-flex gap-2 flex-wrap justify-content-end">
                            <span class="badge status-chip status-p" id="totalRubrosPagado">+0,00</span>
                            <span class="badge status-chip status-v" id="totalRubrosAnulado">-0,00</span>
                            <span class="badge status-chip status-s" id="totalRubrosNeto">0,00</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped" id="tablaRubrosDia">
                            <thead>
                                <tr>
                                    <th>Rubro</th>
                                    <th class="text-end">Pagado</th>
                                    <th class="text-end">Anulado</th>
                                    <th class="text-end">Neto</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card alc-panel mb-4">
        <div class="card-header d-flex flex-column flex-lg-row gap-2 align-items-lg-center justify-content-lg-between">
            <span>Resumen mensual por rubros</span>
            <div class="d-flex gap-2 flex-column flex-sm-row align-items-sm-center">
                <select id="anioReporteMensual" class="form-select form-select-sm w-auto">
                    <?php for ($anio = date('Y'); $anio >= date('Y') - 3; $anio--): ?>
                        <option value="<?= $anio; ?>"><?= $anio; ?></option>
                    <?php endfor; ?>
                </select>
                <select id="mesReporteMensual" class="form-select form-select-sm w-auto">
                    <?php
                    $nombresMes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    for ($mes = 1; $mes <= 12; $mes++):
                        $nombreMes = $nombresMes[$mes - 1];
                    ?>
                        <option value="<?= $mes; ?>" <?= ($mes == (int)date('n')) ? 'selected' : ''; ?>>
                            <?= $nombreMes; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <h5 class="mb-3">Totales por rubro</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="tablaRubrosMensuales">
                            <thead>
                                <tr>
                                    <th>Rubro</th>
                                    <th class="text-end">Monto (Bs)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <h5 class="mb-3">Flujo mensual (Bs)</h5>
                    <ul class="list-group small" id="listaIngresosMensuales"></ul>
                </div>
            </div>
        </div>
    </section>

</div>
<script>
window.catalogosFacturacion = {
    reportes: {
        diario: <?= json_encode($resumenIngresosDia, JSON_UNESCAPED_UNICODE); ?>,
        rubrosDiario: <?= json_encode($resumenRubrosDia, JSON_UNESCAPED_UNICODE); ?>,
        ingresosMensuales: <?= json_encode($indicadoresFinancieros['ingresosMensuales'] ?? [], JSON_UNESCAPED_UNICODE); ?>,
        rubrosMensuales: <?= json_encode($rubrosMesActual, JSON_UNESCAPED_UNICODE); ?>,
        indicadores: <?= json_encode($indicadoresFinancieros, JSON_UNESCAPED_UNICODE); ?>
    }
};
</script>

<script src="/SistAlc/Frontend/estilos/js/bootstrap.bundle.min.js"></script>
<script src="../js/facturacion/reportesFacturacion.js"></script>
