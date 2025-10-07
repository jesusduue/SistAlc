<?php

session_start();

require_once '../clases/SolvenciaMunicipal.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new SolvenciaMunicipal();
$auditoria = new Auditoria();

switch ($accion) {
    case 'registrar':
        $datos = [
            'num_sol'  => trim($_POST['num_sol'] ?? ''),
            'fky_con'  => (int)($_POST['fky_con'] ?? 0),
            'fky_ced'  => (int)($_POST['fky_ced'] ?? 0),
            'sol_sol'  => $_POST['sol_sol'] ?? '',
            'fec_exp_sol' => $_POST['fec_exp_sol'] ?? date('Y-m-d'),
            'vig_sol'  => $_POST['vig_sol'] ?? date('Y-m-d'),
            'fky_usu'  => $_SESSION['id_usu'] ?? 0,
            'est_sol'  => $_POST['est_sol'] ?? 'S',
        ];
        if ($modelo->numero_existe($datos['num_sol'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de solvencia ya existe.'];
        } elseif ($modelo->registrar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Solvencia municipal registrada correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo registrar la solvencia municipal.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Registrar',
            'Solvencia municipal: ' . $datos['num_sol']
        );
        break;

    case 'modificar':
        $datos = [
            'id_sol'   => (int)($_POST['id_sol'] ?? 0),
            'num_sol'  => trim($_POST['num_sol'] ?? ''),
            'fky_con'  => (int)($_POST['fky_con'] ?? 0),
            'fky_ced'  => (int)($_POST['fky_ced'] ?? 0),
            'sol_sol'  => $_POST['sol_sol'] ?? '',
            'fec_exp_sol' => $_POST['fec_exp_sol'] ?? date('Y-m-d'),
            'vig_sol'  => $_POST['vig_sol'] ?? date('Y-m-d'),
            'fky_usu'  => $_SESSION['id_usu'] ?? 0,
            'est_sol'  => $_POST['est_sol'] ?? 'S',
        ];
        if ($modelo->numero_existe($datos['num_sol'], $datos['id_sol'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de solvencia ya existe en otro registro.'];
        } elseif ($modelo->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Solvencia municipal modificada correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar la solvencia municipal.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Modificacion',
            'Solvencia municipal ID ' . $datos['id_sol'],
            'solvencia_municipal',
            $datos['id_sol']
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
                    'Solvencia municipal ID ' . $id,
                    'solvencia_municipal',
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
                    'Solvencia municipal ID ' . $id,
                    'solvencia_municipal',
                    $id,
                    ['nuevo_estado' => $estado]
                );
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo actualizar el estado del registro.'];
            }
        }
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

if (!in_array($accion, ['login', 'logout'])) {
    header('Location: ../../Frontend/Vistas/catastro/gestion_solvencias_municipales.php');
    exit();
}
