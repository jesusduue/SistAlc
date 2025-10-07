<?php
$descripcionEstado = [
    'S' => 'Solicitada',
    'P' => 'Pagada',
    'V' => 'Vencida'
];
?>
<div class="card">
    <div class="card-header">Listado de Licencias</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="tablaLicencias">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Numero</th>
                    <th>Patente</th>
                    <th>Expedicion</th>
                    <th>Vigencia</th>
                    <th>Estado tramite</th>
                    <th>Estado registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($licencias as $lic): ?>
                    <?php
                    $infoPatente = $mapaPatentes[$lic['fky_pat']] ?? ['numero' => 'N/D', 'nombre' => ''];
                    $patenteTexto = trim(($infoPatente['numero'] ?? '') . ' - ' . ($infoPatente['nombre'] ?? ''));
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($lic['id_lic']); ?></td>
                        <td><?= htmlspecialchars($lic['num_lic']); ?></td>
                        <td><?= htmlspecialchars($patenteTexto); ?></td>
                        <td><?= htmlspecialchars($lic['fec_exp_lic']); ?></td>
                        <td><?= htmlspecialchars($lic['vig_lic']); ?></td>
                        <td><?= htmlspecialchars($descripcionEstado[$lic['est_lic']] ?? 'Desconocido'); ?></td>
                        <td>
                            <span class="badge bg-<?= ($lic['est_registro'] === 'A') ? 'success' : 'secondary'; ?>">
                                <?= ($lic['est_registro'] === 'A') ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td class="d-flex gap-2 flex-wrap">
                            <button
                                type="button"
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#modalLicencia"
                                data-id="<?= htmlspecialchars($lic['id_lic']); ?>"
                                data-numero="<?= htmlspecialchars($lic['num_lic']); ?>"
                                data-patente="<?= htmlspecialchars($lic['fky_pat']); ?>"
                                data-expedicion="<?= htmlspecialchars($lic['fec_exp_lic']); ?>"
                                data-vigencia="<?= htmlspecialchars($lic['vig_lic']); ?>"
                                data-estado="<?= htmlspecialchars($lic['est_lic']); ?>"
                                data-etiqueta="<?= htmlspecialchars($patenteTexto); ?>">
                                Editar
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                    Estado tramite
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item btn-actualizar-estado-licencia" href="javascript:void(0);" data-url="../../../Backend/controlador/licenciaController.php?accion=actualizar_estado&id=<?= $lic['id_lic']; ?>&nuevo_estado=S" data-licencia="<?= htmlspecialchars($lic['id_lic']); ?>" data-estado="Solicitada">Solicitada</a></li>
                                    <li><a class="dropdown-item btn-actualizar-estado-licencia" href="javascript:void(0);" data-url="../../../Backend/controlador/licenciaController.php?accion=actualizar_estado&id=<?= $lic['id_lic']; ?>&nuevo_estado=P" data-licencia="<?= htmlspecialchars($lic['id_lic']); ?>" data-estado="Pagada">Pagada</a></li>
                                    <li><a class="dropdown-item btn-actualizar-estado-licencia" href="javascript:void(0);" data-url="../../../Backend/controlador/licenciaController.php?accion=actualizar_estado&id=<?= $lic['id_lic']; ?>&nuevo_estado=V" data-licencia="<?= htmlspecialchars($lic['id_lic']); ?>" data-estado="Vencida">Vencida</a></li>
                                </ul>
                            </div>
                            <a
                                href="javascript:void(0);"
                                class="btn btn-sm btn-<?= ($lic['est_registro'] === 'A') ? 'success' : 'secondary'; ?> btn-cambiar-estado-licencia"
                                data-url="../../../Backend/controlador/licenciaController.php?accion=cambiar_estado&id=<?= $lic['id_lic']; ?>&nuevo_estado=<?= ($lic['est_registro'] === 'A') ? 'I' : 'A'; ?>"
                                data-licencia="<?= htmlspecialchars($lic['id_lic']); ?>">
                                <i class="fas fa-toggle-<?= ($lic['est_registro'] === 'A') ? 'off' : 'on'; ?>"></i>
                            </a>
                            <a
                                href="javascript:void(0);"
                                class="btn btn-sm btn-danger btn-eliminar-licencia"
                                data-url="../../../Backend/controlador/licenciaController.php?accion=eliminar&id=<?= $lic['id_lic']; ?>"
                                data-licencia="<?= htmlspecialchars($lic['id_lic']); ?>">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
