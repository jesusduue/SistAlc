    <div class="card">
        <div class="card-body">
            <?php if (!empty($contribuyentes)) : ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaContribuyentes">
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
                                    <td><?= $con['est_con'] === 'A' ? 'Activo' : 'Inactivo' ?></td>
                                    <td>
                                        <!-- Botones de acciones: editar / cambiar estado -->
                                        <button class="btn btn-sm btn-warning">Editar</button>
                                        <button class="btn btn-sm btn-secondary">Activar/Inactivar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="alert alert-info">No hay contribuyentes registrados.</div>
            <?php endif; ?>
        </div>
    </div>