<?php
session_start();

// Incluir la clase Usuario
require_once '../clases/Usuario.php';
require_once '../clases/auditoria.php';


// Verificar si se ha enviado una acción desde un formulario o URL
if (isset($_REQUEST['accion'])) {
    $accion = $_REQUEST['accion'];
    $controlador = new Usuario();
    $auditoria = new Auditoria();

    switch ($accion) {
        case 'login':
            $controlador->iniciarSesion();
            break;

        case 'logout':

            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'desconocido',
                'Sistema',
                'Logout',
                'Cierre de sesión'
            );
            $controlador->cerrarSesion();

            break;

        case 'registrar':
            $controlador->registrar($_POST);
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario registrado correctamente'];
            
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'desconocido',
                'Sistema',
                'Registrar',
                'Usuario registrado: ' . $_POST['nom_usu']
            );
            break;

        case 'modificar':
            $controlador->modificar($_POST);
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario modificado correctamente'];
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'desconocido',
                'Sistema',
                'Modificación',
                'Usuario modificado: ' . $_POST['nom_usu']
            );
            break;

        case 'cambiar_estado':
            if (isset($_GET['id'], $_GET['estado_nuevo'])) {
                $ok = $controlador->cambiar_estado((int)$_GET['id'], $_GET['estado_nuevo']);
                if ($ok) {
                    $_SESSION['mensaje'] = [
                        'tipo'  => 'success', // success | info | warning | error
                        'titulo' => 'Estado actualizado',
                        'texto' => 'El estado del usuario se cambió correctamente.'
                    ];
                    $auditoria->registrarActividad(
                        $_SESSION['id_usu'] ?? 0,
                        $_SESSION['nom_usu'] ?? 'desconocido',
                        'Sistema',
                        'Cambio de estado',
                        'Estado del usuario cambiado: ID ' . $_GET['id'] . ' a ' . $_GET['estado_nuevo']
                    );
                } else {
                    $_SESSION['mensaje'] = [
                        'tipo'  => 'error',
                        'titulo' => 'No se pudo actualizar',
                        'texto' => 'Ocurrió un problema al cambiar el estado.'
                    ];
                }
                /* $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Estado del usuario actualizado']; */
            }
            break;
            
            case 'eliminar':
                if (isset($_GET['id'])) {
                    $ok = $controlador->eliminar((int)$_GET['id']);
                    if ($ok) {
                        $_SESSION['mensaje'] = [
                            'tipo'  => 'success',
                            'titulo' => 'Usuario eliminado',
                            'texto' => 'El usuario se eliminó correctamente.'
                        ];
                        $auditoria->registrarActividad(
                            $_SESSION['id_usu'] ?? 0,
                            $_SESSION['nom_usu'] ?? 'desconocido',
                            'Sistema',
                            'Eliminación',
                            'Usuario eliminado: ID ' . $_GET['id']
                        );
                    } else {
                        $_SESSION['mensaje'] = [
                            'tipo'  => 'error',
                            'titulo' => 'No se pudo eliminar',
                            'texto' => 'Ocurrió un problema al eliminar el usuario.'
                        ];
                    }
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
