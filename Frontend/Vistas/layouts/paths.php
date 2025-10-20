<?php
// Rutas base para el proyecto - usar rutas root-relative para evitar problemas entre subcarpetas
if (!defined('BASE_URL')) {
    // Ruta hacia la carpeta Frontend del proyecto (ajusta si mueves la app)
    define('BASE_URL', '/SistAlc/Frontend');
    define('ASSETS_CSS', BASE_URL . '/estilos/css');
    define('ASSETS_JS', BASE_URL . '/estilos/js');
    define('BACKEND_URL', '/SistAlc/Backend');
}

?>
