<?php
$descripcionesTramite = [
    'S' => 'Solicitada',
    'P' => 'Pagada',
    'V' => 'Vencida'
];
?>
<div class="card">
    <div class="card-header">Listado de Patentes</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="tablaPatentes">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Número</th>
                    <th>RIF</th>
                    <th>Nombre / Razón Social</th>
                    <th>Representante</th>
                    <th>Dirección</th>
                    <th>Rubro</th>
                    <!--<th>Estado trámite</th>
                    <th>Estado registro</th> -->
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patentes as $pat): ?>
                    <tr>
                        <td><?= htmlspecialchars($pat['id_pat']); ?></td>
                        <td><?= htmlspecialchars($pat['fec_pat']); ?></td>
                        <td><?= htmlspecialchars($pat['num_pat']); ?></td>
                        <td><?= htmlspecialchars($pat['rif_pat']); ?></td>
                        <td><?= htmlspecialchars($pat['nom_pat']); ?></td>
                        <td><?= htmlspecialchars($mapaContribuyentes[$pat['fky_con']] ?? 'N/D'); ?></td>
                        <td><?= htmlspecialchars($pat['dir_pat']); ?></td>
                        <td><?= htmlspecialchars($pat['rub_pat']); ?></td>

                        <!--NO ES NECESARIO MOSTRAR PORQUE SE EVALUA EN LA TABLA licencia    
                        <td><?= htmlspecialchars($descripcionesTramite[$pat['est_pat']] ?? 'Desconocido'); ?></td>
                        <td><?= $pat['est_tab_pat'] === 'A' ? 'Activo' : 'Inactivo'; ?></td> -->

                        <td class="d-flex gap-2 flex-wrap">
                            <button
                                type="button"
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalPatente"
                                data-id="<?= htmlspecialchars($pat['id_pat']); ?>"
                                data-numero="<?= htmlspecialchars($pat['num_pat']); ?>"
                                data-rif="<?= htmlspecialchars($pat['rif_pat']); ?>"
                                data-nombre="<?= htmlspecialchars($pat['nom_pat']); ?>"
                                data-representante="<?= htmlspecialchars($pat['fky_con']); ?>"
                                data-direccion="<?= htmlspecialchars($pat['dir_pat']); ?>"
                                data-rubro="<?= htmlspecialchars($pat['rub_pat']); ?>"
                                data-estado="<?= htmlspecialchars($pat['est_pat']); ?>">
                                Editar
                            </button>

                            <!-- Boton para hacer borrado logico  -->
                            <a
                                href="javascript:void(0);"
                                class="btn btn-sm btn-<?= ($pat['est_tab_pat'] === 'A') ? 'success' : 'secondary'; ?> btn-cambiar-estado-patente"
                                data-url="../../../Backend/controlador/patenteController.php?accion=cambiar_estado&id=<?= $pat['id_pat']; ?>&nuevo_estado=<?= ($pat['est_tab_pat'] === 'A') ? 'I' : 'A'; ?>"
                                data-numero="<?= htmlspecialchars($pat['num_pat']); ?>">
                                <i class="fas fa-toggle-<?= ($pat['est_tab_pat'] === 'A') ? 'off' : 'on'; ?>"></i>
                            </a>

                            <!--BOTON PARA CAMBIAR EL ESTADO MANUAL DE LA SOLICITUD,PAGADO O VENCIMIENTO  de la patente    
                             <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="true">
                                    Estado trámite
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item btn-actualizar-estado" href="javascript:void(0);" data-url="../../../Backend/controlador/patenteController.php?accion=actualizar_estado&id=<?= $pat['id_pat']; ?>&nuevo_estado=S" data-numero="<?= htmlspecialchars($pat['num_pat']); ?>" data-estado="Solicitada">Solicitada</a></li>
                                    <li><a class="dropdown-item btn-actualizar-estado" href="javascript:void(0);" data-url="../../../Backend/controlador/patenteController.php?accion=actualizar_estado&id=<?= $pat['id_pat']; ?>&nuevo_estado=P" data-numero="<?= htmlspecialchars($pat['num_pat']); ?>" data-estado="Pagada">Pagada</a></li>
                                    <li><a class="dropdown-item btn-actualizar-estado" href="javascript:void(0);" data-url="../../../Backend/controlador/patenteController.php?accion=actualizar_estado&id=<?= $pat['id_pat']; ?>&nuevo_estado=V" data-numero="<?= htmlspecialchars($pat['num_pat']); ?>" data-estado="Vencida">Vencida</a></li>
                                </ul>
                            </div> -->


                            <!-- BOTON para hacer borrado fisico -->
                            <a
                                href="javascript:void(0);"
                                class="btn btn-sm btn-danger btn-eliminar-patente"
                                data-url="../../../Backend/controlador/patenteController.php?accion=eliminar&id=<?= $pat['id_pat']; ?>"
                                data-numero="<?= htmlspecialchars($pat['num_pat']); ?>">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>