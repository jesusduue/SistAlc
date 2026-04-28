<?php
session_start();

require_once '../clases/VehiculoFiscal.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new VehiculoFiscal();
$auditoria = new Auditoria();

function mensajeVehiculo(string $tipo, string $texto): void
{
    $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
}

function validarAnoVehiculo(int $ano): bool
{
    $actual = (int)date('Y') + 1;
    return $ano >= 1950 && $ano <= $actual;
}

switch ($accion) {
    case 'registrar':
        $datos = [
            'fky_con' => (int)($_POST['fky_con'] ?? 0),
            'pla_veh' => strtoupper(trim($_POST['pla_veh'] ?? '')),
            'mar_veh' => trim($_POST['mar_veh'] ?? ''),
            'mod_veh' => trim($_POST['mod_veh'] ?? ''),
            'ano_veh' => (int)($_POST['ano_veh'] ?? 0),
            'col_veh' => trim($_POST['col_veh'] ?? ''),
            'tip_veh' => trim($_POST['tip_veh'] ?? ''),
            'cap_car_veh' => trim($_POST['cap_car_veh'] ?? ''),
            'num_ser_veh' => trim($_POST['num_ser_veh'] ?? ''),
            'est_veh' => strtolower(trim($_POST['est_veh'] ?? 'p')),
        ];

        if ($datos['fky_con'] <= 0 || $datos['pla_veh'] === '' || $datos['mar_veh'] === '' || $datos['mod_veh'] === '' || $datos['tip_veh'] === '') {
            mensajeVehiculo('warning', 'Debe completar los campos obligatorios del vehiculo.');
            break;
        }
        if (!validarAnoVehiculo($datos['ano_veh'])) {
            mensajeVehiculo('warning', 'Debe indicar un ano de vehiculo valido.');
            break;
        }
        if (!in_array($datos['est_veh'], ['p', 't', 'e'], true)) {
            mensajeVehiculo('warning', 'El estado del vehiculo no es valido.');
            break;
        }
        if ($modelo->placa_existe($datos['pla_veh'])) {
            mensajeVehiculo('warning', 'La placa indicada ya esta registrada.');
            break;
        }

        if ($modelo->registrar($datos)) {
            mensajeVehiculo('success', 'Vehiculo fiscal registrado correctamente.');
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Vehiculos',
                'Registrar',
                'Vehiculo registrado: ' . $datos['pla_veh'],
                'vehiculo_fiscal',
                $modelo->id_veh_fis ?? null,
                ['contribuyente' => $datos['fky_con']]
            );
        } else {
            mensajeVehiculo('danger', 'No se pudo registrar el vehiculo fiscal.');
        }
        break;

    case 'modificar':
        $datos = [
            'id_veh_fis' => (int)($_POST['id_veh_fis'] ?? 0),
            'fky_con' => (int)($_POST['fky_con'] ?? 0),
            'pla_veh' => strtoupper(trim($_POST['pla_veh'] ?? '')),
            'mar_veh' => trim($_POST['mar_veh'] ?? ''),
            'mod_veh' => trim($_POST['mod_veh'] ?? ''),
            'ano_veh' => (int)($_POST['ano_veh'] ?? 0),
            'col_veh' => trim($_POST['col_veh'] ?? ''),
            'tip_veh' => trim($_POST['tip_veh'] ?? ''),
            'cap_car_veh' => trim($_POST['cap_car_veh'] ?? ''),
            'num_ser_veh' => trim($_POST['num_ser_veh'] ?? ''),
            'est_veh' => strtolower(trim($_POST['est_veh'] ?? 'p')),
        ];

        if ($datos['id_veh_fis'] <= 0 || $datos['fky_con'] <= 0 || $datos['pla_veh'] === '' || $datos['mar_veh'] === '' || $datos['mod_veh'] === '' || $datos['tip_veh'] === '') {
            mensajeVehiculo('warning', 'No se recibieron datos validos para modificar el vehiculo.');
            break;
        }
        if (!validarAnoVehiculo($datos['ano_veh'])) {
            mensajeVehiculo('warning', 'Debe indicar un ano de vehiculo valido.');
            break;
        }
        if (!in_array($datos['est_veh'], ['p', 't', 'e'], true)) {
            mensajeVehiculo('warning', 'El estado del vehiculo no es valido.');
            break;
        }
        if ($modelo->placa_existe($datos['pla_veh'], $datos['id_veh_fis'])) {
            mensajeVehiculo('warning', 'La placa indicada ya esta asignada a otro vehiculo.');
            break;
        }

        if ($modelo->modificar($datos)) {
            mensajeVehiculo('success', 'Vehiculo fiscal actualizado correctamente.');
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Vehiculos',
                'Modificacion',
                'Vehiculo actualizado: ' . $datos['pla_veh'],
                'vehiculo_fiscal',
                $datos['id_veh_fis']
            );
        } else {
            mensajeVehiculo('danger', 'No se pudo actualizar el vehiculo fiscal.');
        }
        break;

    case 'cambiar_estado':
        $id = (int)($_GET['id'] ?? 0);
        $estado = strtolower(trim($_GET['nuevo_estado'] ?? 'a'));
        if ($id > 0 && in_array($estado, ['a', 'i'], true) && $modelo->cambiar_estado($id, $estado)) {
            mensajeVehiculo('success', 'Estado del vehiculo actualizado correctamente.');
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Vehiculos',
                'Cambio de estado',
                'Estado de registro del vehiculo ID ' . $id,
                'vehiculo_fiscal',
                $id,
                ['nuevo_estado' => $estado]
            );
        } else {
            mensajeVehiculo('danger', 'No se pudo actualizar el estado del vehiculo.');
        }
        break;

    case 'eliminar':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0 && $modelo->eliminar($id)) {
            mensajeVehiculo('success', 'Vehiculo fiscal eliminado correctamente.');
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Vehiculos',
                'Eliminacion',
                'Vehiculo eliminado ID ' . $id,
                'vehiculo_fiscal',
                $id
            );
        } else {
            mensajeVehiculo('danger', 'No se pudo eliminar el vehiculo fiscal.');
        }
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

header('Location: ../../Frontend/Vistas/vehiculos/gestion_vehiculos.php');
exit();
?>
