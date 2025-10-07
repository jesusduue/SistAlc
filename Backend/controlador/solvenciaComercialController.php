<?php

session_start();

require_once '../clases/SolvenciaComercial.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new SolvenciaComercial();
$auditoria = new Auditoria();

switch ($accion) {
    case 'registrar':
        $datos = [
            'num_sol_com'     => trim($_POST['num_sol_com'] ?? ''),
            'fky_pat'         => (int)($_POST['fky_pat'] ?? 0),
            'fec_exp_sol_com' => $_POST['fec_exp_sol_com'] ?? date('Y-m-d'),
            'vig_sol_com'     => $_POST['vig_sol_com'] ?? date('Y-m-d'),
            'est_sol_com'     => $_POST['est_sol_com'] ?? 'S',
        ];
        if ($modelo->numero_existe($datos['num_sol_com'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de solvencia comercial ya existe.'];
        } elseif ($modelo->registrar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Solvencia comercial registrada correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo registrar la solvencia comercial.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Comercio',
            'Registrar',
            'Solvencia comercial: ' . $datos['num_sol_com']
        );
        break;

    case 'modificar':
        $datos = [
            'id_sol_com'      => (int)($_POST['id_sol_com'] ?? 0),
            'num_sol_com'     => trim($_POST['num_sol_com'] ?? ''),
            'fky_pat'         => (int)($_POST['fky_pat'] ?? 0),
            'fec_exp_sol_com' => $_POST['fec_exp_sol_com'] ?? date('Y-m-d'),
            'vig_sol_com'     => $_POST['vig_sol_com'] ?? date('Y-m-d'),
            'est_sol_com'     => $_POST['est_sol_com'] ?? 'S',
        ];
        if ($modelo->numero_existe($datos['num_sol_com'], $datos['id_sol_com'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de solvencia comercial ya existe en otro registro.'];
        } elseif ($modelo->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Solvencia comercial modificada correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar la solvencia comercial.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Comercio',
            'Modificacion',
            'Solvencia comercial ID ' . $datos['id_sol_com'],
            'solvencia_comercial',
            $datos['id_sol_com']
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
                    'Comercio',
                    'Actualizacion de tramite',
                    'Solvencia comercial ID ' . $id,
                    'solvencia_comercial',
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
                    'Comercio',
                    'Cambio de estado',
                    'Solvencia comercial ID ' . $id,
                    'solvencia_comercial',
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
    header('Location: ../../Frontend/Vistas/comercio/gestion_solvencias_comerciales.php');
    exit();
}
