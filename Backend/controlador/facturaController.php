<?php

session_start();

require_once '../clases/Factura.php';
require_once '../clases/DetalleImpuesto.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$factura = new Factura();
$detalle = new DetalleImpuesto();
$auditoria = new Auditoria();

function decodificar_detalles($entrada)
{
    if (empty($entrada)) {
        return [];
    }
    if (is_array($entrada)) {
        return $entrada;
    }
    $decoded = json_decode($entrada, true);
    return is_array($decoded) ? $decoded : [];
}

switch ($accion) {
    case 'registrar':
        $datos = [
            'num_fac'  => trim($_POST['num_fac'] ?? ''),
            'fec_fac'  => $_POST['fec_fac'] ?? date('Y-m-d H:i:s'),
            'fky_usu'  => $_SESSION['id_usu'] ?? 0,
            'fky_con'  => (int)($_POST['fky_con'] ?? 0),
            'des_fac'  => $_POST['des_fac'] ?? '',
            'tot_fac'  => (float)($_POST['tot_fac'] ?? 0),
            'est_pago' => $_POST['est_pago'] ?? 'E',
        ];
        $detalles = decodificar_detalles($_POST['detalles'] ?? '');

        if ($factura->numero_existe($datos['num_fac'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de factura ya existe.'];
            break;
        }

        $ok = !empty($detalles)
            ? $factura->registrar_con_detalles($datos, $detalles)
            : $factura->registrar($datos);

        if ($ok) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Factura registrada correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo registrar la factura.'];
        }

        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Facturacion',
            'Registrar',
            'Factura registrada: ' . $datos['num_fac'],
            'factura',
            $factura->id_fac ?? null,
            ['total' => $datos['tot_fac'], 'detalles' => count($detalles)]
        );
        break;

    case 'modificar':
        $datos = [
            'id_fac'   => (int)($_POST['id_fac'] ?? 0),
            'num_fac'  => trim($_POST['num_fac'] ?? ''),
            'fec_fac'  => $_POST['fec_fac'] ?? date('Y-m-d H:i:s'),
            'fky_usu'  => $_SESSION['id_usu'] ?? 0,
            'fky_con'  => (int)($_POST['fky_con'] ?? 0),
            'des_fac'  => $_POST['des_fac'] ?? '',
            'tot_fac'  => (float)($_POST['tot_fac'] ?? 0),
            'est_pago' => $_POST['est_pago'] ?? 'E',
        ];
        $detalles = decodificar_detalles($_POST['detalles'] ?? '');

        if ($factura->numero_existe($datos['num_fac'], $datos['id_fac'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de factura ya existe en otro registro.'];
            break;
        }

        $ok = $factura->modificar($datos);
        if ($ok && !empty($detalles)) {
            $detalle->desactivar_por_factura($datos['id_fac']);
            $ok = $detalle->registrar_lote($datos['id_fac'], $detalles);
        }

        $_SESSION['mensaje'] = $ok
            ? ['tipo' => 'success', 'texto' => 'Factura modificada correctamente.']
            : ['tipo' => 'danger', 'texto' => 'No se pudo modificar la factura.'];

        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Facturacion',
            'Modificacion',
            'Factura modificada ID ' . $datos['id_fac'],
            'factura',
            $datos['id_fac'],
            ['detalles_actualizados' => count($detalles)]
        );
        break;

    case 'cambiar_estado':
        if (isset($_GET['id'], $_GET['nuevo_estado'])) {
            $id = (int)$_GET['id'];
            $estado = $_GET['nuevo_estado'];
            if ($factura->cambiar_estado_registro($id, $estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Estado de la factura actualizado.'];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Facturacion',
                    'Cambio de estado',
                    'Factura ID ' . $id,
                    'factura',
                    $id,
                    ['nuevo_estado' => $estado]
                );
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo actualizar el estado.'];
            }
        }
        break;

    case 'actualizar_pago':
        if (isset($_GET['id'], $_GET['estado'])) {
            $id = (int)$_GET['id'];
            $estado = $_GET['estado'];
            if ($factura->actualizar_estado_pago($id, $estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Estado de pago actualizado.'];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Facturacion',
                    'Actualizacion de pago',
                    'Factura ID ' . $id,
                    'factura',
                    $id,
                    ['estado_pago' => $estado]
                );
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo actualizar el estado de pago.'];
            }
        }
        break;

    case 'eliminar':
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            if ($factura->cambiar_estado_registro($id, 'I')) {
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Factura inactivada.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo inactivar la factura.'];
            }
            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Facturacion',
                'Eliminacion',
                'Factura ID ' . $id,
                'factura',
                $id
            );
        }
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

if (!in_array($accion, ['login', 'logout'])) {
    header('Location: ../../Frontend/Vistas/facturacion/gestion_facturas.php');
    exit();
}
