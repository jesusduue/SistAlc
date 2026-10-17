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
