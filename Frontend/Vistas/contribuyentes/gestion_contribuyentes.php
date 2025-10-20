<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/topbar.php';
restringirAcceso('contribuyentes');

require_once '../../../Backend/clases/Contribuyente.php';
$contribuyente = new Contribuyente();
$contribuyentes = $contribuyente->consultar_activos();
?>

<!-- CSS DataTables (se puede cargar aquí porque bootstrap.css ya viene en el header) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

<div class="container-fluid" style=" padding-top: 2rem; margin-left: 0;">
    <h1 class="mb-4">Gestión de Contribuyentes</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalContribuyente">
        <i class="fas fa-user-plus"></i> Agregar Contribuyente
    </button>

    <?php include 'modalRegistrarActualizarContribuyente.php'; ?>

    <div class="mb-3">
        <input type="text" id="buscador" class="form-control" placeholder="Buscar contribuyente...">
    </div>

    <?php include 'listarContribuyentes.php'; ?>
</div>

<?php /* footer incluido por layouts */ ?>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="/SistAlc/Frontend/Vistas/js/contribuyentes/modalContribuyente.js"></script>
<script src="/SistAlc/Frontend/Vistas/js/contribuyentes/alertContribuyente.js"></script>

<script>
    $(function() {
        const table = $('#tablaContribuyentes').DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            language: {
                paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' },
                info: 'Mostrando _START_ a _END_ de _TOTAL_ contribuyentes',
                infoEmpty: 'Mostrando 0 a 0 de 0 contribuyentes',
                infoFiltered: '(filtrado de _MAX_ contribuyentes totales)',
                zeroRecords: 'No se encontraron contribuyentes',
                search: ''
            },
            dom: 'rt<"d-flex justify-content-between align-items-center"ip>'
        });

        $('#buscador').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>

