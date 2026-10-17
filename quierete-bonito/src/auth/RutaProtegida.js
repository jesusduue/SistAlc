/* Guardia de navegación: redirige al login cuando no existe una sesión demo válida. */
import { estaAutenticado } from './AuthProvider.js';

export function protegerRuta() {
  if (!estaAutenticado()) {
    window.location.hash = '#/login';
    return false;
  }
  return true;
}
