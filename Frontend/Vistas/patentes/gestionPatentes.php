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

<?php include '../layouts/topbar.php'; ?>
<link rel="stylesheet" href="/SistAlc/Frontend/estilos/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

<div class="container-fluid" style="padding-top: 2rem;">
    <h1 class="mb-4">Gestión de Patentes</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalPatente">
        <i class="fas fa-file-circle-plus"></i> Registrar Patente
    </button>

    <?php include 'modalRegistrarActualizarPatente.php'; ?>

    <div class="mb-3">
        <input type="text" id="buscador" class="form-control" placeholder="Buscar patente...">
    </div>

    <?php include 'listarPatentes.php'; ?>
</div>

<!-- Bootstrap bundle: ruta root-relative para consistencia -->
<script src="/SistAlc/Frontend/estilos/js/bootstrap.bundle.min.js"></script>
<!-- Cargamos scripts en el orden correcto: jQuery -> DataTables -> sweetalert -> scripts locales -->
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
                    last: 'Último',
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

