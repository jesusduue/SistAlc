<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/sidebar.php';
restringirAcceso('licencias');

require_once '../../../Backend/clases/licencia.php';
require_once '../../../Backend/clases/patente.php';
require_once '../../../Backend/clases/Contribuyente.php';

$licenciaModel = new Licencia();
$patenteModel  = new Patente();
$contribuyenteModel = new Contribuyente();

$licencias = $licenciaModel->consultar_activas();
$patentes  = $patenteModel->consultar_todas();
$contribuyentes = $contribuyenteModel->consultar_todos();

$mapaContribuyentes = [];
foreach ($contribuyentes as $con) {
    $mapaContribuyentes[$con['id_con']] = [
        'nombre' => $con['nom_con'] ?? '',
        'rif' => $con['rif_con'] ?? '',
        'direccion' => $con['dir_con'] ?? '',
    ];
}

$mapaPatentes = [];
foreach ($patentes as $pat) {
    $contribuyenteId = $pat['fky_con'] ?? null;
    $conInfo = $contribuyenteId && isset($mapaContribuyentes[$contribuyenteId])
        ? $mapaContribuyentes[$contribuyenteId]
        : ['nombre' => '', 'rif' => '', 'direccion' => ''];
    $mapaPatentes[$pat['id_pat']] = [
        'numero' => $pat['num_pat'] ?? '',
        'nombre' => $pat['nom_pat'] ?? '',
        'contribuyente' => $conInfo['nombre'],
        'rif' => $conInfo['rif'],
        'direccion' => $conInfo['direccion'],
        'id_con' => $contribuyenteId,
    ];
}
?>
<link rel="stylesheet" href="../../estilos/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Gestión de Licencias</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalLicencia">
        <i class="fas fa-id-badge"></i> Registrar Licencia
    </button>

    <?php include 'modalRegistrarActualizarLicencia.php'; ?>

    <div class="mb-3">
        <input type="text" id="buscadorLicencia" class="form-control" placeholder="Buscar licencia...">
    </div>

    <?php include 'listarLicencias.php'; ?>
</div>

<script src="../../estilos/js/bootstrap.bundle.min.js"></script>
<script src="../../estilos/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.catalogosLicencias = {
    patentes: <?= json_encode($mapaPatentes, JSON_UNESCAPED_UNICODE); ?>
};
</script>
<script src="../js/licencias/modalLicencia.js"></script>
<script src="../js/licencias/alertLicencia.js"></script>

<script>
    $(function() {
        const table = $('#tablaLicencias').DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            language: {
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                info: 'Mostrando _START_ a _END_ de _TOTAL_ licencias',
                infoEmpty: 'Mostrando 0 a 0 de 0 licencias',
                infoFiltered: '(filtrado de _MAX_ licencias totales)',
                zeroRecords: 'No se encontraron licencias',
                search: ''
            },
            dom: 'rt<"d-flex justify-content-between align-items-center"ip>'
        });

        $('#buscadorLicencia').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>


