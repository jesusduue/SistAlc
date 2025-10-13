<?php
session_start();

require_once '../clases/ReporteFinanciero.php';

if (!isset($_SESSION['id_usu'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'mensaje' => 'No autorizado.']);
    exit();
}

$accion = $_REQUEST['accion'] ?? null;
$reporte = new ReporteFinanciero();

header('Content-Type: application/json; charset=utf-8');

switch ($accion) {
    case 'ingresos_diarios':
        $fecha = $_REQUEST['fecha'] ?? date('Y-m-d');
        $datos = $reporte->resumenDiarioContribuyentes($fecha);
        echo json_encode(['ok' => true, 'data' => $datos], JSON_UNESCAPED_UNICODE);
        break;

    case 'rubros_diarios':
        $fecha = $_REQUEST['fecha'] ?? date('Y-m-d');
        $datos = $reporte->resumenDiarioRubros($fecha);
        echo json_encode(['ok' => true, 'data' => $datos], JSON_UNESCAPED_UNICODE);
        break;

    case 'ingresos_mensuales':
        $anio = isset($_REQUEST['anio']) ? (int)$_REQUEST['anio'] : (int)date('Y');
        $datos = $reporte->ingresosMensuales($anio);
        echo json_encode(['ok' => true, 'data' => $datos], JSON_UNESCAPED_UNICODE);
        break;

    case 'rubros_mensuales':
        $anio = isset($_REQUEST['anio']) ? (int)$_REQUEST['anio'] : (int)date('Y');
        $mes = isset($_REQUEST['mes']) ? (int)$_REQUEST['mes'] : (int)date('n');
        $datos = $reporte->distribucionRubrosMensual($anio, $mes);
        echo json_encode(['ok' => true, 'data' => $datos], JSON_UNESCAPED_UNICODE);
        break;

    case 'indicadores':
        $anio = isset($_REQUEST['anio']) ? (int)$_REQUEST['anio'] : (int)date('Y');
        $dias = isset($_REQUEST['dias']) ? (int)$_REQUEST['dias'] : 30;
        $datos = $reporte->indicadoresDashboard($anio, $dias);
        echo json_encode(['ok' => true, 'data' => $datos], JSON_UNESCAPED_UNICODE);
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'mensaje' => 'Accion no valida.'], JSON_UNESCAPED_UNICODE);
        break;
}
