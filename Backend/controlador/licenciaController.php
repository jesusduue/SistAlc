<?php

session_start();

require_once '../clases/licencia.php';
require_once '../clases/Factura.php';
require_once '../clases/Patente.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

function validarFacturaParaLicencia(
    Licencia $licencia,
    Factura $facturaModel,
    Patente $patenteModel,
    string $numeroFactura,
    int $patenteId,
    ?int $licenciaId = null
): array {
    if ($numeroFactura === '') {
        return ['ok' => false, 'mensaje' => 'Debe indicar el numero de factura asociada.'];
    }

    $factura = $facturaModel->consultar_por_numero($numeroFactura);
    if (!$factura) {
        return ['ok' => false, 'mensaje' => 'No se encontro la factura indicada.'];
    }
    if ($factura['est_registro'] !== 'A') {
        return ['ok' => false, 'mensaje' => 'La factura indicada no esta activa.'];
    }
    if ($factura['est_pago'] !== 'P') {
        return ['ok' => false, 'mensaje' => 'La factura debe estar pagada para emitir la licencia.'];
    }
    if (!empty($factura['fec_anulacion'])) {
        return ['ok' => false, 'mensaje' => 'La factura indicada fue anulada y no puede utilizarse.'];
    }

    $patente = $patenteModel->consultar_por_id($patenteId);
    if (!$patente) {
        return ['ok' => false, 'mensaje' => 'La patente seleccionada no existe.'];
    }
    if ((int)$factura['fky_con'] !== (int)$patente['fky_con']) {
        return ['ok' => false, 'mensaje' => 'La factura pertenece a un contribuyente distinto al de la patente.'];
    }

    if ($licencia->factura_en_uso((int)$factura['id_fac'], $licenciaId)) {
        return ['ok' => false, 'mensaje' => 'La factura indicada ya esta asociada a otra licencia.'];
    }

    return ['ok' => true, 'factura' => $factura];
}

function responderJson(array $payload): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit();
}

$accion = $_REQUEST['accion'];
$licencia = new Licencia();
$facturaModel = new Factura();
$patenteModel = new Patente();
$auditoria = new Auditoria();

switch ($accion) {
    case 'registrar':
        $datos = [
            'num_lic'     => trim($_POST['num_lic'] ?? ''),
            'fky_pat'     => (int)($_POST['fky_pat'] ?? 0),
            'fec_exp_lic' => $_POST['fec_exp_lic'] ?? date('Y-m-d'),
            'vig_lic'     => $_POST['vig_lic'] ?? date('Y-m-d'),
            'est_lic'     => $_POST['est_lic'] ?? 'S',
            'fky_fac'     => null,
        ];
        $numeroFactura = trim($_POST['num_fac_ref'] ?? '');
        $nuevoId = null;

        if ($datos['num_lic'] === '') {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Debe indicar el numero de la licencia.'];
            break;
        }
        if ($datos['fky_pat'] <= 0) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Debe seleccionar la patente asociada.'];
            break;
        }

        $validacionFactura = validarFacturaParaLicencia(
            $licencia,
            $facturaModel,
            $patenteModel,
            $numeroFactura,
            $datos['fky_pat']
        );
        if (!$validacionFactura['ok']) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => $validacionFactura['mensaje']];
            break;
        }

        if ($licencia->numero_existe($datos['num_lic'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de licencia ya existe.'];
            break;
        }
        if ($licencia->existe_activa_para_patente($datos['fky_pat'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'La patente seleccionada ya posee una licencia activa.'];
            break;
        }

        $datos['fky_fac'] = (int)$validacionFactura['factura']['id_fac'];

        if ($licencia->registrar($datos)) {
            $nuevoId = $licencia->id_lic ?? null;
            $enlaceImpresion = $nuevoId
                ? '<br><a href="../../Frontend/Vistas/licencias/imprimirLicencia.php?id=' . $nuevoId . '" class="btn btn-sm btn-outline-primary mt-2" target="_blank" rel="noopener">Imprimir licencia</a>'
                : '';
            $_SESSION['mensaje'] = [
                'tipo' => 'success',
                'texto' => 'Licencia registrada exitosamente.' . $enlaceImpresion,
            ];
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
            ['patente' => $datos['fky_pat'], 'factura' => $numeroFactura]
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
            'fky_fac'    => null,
        ];
        $numeroFactura = trim($_POST['num_fac_ref'] ?? '');

        if ($datos['id_lic'] <= 0) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'No se recibio la licencia a modificar.'];
            break;
        }
        if ($datos['num_lic'] === '') {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Debe indicar el numero de la licencia.'];
            break;
        }
        if ($datos['fky_pat'] <= 0) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Debe seleccionar la patente asociada.'];
            break;
        }

        $validacionFactura = validarFacturaParaLicencia(
            $licencia,
            $facturaModel,
            $patenteModel,
            $numeroFactura,
            $datos['fky_pat'],
            $datos['id_lic']
        );
        if (!$validacionFactura['ok']) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => $validacionFactura['mensaje']];
            break;
        }

        if ($licencia->numero_existe($datos['num_lic'], $datos['id_lic'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El numero de licencia ya esta asignado a otro registro.'];
            break;
        }
        if ($licencia->existe_activa_para_patente($datos['fky_pat'], $datos['id_lic'])) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'La patente seleccionada ya tiene otra licencia activa.'];
            break;
        }

        $datos['fky_fac'] = (int)$validacionFactura['factura']['id_fac'];

        if ($licencia->modificar($datos)) {
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
            $datos['id_lic'],
            ['patente' => $datos['fky_pat'], 'factura' => $numeroFactura]
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

    case 'validar_factura':
        $numero = trim($_REQUEST['num_fac_ref'] ?? '');
        $patenteId = isset($_REQUEST['fky_pat']) ? (int)$_REQUEST['fky_pat'] : 0;
        $licenciaId = isset($_REQUEST['id_lic']) ? (int)$_REQUEST['id_lic'] : null;
        $resultado = validarFacturaParaLicencia(
            $licencia,
            $facturaModel,
            $patenteModel,
            $numero,
            $patenteId,
            $licenciaId
        );
        if (!$resultado['ok']) {
            responderJson(['ok' => false, 'mensaje' => $resultado['mensaje']]);
        }
        $factura = $resultado['factura'];
        responderJson([
            'ok' => true,
            'factura' => [
                'id_fac' => (int)$factura['id_fac'],
                'num_fac' => $factura['num_fac'],
                'fky_con' => (int)$factura['fky_con'],
                'nom_con' => $factura['nom_con'] ?? '',
                'rif_con' => $factura['rif_con'] ?? '',
                'tot_fac' => (float)$factura['tot_fac'],
                'fec_fac' => $factura['fec_fac'],
            ],
        ]);
        break;

    default:
        header('Location: ../../index.php');
        exit();
}

if (!in_array($accion, ['login', 'logout', 'validar_factura'], true)) {
    header('Location: ../../Frontend/Vistas/licencias/gestionLicencias.php');
    exit();
}

