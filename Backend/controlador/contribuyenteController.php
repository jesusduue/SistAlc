<?php


 // Backend/controlador/ContribuyenteController.php
// ======================================
// Controlador para gestionar contribuyentes
// ======================================

session_start();
require_once '../clases/Contribuyente.php';
require_once '../clases/auditoria.php';

// Validar que llegue una acción por POST
if ($accion = isset($_REQUEST['accion'])) {
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
            header('Location: ../../Frontend/Vistas/contribuyentes/gestion_contribuyentes.php');
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

            header('Location: ../../Frontend/Vistas/contribuyentes/gestion_contribuyentes.php');
            break;

  
           //  USAREMOS ESTE METODO PARA HACER BORRADO LOGICO DE REGISTROS EN LA TABLA
           // CON EL CAMBIO DE ESTADO 
      case 'cambiar_estado':
            $id = $_POST['id_con'];
            $nuevo_estado = $_POST['nuevo_estado'];

            if ($contribuyente->cambiar_estado($id, $nuevo_estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Estado del contribuyente actualizado.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo cambiar el estado.'];
            }
            // Registrar actividad de auditoría
            $auditoria->registrarActividad(
                $_SESSION['id_usu'],
                $_SESSION['nom_usu'],
                'Contribuyentes',
                ($_POST['nuevo_estado'] === 'A') ? 'Activación' : 'Inactivación',
                'Cambio de estado contribuyente ID ' . $_POST['id_con'],
                'contribuyente',
                (int)$_POST['id_con'],
                ['nuevo_estado' => $_POST['nuevo_estado']]
            );

            header('Location: ../../Frontend/Vistas/contribuyentes/gestion_contribuyentes.php');
            break; 

        case 'eliminar':
            $id = $_POST['id_con'];

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
                'Eliminación de contribuyente ID ' . $_POST['id_con'],
                'contribuyente',
                (int)$_POST['id_con']
            );

            header('Location: ../../Frontend/Vistas/contribuyentes/gestion_contribuyentes.php');
            break;

        default:
            header('Location: ../../index.php');

            break;
    }
} else {
    header('Location: ../../index.php');
    exit();
} 
?>