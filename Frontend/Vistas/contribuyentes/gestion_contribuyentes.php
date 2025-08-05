<?php
// Vista: gestión de contribuyentes
include '../layouts/header.php';
include '../layouts/proteger_vista.php';
include '../layouts/sidebar.php';
restringirAcceso('Administrador');

// Incluir la clase contribuyente y obtener datos
require_once '../../../Backend/clases/Contribuyente.php';
$contribuyente = new Contribuyente();
$contribuyentes = $contribuyente->consultar_activos();
?>

<div class="container-fluid" style="margin-left: 250px; padding-top: 2rem;">
    <h1 class="mb-4">Gestión de Contribuyentes</h1>
    <!-- Mensaje de estado cuando se realiza una acción -->
<?php if (isset($_SESSION['mensaje'])): ?>
<div class="alert alert-<?php echo $_SESSION['mensaje']['tipo']; ?>">
    <?php echo $_SESSION['mensaje']['texto']; ?>
</div>
<?php unset($_SESSION['mensaje']); endif; ?>

    <!-- Botón para abrir el modal de registro -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregarContribuyente">
        <i class="fas fa-user-plus"></i> Agregar Contribuyente
    </button>

    <!-- Modal para registrar nuevo contribuyente -->
    <?php include 'modalRegistrarContribuyente.php'; ?>


        <!-- Campo de búsqueda -->
    <div class="mb-3">
        <input type="text" id="buscador" class="form-control" placeholder="Buscar contribuyente...">
    </div>
    <!-- Tabla de contribuyentes -->
    <?php include 'listarContribuyentes.php'; ?>
</div>

<?php include '../layouts/footer.php'; ?>

<!-- SCRIPTS PARA REALIZAR LA BUSQUEDA EN TIEMPO REAL CON JAVASCRIPT Y PAGINACION -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    //BUSQUEDA EN TIEMPO REAL
    $(document).ready(function () {

        $("#buscador").on("keyup", function () {
            var valor = $(this).val().toLowerCase();
            $("#tablaContribuyentes tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
                // Para depurar, puedes mostrar el valor buscado en la consola
                //  console.log("Buscando: " + valor);
            });
        });
    });

    //PAGINACION
    $(document).ready(function () {
        $('#tablaContribuyentes').DataTable({
            "paging": true, // Habilitar paginación
            "pageLength": 10, // Número de filas por página
            "lengthChange": false, // Desactivar el cambio de número de filas
            "searching": false,  // Desactivar la búsqueda integrada
            "ordering": true, // Habilitar ordenamiento
            "info": true, // Mostrar información sobre la tabla
            "autoWidth": false, // Desactivar el ajuste automático de ancho
            "language": {
                "paginate": {
                    "first": "Primero", 
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "info": "Mostrando _START_ a _END_ de _TOTAL_ contribuyentes",
                "infoEmpty": "Mostrando 0 a 0 de 0 contribuyentes",
                "infoFiltered": "(filtrado de _MAX_ contribuyentes totales)",
                "zeroRecords": "No se encontraron contribuyentes"
            }
        });
    });

</script>
