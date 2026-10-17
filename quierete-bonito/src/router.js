/* Enrutador hash de la SPA: carga seed, protege rutas y monta cada vista. */
import { seedInitialData } from './lib/db.js';
import { estaAutenticado } from './auth/AuthProvider.js';
import { protegerRuta } from './auth/RutaProtegida.js';
import { activarLogin, renderLogin } from './pages/Login.js';
import { activarDashboard, renderDashboard } from './pages/Dashboard.js';
import { activarNuevaVenta, renderNuevaVenta } from './pages/NuevaVenta.js';
import { activarCatalogo, activarCitas, activarClientes, activarConfiguracion, activarGastos, activarReportes, activarVentaDetalle, activarVentas, renderCitas, renderClientes, renderConfiguracion, renderGastos, renderProductos, renderReportes, renderServicios, renderVentaDetalle, renderVentas } from './pages/Pages.js';

const app = document.getElementById('app');
let renderEnCurso = false;

function rutaActual() {
  const hash = window.location.hash.replace(/^#/, '') || '/';
  return hash.startsWith('/') ? hash : `/${hash}`;
}

function resolver(ruta) {
  if (ruta === '/') return { render: renderDashboard, activar: activarDashboard, privada: true };
  if (ruta === '/login') return { render: renderLogin, activar: activarLogin, privada: false };
  if (ruta === '/clientes') return { render: renderClientes, activar: activarClientes, privada: true };
  if (ruta === '/citas') return { render: renderCitas, activar: activarCitas, privada: true };
  if (ruta === '/ventas') return { render: renderVentas, activar: activarVentas, privada: true };
  if (ruta === '/ventas/nueva') return { render: renderNuevaVenta, activar: activarNuevaVenta, privada: true };
  const detalle = ruta.match(/^\/ventas\/(\d+)$/); if (detalle) return { render: () => renderVentaDetalle(Number(detalle[1])), activar: activarVentaDetalle, privada: true };
  if (ruta === '/servicios') return { render: renderServicios, activar: () => activarCatalogo('servicio'), privada: true };
  if (ruta === '/productos') return { render: renderProductos, activar: () => activarCatalogo('producto'), privada: true };
  if (ruta === '/categorias') return { render: () => renderConfiguracion('categorias'), activar: () => activarConfiguracion('categorias'), privada: true };
  if (ruta === '/categorias-gastos') return { render: () => renderConfiguracion('categorias-gastos'), activar: () => activarConfiguracion('categorias-gastos'), privada: true };
  if (ruta === '/metodos-pago') return { render: () => renderConfiguracion('metodos-pago'), activar: () => activarConfiguracion('metodos-pago'), privada: true };
  if (ruta === '/gastos') return { render: renderGastos, activar: activarGastos, privada: true };
  if (ruta === '/reportes') return { render: renderReportes, activar: activarReportes, privada: true };
  return { render: renderDashboard, activar: activarDashboard, privada: true };
}

async function renderizar() {
  if (renderEnCurso) return;
  renderEnCurso = true;
  try {
    await seedInitialData();
    let ruta = rutaActual();
    if (ruta !== '/login' && !estaAutenticado()) { window.location.hash = '#/login'; return; }
    if (ruta === '/login' && estaAutenticado()) { window.location.hash = '#/'; return; }
    const pagina = resolver(ruta);
    if (pagina.privada && !protegerRuta()) return;
    app.innerHTML = '<div class="flex min-h-screen items-center justify-center p-6"><div class="w-full max-w-4xl space-y-4"><div class="h-8 w-48 animate-pulse rounded-xl bg-slate-100"></div><div class="grid gap-4 sm:grid-cols-3"><div class="h-28 animate-pulse rounded-3xl bg-slate-100"></div><div class="h-28 animate-pulse rounded-3xl bg-slate-100"></div><div class="h-28 animate-pulse rounded-3xl bg-slate-100"></div></div></div></div>';
    app.innerHTML = await pagina.render();
    pagina.activar?.();
    window.lucide?.createIcons();
  } catch (error) {
    console.error(error);
    app.innerHTML = `<div class="flex min-h-screen items-center justify-center p-6"><div class="max-w-md rounded-3xl bg-white p-8 text-center shadow-soft"><div class="mx-auto mb-4 w-fit rounded-2xl bg-rose-50 p-3 text-rose-600">${window.lucide ? '<i data-lucide="circle-alert" class="h-7 w-7"></i>' : '!'}</div><h1 class="text-xl font-bold">No pudimos cargar esta vista</h1><p class="mt-2 text-sm text-slate-500">${error.message || 'Error inesperado'}</p><a href="#/" class="mt-5 inline-flex rounded-xl bg-[#CC498F] px-4 py-2.5 text-sm font-semibold text-white">Volver al inicio</a></div></div>`;
    window.lucide?.createIcons();
  } finally { renderEnCurso = false; }
}

window.addEventListener('hashchange', renderizar);
window.addEventListener('DOMContentLoaded', renderizar);
if (document.readyState !== 'loading') renderizar();
