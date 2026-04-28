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
$redireccion = '../../Frontend/Vistas/usuarios/gestion_usuarios.php';

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

    case 'cambiar_clave':
        $redireccion = '../../Frontend/Vistas/usuarios/perfil.php';
        $idUsuario = (int)($_SESSION['id_usu'] ?? 0);
        $claveActual = (string)($_POST['cla_actual'] ?? '');
        $claveNueva = (string)($_POST['cla_nueva'] ?? '');
        $claveConfirmar = (string)($_POST['cla_confirmar'] ?? '');

        if ($idUsuario <= 0) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo identificar al usuario autenticado.'];
            $auditoria->registrarActividad(
                0,
                $_SESSION['nom_usu'] ?? 'desconocido',
                'Perfil',
                'Cambio de clave rechazado',
                'Intento de cambio de clave sin sesion valida'
            );
            break;
        }

        if ($claveActual === '' || $claveNueva === '' || $claveConfirmar === '') {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Debe completar todos los campos de seguridad.'];
            $auditoria->registrarActividad(
                $idUsuario,
                $_SESSION['nom_usu'] ?? 'desconocido',
                'Perfil',
                'Cambio de clave rechazado',
                'Campos incompletos en cambio de clave'
            );
            break;
        }

        if ($claveNueva !== $claveConfirmar) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'La nueva contrasena y su confirmacion no coinciden.'];
            $auditoria->registrarActividad(
                $idUsuario,
                $_SESSION['nom_usu'] ?? 'desconocido',
                'Perfil',
                'Cambio de clave rechazado',
                'La confirmacion de la nueva clave no coincide'
            );
            break;
        }

        if ($controlador->cambiar_clave($idUsuario, $claveActual, $claveNueva)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Contrasena actualizada correctamente.'];
            $auditoria->registrarActividad(
                $idUsuario,
                $_SESSION['nom_usu'] ?? 'desconocido',
                'Perfil',
                'Cambio de clave',
                'El usuario actualizo su contrasena'
            );
            break;
        }

        $mensajesErrorClave = [
            'usuario_no_encontrado' => 'No se encontro el usuario autenticado.',
            'usuario_inactivo' => 'El usuario se encuentra inactivo y no puede cambiar la contrasena.',
            'clave_actual_incorrecta' => 'La contrasena actual no es correcta.',
            'clave_nueva_corta' => 'La nueva contrasena debe tener al menos 8 caracteres.',
            'clave_igual_actual' => 'La nueva contrasena debe ser distinta a la actual.',
            'error_consulta' => 'No se pudo validar la contrasena actual.',
            'error_actualizacion' => 'No se pudo actualizar la contrasena.',
        ];

        $_SESSION['mensaje'] = [
            'tipo' => 'danger',
            'texto' => $mensajesErrorClave[$controlador->ultimo_error] ?? 'Ocurrio un problema al cambiar la contrasena.'
        ];
        $auditoria->registrarActividad(
            $idUsuario,
            $_SESSION['nom_usu'] ?? 'desconocido',
            'Perfil',
            'Cambio de clave fallido',
            'No se pudo completar el cambio de contrasena',
            'usuario',
            $idUsuario,
            ['motivo' => $controlador->ultimo_error]
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
    header('Location: ' . $redireccion);
    exit();
}
?>
