/* Layout principal responsive: sidebar en desktop, bottom-nav en móvil y encabezado contextual. */
import { cerrarSesion, obtenerSesion } from '../auth/AuthProvider.js';
import { icon } from './index.js';

const grupos = [
  { titulo: 'General', items: [{ ruta: '/', texto: 'Inicio', icono: 'layout-dashboard' }, { ruta: '/clientes', texto: 'Clientes', icono: 'users' }, { ruta: '/citas', texto: 'Citas', icono: 'calendar-days' }] },
  { titulo: 'Operación', items: [{ ruta: '/ventas', texto: 'Ventas', icono: 'receipt-text' }, { ruta: '/servicios', texto: 'Servicios', icono: 'sparkles' }, { ruta: '/productos', texto: 'Productos', icono: 'package' }, { ruta: '/gastos', texto: 'Gastos', icono: 'wallet-cards' }] },
  { titulo: 'Configuración', items: [{ ruta: '/categorias', texto: 'Categorías', icono: 'tags' }, { ruta: '/categorias-gastos', texto: 'Categorías de gastos', icono: 'folder-cog' }, { ruta: '/metodos-pago', texto: 'Métodos de pago', icono: 'credit-card' }, { ruta: '/reportes', texto: 'Reportes', icono: 'chart-no-axes-combined' }] }
];

const rutaActiva = (ruta, actual) => ruta === '/' ? actual === '/' : actual.startsWith(ruta);
const enlace = (item, actual) => `<a href="#${item.ruta}" class="sidebar-link ${rutaActiva(item.ruta, actual) ? 'active' : ''} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-600 transition hover:bg-[#FBEAF3] hover:text-[#CC498F]">${icon(item.icono, 'h-[18px] w-[18px]')}<span>${item.texto}</span></a>`;

export function renderLayout({ ruta, titulo, subtitulo = '', contenido }) {
  const sesion = obtenerSesion();
  return `<div class="min-h-screen bg-[#FAFAFA] md:flex">
    <aside class="hidden w-64 shrink-0 border-r border-slate-100 bg-white px-4 py-5 md:flex md:flex-col">
      <a href="#/" class="mb-8 flex items-center gap-3 px-2"><span class="brand-gradient flex h-10 w-10 items-center justify-center rounded-2xl text-lg font-bold text-white">QB</span><span><b class="block text-sm font-bold tracking-tight">Quierete Bonito</b><small class="text-[11px] text-slate-400">Beauty admin</small></span></a>
      <nav class="nav-scroll flex-1 space-y-6 overflow-y-auto">${grupos.map((grupo) => `<div><p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[.16em] text-slate-400">${grupo.titulo}</p><div class="space-y-1">${grupo.items.map((item) => enlace(item, ruta)).join('')}</div></div>`).join('')}</nav>
      <div class="mt-5 border-t border-slate-100 pt-4"><button id="logout-button" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-500 transition hover:bg-rose-50 hover:text-rose-600">${icon('log-out', 'h-[18px] w-[18px]')}Cerrar sesión</button></div>
    </aside>
    <main class="mobile-safe-bottom min-w-0 flex-1">
      <header class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-100 bg-[#FAFAFA]/90 px-4 py-4 backdrop-blur sm:px-7 lg:px-10"><div><p class="text-xs font-semibold uppercase tracking-[.16em] text-[#CC498F]">Quierete Bonito</p><h1 class="mt-1 text-xl font-bold tracking-tight sm:text-2xl">${titulo}</h1>${subtitulo ? `<p class="mt-1 text-sm text-slate-500">${subtitulo}</p>` : ''}</div><div class="flex items-center gap-2 sm:gap-4"><a href="#/reportes" class="hidden rounded-xl p-2.5 text-slate-400 hover:bg-white hover:text-[#CC498F] sm:block">${icon('bell', 'h-5 w-5')}</a><div class="hidden items-center gap-2 rounded-2xl bg-white px-3 py-2 shadow-card sm:flex"><span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#FBEAF3] text-xs font-bold text-[#CC498F]">AD</span><div><p class="text-xs font-semibold">Admin</p><p class="text-[10px] text-slate-400">${sesion?.user || ''}</p></div></div><button id="logout-button-mobile" class="rounded-xl p-2 text-slate-500 hover:bg-white hover:text-rose-600 sm:hidden">${icon('log-out', 'h-5 w-5')}</button></div></header>
      <div class="px-4 py-5 sm:px-7 sm:py-7 lg:px-10">${contenido}</div>
    </main>
    <nav class="fixed bottom-0 left-0 right-0 z-30 border-t border-slate-100 bg-white/95 px-2 py-2 backdrop-blur md:hidden"><div class="mx-auto grid max-w-lg grid-cols-5 gap-1">${[{ ruta: '/', texto: 'Inicio', icono: 'home' }, { ruta: '/clientes', texto: 'Clientes', icono: 'users' }, { ruta: '/citas', texto: 'Citas', icono: 'calendar-days' }, { ruta: '/ventas', texto: 'Ventas', icono: 'receipt-text' }, { ruta: '/reportes', texto: 'Más', icono: 'menu' }].map((item) => `<a href="#${item.ruta}" class="flex flex-col items-center gap-1 rounded-xl py-1.5 text-[10px] ${rutaActiva(item.ruta, ruta) ? 'font-semibold text-[#CC498F]' : 'text-slate-400'}">${icon(item.icono, 'h-5 w-5')}<span>${item.texto}</span></a>`).join('')}</div></nav>
  </div>`;
}

export function activarLayout() {
  document.querySelector('#logout-button')?.addEventListener('click', cerrarSesion);
  document.querySelector('#logout-button-mobile')?.addEventListener('click', cerrarSesion);
  window.lucide?.createIcons();
}
