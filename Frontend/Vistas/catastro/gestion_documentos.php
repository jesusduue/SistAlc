<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('documentos_catastro');

require_once '../../../Backend/clases/DocumentoLegal.php';
$documentoLegal = new DocumentoLegal();
$documentos = $documentoLegal->consultar_todos();
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/catastro/gestion_documentos.css">
<?php include '../layouts/topbar.php'; ?>

<div class="container-fluid alc-page documentos-page">
    <section class="alc-page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div>
            <h1 class="alc-page-title">Documentos legales</h1>
            <p class="alc-page-subtitle">Resguardo de soportes juridicos asociados a inmuebles y cedulas catastrales.</p>
        </div>
        <div class="alc-page-tools">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDocumento">
                <i class="fas fa-file-circle-plus me-2"></i>Nuevo documento
            </button>
        </div>
    </section>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <section class="card alc-panel">
        <div class="card-header">Listado de documentos</div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Numero</th>
                        <th>Matricula</th>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Descripcion</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documentos as $doc): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['id_doc']); ?></td>
                            <td><?= htmlspecialchars($doc['tip_doc']); ?></td>
                            <td><?= htmlspecialchars($doc['num_doc']); ?></td>
                            <td><?= htmlspecialchars($doc['matr_doc']); ?></td>
                            <td><?= htmlspecialchars($doc['fol_doc']); ?></td>
                            <td><?= htmlspecialchars($doc['fec_doc']); ?></td>
                            <td><?= htmlspecialchars($doc['des_doc']); ?></td>
                            <td>
                                <span class="badge status-chip <?= ($doc['est_registro'] === 'A') ? 'status-a' : 'status-i'; ?>">
                                    <?= ($doc['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDocumento"
                                        data-id="<?= htmlspecialchars($doc['id_doc']); ?>"
                                        data-tip="<?= htmlspecialchars($doc['tip_doc']); ?>"
                                        data-num="<?= htmlspecialchars($doc['num_doc']); ?>"
                                        data-matr="<?= htmlspecialchars($doc['matr_doc']); ?>"
                                        data-fol="<?= htmlspecialchars($doc['fol_doc']); ?>"
                                        data-fec="<?= htmlspecialchars($doc['fec_doc']); ?>"
                                        data-des="<?= htmlspecialchars($doc['des_doc']); ?>">
                                    Editar
                                </button>
                                <a class="btn btn-sm btn-<?= ($doc['est_registro'] === 'A') ? 'success' : 'secondary'; ?>"
                                   href="../../../Backend/controlador/documentoLegalController.php?accion=cambiar_estado&id=<?= $doc['id_doc']; ?>&nuevo_estado=<?= ($doc['est_registro'] === 'A') ? 'I' : 'A'; ?>">
                                    <i class="fas fa-toggle-<?= ($doc['est_registro'] === 'A') ? 'off' : 'on'; ?>"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="modal fade" id="modalDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../../../Backend/controlador/documentoLegalController.php" method="POST" id="documentoForm">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accionDocumento" value="registrar">
                    <input type="hidden" name="id_doc" id="id_doc" value="">
                    <div class="mb-3">
                        <label for="tip_doc" class="form-label">Tipo *</label>
                        <select name="tip_doc" id="tip_doc" class="form-select" required>
                            <option value="Registrado">Registrado</option>
                            <option value="Notariado">Notariado</option>
                            <option value="Juzgado">Juzgado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="num_doc" class="form-label">Numero *</label>
                        <input type="text" name="num_doc" id="num_doc" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="matr_doc" class="form-label">Matricula *</label>
                        <input type="text" name="matr_doc" id="matr_doc" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="fol_doc" class="form-label">Folio *</label>
                        <input type="text" name="fol_doc" id="fol_doc" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="fec_doc" class="form-label">Fecha *</label>
                        <input type="date" name="fec_doc" id="fec_doc" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="des_doc" class="form-label">Descripcion *</label>
                        <textarea name="des_doc" id="des_doc" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarDocumento">
                        <i class="fas fa-plus"></i> <span>Registrar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/SistAlc/Frontend/estilos/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalDocumento');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget || {};
        const form = document.getElementById('documentoForm');
        const accion = document.getElementById('accionDocumento');
        const idInput = document.getElementById('id_doc');
        const btn = document.getElementById('btnGuardarDocumento');
        const campos = {
            tip_doc: 'Registrado',
            num_doc: '',
            matr_doc: '',
            fol_doc: '',
            fec_doc: '',
            des_doc: ''
        };
        if (button.dataset && button.dataset.id) {
            accion.value = 'modificar';
            idInput.value = button.dataset.id;
            Object.keys(campos).forEach(campo => {
                const elemento = document.getElementById(campo);
                elemento.value = button.dataset[campo] || campos[campo];
            });
            document.getElementById('tip_doc').value = button.dataset.tip || 'Registrado';
            document.getElementById('num_doc').value = button.dataset.num || '';
            document.getElementById('matr_doc').value = button.dataset.matr || '';
            document.getElementById('fol_doc').value = button.dataset.fol || '';
            document.getElementById('fec_doc').value = button.dataset.fec || '';
            document.getElementById('des_doc').value = button.dataset.des || '';
            btn.querySelector('i').className = 'fas fa-save';
            btn.querySelector('span').textContent = 'Guardar Cambios';
            modal.querySelector('.modal-title').textContent = 'Editar Documento';
        } else {
            form.reset();
            accion.value = 'registrar';
            idInput.value = '';
            btn.querySelector('i').className = 'fas fa-plus';
            btn.querySelector('span').textContent = 'Registrar';
            modal.querySelector('.modal-title').textContent = 'Registrar Documento';
        }
    });
});
</script>
