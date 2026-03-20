<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('patentes');

require_once '../../../Backend/clases/Patente.php';
require_once '../../../Backend/clases/Contribuyente.php';

$patenteModel = new Patente();
$contribuyenteModel = new Contribuyente();

$patentes = $patenteModel->consultar_activas();
$contribuyentes = $contribuyenteModel->consultar_todos();
$mapaContribuyentes = [];
foreach ($contribuyentes as $con) {
    $mapaContribuyentes[$con['id_con']] = $con['nom_con'];
}
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/patentes/gestion_patentes.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?php include '../layouts/topbar.php'; ?>

<div class="container-fluid alc-page patentes-page">
    <section class="alc-page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
        <div>
            <h1 class="alc-page-title">Gestion de patentes</h1>
            <p class="alc-page-subtitle">Control de registros comerciales, representante legal y estado de tramite.</p>
        </div>
        <div class="alc-page-tools">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPatente">
                <i class="fas fa-file-circle-plus me-2"></i>Registrar patente
            </button>
        </div>
    </section>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php include 'modalRegistrarActualizarPatente.php'; ?>

    <section class="card alc-panel mb-3">
        <div class="card-body">
            <label for="buscador" class="form-label fw-semibold mb-2">Busqueda rapida</label>
            <input type="text" id="buscador" class="form-control" placeholder="Buscar patente, nombre comercial, RIF o representante...">
        </div>
    </section>

    <?php include 'listarPatentes.php'; ?>
</div>

<script src="/SistAlc/Frontend/estilos/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/SistAlc/Frontend/Vistas/js/patentes/modalPatente.js"></script>
<script src="/SistAlc/Frontend/Vistas/js/patentes/alertPatente.js"></script>

<script>
    $(function() {
        const table = $('#tablaPatentes').DataTable({
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
                    last: 'Ultimo',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                info: 'Mostrando _START_ a _END_ de _TOTAL_ patentes',
                infoEmpty: 'Mostrando 0 a 0 de 0 patentes',
                infoFiltered: '(filtrado de _MAX_ patentes totales)',
                zeroRecords: 'No se encontraron patentes',
                search: ''
            },
            dom: 'rt<"d-flex justify-content-between align-items-center"ip>'
        });

        $('#buscador').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
