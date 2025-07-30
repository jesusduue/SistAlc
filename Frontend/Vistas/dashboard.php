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
 <!-- se hace de esta manera ya que el controlador esta recibiendo el valor 
    unicamente por metodo post -->
<form action="../../Backend/controlador/UsuarioController.php" method="POST">
    <input type="hidden" name="accion" value="logout">
    <button type="submit" class="btn btn-danger">Cerrar sesión</button>
</form>

<?php
include 'layouts/footer.php';
?>  