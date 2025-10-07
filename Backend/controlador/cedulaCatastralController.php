<?php

session_start();

require_once '../clases/CedulaCatastral.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new CedulaCatastral();
$auditoria = new Auditoria();

switch ($accion) {
    case 'registrar':
        $datos = [
            'num_ced'     => trim($_POST['num_ced'] ?? ''),
            'num_exp'     => trim($_POST['num_exp'] ?? ''),
            'fky_con'     => (int)($_POST['fky_con'] ?? 0),
            'fky_inm'     => (int)($_POST['fky_inm'] ?? 0),
            'fky_lin'     => (int)($_POST['fky_lin'] ?? 0),
            'fky_doc'     => (int)($_POST['fky_doc'] ?? 0),
            'val_ced'     => (float)($_POST['val_ced'] ?? 0),
            'fec_exp_ced' => $_POST['fec_exp_ced'] ?? date('Y-m-d'),
            'vig_ced'     => $_POST['vig_ced'] ?? date('Y-m-d'),
            'sol_ced'     => $_POST['sol_ced'] ?? '',
            'fky_usu'     => $_SESSION['id_usu'] ?? 0,
            'est_ced'     => $_POST['est_ced'] ?? 'S',
        ];

        if ($modelo->numero_existe($datos['num_ced'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de cedula ya existe.'];
        } elseif ($modelo->registrar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Cedula catastral registrada correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo registrar la cedula catastral.'];
        }

        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Registrar',
            'Cedula catastral: ' . $datos['num_ced']
        );
        break;

    case 'modificar':
        $datos = [
            'id_ced'      => (int)($_POST['id_ced'] ?? 0),
            'num_ced'     => trim($_POST['num_ced'] ?? ''),
            'num_exp'     => trim($_POST['num_exp'] ?? ''),
            'fky_con'     => (int)($_POST['fky_con'] ?? 0),
            'fky_inm'     => (int)($_POST['fky_inm'] ?? 0),
            'fky_lin'     => (int)($_POST['fky_lin'] ?? 0),
            'fky_doc'     => (int)($_POST['fky_doc'] ?? 0),
            'val_ced'     => (float)($_POST['val_ced'] ?? 0),
            'fec_exp_ced' => $_POST['fec_exp_ced'] ?? date('Y-m-d'),
            'vig_ced'     => $_POST['vig_ced'] ?? date('Y-m-d'),
            'sol_ced'     => $_POST['sol_ced'] ?? '',
            'fky_usu'     => $_SESSION['id_usu'] ?? 0,
            'est_ced'     => $_POST['est_ced'] ?? 'S',
        ];

        if ($modelo->numero_existe($datos['num_ced'], $datos['id_ced'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de cedula ya existe en otro registro.'];
        } elseif ($modelo->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Cedula catastral modificada correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar la cedula catastral.'];
        }

        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Modificacion',
            'Cedula catastral ID ' . $datos['id_ced'],
            'cedula_catastral',
            $datos['id_ced']
        );
        break;

    case 'actualizar_estado':
        if (isset($_GET['id'], $_GET['estado'])) {
            $id = (int)$_GET['id'];
            $estado = $_GET['estado'];
            if ($modelo->actualizar_estado($id, $estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Estado del tramite actualizado.'];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Catastro',
                    'Actualizacion de tramite',
                    'Cedula ID ' . $id,
                    'cedula_catastral',
                    $id,
                    ['estado_tramite' => $estado]
                );
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo actualizar el estado del tramite.'];
            }
        }
        break;

    case 'cambiar_estado':
        if (isset($_GET['id'], $_GET['nuevo_estado'])) {
            $id = (int)$_GET['id'];
            $estado = $_GET['nuevo_estado'];
            if ($modelo->cambiar_estado_registro($id, $estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Estado del registro actualizado.'];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Catastro',
                    'Cambio de estado',
                    'Cedula ID ' . $id,
                    'cedula_catastral',
                    $id,
                    ['nuevo_estado' => $estado]
                );
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo actualizar el estado del registro.'];
            }
        }
        break;

    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($modelo->cambiar_estado_registro($id, 'I')) {
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Cedula catastral inactivada.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo inactivar la cedula catastral.'];
            }
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Catastro',
                'Eliminacion',
                'Cedula ID ' . $id,
                'cedula_catastral',
                $id
            );
        }
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

if (!in_array($accion, ['login', 'logout'])) {
    header('Location: ../../Frontend/Vistas/catastro/gestion_cedulas.php');
    exit();
}
