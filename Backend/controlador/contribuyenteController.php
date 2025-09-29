<?php


// Backend/controlador/ContribuyenteController.php
// ======================================
// Controlador para gestionar contribuyentes
// ======================================

session_start();
require_once '../clases/Contribuyente.php';
require_once '../clases/auditoria.php';

// Validar que llegue una acción por POST
if (isset($_REQUEST['accion'])) {
    $accion = $_REQUEST['accion'];
    $contribuyente = new Contribuyente();
    $auditoria = new Auditoria();

    switch ($accion) {

        case 'registrar':
            $datos = [
                'nom_con'  => $_POST['nom_con'],
                'rif_con'  => $_POST['rif_con'],
                'tel_con'  => $_POST['tel_con'],
                'ema_con'  => $_POST['ema_con'],
                'dir_con'  => $_POST['dir_con'],
                'fky_usu'  => $_SESSION['id_usu'] ?? 1 // ID del usuario logueado
            ];

            if ($contribuyente->rif_existe($datos['rif_con'])) {
                $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'El RIF ingresado ya existe.'];
            } elseif ($contribuyente->registrar($datos)) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Contribuyente registrado exitosamente.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Ocurrió un error al registrar.'];
            }

            // Registrar actividad de auditoría
            $auditoria->registrarActividad(
                $_SESSION['id_usu'],
                $_SESSION['nom_usu'],
                'Contribuyentes',
                'Registrar',
                'Nuevo contribuyente: ' . $_POST['nom_con'],
                'contribuyente',
                $nuevoId ?? null, // si tienes el ID recién creado
                ['rif' => $_POST['rif_con']] // metadata opcional
            );
            break;

        case 'modificar':
            $datos = [
                'id_con'   => $_POST['id_con'],
                'nom_con'  => $_POST['nom_con'],
                'rif_con'  => $_POST['rif_con'],
                'tel_con'  => $_POST['tel_con'],
                'ema_con'  => $_POST['ema_con'],
                'dir_con'  => $_POST['dir_con']
            ];

            if ($contribuyente->modificar($datos)) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Datos modificados correctamente.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo modificar el contribuyente.'];
            }

            // Registrar actividad de auditoría
            $auditoria->registrarActividad(
                $_SESSION['id_usu'],
                $_SESSION['nom_usu'],
                'Contribuyentes',
                'modificacion',
                'Edición de contribuyente ID ' . $_POST['id_con'],
                'contribuyente',
                (int)$_POST['id_con']
                // puedes añadir ['antes'=>..., 'despues'=>...] si capturas cambios

            );
            break;


        //  USAREMOS ESTE METODO PARA HACER BORRADO LOGICO DE REGISTROS EN LA TABLA
        // CON EL CAMBIO DE ESTADO 
        case 'cambiar_estado':

            if (isset($_GET['id'], $_GET['nuevo_estado'])) {
                $ok = $contribuyente->cambiar_estado((int)$_GET['id'], $_GET['nuevo_estado']);
                if ($ok) {
                    $_SESSION['mensaje'] = [
                        'tipo'  => 'success', // success | info | warning | error
                        'titulo' => 'Estado actualizado',
                        'texto' => 'El estado del Contribuyente se cambió correctamente.'
                    ];
                    // Registrar actividad de auditoría
                    $auditoria->registrarActividad(
                        $_SESSION['id_usu'],
                        $_SESSION['nom_usu'],
                        'Contribuyentes',
                        /* ($_GET['nuevo_estado'] === 'A') ? 'Activo' : 'Inactivo' */ 'Cambio de Estado',
                        'Cambio de estado contribuyente ID ' . $_GET['id'],
                        'contribuyente',
                        (int)$_GET['id'],
                        ['nuevo_estado' => $_GET['nuevo_estado']]
                    );
                }else{
                         $_SESSION['mensaje'] = [
                        'tipo'  => 'error',
                        'titulo' => 'No se pudo actualizar',
                        'texto' => 'Ocurrió un problema al cambiar el estado.'
                    ];
                }
            }
            break;

        case 'eliminar':
            $id = $_GET['id'];

            if ($contribuyente->eliminar($id)) {
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Contribuyente eliminado.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo eliminar el contribuyente.'];
            }
            // Registrar actividad de auditoría
            $auditoria->registrarActividad(
                $_SESSION['id_usu'],
                $_SESSION['nom_usu'],
                'Contribuyentes',
                'Eliminación',
                'Eliminación de contribuyente ID ' . $_GET['id'],
                'contribuyente',
                (int)$_GET['id']
            );
            break;

        default:
            header('Location: ../../index.php');
            exit();
    }
    //redirige al modulo de gestion de contribuyentes
    if (!in_array($accion, ['login', 'logout'])) {
        header('Location: ../../Frontend/Vistas/contribuyentes/gestion_contribuyentes.php');
        exit();
    }
} else {
    header('Location: ../../index.php');
    exit();
}
