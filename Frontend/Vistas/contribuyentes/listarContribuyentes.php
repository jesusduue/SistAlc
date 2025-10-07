<!-- filepath: Frontend/Vistas/contribuyentes/listarContribuyentes.php -->
<div class="card">
    <div class="card-header">Listado contribuyentes</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="tablaContribuyentes">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre o Comercio</th>
                    <th>RIF/Cédula</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contribuyentes as $con) : ?>
                    <tr>
                        <td><?= htmlspecialchars($con['id_con']) ?></td>
                        <td><?= htmlspecialchars($con['nom_con']) ?></td>
                        <td><?= htmlspecialchars($con['rif_con']) ?></td>
                        <td><?= htmlspecialchars($con['tel_con']) ?></td>
                        <td><?= htmlspecialchars($con['ema_con']) ?></td>
                        <td><?= htmlspecialchars($con['dir_con']) ?></td>
                        <td><?= $con['est_registro'] === 'A' ? 'Activo' : 'Inactivo' ?></td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalContribuyente"
                                data-id="<?= $con['id_con'] ?>"
                                data-nombre="<?= htmlspecialchars($con['nom_con']) ?>"
                                data-rif="<?= htmlspecialchars($con['rif_con']) ?>"
                                data-telefono="<?= htmlspecialchars($con['tel_con']) ?>"
                                data-correo="<?= htmlspecialchars($con['ema_con']) ?>"
                                data-direccion="<?= htmlspecialchars($con['dir_con']) ?>">Editar</button>
                            <!-- Botón para cambiar estado o eliminar -->
                            <?php if ($con['id_con'] != $_SESSION['id_usu']): ?>
                                <!-- boton para cambiar estado -->
                                <a href="javascript:void(0);"
                                    class="btn btn-sm btn-<?php echo ($con['est_registro'] === 'A') ? 'success' : 'danger'; ?> btn-cambiar-estado"
                                    data-url="../../../Backend/controlador/contribuyenteController.php?accion=cambiar_estado&id=<?php echo $con['id_con']; ?>&nuevo_estado=<?php echo ($con['est_registro'] === 'A') ? 'I' : 'A'; ?>"
                                    data-nombre="<?php echo htmlspecialchars($con['nom_con']); ?>">
                                    <i class="fas fa-toggle-<?php echo ($con['est_registro'] === 'A') ? 'off' : 'on'; ?>"></i>
                                </a>

                                <a href="javascript:void(0);"
                                    class="btn btn-sm btn-danger btn-eliminar-contribuyente"
                                    data-url="../../../Backend/controlador/contribuyenteController.php?accion=eliminar&id=<?php echo $con['id_con']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($con['nom_con']); ?>">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

