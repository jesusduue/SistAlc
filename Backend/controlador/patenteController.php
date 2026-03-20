<?php

// Backend/controlador/patenteController.php
// ======================================
// Controlador para gestionar patentes
// ======================================

session_start();
require_once '../clases/Patente.php';
require_once '../clases/auditoria.php';

// Validar que llegue una acción por POST/GET
if (isset($_REQUEST['accion'])) {
    $accion = $_REQUEST['accion'];
    $patente = new Patente();
    $auditoria = new Auditoria();

    switch ($accion) {

        case 'registrar':
            $datos = [
                'num_pat' => $_POST['num_pat'],
                'rif_pat' => $_POST['rif_pat'],
                'nom_pat' => $_POST['nom_pat'],
                'fky_con' => $_POST['fky_con'],
                'dir_pat' => $_POST['dir_pat'],
                'rub_pat' => $_POST['rub_pat'],
                'fky_usu' => $_SESSION['id_usu'] ?? 1, //id del usuario logueado
                'est_pat' => $_POST['est_pat'] ?? 'S' // S = solicitada (se guarda por defecto como solicitada)
            ];
            $nuevoId = null;

            if ($patente->numero_existe($datos['num_pat'])) {
                $_SESSION['mensaje'] = [
                    'tipo' => 'warning',
                    'texto' => 'El número de patente ya está registrado.'
                ];
            }
            elseif ($patente->registrar($datos)) {
                $nuevoId = $patente->id_pat ?? null;
                $_SESSION['mensaje'] = [
                    'tipo' => 'success',
                    'texto' => 'Patente registrada exitosamente.'
                ];
            }
            else {
                $_SESSION['mensaje'] = [
                    'tipo' => 'danger',
                    'texto' => 'Ocurrió un error al registrar la patente.'
                ];
            }

            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Patentes',
                'Registrar',
                'Nueva patente: ' . $datos['num_pat'],
                'patente',
                $nuevoId,
            ['rif' => $datos['rif_pat']]
            );
            break;

        case 'modificar':
            $datos = [
                'id_pat' => $_POST['id_pat'],
                'num_pat' => $_POST['num_pat'],
                'rif_pat' => $_POST['rif_pat'],
                'nom_pat' => $_POST['nom_pat'],
                'fky_con' => $_POST['fky_con'],
                'dir_pat' => $_POST['dir_pat'],
                'rub_pat' => $_POST['rub_pat'],
                'est_pat' => $_POST['est_pat']
            ];

            if ($patente->modificar($datos)) {
                $_SESSION['mensaje'] = [
                    'tipo' => 'success',
                    'texto' => 'La patente se modificó correctamente.'
                ];
            }
            else {
                $_SESSION['mensaje'] = [
                    'tipo' => 'danger',
                    'texto' => 'No se pudo modificar la patente.'
                ];
            }

            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Patentes',
                'Modificación',
                'Edición de patente ID ' . $_POST['id_pat'],
                'patente',
                (int)$_POST['id_pat']
            );
            break;

        case 'cambiar_estado':
            if (isset($_GET['id'], $_GET['nuevo_estado'])) {
                $ok = $patente->cambiar_estado_tabla((int)$_GET['id'], $_GET['nuevo_estado']);
                if ($ok) {
                    $_SESSION['mensaje'] = [
                        'tipo' => 'success',
                        'titulo' => 'Estado actualizado',
                        'texto' => 'El estado del registro de patente se cambió correctamente.'
                    ];
                    $auditoria->registrarActividad(
                        $_SESSION['id_usu'] ?? 0,
                        $_SESSION['nom_usu'] ?? 'Sistema',
                        'Patentes',
                        'Cambio de estado',
                        'Cambio de estado de registro patente ID ' . $_GET['id'],
                        'patente',
                        (int)$_GET['id'],
                    ['nuevo_estado_tabla' => $_GET['nuevo_estado']]
                    );
                }
                else {
                    $_SESSION['mensaje'] = [
                        'tipo' => 'error',
                        'titulo' => 'No se pudo actualizar',
                        'texto' => 'Ocurrió un problema al cambiar el estado.'
                    ];
                }
            }
            break;

        case 'actualizar_estado':
            if (isset($_GET['id'], $_GET['nuevo_estado'])) {
                $ok = $patente->actualizar_estado_patente((int)$_GET['id'], $_GET['nuevo_estado']);
                if ($ok) {
                    $_SESSION['mensaje'] = [
                        'tipo' => 'info',
                        'titulo' => 'Estado de trámite',
                        'texto' => 'El estado de la patente se actualizó correctamente.'
                    ];
                    $auditoria->registrarActividad(
                        $_SESSION['id_usu'] ?? 0,
                        $_SESSION['nom_usu'] ?? 'Sistema',
                        'Patentes',
                        'Cambio de estado',
                        'Actualización de trámite patente ID ' . $_GET['id'],
                        'patente',
                        (int)$_GET['id'],
                    ['estado_patente' => $_GET['nuevo_estado']]
                    );
                }
                else {
                    $_SESSION['mensaje'] = [
                        'tipo' => 'warning',
                        'titulo' => 'Sin cambios',
                        'texto' => 'No se pudo actualizar el estado del trámite.'
                    ];
                }
            }
            break;

        case 'eliminar':
            $id = $_GET['id'] ?? null;

            if ($id && $patente->eliminar($id)) {
                $_SESSION['mensaje'] = [
                    'tipo' => 'info',
                    'texto' => 'Patente eliminada.'
                ];
            }
            else {
                $_SESSION['mensaje'] = [
                    'tipo' => 'danger',
                    'texto' => 'No se pudo eliminar la patente.'
                ];
            }

            $auditoria->registrarActividad(
                $_SESSION['id_usu'] ?? 0,
                $_SESSION['nom_usu'] ?? 'Sistema',
                'Patentes',
                'Eliminación',
                'Eliminación de patente ID ' . ($id ?? 'desconocido'),
                'patente',
                $id ? (int)$id : null
            );
            break;

        default:
            header('Location: ../../index.php');
            exit();
    }

    if (!in_array($accion, ['login', 'logout'])) {
        header('Location: ../../Frontend/Vistas/patentes/gestionPatentes.php');
        exit();
    }
}
else {
    header('Location: ../../index.php');
    exit();
}
