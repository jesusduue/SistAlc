# PROMPT PROFESIONAL PARA CODEX — Sistema de Alcaldía (SistAlc)

## CONTEXTO DEL PROYECTO

Eres un desarrollador senior PHP. Tu tarea es completar al 100% el sistema **SistAlc**, un sistema integral de gestión municipal para la Alcaldía del Municipio García de Hevia. El sistema corre sobre **WAMP (Windows + Apache + MySQL + PHP)** con Bootstrap 5, jQuery/Vanilla JS, Chart.js y SweetAlert2.

---

## ARQUITECTURA ACTUAL

```
SistAlc/
├── index.php                          # Login (entry point)
├── Backend/
│   ├── baseDeDatos/
│   │   ├── alcaldia.sql               # DDL (CREATE TABLE, EVENT)
│   │   └── datos.sql                  # Seed data
│   ├── clases/                        # Modelos (extends Utilidad)
│   │   ├── Utilidad.php               # Clase base: conexión mysqli, asignar_valor(), ejecutar()
│   │   ├── Usuario.php, Rol.php, contribuyente.php
│   │   ├── Factura.php, DetalleImpuesto.php, TipoImpuesto.php
│   │   ├── patente.php, licencia.php
│   │   ├── CedulaCatastral.php, SolvenciaMunicipal.php, SolvenciaComercial.php
│   │   ├── Inmueble.php, Linderos.php, DocumentoLegal.php
│   │   ├── ReporteFinanciero.php, dashboard.php, auditoria.php
│   └── controlador/                   # Controllers (switch-case sobre $_REQUEST['accion'])
│       ├── UsuarioController.php, contribuyenteController.php
│       ├── facturaController.php, tipoImpuestoController.php
│       ├── patenteController.php, licenciaController.php
│       ├── cedulaCatastralController.php, solvenciaMunicipalController.php
│       ├── solvenciaComercialController.php
│       ├── inmuebleController.php, linderosController.php, documentoLegalController.php
│       ├── dashboardController.php, reportesFinancierosController.php
├── Frontend/
│   ├── estilos/
│   │   ├── css/    # Bootstrap 5 local + CSS por módulo (layouts/, dashboard/, facturacion/, etc.)
│   │   ├── js/     # Bootstrap bundle local
│   │   └── img/    # Logos institucionales
│   └── Vistas/
│       ├── dashboard.php
│       ├── layouts/ (header.php, footer.php, topbar.php, sidebar.php, paths.php, proteger_vista.php)
│       ├── usuarios/, contribuyentes/, catastro/, patentes/, licencias/, comercio/
│       ├── facturacion/ (gestion_facturas.php, reporte_ingresos.php)
│       ├── impuestos/, reportes/ (vacío)
│       └── js/ (por módulo: facturacion/, contribuyentes/, licencias/, patentes/, impuestos/)
```

---

## PATRONES DE CÓDIGO OBLIGATORIOS

### Backend — Clase Modelo (extends Utilidad)
```php
<?php
require_once 'Utilidad.php';
class NombreEntidad extends Utilidad {
    public $id_xxx;
    public $campo1;
    public $est_registro;

    public function consultar_activos() { /* WHERE est_registro = 'A' ORDER BY ... */ }
    public function consultar_todos()   { /* sin filtro */ }
    public function consultar_por_id(int $id) { /* prepared stmt, fetch_assoc */ }
    public function registrar(array $datos) { /* INSERT, prepared stmt, return execute() */ }
    public function modificar(array $datos) { /* UPDATE ... WHERE id = ? */ }
    public function cambiar_estado(int $id, string $estado) { /* UPDATE est_registro */ }
    public function numero_existe(string $num, ?int $excluir = null) { /* validación unicidad */ }
    public function buscar($termino) { /* LIKE %term% en campos relevantes */ }
}
```

### Backend — Controller (switch-case)
```php
<?php
session_start();
require_once '../clases/Entidad.php';
require_once '../clases/auditoria.php';
if (!isset($_REQUEST['accion'])) { header('Location: ../../index.php'); exit(); }
$accion = $_REQUEST['accion'];
$modelo = new Entidad();
$auditoria = new Auditoria();
switch ($accion) {
    case 'registrar': /* validar, registrar, auditar, $_SESSION['mensaje'] */ break;
    case 'modificar': /* ... */ break;
    case 'cambiar_estado': /* $_GET['id'], $_GET['nuevo_estado'] */ break;
    case 'eliminar': /* borrado lógico o físico */ break;
    default: header('Location: ../../index.php'); exit();
}
header('Location: ../../Frontend/Vistas/modulo/vista.php'); exit();
```

### Frontend — Vista PHP
- Incluye `layouts/proteger_vista.php` y llama `restringirAcceso('modulo', 'read')`
- Incluye `layouts/header.php` + CSS del módulo
- Incluye `layouts/topbar.php` + `layouts/sidebar.php`
- Tabla Bootstrap con datos de PHP (`<?php foreach ... ?>`)
- Modal Bootstrap para registrar/actualizar
- Incluye `layouts/footer.php` + JS del módulo
- SweetAlert2 para confirmaciones y alertas

### Frontend — JS por módulo
- `modalEntidad.js` → limpiar/poblar modal para crear/editar
- `alertEntidad.js` → confirmaciones SweetAlert2 (eliminar, cambiar estado)
- Formularios POST a `Backend/controlador/XxxController.php` con `<input type="hidden" name="accion" value="...">`

### Base de datos
- `est_registro CHAR(1) DEFAULT 'A'` para borrado lógico en todas las tablas
- Estados de ciclo de vida: `'S'` Solicitada, `'P'` Pagada, `'V'` Vencida (cedulas, solvencias, licencias)
- `est_pago`: `'E'` En espera, `'P'` Pagada, `'A'` Anulada (facturas)
- Event scheduler diario actualiza estados vencidos automáticamente
- `detalle_impuesto` es la tabla pivote central que vincula facturas con documentos

### Permisos por rol
Definidos en `proteger_vista.php` con mapa estático:
- **Administrador**: acceso total (`*`)
- **Jefe de Catastro**: contribuyentes, cédulas, solvencias municipales, documentos, linderos, inmuebles
- **Jefe de Patentes**: patentes, licencias, solvencias comerciales, contribuyentes
- **Tesorero**: todo excepto usuarios
- **Secretaria de Catastro/Patentes**: solo crear y leer en sus módulos
- **Calculista**: solo lectura en contribuyentes y patentes

---

## MÓDULOS FALTANTES — IMPLEMENTAR COMPLETOS

### 1. MÓDULO VEHÍCULOS FISCALES (tablas ya existen en SQL)
**Tablas**: `vehiculo_fiscal`, `patente_vehiculo_fiscal`

Crear:
- `Backend/clases/VehiculoFiscal.php` — CRUD completo
- `Backend/clases/PatenteVehiculoFiscal.php` — CRUD + estados (e/v/a)
- `Backend/controlador/vehiculoFiscalController.php`
- `Backend/controlador/patenteVehiculoFiscalController.php`
- `Frontend/Vistas/vehiculos/gestion_vehiculos.php` — lista + modal CRUD
- `Frontend/Vistas/vehiculos/gestion_patentes_vehiculos.php` — lista + modal
- `Frontend/Vistas/js/vehiculos/modalVehiculo.js`
- `Frontend/Vistas/js/vehiculos/alertVehiculo.js`
- `Frontend/estilos/css/vehiculos/vehiculos.css`
- Agregar enlace en `sidebar.php` y `topbar.php` bajo nueva sección "Vehículos"
- Agregar permisos en `proteger_vista.php`

### 2. MÓDULO REPORTES COMPLETO (carpeta `reportes/` existe vacía)

Crear vista `Frontend/Vistas/reportes/centro_reportes.php` como hub central con:

#### 2.1 Reporte Diario de Ingresos (ya existe parcial en `reporte_ingresos.php`)
- Selector de fecha, tabla de movimientos (pagados + anulados), totales
- Vista por contribuyente y vista por rubro
- Botón imprimir/exportar PDF

#### 2.2 Reporte Mensual de Ingresos
- Selector año/mes, gráfico de barras por rubro
- Tabla resumen con totales por tipo impuesto
- Comparativa mes actual vs mes anterior (porcentaje variación)

#### 2.3 Reporte Anual de Ingresos
- Gráfico de línea 12 meses + tabla resumen
- Comparación interanual (año actual vs anterior)
- Total acumulado y proyección lineal

#### 2.4 Reporte de Recaudación por Rubro
- Filtro por rango de fechas + selector de rubro
- Total recaudado, lista de contribuyentes que pagaron ese rubro
- Modal "Ver Detalles" con facturas individuales

#### 2.5 Reporte de Morosidad
- Contribuyentes con facturas pendientes (`est_pago = 'E'`)
- Días de mora, monto adeudado, datos de contacto
- Filtros por antigüedad (30, 60, 90+ días)

#### 2.6 Reporte de Documentos por Vencer
- Licencias, solvencias municipales, solvencias comerciales, cédulas catastrales
- Filtro por días (próximos 15, 30, 60, 90 días)
- Indicadores de urgencia (colores semáforo)
- Acción rápida para notificar/renovar

#### 2.7 Reporte de Patentes y Comercios
- Listado de patentes activas/inactivas por período
- Estado de licencias y solvencias asociadas
- Resumen por rubro comercial

#### 2.8 Reporte de Auditoría
- Filtros: usuario, módulo, acción, rango de fechas
- Tabla paginada con detalles expandibles (metadata JSON)
- Exportar a CSV/Excel

**Backend**: Agregar métodos en `ReporteFinanciero.php` o crear nuevas clases:
- `ReporteMorosidad.php`
- `ReporteVencimientos.php`
- `ReportePatentes.php`
- Controller: ampliar `reportesFinancierosController.php` con nuevas acciones

### 3. IMPRESIÓN DE DOCUMENTOS OFICIALES

#### 3.1 Impresión de Factura
- `Frontend/Vistas/facturacion/imprimir_factura.php`
- Formato oficio: logo, datos alcaldía, datos contribuyente, tabla de rubros, totales
- Usa CSS `@media print` (sin sidebar/topbar)

#### 3.2 Impresión de Solvencia Municipal
- `Frontend/Vistas/catastro/imprimir_solvencia_municipal.php`
- Formato certificado con número, datos propietario, inmueble, vigencia

#### 3.3 Impresión de Solvencia Comercial
- `Frontend/Vistas/comercio/imprimir_solvencia_comercial.php`
- Datos de patente, contribuyente, vigencia

#### 3.4 Impresión de Cédula Catastral
- `Frontend/Vistas/catastro/imprimir_cedula_catastral.php`
- Datos completos: inmueble, linderos, documento legal, valor, vigencia

#### 3.5 Impresión de Licencia (ya existe `imprimirLicencia.php` — revisar y estandarizar)

### 4. GESTIÓN DE ROLES (CRUD completo)
- `Frontend/Vistas/usuarios/gestion_roles.php` — tabla + modal para crear/editar roles
- `Frontend/Vistas/js/usuarios/modalRol.js`
- Enlace en sidebar bajo "Usuarios"
- La clase `Rol.php` ya existe pero le falta método `modificar()`

### 5. PERFIL DE USUARIO
- `Frontend/Vistas/usuarios/perfil.php` — ver datos propios, cambiar contraseña
- Método `cambiar_clave($id, $claveActual, $claveNueva)` en `Usuario.php`
- Enlace en topbar (junto al chip de usuario)

### 6. ACTIVIDAD RECIENTE (vista completa)
- `Frontend/Vistas/usuarios/actividadReciente.php` ya existe (1128 bytes) — completar:
- Tabla paginada con búsqueda en tiempo real
- Filtros por usuario, módulo, fecha
- Modal detalle con metadata JSON formateada
- Enlace en sidebar

### 7. NOTIFICACIONES DE VENCIMIENTO
- Sistema de alertas en el dashboard para documentos próximos a vencer
- Badge con contador en el topbar
- Método `documentosProximosAVencer(int $dias)` en nueva clase o en Dashboard

### 8. EXPORTACIÓN DE DATOS
- Botón "Exportar" en cada tabla de gestión
- Formatos: CSV y PDF (usar biblioteca TCPDF o FPDF incluida localmente)
- Para cada módulo: contribuyentes, patentes, licencias, facturas, etc.

### 9. BÚSQUEDA GLOBAL
- Campo de búsqueda en topbar que busca en contribuyentes, patentes, facturas
- Resultados en dropdown con enlaces directos
- Endpoint AJAX en nuevo controller `busquedaGlobalController.php`

### 10. DASHBOARD — MEJORAS
- Agregar cards faltantes: total contribuyentes activos, total patentes activas
- Sección "Actividad reciente" con lista de las últimas 10 acciones (ya existe la data)
- Widget de documentos próximos a vencer con enlace al reporte
- Gráfico comparativo año actual vs anterior

---

## MEJORAS DE CALIDAD DE CÓDIGO

1. **Validaciones del lado servidor**: verificar campos vacíos, formatos (RIF, email, teléfono) en TODOS los controllers
2. **Validaciones del lado cliente**: agregar validación HTML5 + JS en todos los formularios
3. **Manejo de errores consistente**: try-catch en todos los controllers, mensajes en `$_SESSION['mensaje']`
4. **Auditoría completa**: asegurar que TODAS las acciones CRUD registran en `log_actividad`
5. **CSRF tokens**: agregar token de sesión en todos los formularios POST
6. **Paginación**: implementar paginación server-side en tablas con muchos registros
7. **Búsqueda en tiempo real**: implementar en módulos de catastro y comercio (ya existe en contribuyentes y patentes)

---

## REGLAS CRÍTICAS

1. **NO cambiar la estructura de carpetas existente** — solo agregar archivos nuevos
2. **NO modificar la clase Utilidad.php** a menos que sea estrictamente necesario
3. **Mantener el idioma español** en toda la interfaz, comentarios y mensajes
4. **Usar sentencias preparadas (prepared statements)** en TODAS las consultas SQL
5. **Respetar el patrón de nombres**: tablas en snake_case, clases en PascalCase, archivos PHP de clases en PascalCase o camelCase según lo existente
6. **Bootstrap 5 local** (NO usar CDN para Bootstrap) — SweetAlert2 y Font Awesome sí pueden ser CDN
7. **Cada vista** debe incluir `proteger_vista.php` y llamar `restringirAcceso()`
8. **Cada controller** debe registrar auditoría con `Auditoria::registrarActividad()`
9. **CSS por módulo**: crear archivo CSS dedicado en `Frontend/estilos/css/[modulo]/`
10. **JS por módulo**: crear archivos en `Frontend/Vistas/js/[modulo]/`
11. **Mantener consistencia visual**: usar las clases CSS existentes (`alc-page`, `alc-page-header`, `kpi-card`, `chart-card`, etc.)
12. **Todos los reportes** deben tener opción de impresión con `@media print`
13. **La base de datos es `alcaldiaV2`** — conexión en `Utilidad.php`

---

## ORDEN DE IMPLEMENTACIÓN SUGERIDO

1. Completar CRUD de Roles (backend + frontend)
2. Perfil de usuario + cambio de contraseña
3. Módulo Vehículos Fiscales completo
4. Impresión de documentos (factura, solvencias, cédula catastral)
5. Centro de Reportes con todos los sub-reportes
6. Actividad reciente (vista completa)
7. Notificaciones de vencimiento + badge topbar
8. Búsqueda global
9. Exportación CSV/PDF
10. Mejoras de dashboard
11. Validaciones y seguridad (CSRF, validaciones server/client)
12. Paginación y búsqueda en módulos faltantes

---

## RESULTADO ESPERADO

Al finalizar, el sistema debe ser un ERP municipal completo donde:
- Todo CRUD funciona con auditoría
- Todos los documentos se pueden imprimir en formato oficial
- Los reportes financieros cubren diario/mensual/anual/por rubro/morosidad
- Los vencimientos se monitorean proactivamente
- El flujo es intuitivo: **Contribuyente → Patente → Licencia → Factura → Reporte**
- Y para catastro: **Contribuyente → Inmueble + Linderos + Documento → Cédula Catastral → Solvencia Municipal → Factura**
- El sistema de permisos restringe acceso por rol en cada módulo
- Toda acción queda registrada en el log de auditoría
