// ===============================
// ACTIVIDAD RECIENTE (desde PHP)
// ===============================
// IMPORTANTE: usamos la MISMA variable que definimos en PHP ($actividadReciente)

// Config de paginación
const pageSize  = 10; // Cantidad de filas por página
let currentPage = 1;// Página actual
const tbody     = document.getElementById('tbodyActividad');
const paginador = document.getElementById('paginador');
const rangeInfo = document.getElementById('rangeInfo');

// Mapeo de clases de badge según acción (ya viene mapeado desde Dashboard::actividadesRecientes,
// pero por si quieres reforzar en front, lo dejamos)
function badgeClass(kind){
  switch(kind){
    case 'success': return 'badge text-bg-success';
    case 'danger':  return 'badge text-bg-danger';
    case 'primary': return 'badge text-bg-primary';
    default:        return 'badge text-bg-warning';
  }
}

// Render de filas
function renderTabla(){
  const start = (currentPage - 1) * pageSize;
  const end   = Math.min(start + pageSize, actividadReciente.length);
  rangeInfo.textContent = (actividadReciente.length === 0) ? '0-0' : `${start + 1}-${end}`;
  tbody.innerHTML = actividadReciente.slice(start, end).map(r => `
    <tr>
      <td>${r.fecha}</td>
      <td>${r.usuario}</td>
      <td><span class="${badgeClass(r.badge)}">${r.accion}</span></td>
      <td>${r.modulo}</td>
      <td>${r.detalle}</td>
    </tr>
  `).join('');
}

// Render de paginador
function renderPaginador(){
  const totalPages = Math.max(1, Math.ceil(actividadReciente.length / pageSize));
  let html = '';
  html += `<li class="page-item ${currentPage===1?'disabled':''}"><a class="page-link" href="#" data-pg="prev">«</a></li>`;
  for (let p = 1; p <= totalPages; p++) {
    html += `<li class="page-item ${p===currentPage?'active':''}"><a class="page-link" href="#" data-pg="${p}">${p}</a></li>`;
  }
  html += `<li class="page-item ${currentPage===totalPages?'disabled':''}"><a class="page-link" href="#" data-pg="next">»</a></li>`;
  paginador.innerHTML = html;
}

// Manejo de clics en paginador
paginador.addEventListener('click', (e) => {
  const a = e.target.closest('a[data-pg]');
  if (!a) return;
  e.preventDefault();
  const totalPages = Math.max(1, Math.ceil(actividadReciente.length / pageSize));
  const pg = a.getAttribute('data-pg');
  if (pg === 'prev' && currentPage > 1) currentPage--;
  else if (pg === 'next' && currentPage < totalPages) currentPage++;
  else if (!isNaN(parseInt(pg))) currentPage = parseInt(pg);
  renderPaginador();
  renderTabla();
});

/* // Inicializar tabla y paginador
renderPaginador();
renderTabla(); */