<div class="modal fade" id="modalLicencia" tabindex="-1" aria-labelledby="modalLicenciaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="licenciaForm" action="../../../Backend/controlador/licenciaController.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLicenciaLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="formAccionLicencia" value="">
                    <input type="hidden" name="id_lic" id="formIdLicencia" value="">

                    <div class="mb-3">
                        <label for="num_lic" class="form-label">Numero de licencia *</label>
                        <input type="text" name="num_lic" id="num_lic" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="fky_pat" class="form-label">Patente *</label>
                        <select name="fky_pat" id="fky_pat" class="form-select" required>
                            <option value="">Seleccione una patente</option>
                            <?php foreach ($patentes as $pat): ?>
                                <option value="<?= htmlspecialchars($pat['id_pat']); ?>">
                                    <?= htmlspecialchars(($pat['num_pat'] ?? '') . ' - ' . ($pat['nom_pat'] ?? '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="fec_exp_lic" class="form-label">Fecha de expedicion *</label>
                            <input type="date" name="fec_exp_lic" id="fec_exp_lic" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="vig_lic" class="form-label">Vigencia *</label>
                            <input type="date" name="vig_lic" id="vig_lic" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="est_lic" class="form-label">Estado del tramite *</label>
                        <select name="est_lic" id="est_lic" class="form-select" required>
                            <option value="S">Solicitada</option>
                            <option value="P">Pagada</option>
                            <option value="V">Vencida</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitLicencia">
                        <i class="fas fa-plus"></i> <span>Registrar Licencia</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
