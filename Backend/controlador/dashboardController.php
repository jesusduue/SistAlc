<?php
// Controlador ligero que retorna JSON para el dashboard (AJAX-friendly)
session_start();
require_once '../clases/Dashboard.php';

// Seguridad mínima: exige sesión activa
if (!isset($_SESSION['id_usu'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;
$dashboard = new Dashboard();

header('Content-Type: application/json; charset=utf-8');

switch ($accion) {
    case 'actividades':
        // Límite configurable vía ?limite= (por defecto 20)
        $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 20;
        $data = $dashboard->actividadesRecientes($limite);
        echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Acción no válida'], JSON_UNESCAPED_UNICODE);
        break;
}
