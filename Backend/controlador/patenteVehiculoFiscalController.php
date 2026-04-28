<?php
session_start();

require_once '../clases/PatenteVehiculoFiscal.php';
require_once '../clases/VehiculoFiscal.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new PatenteVehiculoFiscal();
$vehiculoModel = new VehiculoFiscal();
$auditoria = new Auditoria();

function mensajePatenteVehiculo(string $tipo, string $texto): void
{
    $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
}

switch ($accion) {
    case 'registrar':
        $datos = [
            'num_pat_vf' => trim($_POST['num_pat_vf'] ?? ''),
            'fky_veh_fis' => (int)($_POST['fky_veh_fis'] ?? 0),
            'fec_emi_pat' => $_POST['fec_emi_pat'] ?? date('Y-m-d'),
            'fec_ven_pat' => $_POST['fec_ven_pat'] ?? date('Y-m-d'),
            'mon_pat' => (float)($_POST['mon_pat'] ?? 0),
            'est_pat' => strtolower(trim($_POST['est_pat'] ?? 'e')),
            'fky_usu' => (int)($_SESSION['id_usu'] ?? 0),
        ];

        if ($datos['num_pat_vf'] === '' || $datos['fky_veh_fis'] <= 0) {
            mensajePatenteVehiculo('warning', 'Debe completar los datos obligatorios de la patente vehicular.');
            break;
        }
        if (!$vehiculoModel->consultar_por_id($datos['fky_veh_fis'])) {
            mensajePatenteVehiculo('warning', 'El vehiculo fiscal seleccionado no existe.');
            break;
        }
        if ($datos['mon_pat'] <= 0) {
            mensajePatenteVehiculo('warning', 'Debe indicar un monto valido para la patente.');
            break;
        }
        if ($datos['fec_ven_pat'] < $datos['fec_emi_pat']) {
            mensajePatenteVehiculo('warning', 'La fecha de vencimiento no puede ser anterior a la fecha de emision.');
            break;
        }
        if (!in_array($datos['est_pat'], ['e', 'v', 'a'], true)) {
            mensajePatenteVehiculo('warning', 'El estado de la patente vehicular no es valido.');
            break;
        }
        if ($modelo->numero_existe($datos['num_pat_vf'])) {
            mensajePatenteVehiculo('warning', 'El numero de patente vehicular ya existe.');
            break;
        }

        if ($modelo->registrar($datos)) {
            mensajePatenteVehiculo('success', 'Patente de vehiculo registrada correctamente.');
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Patentes vehiculares',
                'Registrar',
                'Patente vehicular registrada: ' . $datos['num_pat_vf'],
                'patente_vehiculo_fiscal',
                $modelo->id_pat_vf ?? null,
                ['vehiculo' => $datos['fky_veh_fis'], 'monto' => $datos['mon_pat']]
            );
        } else {
            mensajePatenteVehiculo('danger', 'No se pudo registrar la patente vehicular.');
        }
        break;

    case 'modificar':
        $datos = [
            'id_pat_vf' => (int)($_POST['id_pat_vf'] ?? 0),
            'num_pat_vf' => trim($_POST['num_pat_vf'] ?? ''),
            'fky_veh_fis' => (int)($_POST['fky_veh_fis'] ?? 0),
            'fec_emi_pat' => $_POST['fec_emi_pat'] ?? date('Y-m-d'),
            'fec_ven_pat' => $_POST['fec_ven_pat'] ?? date('Y-m-d'),
            'mon_pat' => (float)($_POST['mon_pat'] ?? 0),
            'est_pat' => strtolower(trim($_POST['est_pat'] ?? 'e')),
        ];

        if ($datos['id_pat_vf'] <= 0 || $datos['num_pat_vf'] === '' || $datos['fky_veh_fis'] <= 0) {
            mensajePatenteVehiculo('warning', 'No se recibieron datos validos para modificar la patente vehicular.');
            break;
        }
        if (!$vehiculoModel->consultar_por_id($datos['fky_veh_fis'])) {
            mensajePatenteVehiculo('warning', 'El vehiculo fiscal seleccionado no existe.');
            break;
        }
        if ($datos['mon_pat'] <= 0) {
            mensajePatenteVehiculo('warning', 'Debe indicar un monto valido para la patente.');
            break;
        }
        if ($datos['fec_ven_pat'] < $datos['fec_emi_pat']) {
            mensajePatenteVehiculo('warning', 'La fecha de vencimiento no puede ser anterior a la fecha de emision.');
            break;
        }
        if (!in_array($datos['est_pat'], ['e', 'v', 'a'], true)) {
            mensajePatenteVehiculo('warning', 'El estado de la patente vehicular no es valido.');
            break;
        }
        if ($modelo->numero_existe($datos['num_pat_vf'], $datos['id_pat_vf'])) {
            mensajePatenteVehiculo('warning', 'El numero de patente vehicular ya esta asignado a otro registro.');
            break;
        }

        if ($modelo->modificar($datos)) {
            mensajePatenteVehiculo('success', 'Patente de vehiculo actualizada correctamente.');
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Patentes vehiculares',
                'Modificacion',
                'Patente vehicular actualizada: ' . $datos['num_pat_vf'],
                'patente_vehiculo_fiscal',
                $datos['id_pat_vf'],
                ['vehiculo' => $datos['fky_veh_fis'], 'monto' => $datos['mon_pat']]
            );
        } else {
            mensajePatenteVehiculo('danger', 'No se pudo actualizar la patente vehicular.');
        }
        break;

    case 'cambiar_estado':
        $id = (int)($_GET['id'] ?? 0);
        $estado = strtolower(trim($_GET['nuevo_estado'] ?? 'a'));
        if ($id > 0 && in_array($estado, ['a', 'i'], true) && $modelo->cambiar_estado($id, $estado)) {
            mensajePatenteVehiculo('success', 'Estado del registro de la patente vehicular actualizado.');
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Patentes vehiculares',
                'Cambio de estado',
                'Estado de registro de patente vehicular ID ' . $id,
                'patente_vehiculo_fiscal',
                $id,
                ['nuevo_estado' => $estado]
            );
        } else {
            mensajePatenteVehiculo('danger', 'No se pudo actualizar el estado del registro.');
        }
        break;

    case 'actualizar_estado':
        $id = (int)($_GET['id'] ?? 0);
        $estado = strtolower(trim($_GET['nuevo_estado'] ?? 'e'));
        if ($id > 0 && in_array($estado, ['e', 'v', 'a'], true) && $modelo->actualizar_estado_patente($id, $estado)) {
            mensajePatenteVehiculo('success', 'Estado de la patente vehicular actualizado.');
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Patentes vehiculares',
                'Cambio de estado',
                'Estado de patente vehicular ID ' . $id,
                'patente_vehiculo_fiscal',
                $id,
                ['estado_patente' => $estado]
            );
        } else {
            mensajePatenteVehiculo('danger', 'No se pudo actualizar el estado de la patente vehicular.');
        }
        break;

    case 'eliminar':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0 && $modelo->eliminar($id)) {
            mensajePatenteVehiculo('success', 'Patente de vehiculo eliminada correctamente.');
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Patentes vehiculares',
                'Eliminacion',
                'Patente vehicular eliminada ID ' . $id,
                'patente_vehiculo_fiscal',
                $id
            );
        } else {
            mensajePatenteVehiculo('danger', 'No se pudo eliminar la patente vehicular.');
        }
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

header('Location: ../../Frontend/Vistas/vehiculos/gestion_patentes_vehiculos.php');
exit();
?>
