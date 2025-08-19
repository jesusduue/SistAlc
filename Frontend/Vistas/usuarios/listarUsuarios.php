    <div class="card">
        <div class="card-header">Listado de Usuarios</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_usuarios as $usu): ?>
                        <tr>
                            <td><?php echo $usu['id_usu']; ?></td>
                            <td><?php echo htmlspecialchars($usu['nom_usu']); ?></td>
                            <td><?php echo htmlspecialchars($usu['nom_rol']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo ($usu['est_usu'] === 'A') ? 'success' : 'secondary'; ?>">
                                    <?php echo htmlspecialchars($usu['estado_descripcion']); ?>
                                </span>
                            </td>
                            <td>
                                <!-- botón para editar usuario -->
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#usuarioModal"
                                    data-id="<?php echo $usu['id_usu']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($usu['nom_usu']); ?>"
                                    data-rol="<?php echo $usu['fky_rol']; ?>"
                                    data-estado="<?php echo $usu['est_usu']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($usu['id_usu'] != $_SESSION['id_usu']): ?>
                                    <!-- botón para cambiar estado -->
                                    <a href="javascript:void(0);"
                                        class="btn btn-sm btn-<?php echo ($usu['est_usu'] === 'A') ? 'success' : 'danger'; ?> btn-cambiar-estado"
                                        data-url="../../../Backend/controlador/UsuarioController.php?accion=cambiar_estado&id=<?php echo $usu['id_usu']; ?>&estado_nuevo=<?php echo ($usu['est_usu'] === 'A') ? 'I' : 'A'; ?>"
                                        data-nombre="<?php echo htmlspecialchars($usu['nom_usu']); ?>">
                                        <i class="fas fa-toggle-<?php echo ($usu['est_usu'] === 'A') ? 'off' : 'on'; ?>"></i>
                                    </a>

                                    <!-- botón para eliminar usuario para que funcione con sweetalert -->
                                    <a href="javascript:void(0);"
                                        class="btn btn-sm btn-danger btn-eliminar-usuario"
                                        data-url="../../../Backend/controlador/UsuarioController.php?accion=eliminar&id=<?php echo $usu['id_usu']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($usu['nom_usu']); ?>">
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