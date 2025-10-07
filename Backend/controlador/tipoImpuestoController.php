<?php

session_start();

require_once '../clases/TipoImpuesto.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new TipoImpuesto();
$auditoria = new Auditoria();

switch ($accion) {
    case 'registrar':
        $datos = [
            'nom_tip' => trim($_POST['nom_tip'] ?? ''),
            'des_tip' => $_POST['des_tip'] ?? null,
        ];
        if ($modelo->nombre_existe($datos['nom_tip'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El nombre del impuesto ya existe.'];
        } elseif ($modelo->registrar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Tipo de impuesto registrado correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo registrar el tipo de impuesto.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Impuestos',
            'Registrar',
            'Tipo de impuesto: ' . $datos['nom_tip']
        );
        break;

    case 'modificar':
        $datos = [
            'id_tip'  => (int)($_POST['id_tip'] ?? 0),
            'nom_tip' => trim($_POST['nom_tip'] ?? ''),
            'des_tip' => $_POST['des_tip'] ?? null,
        ];
        if ($modelo->nombre_existe($datos['nom_tip'], $datos['id_tip'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El nombre del impuesto ya existe.'];
        } elseif ($modelo->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Tipo de impuesto modificado.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar el tipo de impuesto.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Impuestos',
            'Modificacion',
            'Tipo de impuesto editado ID ' . $datos['id_tip']
        );
        break;

    case 'cambiar_estado':
        if (isset($_GET['id'], $_GET['nuevo_estado'])) {
            $id = (int)$_GET['id'];
            $estado = $_GET['nuevo_estado'];
            if ($modelo->cambiar_estado($id, $estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Estado actualizado correctamente.'];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Impuestos',
                    'Cambio de estado',
                    'Estado de tipo impuesto ID ' . $id,
                    'tipo_impuesto',
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
    header('Location: ../../Frontend/Vistas/impuestos/gestion_tipo_impuesto.php');
    exit();
}
