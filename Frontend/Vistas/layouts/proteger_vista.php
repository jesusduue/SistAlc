<?php
// layouts/proteger_vista.php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['nom_usu']) || !isset($_SESSION['nom_rol'])) {
    header('Location: ../../index.php');
    exit;
}

$nombre_usuario = $_SESSION['nom_usu'];
$nombre_rol = $_SESSION['nom_rol'];

if (!isset($_SESSION['permisos_modulo']) || !is_array($_SESSION['permisos_modulo'])) {
    $_SESSION['permisos_modulo'] = [];
}

function normalizarClave($valor)
{
    return mb_strtolower(trim($valor ?? ''), 'UTF-8');
}

function normalizarAccion($accion)
{
    $mapa = [
        'leer' => 'read',
        'lectura' => 'read',
        'ver' => 'read',
        'crear' => 'create',
        'registro' => 'create',
        'agregar' => 'create',
        'actualizar' => 'update',
        'editar' => 'update',
        'modificar' => 'update',
        'eliminar' => 'delete',
        'borrar' => 'delete',
    ];

    $accionNormalizada = normalizarClave($accion);
    return $mapa[$accionNormalizada] ?? $accionNormalizada;
}

function obtenerMapaPermisos()
{
    static $permisos = null;

    if ($permisos === null) {
        $permisos = [
            'administrador' => ['*' => ['*']],
            'jefe de catastro' => [
                'contribuyentes' => ['*'],
                'cedulas' => ['*'],
                'solvencias_municipales' => ['*'],
                'documentos_catastro' => ['*'],
                'linderos' => ['*'],
                'inmuebles' => ['*'],
            ],
            'jefe de patentes' => [
                'patentes' => ['*'],
                'licencias' => ['*'],
                'solvencias_comerciales' => ['*'],
                'contribuyentes' => ['*'],
            ],
            'tesorero' => [
                'contribuyentes' => ['*'],
                'cedulas' => ['*'],
                'solvencias_municipales' => ['*'],
                'documentos_catastro' => ['*'],
                'linderos' => ['*'],
                'inmuebles' => ['*'],
                'patentes' => ['*'],
                'licencias' => ['*'],
                'solvencias_comerciales' => ['*'],
                'facturacion' => ['*'],
            ],
            'secretaria de catastro' => [
                'contribuyentes' => ['create', 'read'],
                'cedulas' => ['create', 'read'],
                'solvencias_municipales' => ['create', 'read'],
                'documentos_catastro' => ['create', 'read'],
                'linderos' => ['create', 'read'],
                'inmuebles' => ['create', 'read'],
            ],
            'secretaria de patentes' => [
                'patentes' => ['create', 'read'],
                'licencias' => ['create', 'read'],
                'solvencias_comerciales' => ['create', 'read'],
            ],
            'secretaria de patente' => [
                'patentes' => ['create', 'read'],
                'licencias' => ['create', 'read'],
                'solvencias_comerciales' => ['create', 'read'],
            ],
            'calculista' => [
                'contribuyentes' => ['read'],
                'patentes' => ['read'],
            ],
        ];
    }

    return $permisos;
}

function obtenerAccionesPermitidas($rol, $modulo)
{
    $permisos = obtenerMapaPermisos();

    if (!isset($permisos[$rol])) {
        return [];
    }

    $definicionRol = $permisos[$rol];

    if (isset($definicionRol['*'])) {
        return (array)$definicionRol['*'];
    }

    if (isset($definicionRol[$modulo])) {
        return (array)$definicionRol[$modulo];
    }

    return [];
}

function mostrarAlertaAccesoDenegado()
{
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: "error",
                title: "Acceso denegado",
                text: "No tienes permisos para acceder a este modulo.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: "Volver al inicio"
            }).then(() => {
                window.location.href = "../dashboard.php";
            });
        });
    </script>';
}

function restringirAcceso($modulo, $accion = 'read')
{
    global $nombre_rol;

    $moduloNormalizado = normalizarClave($modulo);
    $accionNormalizada = normalizarAccion($accion);
    $rolNormalizado = normalizarClave($nombre_rol);

    $accionesPermitidas = array_map('normalizarAccion', obtenerAccionesPermitidas($rolNormalizado, $moduloNormalizado));

    $permitido = in_array('*', $accionesPermitidas, true) || in_array($accionNormalizada, $accionesPermitidas, true);

    if (!$permitido) {
        mostrarAlertaAccesoDenegado();
        exit;
    }

    $_SESSION['permisos_modulo'][$moduloNormalizado] = [
        'acciones' => $accionesPermitidas,
        'puedeLeer' => in_array('*', $accionesPermitidas, true) || in_array('read', $accionesPermitidas, true),
        'puedeCrear' => in_array('*', $accionesPermitidas, true) || in_array('create', $accionesPermitidas, true),
        'puedeActualizar' => in_array('*', $accionesPermitidas, true) || in_array('update', $accionesPermitidas, true),
        'puedeEliminar' => in_array('*', $accionesPermitidas, true) || in_array('delete', $accionesPermitidas, true),
    ];

    return $_SESSION['permisos_modulo'][$moduloNormalizado];
}

function puedeRealizarAccion($modulo, $accion)
{
    global $nombre_rol;

    $moduloNormalizado = normalizarClave($modulo);
    $accionNormalizada = normalizarAccion($accion);
    $rolNormalizado = normalizarClave($nombre_rol);

    $accionesPermitidas = array_map('normalizarAccion', obtenerAccionesPermitidas($rolNormalizado, $moduloNormalizado));

    if (empty($accionesPermitidas)) {
        return false;
    }

    return in_array('*', $accionesPermitidas, true) || in_array($accionNormalizada, $accionesPermitidas, true);
}
?>
