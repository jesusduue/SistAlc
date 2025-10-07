<?php
session_start();

require_once '../clases/Usuario.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

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
            'Cierre de sesion'
        );
        $controlador->cerrarSesion();
        break;

    case 'registrar':
        $datos = [
            'nom_usu'      => trim($_POST['nom_usu'] ?? ''),
            'cla_usu'      => $_POST['cla_usu'] ?? '',
            'fky_rol'      => (int)($_POST['fky_rol'] ?? 0),
            'est_registro' => $_POST['est_registro'] ?? ($_POST['est_usu'] ?? 'A'),
        ];
        if ($controlador->registrar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario registrado correctamente'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo registrar el usuario.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'desconocido',
            'Usuarios',
            'Registrar',
            'Usuario registrado: ' . $datos['nom_usu']
        );
        break;

    case 'modificar':
        $datos = [
            'id_usu'       => (int)($_POST['id_usu'] ?? 0),
            'nom_usu'      => $_POST['nom_usu'] ?? '',
            'cla_usu'      => $_POST['cla_usu'] ?? '',
            'fky_rol'      => (int)($_POST['fky_rol'] ?? 0),
            'est_registro' => $_POST['est_registro'] ?? ($_POST['est_usu'] ?? 'A'),
        ];
        if ($controlador->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario modificado correctamente'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar el usuario.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'desconocido',
            'Usuarios',
            'Modificacion',
            'Usuario modificado: ' . $datos['id_usu']
        );
        break;

    case 'cambiar_estado':
        if (isset($_GET['id'], $_GET['estado_nuevo'])) {
            $ok = $controlador->cambiar_estado((int)$_GET['id'], $_GET['estado_nuevo']);
            if ($ok) {
                $_SESSION['mensaje'] = [
                    'tipo'   => 'success',
                    'titulo' => 'Estado actualizado',
                    'texto'  => 'El estado del usuario se cambio correctamente.'
                ];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'desconocido',
                    'Usuarios',
                    'Cambio de estado',
                    'Estado del usuario cambiado: ID ' . $_GET['id'] . ' a ' . $_GET['estado_nuevo']
                );
            } else {
                $_SESSION['mensaje'] = [
                    'tipo'   => 'error',
                    'titulo' => 'No se pudo actualizar',
                    'texto'  => 'Ocurrio un problema al cambiar el estado.'
                ];
            }
        }
        break;

    case 'eliminar':
        if (isset($_GET['id'])) {
            $ok = $controlador->eliminar((int)$_GET['id']);
            if ($ok) {
                $_SESSION['mensaje'] = [
                    'tipo'   => 'success',
                    'titulo' => 'Usuario eliminado',
                    'texto'  => 'El usuario se elimino correctamente.'
                ];
            } else {
                $_SESSION['mensaje'] = [
                    'tipo'   => 'error',
                    'titulo' => 'No se pudo eliminar',
                    'texto'  => 'Ocurrio un problema al eliminar el usuario.'
                ];
            }
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'desconocido',
                'Usuarios',
                'Eliminacion',
                'Usuario eliminado: ID ' . $_GET['id']
            );
        }
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

if (!in_array($accion, ['login', 'logout'])) {
    header('Location: ../../Frontend/Vistas/usuarios/gestion_usuarios.php');
    exit();
}
