<?php
require_once __DIR__ . '/paths.php';
require_once __DIR__ . '/proteger_vista.php';

$requestUri = $_SERVER['REQUEST_URI'] ?? '';

$isDashboard = str_contains($requestUri, '/Vistas/dashboard.php');
$isPerfilUsuario = str_contains($requestUri, '/Vistas/usuarios/perfil.php');
$isGestionUsuarios = str_contains($requestUri, '/Vistas/usuarios/gestion_usuarios.php');
$isGestionRoles = str_contains($requestUri, '/Vistas/usuarios/gestion_roles.php');
$isUsuarios = $isGestionUsuarios || $isGestionRoles;
$isContribuyentes = str_contains($requestUri, '/Vistas/contribuyentes/');
$isCatastro = str_contains($requestUri, '/Vistas/catastro/');
$isPatentes = str_contains($requestUri, '/Vistas/patentes/');
$isLicencias = str_contains($requestUri, '/Vistas/licencias/');
$isComercio = str_contains($requestUri, '/Vistas/comercio/');
$isVehiculos = str_contains($requestUri, '/Vistas/vehiculos/');
$isGestionVehiculos = str_contains($requestUri, '/Vistas/vehiculos/gestion_vehiculos.php');
$isGestionPatentesVehiculos = str_contains($requestUri, '/Vistas/vehiculos/gestion_patentes_vehiculos.php');
$isFacturacion = str_contains($requestUri, '/Vistas/facturacion/');
$isImpuestos = str_contains($requestUri, '/Vistas/impuestos/');

$catastroActivo = $isContribuyentes || $isCatastro;
$patentesActivo = $isPatentes || $isLicencias || $isComercio;
$vehiculosActivo = $isVehiculos;
$facturacionActivo = $isFacturacion || $isImpuestos;
$puedeVerUsuarios = puedeRealizarAccion('usuarios', 'read');
$puedeVerRoles = puedeRealizarAccion('roles', 'read');
$puedeVerPerfil = puedeRealizarAccion('perfil', 'read');
$puedeVerVehiculos = puedeRealizarAccion('vehiculos', 'read');
$puedeVerPatentesVehiculos = puedeRealizarAccion('patentes_vehiculos', 'read');
?>
<nav class="navbar navbar-expand-xl alc-topbar sticky-top">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand alc-brand" href="<?= BASE_URL ?>/Vistas/dashboard.php">
            <span class="alc-brand-icon"><i class="fas fa-landmark"></i></span>
            <span class="alc-brand-copy">
                <span class="alc-brand-title">Alcaldia</span>
                <span class="alc-brand-subtitle">Sistema integral municipal</span>
            </span>
        </a>

        <button class="navbar-toggler alc-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#alcTopbarNav" aria-controls="alcTopbarNav" aria-expanded="false" aria-label="Alternar navegacion">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="alcTopbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-xl-0 alc-topbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= $isDashboard ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/dashboard.php"><i class="fas fa-house"></i><span>Inicio</span></a>
                </li>

                <?php if ($puedeVerUsuarios || $puedeVerRoles): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= $isUsuarios ? 'active' : ''; ?>" href="#" id="usuariosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-users-cog"></i><span>Usuarios</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="usuariosDropdown">
                            <?php if ($puedeVerUsuarios): ?>
                                <li><a class="dropdown-item <?= $isGestionUsuarios ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/usuarios/gestion_usuarios.php"><i class="fas fa-users"></i>Gestion de usuarios</a></li>
                            <?php endif; ?>
                            <?php if ($puedeVerRoles): ?>
                                <li><a class="dropdown-item <?= $isGestionRoles ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/usuarios/gestion_roles.php"><i class="fas fa-user-tag"></i>Gestion de roles</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $catastroActivo ? 'active' : ''; ?>" href="#" id="catastroDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-map-location-dot"></i><span>Catastro</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="catastroDropdown">
                        <li><a class="dropdown-item <?= $isContribuyentes ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/contribuyentes/gestion_contribuyentes.php"><i class="fas fa-id-card"></i>Contribuyentes</a></li>
                        <li><a class="dropdown-item <?= str_contains($requestUri, '/gestion_inmuebles.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/catastro/gestion_inmuebles.php"><i class="fas fa-house"></i>Inmuebles</a></li>
                        <li><a class="dropdown-item <?= str_contains($requestUri, '/gestion_linderos.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/catastro/gestion_linderos.php"><i class="fas fa-border-all"></i>Linderos</a></li>
                        <li><a class="dropdown-item <?= str_contains($requestUri, '/gestion_documentos.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/catastro/gestion_documentos.php"><i class="fas fa-file-contract"></i>Documentos</a></li>
                        <li><a class="dropdown-item <?= str_contains($requestUri, '/gestion_cedulas.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/catastro/gestion_cedulas.php"><i class="fas fa-file-signature"></i>Cedulas</a></li>
                        <li><a class="dropdown-item <?= str_contains($requestUri, '/gestion_solvencias_municipales.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/catastro/gestion_solvencias_municipales.php"><i class="fas fa-scroll"></i>Solvencias municipales</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $patentesActivo ? 'active' : ''; ?>" href="#" id="patentesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-building"></i><span>Comercio</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="patentesDropdown">
                        <li><a class="dropdown-item <?= $isPatentes ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/patentes/gestionPatentes.php"><i class="fas fa-certificate"></i>Patentes</a></li>
                        <li><a class="dropdown-item <?= $isLicencias ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/licencias/gestionLicencias.php"><i class="fas fa-id-badge"></i>Licencias</a></li>
                        <li><a class="dropdown-item <?= $isComercio ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/comercio/gestion_solvencias_comerciales.php"><i class="fas fa-store"></i>Solvencias comerciales</a></li>
                    </ul>
                </li>

                <?php if ($puedeVerVehiculos || $puedeVerPatentesVehiculos): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= $vehiculosActivo ? 'active' : ''; ?>" href="#" id="vehiculosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-car-side"></i><span>Vehiculos</span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="vehiculosDropdown">
                            <?php if ($puedeVerVehiculos): ?>
                                <li><a class="dropdown-item <?= $isGestionVehiculos ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/vehiculos/gestion_vehiculos.php"><i class="fas fa-car-side"></i>Gestion de vehiculos</a></li>
                            <?php endif; ?>
                            <?php if ($puedeVerPatentesVehiculos): ?>
                                <li><a class="dropdown-item <?= $isGestionPatentesVehiculos ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/vehiculos/gestion_patentes_vehiculos.php"><i class="fas fa-file-signature"></i>Patentes vehiculares</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= $facturacionActivo ? 'active' : ''; ?>" href="#" id="facturacionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-invoice-dollar"></i><span>Facturacion</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="facturacionDropdown">
                        <li><a class="dropdown-item <?= str_contains($requestUri, '/gestion_facturas.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/facturacion/gestion_facturas.php"><i class="fas fa-file-invoice"></i>Gestion de facturas</a></li>
                        <li><a class="dropdown-item <?= str_contains($requestUri, '/reporte_ingresos.php') ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/facturacion/reporte_ingresos.php"><i class="fas fa-chart-line"></i>Reporte de ingresos</a></li>
                        <li><a class="dropdown-item <?= $isImpuestos ? 'active' : ''; ?>" href="<?= BASE_URL ?>/Vistas/impuestos/gestion_tipo_impuesto.php"><i class="fas fa-percent"></i>Tipos de impuesto</a></li>
                    </ul>
                </li>
            </ul>

            <div class="alc-user-panel">
                <div class="alc-user-chip" title="Usuario autenticado">
                    <i class="fas fa-user-shield"></i>
                    <span>
                        <strong><?= htmlspecialchars($_SESSION['nom_usu'] ?? ''); ?></strong>
                        <small><?= htmlspecialchars($_SESSION['nom_rol'] ?? ''); ?></small>
                    </span>
                </div>
                <?php if ($puedeVerPerfil): ?>
                    <a href="<?= BASE_URL ?>/Vistas/usuarios/perfil.php" class="btn alc-btn-profile <?= $isPerfilUsuario ? 'active' : ''; ?>">
                        <i class="fas fa-user-pen"></i>
                        <span>Perfil</span>
                    </a>
                <?php endif; ?>
                <form action="<?= BACKEND_URL ?>/controlador/UsuarioController.php" method="POST" class="m-0">
                    <input type="hidden" name="accion" value="logout">
                    <button class="btn alc-btn-logout" type="submit">
                        <i class="fas fa-right-from-bracket"></i>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
