<!-- layouts/sidebar.php -->
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light border-end vh-100" style="width: 250px; position: fixed;">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
        <i class="fas fa-landmark fa-lg me-2 text-primary"></i>
        <span class="fs-5 fw-semibold">Alcaldía</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="../dashboard.php" class="nav-link text-dark">
                <i class="fas fa-home me-2"></i> Inicio
            </a>
        </li>
        <li>
            <a href="../../Vistas/usuarios/gestion_usuarios.php" class="nav-link text-dark">
                <i class="fas fa-users-cog me-2"></i> Usuarios
            </a>
        </li>
        <li>
            <a href="../contribuyentes/gestion_contribuyentes.php" class="nav-link text-dark">
                <i class="fas fa-id-card me-2"></i> Contribuyentes
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-dark">
                <i class="fas fa-building me-2"></i> Patentes
            </a>
        </li>
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
