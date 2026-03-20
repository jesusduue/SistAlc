<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('impuestos');

require_once '../../../Backend/clases/TipoImpuesto.php';
$tipoImpuesto = new TipoImpuesto();
$tipos = $tipoImpuesto->consultar_todos();
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/impuestos/gestion_tipo_impuesto.css">
<?php include '../layouts/topbar.php'; ?>

<div class="container-fluid alc-page impuestos-page">
    <section class="alc-page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div>
            <h1 class="alc-page-title">Tipos de impuesto</h1>
            <p class="alc-page-subtitle">Catalogo tributario base para la construccion de facturas y reportes financieros.</p>
        </div>
        <div class="alc-page-tools">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTipoImpuesto">
                <i class="fas fa-plus me-2"></i>Nuevo tipo de impuesto
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
        <div class="card-header">Listado de tipos de impuesto</div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tipos as $tip): ?>
                        <tr>
                            <td><?= htmlspecialchars($tip['id_tip']); ?></td>
                            <td><?= htmlspecialchars($tip['nom_tip']); ?></td>
                            <td><?= htmlspecialchars($tip['des_tip']); ?></td>
                            <td>
                                <span class="badge status-chip <?= ($tip['est_registro'] === 'A') ? 'status-a' : 'status-i'; ?>">
                                    <?= ($tip['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalTipoImpuesto"
                                    data-id="<?= htmlspecialchars($tip['id_tip']); ?>"
                                    data-nombre="<?= htmlspecialchars($tip['nom_tip']); ?>"
                                    data-descripcion="<?= htmlspecialchars($tip['des_tip']); ?>">
                                    Editar
                                </button>
                                <a
                                    href="../../../Backend/controlador/tipoImpuestoController.php?accion=cambiar_estado&id=<?= $tip['id_tip']; ?>&nuevo_estado=<?= ($tip['est_registro'] === 'A') ? 'I' : 'A'; ?>"
                                    class="btn btn-sm btn-<?= ($tip['est_registro'] === 'A') ? 'success' : 'secondary'; ?>"
                                >
                                    <i class="fas fa-toggle-<?= ($tip['est_registro'] === 'A') ? 'off' : 'on'; ?>"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="modal fade" id="modalTipoImpuesto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="tipoImpuestoForm" action="../../../Backend/controlador/tipoImpuestoController.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Tipo de Impuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="formAccionTipo" value="registrar">
                    <input type="hidden" name="id_tip" id="formIdTipo" value="">
                    <div class="mb-3">
                        <label for="nom_tip" class="form-label">Nombre *</label>
                        <input type="text" name="nom_tip" id="nom_tip" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="des_tip" class="form-label">Descripcion</label>
                        <textarea name="des_tip" id="des_tip" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitTipo">
                        <i class="fas fa-plus"></i> <span>Registrar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/SistAlc/Frontend/estilos/js/bootstrap.bundle.min.js"></script>
<script src="../js/impuestos/modalTipoImpuesto.js"></script>
