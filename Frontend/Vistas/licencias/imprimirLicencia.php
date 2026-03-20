<?php
include '../layouts/proteger_vista.php';
restringirAcceso('licencias');

require_once '../../../Backend/clases/licencia.php';

$licenciaModel = new Licencia();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$licencia = $id > 0 ? $licenciaModel->consultar_detallada($id) : null;

if (!$licencia) {
    http_response_code(404);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Licencia no encontrada</title></head><body><p>La licencia solicitada no existe.</p></body></html>';
    exit;
}

$vigenteHasta = new DateTime($licencia['vig_lic']);
$vigenteHastaTexto = $vigenteHasta->format('d/m/Y');
$expedicion = new DateTime($licencia['fec_exp_lic']);
$expedicionTexto = $expedicion->format('d/m/Y');
$fechaActual = (new DateTime())->format('d/m/Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licencia <?= htmlspecialchars($licencia['num_lic']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/SistAlc/Frontend/estilos/css/licencias/imprimir_licencia.css">
</head>
<body class="licencia-print-page">
    <div class="print-container">
        <div class="print-actions d-flex justify-content-end gap-2 mb-3">
            <a href="gestionLicencias.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>

        <header class="print-header">
            <div class="escudo-wrap">
                <img src="/SistAlc/Frontend/estilos/img/logo_alcaldia_la_fria.png" alt="Escudo municipal">
            </div>
            <div>
                <h1>Republica Bolivariana de Venezuela</h1>
                <h2>Alcaldia del Municipio Garcia de Hevia</h2>
                <p>Direccion de Hacienda Municipal</p>
            </div>
        </header>

        <section class="document-title">
            <h3>Licencia de actividad economica</h3>
            <p>Documento oficial emitido para el ejercicio comercial autorizado.</p>
        </section>

        <section class="document-chip">
            <span><strong>Numero de licencia:</strong> <?= htmlspecialchars($licencia['num_lic']); ?></span>
            <span><strong>Fecha de expedicion:</strong> <?= $expedicionTexto; ?></span>
            <span><strong>Vigencia hasta:</strong> <?= $vigenteHastaTexto; ?></span>
        </section>

        <section class="info-block">
            <h4>Datos de la patente</h4>
            <table class="info-table">
                <tr><td class="info-label">Numero de patente</td><td><?= htmlspecialchars($licencia['num_pat']); ?></td></tr>
                <tr><td class="info-label">Nombre comercial</td><td><?= htmlspecialchars($licencia['nom_pat']); ?></td></tr>
                <tr><td class="info-label">RIF patente</td><td><?= htmlspecialchars($licencia['rif_pat']); ?></td></tr>
                <tr><td class="info-label">Direccion fiscal</td><td><?= htmlspecialchars($licencia['dir_pat']); ?></td></tr>
            </table>
        </section>

        <section class="info-block">
            <h4>Contribuyente responsable</h4>
            <table class="info-table">
                <tr><td class="info-label">Nombre o razon social</td><td><?= htmlspecialchars($licencia['nom_con']); ?></td></tr>
                <tr><td class="info-label">RIF</td><td><?= htmlspecialchars($licencia['rif_con']); ?></td></tr>
                <tr><td class="info-label">Direccion</td><td><?= htmlspecialchars($licencia['dir_con']); ?></td></tr>
            </table>
        </section>

        <section class="info-block">
            <h4>Factura asociada</h4>
            <table class="info-table">
                <tr><td class="info-label">Numero de factura</td><td><?= $licencia['num_fac'] ? htmlspecialchars($licencia['num_fac']) : 'No disponible'; ?></td></tr>
                <tr><td class="info-label">Fecha de emision</td><td><?= $licencia['fec_fac'] ? date('d/m/Y H:i', strtotime($licencia['fec_fac'])) : 'No disponible'; ?></td></tr>
                <tr><td class="info-label">Monto total</td><td><?= $licencia['tot_fac'] ? 'Bs. ' . number_format($licencia['tot_fac'], 2, ',', '.') : 'No disponible'; ?></td></tr>
            </table>
        </section>

        <footer class="document-footer">
            <p>Este documento certifica la autorizacion municipal para el ejercicio de actividades comerciales segun la patente descrita.</p>
            <p>Fecha de impresion: <?= $fechaActual; ?>.</p>
            <div class="firmas">
                <div>
                    <span>Lugar para sello oficial</span>
                </div>
                <div>
                    <span>Firma autorizada</span>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
