/* Autenticación simulada de la demo; preparada para sustituirse por Supabase Auth. */
const CLAVE_SESION = 'belleza_admin_session';
const CREDENCIALES = { user: 'admin@belleza.com', password: 'admin123' };

export function obtenerSesion() {
  try { return JSON.parse(localStorage.getItem(CLAVE_SESION) || 'null'); } catch { return null; }
}

export function iniciarSesion(email, password) {
  if (email !== CREDENCIALES.user || password !== CREDENCIALES.password) throw new Error('Correo o contraseña incorrectos.');
  const sesion = { user: email, token: 'demo-token', loginTime: Date.now() };
  localStorage.setItem(CLAVE_SESION, JSON.stringify(sesion));
  return sesion;
}

export function cerrarSesion() {
  localStorage.removeItem(CLAVE_SESION);
  window.location.hash = '#/login';
}

export function estaAutenticado() { return Boolean(obtenerSesion()?.token); }
