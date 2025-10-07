<?php

session_start();

require_once '../clases/Linderos.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new Linderos();
$auditoria = new Auditoria();

function datos_linderos()
{
    return [
        'lin_norte' => $_POST['lin_norte'] ?? '',
        'lin_sur'   => $_POST['lin_sur'] ?? '',
        'lin_este'  => $_POST['lin_este'] ?? '',
        'lin_oeste' => $_POST['lin_oeste'] ?? '',
        'mt2_norte' => (float)($_POST['mt2_norte'] ?? 0),
        'mt2_sur'   => (float)($_POST['mt2_sur'] ?? 0),
        'mt2_este'  => (float)($_POST['mt2_este'] ?? 0),
        'mt2_oeste' => (float)($_POST['mt2_oeste'] ?? 0),
        'mt2_total' => (float)($_POST['mt2_total'] ?? 0),
    ];
}

switch ($accion) {
    case 'registrar':
        $datos = datos_linderos();
        if ($modelo->registrar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Linderos registrados correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo registrar el conjunto de linderos.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Registrar',
            'Conjunto de linderos registrado'
        );
        break;

    case 'modificar':
        $datos = datos_linderos();
        $datos['id_lin'] = (int)($_POST['id_lin'] ?? 0);
        if ($modelo->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Linderos modificados correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar el conjunto de linderos.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Modificacion',
            'Linderos ID ' . $datos['id_lin'],
            'linderos',
            $datos['id_lin']
        );
        break;

    case 'cambiar_estado':
        if (isset($_GET['id'], $_GET['nuevo_estado'])) {
            $id = (int)$_GET['id'];
            $estado = $_GET['nuevo_estado'];
            if ($modelo->cambiar_estado($id, $estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Estado actualizado.'];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Catastro',
                    'Cambio de estado',
                    'Linderos ID ' . $id,
                    'linderos',
                    $id,
                    ['nuevo_estado' => $estado]
                );
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo actualizar el estado.'];
            }
        }
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

if (!in_array($accion, ['login', 'logout'])) {
    header('Location: ../../Frontend/Vistas/catastro/gestion_linderos.php');
    exit();
}
