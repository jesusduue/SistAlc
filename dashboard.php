<?php
include 'Frontend/Vistas/layouts/header.php';
?>

<div class="container">
    <h1>Bienvenido al sistema de Alcaldía</h1>
    <p>
        <?php
        echo "Bienvenido " . $_SESSION['id_usu'] . " al sistema de Alcaldía" . " " . $_SESSION['rol'];
        ?>
    </p>
</div>  






<?php
include 'Frontend/Vistas/layouts/footer.php';
?>  