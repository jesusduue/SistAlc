/* Registro de venta: cliente, método, moneda, tasa manual, líneas de productos/servicios y stock. */
import { getAll, insert, redondear, update } from '../lib/db.js';
import { formatearMoneda } from '../lib/format.js';
import { convertirBaseAMoneda, sugerirUltimaTasa, validarTasa } from '../lib/tasas.js';
import { alerta, area, boton, campo, formatearFormulario, icon, selector, mostrarToast, tarjeta } from '../components/index.js';
import { renderLayout, activarLayout } from '../components/Layout.js';

let estadoVenta = { lineas: [], monedas: [], clientes: [], metodos: [], servicios: [], productos: [] };

const lineaHTML = (linea, indice) => `<div class="linea-venta grid gap-3 rounded-2xl border border-slate-100 bg-slate-50/60 p-3 sm:grid-cols-[1.2fr_.45fr_.65fr_auto]" data-indice="${indice}"><div><p class="text-sm font-semibold text-[#202020]">${linea.descripcion}</p><p class="mt-1 text-xs text-slate-400">${linea.tipo_item === 'producto' ? 'Producto' : 'Servicio'} · ${formatearMoneda(linea.precio_unitario, 'COP', estadoVenta.monedas)}</p></div><label class="text-xs font-semibold text-slate-500">Cantidad<input data-linea-cantidad="${indice}" type="number" min="1" step="1" value="${linea.cantidad}" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" /></label><div><p class="text-xs font-semibold text-slate-500">Subtotal</p><p class="mt-2 text-sm font-bold text-[#CC498F]">${formatearMoneda(linea.subtotal, 'COP', estadoVenta.monedas)}</p></div><button data-quitar-linea="${indice}" class="self-center rounded-xl p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600">${icon('trash-2', 'h-4 w-4')}</button></div>`;

export async function renderNuevaVenta() {
  const [monedas, clientes, metodos, servicios, productos] = await Promise.all(['monedas', 'clientes', 'metodos_pago', 'servicios', 'productos'].map(getAll));
  estadoVenta = { lineas: [], monedas: monedas.filter((moneda) => moneda.activa), clientes, metodos: metodos.filter((metodo) => metodo.activo), servicios: servicios.filter((servicio) => servicio.activo), productos: productos.filter((producto) => producto.activo) };
  const opcionesItem = [...estadoVenta.servicios.map((item) => ({ value: `servicio:${item.id}`, label: `Servicio · ${item.nombre}` })), ...estadoVenta.productos.map((item) => ({ value: `producto:${item.id}`, label: `Producto · ${item.nombre}` }))];
  const contenido = `<div class="mx-auto max-w-5xl space-y-6"><div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"><div><a href="#/ventas" class="mb-2 inline-flex items-center gap-1 text-sm font-semibold text-[#CC498F]">${icon('arrow-left', 'h-4 w-4')}Volver a ventas</a><p class="text-sm text-slate-500">Crea una venta y conserva su trazabilidad.</p></div></div><form id="venta-form" class="space-y-6">${tarjeta(`<div class="mb-5 flex items-center gap-3"><span class="rounded-xl bg-[#FBEAF3] p-2 text-[#CC498F]">${icon('file-plus-2')}</span><div><h2 class="font-semibold">Información general</h2><p class="text-xs text-slate-400">Los precios y subtotales se mantienen en COP; la moneda elegida solo define cuánto paga la persona.</p></div></div><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">${selector({ label: 'Cliente', name: 'cliente_id', opciones: [{ value: '', label: 'Venta mostrador' }, ...clientes.map((cliente) => ({ value: cliente.id, label: cliente.nombre }))] })}${selector({ label: 'Método de pago', name: 'metodo_pago_id', opciones: metodos.map((metodo) => ({ value: metodo.id, label: `${metodo.nombre} · ${metodo.moneda}` })), required: true })}${selector({ label: 'Moneda de pago', name: 'moneda_pago', opciones: estadoVenta.monedas.map((moneda) => ({ value: moneda.codigo, label: `${moneda.codigo} · ${moneda.nombre}` })), required: true })}${campo({ label: 'Fecha', name: 'fecha', type: 'date', value: new Date().toISOString().slice(0, 10), required: true })}</div><div class="mt-4 grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">${campo({ label: 'Tasa de la moneda a COP', name: 'tasa_a_base', type: 'number', min: '0.0001', step: '0.0001', value: '1', required: true, hint: 'Ejemplo: 1 USD = 3.200 COP o 1 VES = 3,1 COP. COP usa tasa 1.' })}${boton('Usar última tasa usada', { tipo: 'secondary', id: 'usar-tasa-button', icono: 'history' })}</div>${area({ label: 'Notas', name: 'notas', placeholder: 'Observaciones opcionales' })}</div>`)}${tarjeta(`<div class="mb-5 flex items-center justify-between"><div><h2 class="font-semibold">Items de la venta</h2><p class="text-xs text-slate-400">Los precios base del catálogo siempre están expresados en COP.</p></div>${boton('Agregar item', { tipo: 'secondary', id: 'agregar-linea-button', icono: 'plus' })}</div><div class="grid gap-3 sm:grid-cols-[1fr_auto]"><select id="item-selector" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm focus:border-[#CC498F] focus:ring-4 focus:ring-[#F4D4E6]">${opcionesItem.map((item) => `<option value="${item.value}">${item.label}</option>`).join('')}</select><span class="hidden text-xs text-slate-400 sm:flex sm:items-center">Elige un item y presiona agregar.</span></div><div id="lineas-venta" class="mt-4 space-y-3">${alerta('Todavía no hay items. Agrega al menos un servicio o producto para continuar.', 'info')}</div><div class="mt-5 flex flex-col items-end gap-2 border-t border-slate-100 pt-5"><span id="total-original-label" class="text-sm text-slate-500">Total a pagar</span><strong id="total-original" class="text-2xl font-bold text-[#202020]">$ 0,00</strong><span class="text-sm text-slate-500">Total en COP</span><strong id="total-base" class="text-3xl font-bold text-[#CC498F]">$ 0,00</strong></div></div>`)}<div class="flex justify-end gap-3">${boton('Cancelar', { tipo: 'secondary', clase: 'cancelar-venta' })}${boton('Guardar venta', { type: 'submit', icono: 'check' })}</div></form></div>`;
  return renderLayout({ ruta: '/ventas/nueva', titulo: 'Nueva venta', subtitulo: 'Registra el movimiento y mantén tus números claros.', contenido });
}

function recalcularLineas() {
  estadoVenta.lineas = estadoVenta.lineas.map((linea) => ({ ...linea, subtotal: redondear(Number(linea.cantidad) * Number(linea.precio_unitario)) }));
  const raiz = document.getElementById('lineas-venta');
  if (raiz) raiz.innerHTML = estadoVenta.lineas.length ? estadoVenta.lineas.map(lineaHTML).join('') : alerta('Todavía no hay items. Agrega al menos un servicio o producto para continuar.', 'info');
  const total = estadoVenta.lineas.reduce((suma, linea) => suma + Number(linea.subtotal), 0);
  const moneda = document.querySelector('[name="moneda_pago"]')?.value || 'COP';
  const tasa = Number(document.querySelector('[name="tasa_a_base"]')?.value || 0);
  const totalAPagar = convertirBaseAMoneda(total, tasa, moneda);
  document.getElementById('total-original-label').textContent = `Total a pagar (${moneda})`;
  document.getElementById('total-original').textContent = formatearMoneda(totalAPagar, moneda, estadoVenta.monedas);
  document.getElementById('total-base').textContent = formatearMoneda(total, 'COP', estadoVenta.monedas);
  window.lucide?.createIcons();
}

export function activarNuevaVenta() {
  activarLayout();
  const formulario = document.getElementById('venta-form'); if (!formulario) return;
  const selectorItem = document.getElementById('item-selector');
  document.getElementById('agregar-linea-button')?.addEventListener('click', () => { const [tipo, id] = selectorItem.value.split(':'); const fuente = tipo === 'servicio' ? estadoVenta.servicios : estadoVenta.productos; const item = fuente.find((registro) => String(registro.id) === id); if (!item) return; estadoVenta.lineas.push({ tipo_item: tipo, item_id: item.id, descripcion: item.nombre, cantidad: 1, precio_unitario: Number(item.precio_base), subtotal: Number(item.precio_base) }); recalcularLineas(); });
  document.getElementById('lineas-venta')?.addEventListener('input', (evento) => { const indice = evento.target.dataset.lineaCantidad; if (indice !== undefined) { estadoVenta.lineas[indice].cantidad = Math.max(1, Number(evento.target.value || 1)); recalcularLineas(); } });
  document.getElementById('lineas-venta')?.addEventListener('click', (evento) => { const botonQuitar = evento.target.closest('[data-quitar-linea]'); if (botonQuitar) { estadoVenta.lineas.splice(Number(botonQuitar.dataset.quitarLinea), 1); recalcularLineas(); } });
  document.querySelector('[name="moneda_pago"]')?.addEventListener('change', async (evento) => { const moneda = evento.target.value; const tasa = moneda === 'COP' ? 1 : await sugerirUltimaTasa(moneda); const campoTasa = document.querySelector('[name="tasa_a_base"]'); campoTasa.value = tasa || ''; campoTasa.readOnly = moneda === 'COP'; recalcularLineas(); });
  document.getElementById('usar-tasa-button')?.addEventListener('click', async () => { const moneda = document.querySelector('[name="moneda_pago"]').value; const tasa = await sugerirUltimaTasa(moneda); if (tasa) { document.querySelector('[name="tasa_a_base"]').value = tasa; recalcularLineas(); } else mostrarToast('No hay una tasa previa para esa moneda.', 'error'); });
  document.querySelector('[name="tasa_a_base"]')?.addEventListener('input', recalcularLineas);
  document.querySelector('.cancelar-venta')?.addEventListener('click', () => { window.location.hash = '#/ventas'; });
  formulario.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    if (!estadoVenta.lineas.length) return mostrarToast('Agrega al menos un item a la venta.', 'error');
    try {
      const datos = formatearFormulario(formulario); const tasa = Number(datos.tasa_a_base); const errorTasa = validarTasa(datos.moneda_pago, tasa); if (errorTasa) throw new Error(errorTasa);
      const totalBase = redondear(estadoVenta.lineas.reduce((suma, linea) => suma + Number(linea.subtotal), 0)); const totalOriginal = convertirBaseAMoneda(totalBase, tasa, datos.moneda_pago);
      const venta = await insert('ventas', { fecha: datos.fecha, cliente_id: datos.cliente_id ? Number(datos.cliente_id) : null, metodo_pago_id: Number(datos.metodo_pago_id), moneda_pago: datos.moneda_pago, total_original: totalOriginal, tasa_a_base: tasa, total_en_base: totalBase, notas: datos.notas || '' });
      for (const linea of estadoVenta.lineas) { await insert('venta_detalles', { venta_id: venta.id, tipo_item: linea.tipo_item, item_id: Number(linea.item_id), descripcion: linea.descripcion, cantidad: Number(linea.cantidad), precio_unitario: Number(linea.precio_unitario), subtotal: Number(linea.subtotal) }); if (linea.tipo_item === 'producto') { const producto = estadoVenta.productos.find((item) => item.id === Number(linea.item_id)); if (producto) await update('productos', producto.id, { stock_actual: Math.max(0, Number(producto.stock_actual) - Number(linea.cantidad)) }); } }
      mostrarToast('Venta guardada correctamente.'); window.location.hash = `#/ventas/${venta.id}`;
    } catch (error) { mostrarToast(error.message || 'No se pudo guardar la venta.', 'error'); }
  });
  window.lucide?.createIcons();
}
