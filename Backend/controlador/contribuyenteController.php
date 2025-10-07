<?php

session_start();

require_once '../clases/Contribuyente.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$contribuyente = new Contribuyente();
$auditoria = new Auditoria();

switch ($accion) {
    case 'registrar':
        $datos = [
            'nom_con'          => trim($_POST['nom_con'] ?? ''),
            'rif_con'          => trim($_POST['rif_con'] ?? ''),
            'tel_con'          => $_POST['tel_con'] ?? '',
            'ema_con'          => $_POST['ema_con'] ?? '',
            'dir_con'          => $_POST['dir_con'] ?? '',
            'fky_usu_registro' => $_SESSION['id_usu'] ?? 1,
        ];

        if ($contribuyente->rif_existe($datos['rif_con'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El RIF ingresado ya existe.'];
            break;
        }

        $nuevoId = null;
        if ($contribuyente->registrar($datos)) {
            $nuevoId = $contribuyente->id_con ?? null;
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Contribuyente registrado exitosamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Ocurrio un error al registrar.'];
        }

        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Contribuyentes',
            'Registrar',
            'Nuevo contribuyente: ' . ($datos['nom_con'] ?: 'DESCONOCIDO'),
            'contribuyente',
            $nuevoId,
            ['rif' => $datos['rif_con']]
        );
        break;

    case 'modificar':
        $datos = [
            'id_con'  => (int)($_POST['id_con'] ?? 0),
            'nom_con' => $_POST['nom_con'] ?? '',
            'rif_con' => $_POST['rif_con'] ?? '',
            'tel_con' => $_POST['tel_con'] ?? '',
            'ema_con' => $_POST['ema_con'] ?? '',
            'dir_con' => $_POST['dir_con'] ?? '',
        ];

        if ($contribuyente->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Datos modificados correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar el contribuyente.'];
        }

        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Contribuyentes',
            'Modificacion',
            'Edicion de contribuyente ID ' . $datos['id_con'],
            'contribuyente',
            $datos['id_con']
        );
        break;

    case 'cambiar_estado':
        if (isset($_GET['id'], $_GET['nuevo_estado'])) {
            $id = (int)$_GET['id'];
            $nuevoEstado = $_GET['nuevo_estado'];
            $ok = $contribuyente->cambiar_estado($id, $nuevoEstado);
            if ($ok) {
                $_SESSION['mensaje'] = [
                    'tipo'   => 'success',
                    'titulo' => 'Estado actualizado',
                    'texto'  => 'El estado del contribuyente se actualizo correctamente.'
                ];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Contribuyentes',
                    'Cambio de estado',
                    'Cambio de estado contribuyente ID ' . $id,
                    'contribuyente',
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

    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($contribuyente->eliminar($id)) {
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Contribuyente eliminado.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo eliminar el contribuyente.'];
            }
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Contribuyentes',
                'Eliminacion',
                'Eliminacion de contribuyente ID ' . $id,
                'contribuyente',
                $id
            );
        }
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

if (!in_array($accion, ['login', 'logout'])) {
    header('Location: ../../Frontend/Vistas/contribuyentes/gestion_contribuyentes.php');
    exit();
}
