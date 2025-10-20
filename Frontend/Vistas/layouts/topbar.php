<?php require_once __DIR__ . '/paths.php';
$paginaActual = basename($_SERVER['PHP_SELF']);?>

<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/Vistas/dashboard.php">
            <i class="fas fa-landmark fa-lg me-2 text-primary"></i>
            <span class="fs-5 fw-semibold">Alcaldia</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                    <?php if ($paginaActual == 'dashboard.php'): ?>
                        <a class="nav-link active" aria-current="page" href="#"><i class="fas fa-home me-2"></i>Inicio</a>
                    <?php else: ?>
                        <a class="nav-link" href="../dashboard.php"><i class="fas fa-home me-2"></i>Inicio</a>
                    <?php endif; ?>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/Vistas/usuarios/gestion_usuarios.php"><i class="fas fa-users-cog me-2"></i>Usuarios</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="catastroDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-map me-2"></i> Catastro
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="catastroDropdown">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/contribuyentes/gestion_contribuyentes.php"><i class="fas fa-id-card me-2"></i>Contribuyentes</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/catastro/gestion_inmuebles.php"><i class="fas fa-house me-2"></i>Inmuebles</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/catastro/gestion_linderos.php"><i class="fas fa-border-style me-2"></i>Linderos</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/catastro/gestion_documentos.php"><i class="fas fa-file-contract me-2"></i>Documentos</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/catastro/gestion_cedulas.php"><i class="fas fa-map me-2"></i>Cedulas</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/catastro/gestion_solvencias_municipales.php"><i class="fas fa-scroll me-2"></i>Solvencias municipales</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="patentesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-building me-2"></i> Patentes
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="patentesDropdown">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/patentes/gestionPatentes.php"><i class="fas fa-building me-2"></i>Patentes</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/licencias/gestionLicencias.php"><i class="fas fa-id-badge me-2"></i>Licencias</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/comercio/gestion_solvencias_comerciales.php"><i class="fas fa-store me-2"></i>Solvencias comerciales</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="facturacionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Facturación
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="facturacionDropdown">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/facturacion/gestion_facturas.php"><i class="fas fa-file-invoice-dollar me-2"></i>Facturación</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/reportes/ingresos_diarios.php"><i class="fas fa-chart-line me-2"></i>Ingresos diarios</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/reportes/ingresos_por_rubros.php"><i class="fas fa-chart-pie me-2"></i>Ingresos por rubros</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/Vistas/impuestos/gestion_tipo_impuesto.php"><i class="fas fa-percent me-2"></i>Tipos de impuesto</a></li>
                    </ul>
                </li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"></li>
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">
                        <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($_SESSION['nom_usu'] ?? ''); ?> (<?= htmlspecialchars($_SESSION['nom_rol'] ?? ''); ?>)
                    </a>
                </ul>
            <form action="<?= BACKEND_URL ?>/controlador/UsuarioController.php" method="POST" class="d-flex">
                <input type="hidden" name="accion" value="logout">
                <button class="btn btn-outline-danger" type="submit">
                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</nav>

