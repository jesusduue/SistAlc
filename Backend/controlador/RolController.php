<?php
session_start();

require_once '../clases/Rol.php';
require_once '../clases/auditoria.php';

if (!isset($_REQUEST['accion'])) {
    header('Location: ../../index.php');
    exit();
}

$accion = $_REQUEST['accion'];
$modelo = new Rol();
$auditoria = new Auditoria();

function redirigirGestionRoles()
{
    header('Location: ../../Frontend/Vistas/usuarios/gestion_roles.php');
    exit();
}

function asignarMensajeRol(string $tipo, string $texto)
{
    $_SESSION['mensaje'] = [
        'tipo' => $tipo,
        'texto' => $texto,
    ];
}

function normalizarNombreRol(string $valor): string
{
    return mb_strtolower(trim($valor), 'UTF-8');
}

function esRolBaseSistema(string $nombreRol): bool
{
    static $rolesBase = [
        'administrador',
        'jefe de catastro',
        'jefe de patentes',
        'tesorero',
        'secretaria de catastro',
        'secretaria de patentes',
        'calculista',
    ];

    return in_array(normalizarNombreRol($nombreRol), $rolesBase, true);
}

function registrarAuditoriaRol(
    Auditoria $auditoria,
    string $accion,
    string $detalle,
    ?int $idRol = null,
    ?array $metadata = null
) {
    $auditoria->registrarActividad(
        $_SESSION['id_usu'] ?? 0,
        $_SESSION['nom_usu'] ?? 'Sistema',
        'Roles',
        $accion,
        $detalle,
        'rol',
        $idRol,
        $metadata
    );
}

try {
    switch ($accion) {
        case 'registrar':
            $datos = [
                'nom_rol' => preg_replace('/\s+/', ' ', trim((string)($_POST['nom_rol'] ?? ''))),
                'est_registro' => ($_POST['est_registro'] ?? 'A') === 'I' ? 'I' : 'A',
            ];

            if ($datos['nom_rol'] === '') {
                asignarMensajeRol('warning', 'Debe indicar el nombre del rol.');
                registrarAuditoriaRol($auditoria, 'Registrar rechazado', 'Intento de crear rol sin nombre.');
                break;
            }

            if (mb_strlen($datos['nom_rol'], 'UTF-8') > 50) {
                asignarMensajeRol('warning', 'El nombre del rol no puede exceder 50 caracteres.');
                registrarAuditoriaRol($auditoria, 'Registrar rechazado', 'Intento de crear rol con nombre demasiado largo.');
                break;
            }

            if ($modelo->nombre_existe($datos['nom_rol'])) {
                asignarMensajeRol('warning', 'Ya existe un rol con ese nombre.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Registrar rechazado',
                    'Intento de crear rol duplicado: ' . $datos['nom_rol'],
                    null,
                    ['nom_rol' => $datos['nom_rol']]
                );
                break;
            }

            if ($modelo->registrar($datos)) {
                asignarMensajeRol('success', 'Rol registrado correctamente.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Registrar',
                    'Rol registrado: ' . $datos['nom_rol'],
                    null,
                    $datos
                );
            } else {
                asignarMensajeRol('danger', 'No se pudo registrar el rol.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Registrar fallido',
                    'Fallo al registrar rol: ' . $datos['nom_rol'],
                    null,
                    $datos
                );
            }
            break;

        case 'modificar':
            $datos = [
                'id_rol' => (int)($_POST['id_rol'] ?? 0),
                'nom_rol' => preg_replace('/\s+/', ' ', trim((string)($_POST['nom_rol'] ?? ''))),
                'est_registro' => ($_POST['est_registro'] ?? 'A') === 'I' ? 'I' : 'A',
            ];

            if ($datos['id_rol'] <= 0) {
                asignarMensajeRol('warning', 'El rol indicado no es valido.');
                registrarAuditoriaRol($auditoria, 'Modificacion rechazada', 'Intento de editar un rol sin identificador valido.');
                break;
            }

            $rolActual = $modelo->consultar_por_id($datos['id_rol']);
            if (!$rolActual) {
                asignarMensajeRol('warning', 'No se encontro el rol solicitado.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Modificacion rechazada',
                    'Intento de editar un rol inexistente.',
                    $datos['id_rol']
                );
                break;
            }

            if ($datos['nom_rol'] === '') {
                asignarMensajeRol('warning', 'Debe indicar el nombre del rol.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Modificacion rechazada',
                    'Intento de editar el rol ID ' . $datos['id_rol'] . ' sin nombre.',
                    $datos['id_rol']
                );
                break;
            }

            if (mb_strlen($datos['nom_rol'], 'UTF-8') > 50) {
                asignarMensajeRol('warning', 'El nombre del rol no puede exceder 50 caracteres.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Modificacion rechazada',
                    'Nombre demasiado largo para el rol ID ' . $datos['id_rol'],
                    $datos['id_rol']
                );
                break;
            }

            $nombreActual = normalizarNombreRol($rolActual['nom_rol']);
            $nombreNuevo = normalizarNombreRol($datos['nom_rol']);

            if (esRolBaseSistema($rolActual['nom_rol']) && $nombreActual !== $nombreNuevo) {
                asignarMensajeRol('warning', 'Los roles base del sistema no pueden renombrarse porque la matriz de permisos depende de su nombre.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Modificacion rechazada',
                    'Intento de renombrar rol base: ' . $rolActual['nom_rol'],
                    $datos['id_rol'],
                    ['nombre_actual' => $rolActual['nom_rol'], 'nombre_nuevo' => $datos['nom_rol']]
                );
                break;
            }

            if ($modelo->nombre_existe($datos['nom_rol'], $datos['id_rol'])) {
                asignarMensajeRol('warning', 'Ya existe otro rol con ese nombre.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Modificacion rechazada',
                    'Intento de duplicar el nombre del rol: ' . $datos['nom_rol'],
                    $datos['id_rol']
                );
                break;
            }

            if (
                $rolActual['est_registro'] !== $datos['est_registro']
                && $datos['est_registro'] === 'I'
                && $modelo->contar_usuarios_asociados($datos['id_rol'], 'A') > 0
            ) {
                asignarMensajeRol('warning', 'No puede inactivar un rol que todavia tiene usuarios activos asignados.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Modificacion rechazada',
                    'Intento de inactivar rol con usuarios activos: ' . $rolActual['nom_rol'],
                    $datos['id_rol']
                );
                break;
            }

            if ($modelo->modificar($datos)) {
                asignarMensajeRol('success', 'Rol actualizado correctamente.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Modificacion',
                    'Rol actualizado: ' . $rolActual['nom_rol'],
                    $datos['id_rol'],
                    [
                        'antes' => $rolActual,
                        'despues' => $datos,
                    ]
                );
            } else {
                asignarMensajeRol('danger', 'No se pudo actualizar el rol.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Modificacion fallida',
                    'Fallo al actualizar el rol ID ' . $datos['id_rol'],
                    $datos['id_rol'],
                    $datos
                );
            }
            break;

        case 'cambiar_estado':
            $idRol = (int)($_GET['id'] ?? 0);
            $nuevoEstado = ($_GET['nuevo_estado'] ?? 'A') === 'I' ? 'I' : 'A';

            if ($idRol <= 0) {
                asignarMensajeRol('warning', 'El rol indicado no es valido.');
                registrarAuditoriaRol($auditoria, 'Cambio de estado rechazado', 'Intento de cambiar estado sin identificador valido.');
                break;
            }

            $rolActual = $modelo->consultar_por_id($idRol);
            if (!$rolActual) {
                asignarMensajeRol('warning', 'No se encontro el rol solicitado.');
                registrarAuditoriaRol($auditoria, 'Cambio de estado rechazado', 'Intento de cambiar estado de un rol inexistente.', $idRol);
                break;
            }

            if ($rolActual['est_registro'] === $nuevoEstado) {
                asignarMensajeRol('info', 'El rol ya se encuentra en el estado solicitado.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Cambio de estado omitido',
                    'El rol ' . $rolActual['nom_rol'] . ' ya tenia el estado solicitado.',
                    $idRol,
                    ['estado' => $nuevoEstado]
                );
                break;
            }

            if ($nuevoEstado === 'I' && $modelo->contar_usuarios_asociados($idRol, 'A') > 0) {
                asignarMensajeRol('warning', 'No puede inactivar un rol con usuarios activos asignados.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Cambio de estado rechazado',
                    'Intento de inactivar rol con usuarios activos: ' . $rolActual['nom_rol'],
                    $idRol
                );
                break;
            }

            if ($modelo->cambiar_estado($idRol, $nuevoEstado)) {
                asignarMensajeRol('success', 'Estado del rol actualizado correctamente.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Cambio de estado',
                    'Estado del rol actualizado: ' . $rolActual['nom_rol'],
                    $idRol,
                    ['estado_anterior' => $rolActual['est_registro'], 'estado_nuevo' => $nuevoEstado]
                );
            } else {
                asignarMensajeRol('danger', 'No se pudo actualizar el estado del rol.');
                registrarAuditoriaRol(
                    $auditoria,
                    'Cambio de estado fallido',
                    'Fallo al actualizar el estado del rol: ' . $rolActual['nom_rol'],
                    $idRol,
                    ['estado_nuevo' => $nuevoEstado]
                );
            }
            break;

        default:
            header('Location: ../../index.php');
            exit();
    }
} catch (Throwable $exception) {
    error_log('RolController.php: ' . $exception->getMessage());
    asignarMensajeRol('danger', 'Ocurrio un error interno al procesar la solicitud.');
}

redirigirGestionRoles();
?>
