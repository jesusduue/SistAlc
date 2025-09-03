 <!-- filepath: Frontend/Vistas/contribuyentes/modalRegistrarActualizarContribuyente.php -->

<div class="modal fade" id="modalContribuyente" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="contribuyenteForm" action="../../../Backend/controlador/contribuyenteController.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="formAccion" value="">
                    <input type="hidden" name="id_con" id="formIdContribuyente" value="">

                    <div class="mb-3">
                          <label for="id_con" class="form-label">ID Contribuyente *</label>
                          <input type="text" name="id_con" id="id_con" class="form-control" readonly>
                    </div>    
                    <div class="mb-3">
                        <label for="nom_con" class="form-label">Nombre o Comercio *</label>
                        <input type="text" name="nom_con" id="nom_con" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="rif_con" class="form-label">RIF/Cédula *</label>
                        <input type="text" name="rif_con" id="rif_con" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="tel_con" class="form-label">Teléfono *</label>
                        <input type="text" name="tel_con" id="tel_con" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="ema_con" class="form-label">Correo *</label>
                        <input type="email" name="ema_con" id="ema_con" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="dir_con" class="form-label">Dirección *</label>
                        <textarea name="dir_con" id="dir_con" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-plus"></i> <span>Registrar Contribuyente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

