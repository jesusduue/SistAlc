<?php

session_start();

require_once '../clases/Inmueble.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new Inmueble();
$auditoria = new Auditoria();

switch ($accion) {
    case 'registrar':
        $datos = [
            'dir_inm'  => $_POST['dir_inm'] ?? '',
            'tip_inm'  => $_POST['tip_inm'] ?? 'Terreno',
            'area_inm' => $_POST['area_inm'] ?? 'Urbano',
        ];
        if ($modelo->registrar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Inmueble registrado correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo registrar el inmueble.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Registrar',
            'Inmueble registrado'
        );
        break;

    case 'modificar':
        $datos = [
            'id_inm'   => (int)($_POST['id_inm'] ?? 0),
            'dir_inm'  => $_POST['dir_inm'] ?? '',
            'tip_inm'  => $_POST['tip_inm'] ?? 'Terreno',
            'area_inm' => $_POST['area_inm'] ?? 'Urbano',
        ];
        if ($modelo->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Inmueble modificado correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar el inmueble.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Modificacion',
            'Inmueble ID ' . $datos['id_inm'],
            'inmueble',
            $datos['id_inm']
        );
        break;

    case 'cambiar_estado':
        if (isset($_GET['id'], $_GET['nuevo_estado'])) {
            $id = (int)$_GET['id'];
            $estado = $_GET['nuevo_estado'];
            if ($modelo->cambiar_estado($id, $estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Estado del inmueble actualizado.'];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Catastro',
                    'Cambio de estado',
                    'Inmueble ID ' . $id,
                    'inmueble',
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
    header('Location: ../../Frontend/Vistas/catastro/gestion_inmuebles.php');
    exit();
}
