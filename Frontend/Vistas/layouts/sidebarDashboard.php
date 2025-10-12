<!-- layouts/sidebarDashboard.php -->
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light border-end vh-100" style="width: 250px; position: fixed;">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
        <i class="fas fa-landmark fa-lg me-2 text-primary"></i>
        <span class="fs-5 fw-semibold">Alcaldía</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-dark">
                <i class="fas fa-home me-2"></i> Inicio
            </a>
        </li>
        <li><a href="usuarios/gestion_usuarios.php" class="nav-link text-dark"><i class="fas fa-users-cog me-2"></i> Usuarios</a></li>
        <li class="mt-3 text-muted fw-semibold">Catastro</li>
        <li><a href="contribuyentes/gestion_contribuyentes.php" class="nav-link text-dark"><i class="fas fa-id-card me-2"></i> Contribuyentes</a></li>
        <li><a href="catastro/gestion_inmuebles.php" class="nav-link text-dark"><i class="fas fa-house me-2"></i> Inmuebles</a></li>
        <li><a href="catastro/gestion_linderos.php" class="nav-link text-dark"><i class="fas fa-border-style me-2"></i> Linderos</a></li>
        <li><a href="catastro/gestion_documentos.php" class="nav-link text-dark"><i class="fas fa-file-contract me-2"></i> Documentos</a></li>
        <li><a href="catastro/gestion_cedulas.php" class="nav-link text-dark"><i class="fas fa-map me-2"></i> Cedulas</a></li>
        <li><a href="catastro/gestion_solvencias_municipales.php" class="nav-link text-dark"><i class="fas fa-scroll me-2"></i> Solvencias municipales</a></li>
        <li class="mt-3 text-muted fw-semibold">Patentes</li>
        <li><a href="patentes/gestionPatentes.php" class="nav-link text-dark"><i class="fas fa-building me-2"></i> Patentes</a></li>
        <li><a href="licencias/gestionLicencias.php" class="nav-link text-dark"><i class="fas fa-id-badge me-2"></i> Licencias</a></li>
        <li><a href="comercio/gestion_solvencias_comerciales.php" class="nav-link text-dark"><i class="fas fa-store me-2"></i> Solvencias comerciales</a></li>
        <li class="mt-3 text-muted fw-semibold">Facturacion</li>
        <li><a href="facturacion/gestion_facturas.php" class="nav-link text-dark"><i class="fas fa-file-invoice-dollar me-2"></i> Facturacion</a></li>
        <li><a href="impuestos/gestion_tipo_impuesto.php" class="nav-link text-dark"><i class="fas fa-percent me-2"></i> Tipos de impuesto</a></li>
    </ul>
    <hr>
   <!--  <form action="../../Backend/controlador/UsuarioController.php" method="POST"> -->
     <form action="../../Backend/controlador/UsuarioController.php" method="POST">
        <input type="hidden" name="accion" value="logout">
        <button class="btn btn-outline-danger w-100">
            <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
        </button>
    </form>
</div>
