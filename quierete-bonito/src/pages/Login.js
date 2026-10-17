/* Pantalla de acceso demo con credenciales hardcodeadas según el alcance solicitado. */
import { iniciarSesion } from '../auth/AuthProvider.js';
import { boton, campo, mostrarToast } from '../components/index.js';

export function renderLogin() {
  return `<div class="bg-grid flex min-h-screen items-center justify-center p-4"><div class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white bg-white shadow-soft lg:grid-cols-[1.05fr_.95fr]"><div class="brand-gradient relative hidden overflow-hidden p-10 text-white lg:flex lg:flex-col lg:justify-between"><div class="absolute -right-16 -top-16 h-64 w-64 rounded-full border-[32px] border-white/10"></div><div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full border-[42px] border-white/10"></div><div><span class="mb-10 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-xl font-bold">QB</span><p class="mb-3 text-sm font-semibold uppercase tracking-[.2em] text-white/70">Beauty admin</p><h1 class="max-w-sm text-5xl font-semibold leading-[1.04]">Todo lo bonito, en un solo lugar.</h1><p class="mt-6 max-w-sm text-base leading-7 text-white/80">Agenda, clientes, ventas y reportes para que tu negocio crezca con intención.</p></div><p class="text-sm text-white/60">Quierete Bonito · Venezuela</p></div><div class="p-7 sm:p-10"><div class="mb-10 lg:hidden"><span class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FBEAF3] text-lg font-bold text-[#CC498F]">QB</span><p class="text-xs font-semibold uppercase tracking-[.18em] text-[#CC498F]">Quierete Bonito</p></div><p class="text-sm font-semibold text-[#CC498F]">Bienvenida de vuelta</p><h2 class="mt-2 text-3xl font-bold tracking-tight">Inicia sesión</h2><p class="mt-2 text-sm text-slate-500">Accede a tu panel de administración.</p><form id="login-form" class="mt-8 space-y-5">${campo({ label: 'Correo electrónico', name: 'email', type: 'email', value: 'admin@belleza.com', required: true })}${campo({ label: 'Contraseña', name: 'password', type: 'password', value: 'admin123', required: true })}${boton('Entrar al panel', { type: 'submit', tamano: 'lg', clase: 'w-full' })}</form><div class="mt-6 rounded-2xl bg-[#FDF5F9] p-4 text-xs text-slate-500"><b class="text-[#A92E6D]">Demo rápida:</b> admin@belleza.com · admin123</div></div></div></div>`;
}

export function activarLogin() {
  document.getElementById('login-form')?.addEventListener('submit', (evento) => {
    evento.preventDefault();
    const datos = Object.fromEntries(new FormData(evento.currentTarget).entries());
    try { iniciarSesion(datos.email, datos.password); window.location.hash = '#/'; } catch (error) { mostrarToast(error.message, 'error'); }
  });
}
