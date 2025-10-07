<?php

session_start();

require_once '../clases/licencia.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$licencia = new Licencia();
$auditoria = new Auditoria();

switch ($accion) {
    case 'registrar':
        $datos = [
            'num_lic'     => trim($_POST['num_lic'] ?? ''),
            'fky_pat'     => (int)($_POST['fky_pat'] ?? 0),
            'fec_exp_lic' => $_POST['fec_exp_lic'] ?? date('Y-m-d'),
            'vig_lic'     => $_POST['vig_lic'] ?? date('Y-m-d'),
            'est_lic'     => $_POST['est_lic'] ?? 'S',
        ];
        $nuevoId = null;

        if ($licencia->numero_existe($datos['num_lic'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de licencia ya existe.'];
        } elseif ($licencia->existe_activa_para_patente($datos['fky_pat'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'La patente seleccionada ya posee una licencia activa.'];
        } elseif ($licencia->registrar($datos)) {
            $nuevoId = $licencia->id_lic ?? null;
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Licencia registrada exitosamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Ocurrio un error al registrar la licencia.'];
        }

        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Licencias',
            'Registrar',
            'Nueva licencia: ' . ($datos['num_lic'] ?: 'SIN_NUMERO'),
            'licencia',
            $nuevoId,
            ['patente' => $datos['fky_pat']]
        );
        break;

    case 'modificar':
        $datos = [
            'id_lic'     => (int)($_POST['id_lic'] ?? 0),
            'num_lic'    => trim($_POST['num_lic'] ?? ''),
            'fky_pat'    => (int)($_POST['fky_pat'] ?? 0),
            'fec_exp_lic'=> $_POST['fec_exp_lic'] ?? date('Y-m-d'),
            'vig_lic'    => $_POST['vig_lic'] ?? date('Y-m-d'),
            'est_lic'    => $_POST['est_lic'] ?? 'S',
        ];

        if ($licencia->numero_existe($datos['num_lic'], $datos['id_lic'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de licencia ya esta asignado a otro registro.'];
        } elseif ($licencia->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'La licencia se modifico correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar la licencia.'];
        }

        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Licencias',
            'Modificacion',
            'Edicion de licencia ID ' . $datos['id_lic'],
            'licencia',
            $datos['id_lic']
        );
        break;

    case 'cambiar_estado':
        if (isset($_GET['id'], $_GET['nuevo_estado'])) {
            $id = (int)$_GET['id'];
            $nuevoEstado = $_GET['nuevo_estado'];
            $ok = $licencia->cambiar_estado_registro($id, $nuevoEstado);
            if ($ok) {
                $_SESSION['mensaje'] = [
                    'tipo'   => 'success',
                    'titulo' => 'Estado actualizado',
                    'texto'  => 'El estado del registro de la licencia se actualizo correctamente.'
                ];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Licencias',
                    'Cambio de estado',
                    'Cambio de estado de registro licencia ID ' . $id,
                    'licencia',
                    $id,
                    ['nuevo_estado' => $nuevoEstado]
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

    case 'actualizar_estado':
        if (isset($_GET['id'], $_GET['nuevo_estado'])) {
            $id = (int)$_GET['id'];
            $nuevoEstado = $_GET['nuevo_estado'];
            $ok = $licencia->actualizar_estado_licencia($id, $nuevoEstado);
            if ($ok) {
                $_SESSION['mensaje'] = [
                    'tipo'   => 'info',
                    'titulo' => 'Estado del tramite',
                    'texto'  => 'El estado de la licencia se actualizo correctamente.'
                ];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Licencias',
                    'Cambio de estado',
                    'Actualizacion de tramite licencia ID ' . $id,
                    'licencia',
                    $id,
                    ['estado_licencia' => $nuevoEstado]
                );
            } else {
                $_SESSION['mensaje'] = [
                    'tipo'   => 'warning',
                    'titulo' => 'Sin cambios',
                    'texto'  => 'No se pudo actualizar el estado del tramite.'
                ];
            }
        }
        break;

    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($licencia->eliminar($id)) {
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Licencia eliminada.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo eliminar la licencia.'];
            }
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Licencias',
                'Eliminacion',
                'Eliminacion de licencia ID ' . $id,
                'licencia',
                $id
            );
        }
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

if (!in_array($accion, ['login', 'logout'])) {
    header('Location: ../../Frontend/Vistas/licencias/gestionLicencias.php');
    exit();
}
