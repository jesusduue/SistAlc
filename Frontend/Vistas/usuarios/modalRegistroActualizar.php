<div class="modal fade" id="usuarioModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="usuarioForm" action="../../../Backend/controlador/UsuarioController.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="accion" id="formAccion">
                    <input type="hidden" name="id_usu" id="formIdUsuario">

                    <div class="mb-3">
                        <label for="nom_usu" class="form-label">Usuario *</label>
                        <input type="text" autocomplete="username" name="nom_usu" id="nom_usu" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="cla_usu" class="form-label" id="labelPassword">Contraseña *</label>
                        <input type="password" autocomplete="current-password" name="cla_usu" id="cla_usu" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fky_rol" class="form-label">Rol *</label>
                            <select name="fky_rol" id="fky_rol" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol['id_rol']; ?>"><?php echo htmlspecialchars($rol['nom_rol']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="est_usu" class="form-label">Estado *</label>
                            <select name="est_usu" id="est_usu" class="form-select" required>
                                <option value="A">Activo</option>
                                <option value="I">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save"></i> <span>Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>