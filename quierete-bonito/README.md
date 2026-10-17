# Quierete Bonito — Administración

SPA administrativa de alta fidelidad para un negocio de belleza en Venezuela. Está construida con HTML5, Tailwind CSS por CDN y JavaScript vanilla con módulos ES6. La demo funciona inmediatamente en el navegador: no necesita backend ni base de datos externa.

## Ejecutar en local

```bash
cd quierete-bonito
python3 -m http.server 4173
```

Luego abre <http://localhost:4173>.

> Los módulos ES6 deben servirse por HTTP; no abras `index.html` directamente con `file://`.

## Acceso de demo

- Correo: `admin@belleza.com`
- Contraseña: `admin123`

Al primer inicio se cargan datos de ejemplo en `localStorage`: monedas COP/VES/USD, métodos de pago, categorías, clientes, servicios, productos y ventas.

## Persistencia y modelo

`src/lib/db.js` implementa un wrapper async/await sobre localStorage con `getAll`, `getById`, `insert`, `update` y `delete`. Cada colección utiliza claves `belleza_admin_*` y respeta el espejo del esquema definido para migrar la lógica de negocio sin cambios.

La moneda de reportes es COP. Las ventas y gastos conservan el monto original, moneda, tasa manual y total convertido. El wrapper valida los redondeos equivalentes a las restricciones SQL:

- `ROUND(total_original * tasa_a_base, 2) === total_en_base`
- `ROUND(monto_original * tasa_a_base, 2) === monto_en_base`
- `ROUND(cantidad * precio_unitario, 2) === subtotal`

### Regla de conversión de ventas

Los precios de servicios y productos están definidos siempre en COP. `tasa_a_base` significa cuántos COP equivalen a una unidad de la moneda recibida: por ejemplo, `1 USD = 3.200 COP` o `1 VES = 3,1 COP`. Por eso, al seleccionar USD o VES, el total a pagar se calcula como `total_en_base / tasa_a_base`; no se altera el precio del catálogo. La venta guarda `total_original` en la moneda recibida y `total_en_base` en COP, que es el valor usado para ganancias y reportes. Para convertir un monto recibido nuevamente a COP se aplica `monto_original * tasa_a_base`.

## Migración futura a Supabase/PostgreSQL

1. Mantener las mismas funciones públicas del wrapper (`getAll`, `getById`, `insert`, `update`, `delete`).
2. Reemplazar únicamente el adaptador de lectura/escritura de `src/lib/db.js` por consultas a tablas con los mismos nombres y campos.
3. Mover autenticación demo a Supabase Auth y sustituir `AuthProvider.js` por la sesión real.
4. Conservar `format.js`, `tasas.js`, páginas y validaciones de negocio: no dependen del medio de persistencia.
5. Añadir RLS y políticas por usuario antes de desplegar datos reales.

## Estructura

```text
quierete-bonito/
├── index.html
├── vercel.json
├── public/manus-routes.json
└── src/
    ├── router.js
    ├── styles.css
    ├── auth/{AuthProvider.js,RutaProtegida.js}
    ├── components/{index.js,Layout.js}
    ├── lib/{db.js,format.js,tasas.js}
    └── pages/{Login.js,Dashboard.js,NuevaVenta.js,Pages.js}
```
