<?php

session_start();

require_once '../clases/DocumentoLegal.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new DocumentoLegal();
$auditoria = new Auditoria();

switch ($accion) {
    case 'registrar':
        $datos = [
            'tip_doc' => $_POST['tip_doc'] ?? 'Registrado',
            'num_doc' => trim($_POST['num_doc'] ?? ''),
            'matr_doc'=> $_POST['matr_doc'] ?? '',
            'fol_doc' => $_POST['fol_doc'] ?? '',
            'fec_doc' => $_POST['fec_doc'] ?? date('Y-m-d'),
            'des_doc' => $_POST['des_doc'] ?? '',
        ];
        if ($modelo->registrar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Documento legal registrado correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo registrar el documento legal.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Registrar',
            'Documento legal: ' . $datos['num_doc']
        );
        break;

    case 'modificar':
        $datos = [
            'id_doc'  => (int)($_POST['id_doc'] ?? 0),
            'tip_doc' => $_POST['tip_doc'] ?? 'Registrado',
            'num_doc' => trim($_POST['num_doc'] ?? ''),
            'matr_doc'=> $_POST['matr_doc'] ?? '',
            'fol_doc' => $_POST['fol_doc'] ?? '',
            'fec_doc' => $_POST['fec_doc'] ?? date('Y-m-d'),
            'des_doc' => $_POST['des_doc'] ?? '',
        ];
        if ($modelo->modificar($datos)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Documento legal modificado correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar el documento legal.'];
        }
        $auditoria->registrarActividad(
            $_SESSION['id_usu'] ?? 0,
            $_SESSION['nom_usu'] ?? 'Sistema',
            'Catastro',
            'Modificacion',
            'Documento legal ID ' . $datos['id_doc'],
            'documentos_legales',
            $datos['id_doc']
        );
        break;

    case 'cambiar_estado':
        if (isset($_GET['id'], $_GET['nuevo_estado'])) {
            $id = (int)$_GET['id'];
            $estado = $_GET['nuevo_estado'];
            if ($modelo->cambiar_estado($id, $estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Estado del documento actualizado.'];
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'Sistema',
                    'Catastro',
                    'Cambio de estado',
                    'Documento legal ID ' . $id,
                    'documentos_legales',
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
    header('Location: ../../Frontend/Vistas/catastro/gestion_documentos.php');
    exit();
}
