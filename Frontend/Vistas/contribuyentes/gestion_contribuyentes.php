<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/sidebar.php';
restringirAcceso('Administrador');

require_once '../../../Backend/clases/Contribuyente.php';
$contribuyente = new Contribuyente();
$contribuyentes = $contribuyente->consultar_activos(); // se puede camnbiar a consultar todos para ver los contribuyentes inactivos
?>
<link rel="stylesheet" href="../../estilos/css/bootstrap.min.css">
<!-- CSS de DataTables (Bootstrap 5) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Gestión de Contribuyentes</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensaje']['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje']['texto']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <!-- Botón de agregar que usa el mismo modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalContribuyente">
        <i class="fas fa-user-plus"></i> Agregar Contribuyente
    </button>

    <!-- Incluye el modal único para actualizar o registrar -->
    <?php include 'modalRegistrarActualizarContribuyente.php'; ?>

    <div class="mb-3">
        <input type="text" id="buscador" class="form-control" placeholder="Buscar contribuyente...">
    </div>

    <?php include 'listarContribuyentes.php'; ?>
</div>

<?php /* include '../layouts/footer.php'; */ ?>

<script src="../../estilos/js/bootstrap.bundle.min.js"></script>    
<script src="../../estilos/js/bootstrap.min.js"></script>
<script src="../js/contribuyentes/modalContribuyente.js"></script>
<script src="../js/contribuyentes/alertContribuyente.js"></script>
<!-- JS DataTables + Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    //Inicializa DataTable y conecta el input al buscador nativo de DataTables
     $(function() {
        const table = $('#tablaContribuyentes').DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            searching: true, // lo mantenemos ON para usar su API
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
                info: 'Mostrando _START_ a _END_ de _TOTAL_ contribuyentes',
                infoEmpty: 'Mostrando 0 a 0 de 0 contribuyentes',
                infoFiltered: '(filtrado de _MAX_ contribuyentes totales)',
                zeroRecords: 'No se encontraron contribuyentes',
                search: ''
            },
            dom: 'rt<"d-flex justify-content-between align-items-center"ip>' // sin searchbox nativo visible
        });

        // Conecta tu input externo al buscador de DataTables
        $('#buscador').on('keyup', function() {
            table.search(this.value).draw();
        });
    }); 
</script>

