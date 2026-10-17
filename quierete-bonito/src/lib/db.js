/* Wrapper de persistencia LocalStorage con el espejo del esquema SQL de Quierete Bonito. */

const PREFIJO = 'belleza_admin_';
const MARCA_SEED = `${PREFIJO}seed_v1`;

export const TABLAS = {
  monedas: 'codigo',
  metodos_pago: 'id',
  clientes: 'id',
  categorias_servicios: 'id',
  categorias_productos: 'id',
  servicios: 'id',
  productos: 'id',
  citas: 'id',
  ventas: 'id',
  venta_detalles: 'id',
  categorias_gastos: 'id',
  gastos: 'id'
};

const ahoraISO = () => new Date().toISOString();
const clonar = (valor) => JSON.parse(JSON.stringify(valor));
export const redondear = (valor) => Math.round((Number(valor) + Number.EPSILON) * 100) / 100;
const clave = (tabla) => `${PREFIJO}${tabla}`;
const obtener = (tabla) => JSON.parse(localStorage.getItem(clave(tabla)) || '[]');
const guardar = (tabla, registros) => localStorage.setItem(clave(tabla), JSON.stringify(registros));
const siguienteId = (tabla) => {
  const registros = obtener(tabla).filter((registro) => typeof registro.id === 'number');
  return registros.reduce((mayor, registro) => Math.max(mayor, registro.id), 0) + 1;
};

function validarTabla(tabla) {
  if (!TABLAS[tabla]) throw new Error(`Tabla no permitida: ${tabla}`);
}

function validarRestricciones(tabla, registro) {
  if (tabla === 'ventas') {
    const esperado = redondear(Number(registro.total_original) * Number(registro.tasa_a_base));
    if (esperado !== redondear(registro.total_en_base)) throw new Error('La conversión de la venta no coincide con su tasa.');
  }
  if (tabla === 'gastos') {
    const esperado = redondear(Number(registro.monto_original) * Number(registro.tasa_a_base));
    if (esperado !== redondear(registro.monto_en_base)) throw new Error('La conversión del gasto no coincide con su tasa.');
  }
  if (tabla === 'venta_detalles') {
    const esperado = redondear(Number(registro.cantidad) * Number(registro.precio_unitario));
    if (esperado !== redondear(registro.subtotal)) throw new Error('El subtotal no coincide con cantidad × precio.');
    if (!['producto', 'servicio'].includes(registro.tipo_item)) throw new Error('Tipo de item no válido.');
  }
}

export async function getAll(tabla) {
  validarTabla(tabla);
  return clonar(obtener(tabla));
}

export async function getById(tabla, id) {
  validarTabla(tabla);
  const pk = TABLAS[tabla];
  return clonar(obtener(tabla).find((registro) => String(registro[pk]) === String(id)) || null);
}

export async function insert(tabla, datos) {
  validarTabla(tabla);
  const registros = obtener(tabla);
  const pk = TABLAS[tabla];
  const registro = { ...datos };
  if (pk === 'id' && (registro.id === undefined || registro.id === null)) registro.id = siguienteId(tabla);
  if (registro[pk] === undefined || registro[pk] === null || registro[pk] === '') throw new Error(`Falta la clave primaria de ${tabla}.`);
  if (registros.some((item) => String(item[pk]) === String(registro[pk]))) throw new Error(`Ya existe un registro con esa clave en ${tabla}.`);
  validarRestricciones(tabla, registro);
  registros.push(clonar(registro));
  guardar(tabla, registros);
  return clonar(registro);
}

export async function update(tabla, id, cambios) {
  validarTabla(tabla);
  const registros = obtener(tabla);
  const pk = TABLAS[tabla];
  const indice = registros.findIndex((registro) => String(registro[pk]) === String(id));
  if (indice < 0) throw new Error('Registro no encontrado.');
  const actualizado = { ...registros[indice], ...cambios, [pk]: registros[indice][pk] };
  validarRestricciones(tabla, actualizado);
  registros[indice] = clonar(actualizado);
  guardar(tabla, registros);
  return clonar(actualizado);
}

export async function eliminar(tabla, id) {
  validarTabla(tabla);
  const registros = obtener(tabla);
  const pk = TABLAS[tabla];
  const restantes = registros.filter((registro) => String(registro[pk]) !== String(id));
  if (restantes.length === registros.length) throw new Error('Registro no encontrado.');
  guardar(tabla, restantes);
  return true;
}

export const deleteRecord = eliminar;

export async function seedInitialData() {
  if (localStorage.getItem(MARCA_SEED)) return;
  const hoy = new Date();
  const fecha = (dias = 0) => {
    const valor = new Date(hoy);
    valor.setDate(valor.getDate() + dias);
    return valor.toISOString().slice(0, 10);
  };
  const fechaHora = (dias = 0, hora = 10) => `${fecha(dias)}T${String(hora).padStart(2, '0')}:00:00`;

  const semillas = {
    monedas: [
      { codigo: 'COP', nombre: 'Peso colombiano', simbolo: '$', activa: true },
      { codigo: 'VES', nombre: 'Bolívar digital', simbolo: 'Bs', activa: true },
      { codigo: 'USD', nombre: 'Dólar estadounidense', simbolo: 'US$', activa: true }
    ],
    metodos_pago: [
      { id: 1, nombre: 'Efectivo', moneda: 'VES', activo: true },
      { id: 2, nombre: 'Pago móvil', moneda: 'VES', activo: true },
      { id: 3, nombre: 'Transferencia', moneda: 'USD', activo: true },
      { id: 4, nombre: 'Efectivo COP', moneda: 'COP', activo: true }
    ],
    categorias_servicios: [
      { id: 1, nombre: 'Manos y pies', descripcion: 'Manicure y pedicure' },
      { id: 2, nombre: 'Cabello', descripcion: 'Tratamientos y peinados' },
      { id: 3, nombre: 'Mirada', descripcion: 'Cejas y pestañas' }
    ],
    categorias_productos: [
      { id: 1, nombre: 'Perfumería', descripcion: 'Fragancias para cada ocasión' },
      { id: 2, nombre: 'Cabello', descripcion: 'Cuidado y styling' },
      { id: 3, nombre: 'Joyería', descripcion: 'Accesorios seleccionados' }
    ],
    categorias_gastos: [
      { id: 1, nombre: 'Operación', tipo: 'operativo' },
      { id: 2, nombre: 'Compras', tipo: 'inventario' },
      { id: 3, nombre: 'Servicios', tipo: 'servicios' }
    ],
    clientes: [
      { id: 1, nombre: 'María Fernanda Rojas', telefono: '+58 414 555 0198', email: 'maria.rojas@email.com', notas: 'Prefiere citas en la tarde.', creado_en: `${fecha(-45)}T09:30:00` },
      { id: 2, nombre: 'Valentina Pérez', telefono: '+58 424 555 0132', email: 'valentina.p@email.com', notas: 'Cliente frecuente.', creado_en: `${fecha(-20)}T11:20:00` }
    ],
    servicios: [
      { id: 1, nombre: 'Manicure semipermanente', categoria_id: 1, precio_base: 85000, moneda_precio: 'COP', activo: true, creado_en: `${fecha(-50)}T10:00:00` },
      { id: 2, nombre: 'Alisado orgánico', categoria_id: 2, precio_base: 320000, moneda_precio: 'COP', activo: true, creado_en: `${fecha(-50)}T10:00:00` },
      { id: 3, nombre: 'Lifting de pestañas', categoria_id: 3, precio_base: 120000, moneda_precio: 'COP', activo: true, creado_en: `${fecha(-50)}T10:00:00` }
    ],
    productos: [
      { id: 1, nombre: 'Perfume Flor de Cerezo', categoria_id: 1, precio_base: 145000, moneda_precio: 'COP', stock_actual: 8, stock_minimo: 3, activo: true, creado_en: `${fecha(-40)}T10:00:00` },
      { id: 2, nombre: 'Aceite nutritivo capilar', categoria_id: 2, precio_base: 68000, moneda_precio: 'COP', stock_actual: 2, stock_minimo: 4, activo: true, creado_en: `${fecha(-40)}T10:00:00` },
      { id: 3, nombre: 'Aros dorados minimal', categoria_id: 3, precio_base: 95000, moneda_precio: 'COP', stock_actual: 12, stock_minimo: 2, activo: true, creado_en: `${fecha(-40)}T10:00:00` }
    ],
    citas: [
      { id: 1, cliente_id: 1, servicio_id: 1, fecha_hora: fechaHora(1, 14), duracion_minutos: 90, estado: 'confirmada', notas: 'Traer diseño de referencia.', creado_en: `${fecha(-2)}T09:00:00` },
      { id: 2, cliente_id: 2, servicio_id: 3, fecha_hora: fechaHora(2, 16), duracion_minutos: 75, estado: 'pendiente', notas: '', creado_en: `${fecha(-1)}T10:30:00` }
    ],
    ventas: [
      { id: 1, fecha: fecha(-1), cliente_id: 1, metodo_pago_id: 2, moneda_pago: 'VES', total_original: 1050, tasa_a_base: 380, total_en_base: 399000, notas: 'Venta de servicio y producto.' },
      { id: 2, fecha: fecha(-3), cliente_id: 2, metodo_pago_id: 3, moneda_pago: 'USD', total_original: 95, tasa_a_base: 4200, total_en_base: 399000, notas: 'Cliente frecuente.' }
    ],
    venta_detalles: [
      { id: 1, venta_id: 1, tipo_item: 'servicio', item_id: 1, descripcion: 'Manicure semipermanente', cantidad: 1, precio_unitario: 85000, subtotal: 85000 },
      { id: 2, venta_id: 1, tipo_item: 'producto', item_id: 2, descripcion: 'Aceite nutritivo capilar', cantidad: 1, precio_unitario: 68000, subtotal: 68000 },
      { id: 3, venta_id: 2, tipo_item: 'servicio', item_id: 3, descripcion: 'Lifting de pestañas', cantidad: 1, precio_unitario: 120000, subtotal: 120000 },
      { id: 4, venta_id: 2, tipo_item: 'producto', item_id: 3, descripcion: 'Aros dorados minimal', cantidad: 1, precio_unitario: 95000, subtotal: 95000 }
    ],
    gastos: [
      { id: 1, concepto: 'Compra de productos capilares', categoria_id: 2, monto_original: 200, moneda: 'USD', tasa_a_base: 4200, monto_en_base: 840000, fecha: fecha(-4), metodo_pago_id: 3, comprobante_url: '', notas: 'Reposición de inventario.' },
      { id: 2, concepto: 'Servicio de internet', categoria_id: 3, monto_original: 180000, moneda: 'COP', tasa_a_base: 1, monto_en_base: 180000, fecha: fecha(-6), metodo_pago_id: 4, comprobante_url: '', notas: '' }
    ]
  };

  for (const [tabla, registros] of Object.entries(semillas)) guardar(tabla, registros);
  localStorage.setItem(MARCA_SEED, 'true');
}

export async function ultimaTasaUsada(moneda) {
  if (moneda === 'COP') return 1;
  const [ventas, gastos] = await Promise.all([getAll('ventas'), getAll('gastos')]);
  const movimientos = [
    ...ventas.filter((venta) => venta.moneda_pago === moneda).map((venta) => ({ fecha: venta.fecha, tasa: venta.tasa_a_base })),
    ...gastos.filter((gasto) => gasto.moneda === moneda).map((gasto) => ({ fecha: gasto.fecha, tasa: gasto.tasa_a_base }))
  ].sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
  return movimientos[0]?.tasa || null;
}

export async function resetDemoData() {
  Object.keys(TABLAS).forEach((tabla) => localStorage.removeItem(clave(tabla)));
  localStorage.removeItem(MARCA_SEED);
  await seedInitialData();
}
