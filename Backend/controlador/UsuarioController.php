<?php
session_start();

// Incluir la clase Usuario
require_once '../clases/Usuario.php';

// Verificar si se ha enviado una acción desde un formulario o URL
if (isset($_REQUEST['accion'])) {
    $accion = $_REQUEST['accion'];
    $controlador = new Usuario();

    switch ($accion) {
        case 'login':
            $controlador->iniciarSesion();
            break;

        case 'logout':
            $controlador->cerrarSesion();
            break;

        case 'registrar':
            $controlador->registrar($_POST);
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario registrado correctamente'];
            break;

        case 'modificar':
            $controlador->modificar($_POST);
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario modificado correctamente'];
            break;

        case 'cambiar_estado':
            if (isset($_GET['id'], $_GET['estado_nuevo'])) {
                $controlador->cambiar_estado((int)$_GET['id'], $_GET['estado_nuevo']);
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Estado del usuario actualizado'];
            }
            break;

        default:
            header('Location: ../../index.php');
            exit();
    }
    // Redirige al módulo de gestión para todo excepto login/logout
    if (!in_array($accion, ['login', 'logout'])) {
        header('Location: ../../Frontend/Vistas/usuarios/gestion_usuarios.php');
        exit();
    }
} else {
    header('Location: ../../index.php');
    exit();
}
?>
