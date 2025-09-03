
 <!-- filepath: Frontend/Vistas/contribuyentes/listarContribuyentes.php -->
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
                    <button class="btn btn-sm btn-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#modalContribuyente"
                        data-id="<?= $con['id_con'] ?>"
                        data-nombre="<?= htmlspecialchars($con['nom_con']) ?>"
                        data-rif="<?= htmlspecialchars($con['rif_con']) ?>"
                        data-telefono="<?= htmlspecialchars($con['tel_con']) ?>"
                        data-correo="<?= htmlspecialchars($con['ema_con']) ?>"
                        data-direccion="<?= htmlspecialchars($con['dir_con']) ?>"
                    >Editar</button>
                    <!-- Botón para cambiar estado o eliminar según tu lógica -->
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>