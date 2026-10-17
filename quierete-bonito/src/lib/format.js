/* Formateadores compartidos para valores monetarios, números y fechas en español venezolano. */

const formatoNumero = new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const formatoFecha = new Intl.DateTimeFormat('es-VE', { day: '2-digit', month: '2-digit', year: 'numeric' });
const formatoFechaLarga = new Intl.DateTimeFormat('es-VE', { day: '2-digit', month: 'short', year: 'numeric' });

export const numero = (valor) => formatoNumero.format(Number(valor || 0));
export const entero = (valor) => new Intl.NumberFormat('es-VE').format(Number(valor || 0));
export const fecha = (valor) => valor ? formatoFecha.format(new Date(`${String(valor).slice(0, 10)}T12:00:00`)) : '—';
export const fechaLarga = (valor) => valor ? formatoFechaLarga.format(new Date(`${String(valor).slice(0, 10)}T12:00:00`)) : '—';
export const fechaHora = (valor) => valor ? new Intl.DateTimeFormat('es-VE', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(valor)) : '—';
export const mesActual = (valor = new Date()) => new Intl.DateTimeFormat('es-VE', { month: 'long', year: 'numeric' }).format(valor);

export function formatearMoneda(valor, moneda = 'COP', monedas = []) {
  const registro = monedas.find((item) => item.codigo === moneda);
  const simbolo = registro?.simbolo || moneda;
  return `${simbolo} ${numero(valor)}`;
}

export function porcentaje(valor) {
  return `${Number(valor || 0).toFixed(1).replace('.', ',')}%`;
}

export function escapar(valor = '') {
  return String(valor).replace(/[&<>"']/g, (caracter) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[caracter]));
}

export function descargarCSV(nombre, filas) {
  const contenido = filas.map((fila) => fila.map((celda) => `"${String(celda ?? '').replace(/"/g, '""')}"`).join(';')).join('\n');
  const blob = new Blob([`\ufeff${contenido}`], { type: 'text/csv;charset=utf-8;' });
  const enlace = document.createElement('a');
  enlace.href = URL.createObjectURL(blob);
  enlace.download = nombre;
  enlace.click();
  URL.revokeObjectURL(enlace.href);
}
