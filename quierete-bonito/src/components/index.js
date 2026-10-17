/* Componentes HTML reutilizables para la interfaz administrativa vanilla. */
import { escapar } from '../lib/format.js';

export const icon = (nombre, clase = 'w-5 h-5') => `<i data-lucide="${nombre}" class="${clase}"></i>`;

export function boton(texto, opciones = {}) {
  const { tipo = 'primary', tamano = 'md', icono = '', id = '', clase = '', type = 'button', disabled = false } = opciones;
  const estilos = {
    primary: 'bg-[#CC498F] text-white hover:bg-[#b83d7d]',
    secondary: 'bg-white text-[#202020] border border-slate-200 hover:border-[#CC498F] hover:text-[#CC498F]',
    ghost: 'text-slate-600 hover:bg-[#FBEAF3] hover:text-[#CC498F]',
    danger: 'bg-rose-50 text-rose-600 hover:bg-rose-100',
  };
  const tamanos = { sm: 'px-3 py-2 text-xs', md: 'px-4 py-2.5 text-sm', lg: 'px-5 py-3 text-sm' };
  return `<button type="${type}" ${id ? `id="${id}"` : ''} ${disabled ? 'disabled' : ''} class="inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition ${estilos[tipo] || estilos.primary} ${tamanos[tamano] || tamanos.md} ${disabled ? 'cursor-not-allowed opacity-50' : ''} ${clase}">${icono ? icon(icono, 'w-4 h-4') : ''}<span>${texto}</span></button>`;
}

export function tarjeta(contenido, clase = '') {
  return `<section class="rounded-3xl border border-slate-100 bg-white p-5 shadow-card ${clase}">${contenido}</section>`;
}

export function insignia(texto, tono = 'neutral') {
  const estilos = {
    success: 'bg-emerald-50 text-emerald-700', warning: 'bg-amber-50 text-amber-700', danger: 'bg-rose-50 text-rose-700',
    brand: 'bg-[#FBEAF3] text-[#A92E6D]', neutral: 'bg-slate-100 text-slate-600', info: 'bg-sky-50 text-sky-700'
  };
  return `<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ${estilos[tono] || estilos.neutral}">${escapar(texto)}</span>`;
}

export function estado(texto) {
  const mapa = { activo: 'success', pagado: 'success', ingreso: 'success', confirmada: 'success', atendida: 'success', pendiente: 'warning', advertencia: 'warning', cancelada: 'danger', error: 'danger', inactivo: 'danger', no_asistio: 'danger' };
  return insignia(String(texto).replace('_', ' '), mapa[texto] || 'neutral');
}

export function campo({ label, name, value = '', type = 'text', placeholder = '', required = false, hint = '', error = '', min = '', step = '', className = '' }) {
  return `<label class="block text-sm font-medium text-slate-700 ${className}"><span class="mb-1.5 block">${label}${required ? ' <b class="text-[#CC498F]">*</b>' : ''}</span><input name="${name}" value="${escapar(value)}" type="${type}" ${placeholder ? `placeholder="${placeholder}"` : ''} ${required ? 'required' : ''} ${min !== '' ? `min="${min}"` : ''} ${step !== '' ? `step="${step}"` : ''} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-[#202020] placeholder:text-slate-400 transition focus:border-[#CC498F] focus:ring-4 focus:ring-[#F4D4E6]" />${hint ? `<small class="mt-1 block text-xs text-slate-400">${hint}</small>` : ''}${error ? `<small class="mt-1 block text-xs text-rose-600">${error}</small>` : ''}</label>`;
}

export function area({ label, name, value = '', placeholder = '', required = false }) {
  return `<label class="block text-sm font-medium text-slate-700"><span class="mb-1.5 block">${label}${required ? ' <b class="text-[#CC498F]">*</b>' : ''}</span><textarea name="${name}" rows="3" ${required ? 'required' : ''} placeholder="${placeholder}" class="w-full resize-none rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#CC498F] focus:ring-4 focus:ring-[#F4D4E6]">${escapar(value)}</textarea></label>`;
}

export function selector({ label, name, opciones = [], value = '', required = false, className = '' }) {
  return `<label class="block text-sm font-medium text-slate-700 ${className}"><span class="mb-1.5 block">${label}${required ? ' <b class="text-[#CC498F]">*</b>' : ''}</span><select name="${name}" ${required ? 'required' : ''} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm focus:border-[#CC498F] focus:ring-4 focus:ring-[#F4D4E6]">${opciones.map((opcion) => `<option value="${escapar(opcion.value)}" ${String(opcion.value) === String(value) ? 'selected' : ''}>${escapar(opcion.label)}</option>`).join('')}</select></label>`;
}

export function tabla(encabezados, filas, opciones = {}) {
  const { vacio = 'No hay datos para mostrar.', clase = '' } = opciones;
  if (!filas.length) return emptyState(vacio);
  return `<div class="overflow-x-auto"><table class="w-full min-w-[680px] text-left ${clase}"><thead><tr class="border-b border-slate-100 text-xs uppercase tracking-[.12em] text-slate-400">${encabezados.map((encabezado) => `<th class="px-3 py-3 font-semibold">${encabezado}</th>`).join('')}</tr></thead><tbody class="divide-y divide-slate-100">${filas.join('')}</tbody></table></div>`;
}

export function fila(celdas, clase = '') { return `<tr class="text-sm text-slate-600 hover:bg-slate-50 ${clase}">${celdas.map((celda) => `<td class="px-3 py-3.5 align-middle">${celda}</td>`).join('')}</tr>`; }
export function emptyState(texto, accion = '') { return `<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 px-6 py-12 text-center"><div class="mb-3 rounded-2xl bg-[#FBEAF3] p-3 text-[#CC498F]">${icon('inbox', 'w-6 h-6')}</div><p class="text-sm font-medium text-slate-600">${texto}</p>${accion ? `<div class="mt-4">${accion}</div>` : ''}</div>`; }
export function skeleton(clase = 'h-24') { return `<div class="animate-pulse rounded-2xl bg-slate-100 ${clase}"></div>`; }
export function alerta(texto, tono = 'info') { const estilos = { info: 'bg-sky-50 text-sky-800 border-sky-100', warning: 'bg-amber-50 text-amber-800 border-amber-100', danger: 'bg-rose-50 text-rose-800 border-rose-100', success: 'bg-emerald-50 text-emerald-800 border-emerald-100' }; return `<div class="flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm ${estilos[tono] || estilos.info}">${icon(tono === 'danger' ? 'circle-alert' : tono === 'warning' ? 'triangle-alert' : 'info', 'mt-0.5 h-4 w-4 shrink-0')}<span>${texto}</span></div>`; }

export function modal({ id, titulo, contenido, ancho = 'max-w-lg' }) {
  return `<div id="${id}" class="modal-backdrop fixed inset-0 z-50 hidden items-center justify-center bg-[#202020]/45 p-4"><div class="modal-panel ${ancho} max-h-[90vh] w-full overflow-y-auto rounded-3xl bg-white p-5 shadow-soft sm:p-7"><div class="mb-5 flex items-start justify-between gap-4"><div><h3 class="text-lg font-semibold text-[#202020]">${titulo}</h3><p class="mt-1 text-sm text-slate-500">Completa la información y guarda los cambios.</p></div><button data-close-modal="${id}" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">${icon('x')}</button></div>${contenido}</div></div>`;
}

export function abrirModal(id) { const elemento = document.getElementById(id); if (elemento) { elemento.classList.remove('hidden'); elemento.classList.add('flex'); } }
export function cerrarModal(id) { const elemento = document.getElementById(id); if (elemento) { elemento.classList.add('hidden'); elemento.classList.remove('flex'); } }

export function mostrarToast(mensaje, tipo = 'success') {
  const root = document.getElementById('toast-root');
  if (!root) return;
  const color = tipo === 'error' ? 'border-rose-200 text-rose-700' : 'border-emerald-200 text-emerald-700';
  const item = document.createElement('div');
  item.className = `toast-in mb-3 flex max-w-sm items-center gap-3 rounded-2xl border bg-white px-4 py-3 text-sm font-medium shadow-soft ${color}`;
  item.innerHTML = `${icon(tipo === 'error' ? 'circle-alert' : 'circle-check', 'h-5 w-5 shrink-0')}<span>${escapar(mensaje)}</span>`;
  root.appendChild(item);
  window.lucide?.createIcons();
  setTimeout(() => item.remove(), 3000);
}

export function formatearFormulario(formulario) {
  return Object.fromEntries(new FormData(formulario).entries());
}
