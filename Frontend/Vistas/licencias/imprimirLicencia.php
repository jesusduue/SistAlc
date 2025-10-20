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
    <title>Licencia <?= htmlspecialchars($licencia['num_lic']); ?></title>
    <link rel="stylesheet" href="/SistAlc/Frontend/estilos/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .print-container {
            max-width: 900px;
            margin: 2rem auto;
            background: #fff;
            padding: 2rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-radius: 1rem;
        }
        .print-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .print-header h1 {
            font-size: 1.8rem;
            margin-bottom: .25rem;
        }
        .badge-status {
            font-size: .9rem;
        }
        .section-title {
            font-weight: 600;
            font-size: 1rem;
            color: #555;
            margin-bottom: .5rem;
        }
        .info-table {
            width: 100%;
            margin-bottom: 1.5rem;
        }
        .info-table td {
            padding: .35rem 0;
            vertical-align: top;
        }
        .info-label {
            font-weight: 600;
            width: 30%;
            color: #495057;
        }
        @media print {
            body {
                background: #fff;
            }
            .print-container {
                box-shadow: none;
                margin: 0;
                border-radius: 0;
            }
            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="print-actions d-flex justify-content-end gap-2 mb-3">
            <a href="gestionLicencias.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
        <div class="print-header">
            <h1>Licencia Comercial</h1>
            <p class="text-muted mb-1">Emitida el <?= $expedicionTexto; ?> · Vigente hasta <?= $vigenteHastaTexto; ?></p>
            <span class="badge bg-primary badge-status">N° <?= htmlspecialchars($licencia['num_lic']); ?></span>
        </div>

        <div class="mb-4">
            <div class="section-title">Datos de la Patente</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Número de Patente</td>
                    <td><?= htmlspecialchars($licencia['num_pat']); ?></td>
                </tr>
                <tr>
                    <td class="info-label">Nombre Comercial</td>
                    <td><?= htmlspecialchars($licencia['nom_pat']); ?></td>
                </tr>
                <tr>
                    <td class="info-label">RIF Patente</td>
                    <td><?= htmlspecialchars($licencia['rif_pat']); ?></td>
                </tr>
                <tr>
                    <td class="info-label">Dirección Fiscal</td>
                    <td><?= htmlspecialchars($licencia['dir_pat']); ?></td>
                </tr>
            </table>
        </div>

        <div class="mb-4">
            <div class="section-title">Contribuyente Responsable</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Nombre / Razón Social</td>
                    <td><?= htmlspecialchars($licencia['nom_con']); ?></td>
                </tr>
                <tr>
                    <td class="info-label">RIF</td>
                    <td><?= htmlspecialchars($licencia['rif_con']); ?></td>
                </tr>
                <tr>
                    <td class="info-label">Dirección</td>
                    <td><?= htmlspecialchars($licencia['dir_con']); ?></td>
                </tr>
            </table>
        </div>

        <div class="mb-4">
            <div class="section-title">Factura Asociada</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Número de Factura</td>
                    <td><?= $licencia['num_fac'] ? htmlspecialchars($licencia['num_fac']) : 'No disponible'; ?></td>
                </tr>
                <tr>
                    <td class="info-label">Fecha de Emisión</td>
                    <td><?= $licencia['fec_fac'] ? date('d/m/Y H:i', strtotime($licencia['fec_fac'])) : 'No disponible'; ?></td>
                </tr>
                <tr>
                    <td class="info-label">Monto Total</td>
                    <td><?= $licencia['tot_fac'] ? 'Bs. ' . number_format($licencia['tot_fac'], 2, ',', '.') : 'No disponible'; ?></td>
                </tr>
            </table>
        </div>

        <div class="mb-4">
            <div class="section-title">Detalle de Vigencia</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">Fecha de Expedición</td>
                    <td><?= $expedicionTexto; ?></td>
                </tr>
                <tr>
                    <td class="info-label">Vigencia Hasta</td>
                    <td><?= $vigenteHastaTexto; ?></td>
                </tr>
                <tr>
                    <td class="info-label">Fecha de Impresión</td>
                    <td><?= $fechaActual; ?></td>
                </tr>
            </table>
        </div>

        <div class="text-muted" style="font-size: .85rem;">
            <p class="mb-1">Este documento certifica la autorización municipal para el ejercicio de actividades comerciales según la patente descrita.</p>
            <p class="mb-0">Será nulo si se detecta falsificación, alteración o vencimiento del plazo indicado.</p>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/5c4b9b5b59.js" crossorigin="anonymous"></script>
</body>
</html>
