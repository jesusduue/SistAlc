<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/sidebar.php';
restringirAcceso('impuestos');

require_once '../../../Backend/clases/TipoImpuesto.php';
$tipoImpuesto = new TipoImpuesto();
$tipos = $tipoImpuesto->consultar_todos();
?>
<link rel="stylesheet" href="../../estilos/css/bootstrap.min.css">
<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Tipos de Impuesto</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTipoImpuesto">
        <i class="fas fa-plus"></i> Nuevo Tipo de Impuesto
    </button>

    <div class="card">
        <div class="card-header">Listado</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
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
                                <span class="badge bg-<?= ($tip['est_registro'] === 'A') ? 'success' : 'secondary'; ?>">
                                    <?= ($tip['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="d-flex gap-2">
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
    </div>
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

<script src="../../estilos/js/bootstrap.bundle.min.js"></script>
<script src="../js/impuestos/modalTipoImpuesto.js"></script>
