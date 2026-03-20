<?php
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
restringirAcceso('contribuyentes');

require_once '../../../Backend/clases/Contribuyente.php';
$contribuyente = new Contribuyente();
$contribuyentes = $contribuyente->consultar_activos();
?>
<link rel="stylesheet" href="<?= ASSETS_CSS ?>/contribuyentes/gestion_contribuyentes.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?php include '../layouts/topbar.php'; ?>

<main class="container-fluid alc-page contribuyentes-page">
    <section class="alc-page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center contribuyentes-hero">
        <div>
            <h1 class="alc-page-title">Gestion de contribuyentes</h1>
            <p class="alc-page-subtitle">Consolida y depura la base de contribuyentes para procesos catastrales y comerciales.</p>
        </div>
        <div class="alc-page-tools">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalContribuyente">
                <i class="fas fa-user-plus me-2"></i>Agregar contribuyente
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

    <?php include 'modalRegistrarActualizarContribuyente.php'; ?>

    <section class="card alc-panel mb-3">
        <div class="card-body">
            <label for="buscador" class="form-label fw-semibold mb-2">Busqueda rapida</label>
            <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre, RIF, telefono o direccion...">
        </div>
    </section>

    <section class="card alc-panel">
        <div class="card-header">Listado de contribuyentes</div>
        <div class="card-body">
            <?php include 'listarContribuyentes.php'; ?>
        </div>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
                paginate: { first: 'Primero', last: 'Ultimo', next: 'Siguiente', previous: 'Anterior' },
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

<?php include '../layouts/footer.php'; ?>
