/* Reglas de negocio multimoneda: tasa manual, última tasa usada y conversión a COP. */
import { redondear, ultimaTasaUsada } from './db.js';

export function validarTasa(moneda, tasa) {
  const valor = Number(tasa);
  if (moneda === 'COP' && valor !== 1) return 'La tasa COP debe ser 1.';
  if (moneda !== 'COP' && (!Number.isFinite(valor) || valor <= 0)) return 'Escribe una tasa mayor que cero.';
  return '';
}

export function calcularConversion(monto, tasa) {
  return redondear(Number(monto || 0) * Number(tasa || 0));
}

// La tasa se guarda como moneda de pago → COP: 1 USD = 3.200 COP.
// Por eso, cuando el precio base está en COP, el importe a cobrar se divide.
export function convertirBaseAMoneda(montoEnBase, tasa, moneda = 'COP') {
  if (moneda === 'COP') return redondear(montoEnBase);
  // Se guardan hasta 8 decimales para que, al volver a multiplicar por la
  // tasa, la restricción SQL redondeada siga cerrando en el total COP.
  const convertido = Number(montoEnBase || 0) / Number(tasa || 1);
  return Math.round((convertido + Number.EPSILON) * 100000000) / 100000000;
}

export function convertirMonedaABase(monto, tasa) {
  return calcularConversion(monto, tasa);
}

export async function sugerirUltimaTasa(moneda) {
  return ultimaTasaUsada(moneda);
}

export function configurarSelectorMoneda({ selector, campoTasa, botonTasa, onChange }) {
  const actualizar = async () => {
    const moneda = selector.value;
    const tasa = moneda === 'COP' ? 1 : await sugerirUltimaTasa(moneda);
    campoTasa.value = tasa || '';
    campoTasa.readOnly = moneda === 'COP';
    campoTasa.classList.toggle('bg-slate-100', moneda === 'COP');
    botonTasa.classList.toggle('hidden', moneda === 'COP');
    onChange?.(moneda, tasa);
  };
  selector.addEventListener('change', actualizar);
  actualizar();
  botonTasa.addEventListener('click', async () => {
    const tasa = await sugerirUltimaTasa(selector.value);
    if (tasa) campoTasa.value = tasa;
  });
  return actualizar;
}
