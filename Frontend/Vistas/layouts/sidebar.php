<?php
require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/proteger_vista.php';

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$puedeVerUsuarios = puedeRealizarAccion('usuarios', 'read');
$puedeVerRoles = puedeRealizarAccion('roles', 'read');
$puedeVerVehiculos = puedeRealizarAccion('vehiculos', 'read');
$puedeVerPatentesVehiculos = puedeRealizarAccion('patentes_vehiculos', 'read');
$usuariosActivo = str_contains($requestUri, '/Vistas/usuarios/gestion_usuarios.php');
$rolesActivo = str_contains($requestUri, '/Vistas/usuarios/gestion_roles.php');
$vehiculosActivo = str_contains($requestUri, '/Vistas/vehiculos/gestion_vehiculos.php');
$patentesVehiculosActivo = str_contains($requestUri, '/Vistas/vehiculos/gestion_patentes_vehiculos.php');
?>
<aside class="alc-sidebar" aria-label="Menu lateral del sistema">
    <div class="alc-sidebar-inner">
        <a href="<?= BASE_URL ?>/Vistas/dashboard.php" class="alc-sidebar-brand">
            <span class="alc-sidebar-brand-icon"><i class="fas fa-landmark"></i></span>
            <span>
                <strong>Alcaldia</strong>
                <small>Gestion municipal</small>
            </span>
        </a>

        <nav class="alc-sidebar-nav">
            <a href="<?= BASE_URL ?>/Vistas/dashboard.php" class="<?= str_contains($requestUri, '/Vistas/dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-house"></i><span>Inicio</span></a>

            <?php if ($puedeVerUsuarios || $puedeVerRoles): ?>
                <p class="alc-sidebar-group">Usuarios</p>
                <?php if ($puedeVerUsuarios): ?>
                    <a href="<?= BASE_URL ?>/Vistas/usuarios/gestion_usuarios.php" class="<?= $usuariosActivo ? 'active' : ''; ?>"><i class="fas fa-users-cog"></i><span>Gestion de usuarios</span></a>
                <?php endif; ?>
                <?php if ($puedeVerRoles): ?>
                    <a href="<?= BASE_URL ?>/Vistas/usuarios/gestion_roles.php" class="<?= $rolesActivo ? 'active' : ''; ?>"><i class="fas fa-user-tag"></i><span>Gestion de roles</span></a>
                <?php endif; ?>
            <?php endif; ?>

            <p class="alc-sidebar-group">Catastro</p>
            <a href="<?= BASE_URL ?>/Vistas/contribuyentes/gestion_contribuyentes.php" class="<?= str_contains($requestUri, '/Vistas/contribuyentes/') ? 'active' : ''; ?>"><i class="fas fa-id-card"></i><span>Contribuyentes</span></a>
            <a href="<?= BASE_URL ?>/Vistas/catastro/gestion_inmuebles.php" class="<?= str_contains($requestUri, '/gestion_inmuebles.php') ? 'active' : ''; ?>"><i class="fas fa-house"></i><span>Inmuebles</span></a>
            <a href="<?= BASE_URL ?>/Vistas/catastro/gestion_linderos.php" class="<?= str_contains($requestUri, '/gestion_linderos.php') ? 'active' : ''; ?>"><i class="fas fa-border-all"></i><span>Linderos</span></a>
            <a href="<?= BASE_URL ?>/Vistas/catastro/gestion_documentos.php" class="<?= str_contains($requestUri, '/gestion_documentos.php') ? 'active' : ''; ?>"><i class="fas fa-file-contract"></i><span>Documentos</span></a>
            <a href="<?= BASE_URL ?>/Vistas/catastro/gestion_cedulas.php" class="<?= str_contains($requestUri, '/gestion_cedulas.php') ? 'active' : ''; ?>"><i class="fas fa-file-signature"></i><span>Cedulas</span></a>
            <a href="<?= BASE_URL ?>/Vistas/catastro/gestion_solvencias_municipales.php" class="<?= str_contains($requestUri, '/gestion_solvencias_municipales.php') ? 'active' : ''; ?>"><i class="fas fa-scroll"></i><span>Solvencias municipales</span></a>

            <p class="alc-sidebar-group">Comercio</p>
            <a href="<?= BASE_URL ?>/Vistas/patentes/gestionPatentes.php" class="<?= str_contains($requestUri, '/Vistas/patentes/') ? 'active' : ''; ?>"><i class="fas fa-certificate"></i><span>Patentes</span></a>
            <a href="<?= BASE_URL ?>/Vistas/licencias/gestionLicencias.php" class="<?= str_contains($requestUri, '/Vistas/licencias/') ? 'active' : ''; ?>"><i class="fas fa-id-badge"></i><span>Licencias</span></a>
            <a href="<?= BASE_URL ?>/Vistas/comercio/gestion_solvencias_comerciales.php" class="<?= str_contains($requestUri, '/Vistas/comercio/') ? 'active' : ''; ?>"><i class="fas fa-store"></i><span>Solvencias comerciales</span></a>

            <?php if ($puedeVerVehiculos || $puedeVerPatentesVehiculos): ?>
                <p class="alc-sidebar-group">Vehiculos</p>
                <?php if ($puedeVerVehiculos): ?>
                    <a href="<?= BASE_URL ?>/Vistas/vehiculos/gestion_vehiculos.php" class="<?= $vehiculosActivo ? 'active' : ''; ?>"><i class="fas fa-car-side"></i><span>Gestion de vehiculos</span></a>
                <?php endif; ?>
                <?php if ($puedeVerPatentesVehiculos): ?>
                    <a href="<?= BASE_URL ?>/Vistas/vehiculos/gestion_patentes_vehiculos.php" class="<?= $patentesVehiculosActivo ? 'active' : ''; ?>"><i class="fas fa-file-signature"></i><span>Patentes vehiculares</span></a>
                <?php endif; ?>
            <?php endif; ?>

            <p class="alc-sidebar-group">Facturacion</p>
            <a href="<?= BASE_URL ?>/Vistas/facturacion/gestion_facturas.php" class="<?= str_contains($requestUri, '/gestion_facturas.php') ? 'active' : ''; ?>"><i class="fas fa-file-invoice"></i><span>Facturas</span></a>
            <a href="<?= BASE_URL ?>/Vistas/facturacion/reporte_ingresos.php" class="<?= str_contains($requestUri, '/reporte_ingresos.php') ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i><span>Reporte ingresos</span></a>
            <a href="<?= BASE_URL ?>/Vistas/impuestos/gestion_tipo_impuesto.php" class="<?= str_contains($requestUri, '/Vistas/impuestos/') ? 'active' : ''; ?>"><i class="fas fa-percent"></i><span>Tipos de impuesto</span></a>
        </nav>

        <form action="<?= BACKEND_URL ?>/controlador/UsuarioController.php" method="POST" class="alc-sidebar-logout">
            <input type="hidden" name="accion" value="logout">
            <button type="submit" class="btn alc-btn-logout w-100"><i class="fas fa-right-from-bracket"></i><span>Cerrar sesion</span></button>
        </form>
    </div>
</aside>
