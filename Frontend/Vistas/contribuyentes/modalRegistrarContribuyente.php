<!-- Archivo: modalRegistrarContribuyente.php -->
<div class="modal fade" id="modalAgregarContribuyente" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../../Backend/controlador/ContribuyenteController.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Registrar Contribuyente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" value="registrar">
                    <div class="mb-3">
                        <label for="nom_con" class="form-label">Nombre o Comercio</label>
                        <input type="text" name="nom_con" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="rif_con" class="form-label">RIF o Cédula</label>
                        <input type="text" name="rif_con" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="tel_con" class="form-label">Teléfono</label>
                        <input type="text" name="tel_con" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="ema_con" class="form-label">Correo electrónico</label>
                        <input type="email" name="ema_con" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="dir_con" class="form-label">Dirección</label>
                        <textarea name="dir_con" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
