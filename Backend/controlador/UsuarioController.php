<?php
session_start();
    
// Incluir la clase Usuario
require_once '../clases/Usuario.php';

// Verificar si se ha enviado una acción desde un formulario
if (isset($_REQUEST['accion'])) {
    
    // Crear una instancia del controlador
    $controlador = new Usuario();

    // Determinar la acción a realizar
    switch ($_POST['accion']) {
        case 'login':
            $controlador->iniciarSesion();
            break;
        case 'logout':
            $controlador->cerrarSesion();
            break;
        // Aquí se pueden agregar más casos (registrar, actualizar, etc.)
        default:
            // Redirigir si la acción no es válida
            header('Location: ../../index.php');
            break;
    }
} else {
    // Si no se especifica una acción, redirigir al inicio
    header('Location: ../../index.php');
    exit();
}




?>
