<?php
// ======================================
// Controlador para gestionar contribuyentes
// ======================================

session_start();
require_once '../clases/Contribuyente.php';

// Validar que llegue una acción por POST
if (isset($_REQUEST['accion'])) {
    $contribuyente = new Contribuyente();

    switch ($_POST['accion']) {

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
            header('Location: ../../Frontend/Vistas/contribuyentes/gestion_contribuyentes.php');
            break;

        case 'cambiar_estado':
            $id = $_POST['id_con'];
            $nuevo_estado = $_POST['nuevo_estado'];

            if ($contribuyente->cambiar_estado($id, $nuevo_estado)) {
                $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Estado del contribuyente actualizado.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'No se pudo cambiar el estado.'];
            }
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
