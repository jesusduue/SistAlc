<!-- modal para actualizar o registrar patente -->
<div class="modal fade" id="modalPatente" tabindex="-1" aria-labelledby="modalPatenteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="patenteForm" action="../../../Backend/controlador/patenteController.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPatenteLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="formAccion" value="">
                    <input type="hidden" name="id_pat" id="formIdPatente" value="">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="num_pat" class="form-label">Número de Patente *</label>
                            <input type="text" name="num_pat" id="num_pat" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="rif_pat" class="form-label">RIF / Cédula *</label>
                            <input type="text" name="rif_pat" id="rif_pat" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nom_pat" class="form-label">Nombre / Razón Social *</label>
                            <input type="text" name="nom_pat" id="nom_pat" class="form-control" required>
                        </div>

                        <!-- buscardor en tiempo real para encontra el Representante Legal requerido -->
                        <div class="col-md-6">
                            <label for="fky_con" class="form-label">Representante Legal *</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input
                                    type="text"
                                    id="buscarRepresentante"
                                    class="form-control"
                                    placeholder="Buscar contribuyente..."
                                    autocomplete="off">
                            </div>
                            <select name="fky_con" id="fky_con" class="form-select" required>
                                <option value="">Seleccione un contribuyente</option>
                                <?php foreach ($contribuyentes as $con): ?>
                                    <option value="<?= htmlspecialchars($con['id_con']); ?>">
                                        <?= htmlspecialchars($con['nom_con']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="sinCoincidenciasRepresentante" class="form-text text-danger d-none">
                                No se encontraron contribuyentes con ese criterio.
                            </div>
                        </div>


                        <div class="col-12">
                            <label for="dir_pat" class="form-label">Dirección *</label>
                            <textarea name="dir_pat" id="dir_pat" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-12">
                            <label for="rub_pat" class="form-label">Rubro *</label>
                            <textarea name="rub_pat" id="rub_pat" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="est_pat" class="form-label">Estado del trámite *</label>
                            <select name="est_pat" id="est_pat" class="form-select" required>
                                <option value="S">Solicitada</option>
                                <option value="P">Pagada</option>
                                <option value="V">Vencida</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-plus"></i> <span>Registrar Patente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>