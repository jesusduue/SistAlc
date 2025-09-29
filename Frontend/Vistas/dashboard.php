<?php
// Dashboard.php // vista

include 'layouts/headerDashboard.php';

// Verificar sesión (puedes moverlo a proteger_vista si prefieres)
if (!isset($_SESSION['nom_usu']) || !isset($_SESSION['nom_rol'])) {
    header('Location: ../../index.php');
    exit;
}

$nombre_usuario = $_SESSION['nom_usu'];
$nombre_rol     = $_SESSION['nom_rol'];

include 'layouts/sidebarDashboard.php';

// ==========================
// Placeholders (luego los tomamos de un DashboardController)
// ==========================
require_once '../../Backend/clases/dashboard.php';

$dash = new Dashboard();

$totalIngresosAnio   = 5742301.00;
$facturasPendientes  = 124;
$vencimientosProx30  = 37;

$ingresosMensuales = [380000, 420000, 390000, 580000, 460000, 520000, 610000, 570000, 630000, 680000, 720000, 790000];
$labelsMeses       = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

$labelsTipos = ['Impuesto Inmobiliario','Patentes Comerciales','Permisos de Construcción','Tasas Municipales','Otros'];
$valoresTipos = [35, 28, 12, 18, 7];

//Actividad Reciente
$actividadReciente = $dash->actividadesRecientes();
?>

<link rel="stylesheet" href="../estilos/css/bootstrap.min.css">

<!-- ★ Estilos responsive específicos del dashboard -->
<style>
/* Contenedor principal:
   - En móviles: padding-top estándar.
   - Desde md: deja espacio a la izquierda para el sidebar fijo. */
.dashboard-wrapper {
  padding-top: 1.25rem;
}
@media (min-width: 768px) {
  .dashboard-wrapper {
    margin-left: 250px; /* coincide con el ancho de tu sidebar */
  }
}

/* KPIs: icono redondo consistente */
.kpi-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 48px; height: 48px;
  border-radius: 9999px;
}

/* Tarjetas con sombra suave y sin bordes duros */
.card.border-0, .card.shadow-sm {
  border-radius: .75rem;
}

/* ★ Contenedor de gráficos:
   - Altura fluida sin forzar width/height en el canvas.
   - Usa min-height como fallback para navegadores sin aspect-ratio. */
.chart-container {
  position: relative;
  width: 100%;
  min-height: 260px;      /* fallback */
  height: 40vh;           /* crece con la pantalla */
  max-height: 520px;      /* evita ser enorme en monitores grandes */
}
@media (max-width: 576px) {
  .chart-container {
    height: 35vh;
    min-height: 220px;
  }
}

/* Tabla actividad: mejora scroll en móviles */
.table-responsive {
  -webkit-overflow-scrolling: touch;
}
</style>

<div class="container-fluid dashboard-wrapper"><!-- ★ wrapper responsive -->
    <!-- Bienvenida -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-end justify-content-between gap-2 mb-3">
      <div>
        <h1 class="mb-1 h3">Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></h1>
        <p class="text-muted mb-0">Rol: <strong><?php echo htmlspecialchars($nombre_rol); ?></strong></p>
      </div>
      <!-- (Opcional) Espacio para filtros del dashboard -->
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-primary-subtle">
                      <i class="fas fa-sack-dollar fa-lg text-primary"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Ingresos (<?php echo date('Y'); ?>)</div>
                        <div class="h5 mb-0">Bs. <?php echo number_format($totalIngresosAnio, 2, ',', '.'); ?></div>
                        <a href="#" class="small">Ver reporte completo</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-warning-subtle">
                      <i class="fas fa-file-invoice-dollar fa-lg text-warning"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Facturas Pendientes</div>
                        <div class="h5 mb-0"><?php echo (int)$facturasPendientes; ?></div>
                        <a href="#" class="small">Ver todas las facturas</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-info-subtle">
                      <i class="fas fa-bell fa-lg text-info"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Vencimientos Próximos (30 días)</div>
                        <div class="h5 mb-0"><?php echo (int)$vencimientosProx30; ?></div>
                        <a href="#" class="small">Ver vencimientos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de navegación -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text">Gestión de cuentas y roles de acceso.</p>
                    <a href="../Vistas/usuarios/gestion_usuarios.php" class="btn btn-outline-primary">Administrar</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-id-card fa-3x mb-3 text-success"></i>
                    <h5 class="card-title">Contribuyentes</h5>
                    <p class="card-text">Registro y control de contribuyentes.</p>
                    <a href="../Vistas/contribuyentes/gestion_contribuyentes.php" class="btn btn-outline-success">Administrar</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <i class="fas fa-building fa-3x mb-3 text-warning"></i>
                    <h5 class="card-title">Patentes</h5>
                    <p class="card-text">Control de actividades económicas y licencias.</p>
                    <a href="#" class="btn btn-outline-warning">Administrar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas (estático por ahora) -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8"><!-- ★ xl en vez de lg para más comodidad -->
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="fw-semibold">Ingresos Mensuales (<?php echo date('Y'); ?>)</span>
                    <div class="btn-group btn-group-sm" role="group">
                        <button id="btnMensual" class="btn btn-outline-secondary active">Mensual</button>
                        <button id="btnTrimestral" class="btn btn-outline-secondary">Trimestral</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container"><!-- ★ contenedor flexible -->
                      <canvas id="chartIngresos"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="fw-semibold">Distribución por Tipo de Impuesto</span>
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-file-export me-1"></i>Exportar</button>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="min-height:240px; height: 38vh;"><!-- ★ más bajo para doughnut -->
                      <canvas id="chartTipos"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad reciente -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="fw-semibold">Actividad Reciente</span>
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted">Mostrando <span id="rangeInfo">1-5</span></span>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="paginador"></ul>
                </nav>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody id="tbodyActividad">
                    <!-- Filas generadas por JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'layouts/footerDashboard.php'; ?>

<!-- Chart.js (no depende de jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// ===== Datos PHP -> JS =====
const labelsMeses       = <?php echo json_encode($labelsMeses, JSON_UNESCAPED_UNICODE); ?>;
const ingresosMensuales = <?php echo json_encode($ingresosMensuales); ?>;
const labelsTipos       = <?php echo json_encode($labelsTipos, JSON_UNESCAPED_UNICODE); ?>;
const valoresTipos      = <?php echo json_encode($valoresTipos); ?>;
const actividadReciente = <?php echo json_encode($actividadReciente, JSON_UNESCAPED_UNICODE); ?>;

// ===== Línea: ingresos mensuales / trimestral =====
let chartIngresos;
function renderIngresos(labels, data){
  const ctx = document.getElementById('chartIngresos');
  if(chartIngresos) chartIngresos.destroy();
  chartIngresos = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{ label: 'Ingresos (Bs.)', data, tension: 0.35, fill: true }]
    },
    options: {
      responsive: true,              // ★
      maintainAspectRatio: false,    // ★ usa el alto del contenedor .chart-container
      plugins: { legend: { display: false } },
      scales: {
        x: { ticks: { maxRotation: 0 } },
        y: { ticks: { callback: v => 'Bs. ' + v.toLocaleString('es-VE') } }
      }
    }
  });
}
function aTrimestres(series){
  const out = [];
  for(let i=0;i<series.length;i+=3){ out.push(series.slice(i,i+3).reduce((a,b)=>a+b,0)); }
  return out;
}
const btnMensual = document.getElementById('btnMensual');
const btnTrimestral = document.getElementById('btnTrimestral');
btnMensual.addEventListener('click', ()=>{
  btnMensual.classList.add('active'); btnTrimestral.classList.remove('active');
  renderIngresos(labelsMeses, ingresosMensuales);
});
btnTrimestral.addEventListener('click', ()=>{
  btnTrimestral.classList.add('active'); btnMensual.classList.remove('active');
  renderIngresos(['T1','T2','T3','T4'], aTrimestres(ingresosMensuales));
});
renderIngresos(labelsMeses, ingresosMensuales);

// ===== Doughnut: distribución por tipo =====
new Chart(document.getElementById('chartTipos'), {
  type: 'doughnut',
  data: { labels: labelsTipos, datasets: [{ data: valoresTipos }] },
  options: {
    responsive: true,             // ★
    maintainAspectRatio: false,   // ★
    plugins: { legend: { position: 'bottom' } }
  }
});

// ===== Actividad Reciente: paginación nativa =====
const pageSize = 5;
let currentPage = 1;
const tbody      = document.getElementById('tbodyActividad');
const paginador  = document.getElementById('paginador');
const rangeInfo  = document.getElementById('rangeInfo');

function badgeClass(kind){
  switch(kind){
    case 'success': return 'badge text-bg-success';
    case 'danger':  return 'badge text-bg-danger';
    case 'primary': return 'badge text-bg-primary';
    default:        return 'badge text-bg-secondary';
  }
}
function renderTabla(){
  const start = (currentPage-1)*pageSize;
  const end   = Math.min(start+pageSize, actividadReciente.length);
  rangeInfo.textContent = (actividadReciente.length===0) ? '0-0' : `${start+1}-${end}`;
  tbody.innerHTML = actividadReciente.slice(start,end).map(r=>`
    <tr>
      <td>${r.fecha}</td>
      <td>${r.usuario}</td>
      <td><span class="${badgeClass(r.badge)}">${r.accion}</span></td>
      <td>${r.modulo}</td>
      <td>${r.detalle}</td>
    </tr>`).join('');
}
function renderPaginador(){
  const totalPages = Math.max(1, Math.ceil(actividadReciente.length / pageSize));
  let html = '';
  html += `<li class="page-item ${currentPage===1?'disabled':''}"><a class="page-link" href="#" data-pg="prev">«</a></li>`;
  for(let p=1;p<=totalPages;p++){
    html += `<li class="page-item ${p===currentPage?'active':''}"><a class="page-link" href="#" data-pg="${p}">${p}</a></li>`;
  }
  html += `<li class="page-item ${currentPage===totalPages?'disabled':''}"><a class="page-link" href="#" data-pg="next">»</a></li>`;
  paginador.innerHTML = html;
}
paginador.addEventListener('click', (e)=>{
  const a = e.target.closest('a[data-pg]');
  if(!a) return;
  e.preventDefault();
  const totalPages = Math.max(1, Math.ceil(actividadReciente.length / pageSize));
  const pg = a.getAttribute('data-pg');
  if(pg==='prev' && currentPage>1) currentPage--;
  else if(pg==='next' && currentPage<totalPages) currentPage++;
  else if(!isNaN(parseInt(pg))) currentPage = parseInt(pg);
  renderPaginador();
  renderTabla();
});
renderPaginador();
renderTabla();
</script>
