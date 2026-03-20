<?php
include 'layouts/headerDashboard.php';

if (!isset($_SESSION['nom_usu']) || !isset($_SESSION['nom_rol'])) {
    header('Location: ../../index.php');
    exit;
}

$nombre_usuario = $_SESSION['nom_usu'];
$nombre_rol = $_SESSION['nom_rol'];

require_once 'layouts/paths.php';
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/dashboard/dashboard.css">
<?php include 'layouts/topbar.php'; ?>

<?php
require_once '../../Backend/clases/dashboard.php';
require_once '../../Backend/clases/ReporteFinanciero.php';

$dash = new Dashboard();
$reporteFinanciero = new ReporteFinanciero();

$anioActual = (int)date('Y');
$mesActual = (int)date('n');
$labelsMeses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

$indicadores = $reporteFinanciero->indicadoresDashboard($anioActual);
$ingresosMensuales = $indicadores['ingresosMensuales'] ?? array_fill(0, 12, 0);
$totalIngresosAnio = $indicadores['ingresosAnuales'] ?? 0.0;
$facturasPendientes = $indicadores['facturasPendientes'] ?? 0;
$vencimientosProx30 = $indicadores['vencimientos']['total'] ?? 0;
$totalAnuladoAnio = $indicadores['anuladoAnual'] ?? 0.0;

$rubrosMesData = $reporteFinanciero->distribucionRubrosMensual($anioActual, $mesActual);
$labelsTipos = [];
$valoresTipos = [];
if (!empty($rubrosMesData['rubros'])) {
    foreach ($rubrosMesData['rubros'] as $rubro) {
        $labelsTipos[] = $rubro['nombre'];
        $valoresTipos[] = $rubro['total'];
    }
}
if (!$labelsTipos) {
    $labelsTipos = ['Sin datos'];
    $valoresTipos = [0];
}

$actividadReciente = $dash->actividadesRecientes();
?>

<div class="container-fluid alc-page dashboard-page">
    <section class="alc-page-header dashboard-hero">
        <div>
            <h1 class="alc-page-title">Panel de gestion municipal</h1>
            <p class="alc-page-subtitle">Bienvenido, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong>. Rol activo: <strong><?php echo htmlspecialchars($nombre_rol); ?></strong>.</p>
        </div>
        <div class="dashboard-meta">
            <span><i class="fas fa-calendar-days"></i> <?php echo date('d/m/Y'); ?></span>
            <span><i class="fas fa-chart-simple"></i> Ejercicio <?php echo $anioActual; ?></span>
        </div>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card kpi-card h-100">
                <div class="card-body">
                    <div class="kpi-head">
                        <span class="kpi-icon"><i class="fas fa-sack-dollar"></i></span>
                        <p>Ingresos netos</p>
                    </div>
                    <h3>Bs. <?php echo number_format($totalIngresosAnio, 2, ',', '.'); ?></h3>
                    <small>Anulado: Bs. <?php echo number_format($totalAnuladoAnio, 2, ',', '.'); ?></small>
                </div>
            </article>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card kpi-card h-100">
                <div class="card-body">
                    <div class="kpi-head">
                        <span class="kpi-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <p>Facturas pendientes</p>
                    </div>
                    <h3><?php echo (int)$facturasPendientes; ?></h3>
                    <small>Documentos por gestionar</small>
                </div>
            </article>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card kpi-card h-100">
                <div class="card-body">
                    <div class="kpi-head">
                        <span class="kpi-icon"><i class="fas fa-bell"></i></span>
                        <p>Vencimientos proximos (30 dias)</p>
                    </div>
                    <h3><?php echo (int)$vencimientosProx30; ?></h3>
                    <small>Control preventivo de tramites</small>
                </div>
            </article>
        </div>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card dashboard-link-card h-100">
                <div class="card-body">
                    <i class="fas fa-users"></i>
                    <h4>Usuarios</h4>
                    <p>Administracion de cuentas y roles institucionales.</p>
                    <a href="<?= BASE_URL ?>/Vistas/usuarios/gestion_usuarios.php" class="btn btn-outline-primary btn-sm">Ir al modulo</a>
                </div>
            </article>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card dashboard-link-card h-100">
                <div class="card-body">
                    <i class="fas fa-id-card"></i>
                    <h4>Contribuyentes</h4>
                    <p>Registro y consulta de responsables tributarios.</p>
                    <a href="<?= BASE_URL ?>/Vistas/contribuyentes/gestion_contribuyentes.php" class="btn btn-outline-primary btn-sm">Ir al modulo</a>
                </div>
            </article>
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <article class="card dashboard-link-card h-100">
                <div class="card-body">
                    <i class="fas fa-certificate"></i>
                    <h4>Patentes y licencias</h4>
                    <p>Control de autorizaciones comerciales y solvencias.</p>
                    <a href="<?= BASE_URL ?>/Vistas/patentes/gestionPatentes.php" class="btn btn-outline-primary btn-sm">Ir al modulo</a>
                </div>
            </article>
        </div>
    </section>

    <section class="row g-3">
        <div class="col-12 col-xxl-8">
            <article class="card chart-card h-100">
                <header class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span>Ingresos mensuales (<?php echo date('Y'); ?>)</span>
                    <div class="btn-group btn-group-sm" role="group">
                        <button id="btnMensual" class="btn btn-outline-secondary active">Mensual</button>
                        <button id="btnTrimestral" class="btn btn-outline-secondary">Trimestral</button>
                    </div>
                </header>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartIngresos"></canvas>
                    </div>
                </div>
            </article>
        </div>
        <div class="col-12 col-xxl-4">
            <article class="card chart-card h-100">
                <header class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span>Distribucion por rubro</span>
                    <button class="btn btn-sm btn-outline-primary" type="button"><i class="fas fa-file-export me-1"></i>Exportar</button>
                </header>
                <div class="card-body">
                    <div class="chart-container chart-doughnut">
                        <canvas id="chartTipos"></canvas>
                    </div>
                </div>
            </article>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const labelsMeses = <?php echo json_encode($labelsMeses, JSON_UNESCAPED_UNICODE); ?>;
  const ingresosMensuales = <?php echo json_encode($ingresosMensuales); ?>;
  const labelsTipos = <?php echo json_encode($labelsTipos, JSON_UNESCAPED_UNICODE); ?>;
  const valoresTipos = <?php echo json_encode($valoresTipos); ?>;
  const actividadReciente = <?php echo json_encode($actividadReciente, JSON_UNESCAPED_UNICODE); ?>;

  let chartIngresos;

  function renderIngresos(labels, data) {
    const ctx = document.getElementById('chartIngresos');
    if (chartIngresos) chartIngresos.destroy();
    chartIngresos = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Ingresos (Bs.)',
          data,
          tension: 0.35,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            ticks: {
              maxRotation: 0
            }
          },
          y: {
            ticks: {
              callback: v => 'Bs. ' + v.toLocaleString('es-VE')
            }
          }
        }
      }
    });
  }

  function aTrimestres(series) {
    const out = [];
    for (let i = 0; i < series.length; i += 3) {
      out.push(series.slice(i, i + 3).reduce((a, b) => a + b, 0));
    }
    return out;
  }
  const btnMensual = document.getElementById('btnMensual');
  const btnTrimestral = document.getElementById('btnTrimestral');
  btnMensual.addEventListener('click', () => {
    btnMensual.classList.add('active');
    btnTrimestral.classList.remove('active');
    renderIngresos(labelsMeses, ingresosMensuales);
  });
  btnTrimestral.addEventListener('click', () => {
    btnTrimestral.classList.add('active');
    btnMensual.classList.remove('active');
    renderIngresos(['T1', 'T2', 'T3', 'T4'], aTrimestres(ingresosMensuales));
  });
  renderIngresos(labelsMeses, ingresosMensuales);

  new Chart(document.getElementById('chartTipos'), {
    type: 'doughnut',
    data: {
      labels: labelsTipos,
      datasets: [{
        data: valoresTipos
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
</script>
<?php include 'layouts/footerDashboard.php'; ?>
