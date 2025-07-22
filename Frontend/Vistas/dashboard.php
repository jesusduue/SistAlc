<?php
include 'layouts/header.php';
?>

<div class="container">
    <h1>Bienvenido al sistema de Alcaldía</h1>
    <p>
        <?php
        echo $nombre_usuario . " " . $nombre_rol;
        ?>
    </p>
</div>  

<!-- Boton para cerrar sesion    -->
<a href="../../Backend/controlador/UsuarioController.php?accion=logout" class="btn btn-danger">Cerrar sesión</a>




<?php
include 'layouts/footer.php';
?>  