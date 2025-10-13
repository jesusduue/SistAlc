<?php
require_once 'Utilidad.php';

class ReporteFinanciero extends Utilidad
{
    private function normalizarFecha(string $fecha): string
    {
        $dt = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$dt) {
            $dt = new DateTime(); // hoy como respaldo
        }
        return $dt->format('Y-m-d');
    }

    private function normalizarMes(int $anio, int $mes): array
    {
        $anio = $anio > 0 ? $anio : (int)date('Y');
        $mes = ($mes >= 1 && $mes <= 12) ? $mes : (int)date('n');
        return [$anio, $mes];
    }

    public function resumenDiarioContribuyentes(string $fecha): array
    {
        $fecha = $this->normalizarFecha($fecha);

        $sqlPagadas = "SELECT f.id_fac,
                              f.num_fac,
                              f.fec_fac,
                              c.nom_con,
                              c.rif_con,
                              f.tot_fac,
                              'P' AS tipo_movimiento
                         FROM factura f
                         INNER JOIN contribuyente c ON f.fky_con = c.id_con
                        WHERE f.est_registro = 'A'
                          AND f.est_pago = 'P'
                          AND DATE(f.fec_fac) = ?";
        $stmt = $this->con_bd->prepare($sqlPagadas);
        $stmt->bind_param('s', $fecha);
        $stmt->execute();
        $res = $stmt->get_result();
        $pagadas = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $sqlAnuladas = "SELECT f.id_fac,
                               f.num_fac,
                               f.fec_anulacion AS fec_fac,
                               c.nom_con,
                               c.rif_con,
                               f.tot_fac,
                               'A' AS tipo_movimiento
                          FROM factura f
                          INNER JOIN contribuyente c ON f.fky_con = c.id_con
                         WHERE f.est_registro = 'A'
                           AND f.est_pago = 'A'
                           AND f.fec_anulacion IS NOT NULL
                           AND DATE(f.fec_anulacion) = ?";
        $stmt = $this->con_bd->prepare($sqlAnuladas);
        $stmt->bind_param('s', $fecha);
        $stmt->execute();
        $res = $stmt->get_result();
        $anuladas = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $movimientos = array_merge($pagadas, $anuladas);
        usort($movimientos, static function ($a, $b) {
            return strcmp((string)$a['fec_fac'], (string)$b['fec_fac']);
        });

        $totalPagado = array_reduce($pagadas, static function ($carry, $item) {
            return $carry + (float)$item['tot_fac'];
        }, 0.0);
        $totalAnulado = array_reduce($anuladas, static function ($carry, $item) {
            return $carry + (float)$item['tot_fac'];
        }, 0.0);

        return [
            'fecha' => $fecha,
            'movimientos' => $movimientos,
            'totales' => [
                'pagado' => round($totalPagado, 2),
                'anulado' => round($totalAnulado, 2),
                'neto' => round($totalPagado - $totalAnulado, 2),
            ],
        ];
    }

    public function resumenDiarioRubros(string $fecha): array
    {
        $fecha = $this->normalizarFecha($fecha);

        $sqlBase = "SELECT ti.id_tip,
                           ti.nom_tip,
                           SUM(di.mon_imp) AS total
                      FROM factura f
                      INNER JOIN detalle_impuesto di ON di.fky_fac = f.id_fac AND di.est_registro = 'A'
                      INNER JOIN tipo_impuesto ti ON ti.id_tip = di.fky_tip_imp
                     WHERE %s
                  GROUP BY ti.id_tip, ti.nom_tip";

        $condicionPagadas = "f.est_registro = 'A' AND f.est_pago = 'P' AND DATE(f.fec_fac) = ?";
        $stmt = $this->con_bd->prepare(sprintf($sqlBase, $condicionPagadas));
        $stmt->bind_param('s', $fecha);
        $stmt->execute();
        $res = $stmt->get_result();
        $pagados = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $condicionAnuladas = "f.est_registro = 'A' AND f.est_pago = 'A' AND f.fec_anulacion IS NOT NULL AND DATE(f.fec_anulacion) = ?";
        $stmt = $this->con_bd->prepare(sprintf($sqlBase, $condicionAnuladas));
        $stmt->bind_param('s', $fecha);
        $stmt->execute();
        $res = $stmt->get_result();
        $anulados = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $map = [];
        foreach ($pagados as $row) {
            $id = $row['id_tip'];
            $map[$id] = [
                'id_tip' => (int)$row['id_tip'],
                'nombre' => $row['nom_tip'],
                'pagado' => (float)$row['total'],
                'anulado' => 0.0,
            ];
        }
        foreach ($anulados as $row) {
            $id = $row['id_tip'];
            if (!isset($map[$id])) {
                $map[$id] = [
                    'id_tip' => (int)$row['id_tip'],
                    'nombre' => $row['nom_tip'],
                    'pagado' => 0.0,
                    'anulado' => 0.0,
                ];
            }
            $map[$id]['anulado'] += (float)$row['total'];
        }

        $rubros = array_values(array_map(static function ($item) {
            $item['neto'] = round($item['pagado'] - $item['anulado'], 2);
            $item['pagado'] = round($item['pagado'], 2);
            $item['anulado'] = round($item['anulado'], 2);
            return $item;
        }, $map));

        usort($rubros, static function ($a, $b) {
            return strcmp($a['nombre'], $b['nombre']);
        });

        $totalPagado = array_reduce($rubros, static function ($carry, $item) {
            return $carry + $item['pagado'];
        }, 0.0);
        $totalAnulado = array_reduce($rubros, static function ($carry, $item) {
            return $carry + $item['anulado'];
        }, 0.0);

        return [
            'fecha' => $fecha,
            'rubros' => $rubros,
            'totales' => [
                'pagado' => round($totalPagado, 2),
                'anulado' => round($totalAnulado, 2),
                'neto' => round($totalPagado - $totalAnulado, 2),
            ],
        ];
    }

    public function ingresosMensuales(int $anio): array
    {
        $anio = $anio > 0 ? $anio : (int)date('Y');
        $base = array_fill(1, 12, 0.0);

        $sqlPagado = "SELECT MONTH(f.fec_fac) AS mes, SUM(f.tot_fac) AS total
                        FROM factura f
                       WHERE f.est_registro = 'A'
                         AND f.est_pago = 'P'
                         AND YEAR(f.fec_fac) = ?
                    GROUP BY mes";
        $stmt = $this->con_bd->prepare($sqlPagado);
        $stmt->bind_param('i', $anio);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $base[(int)$row['mes']] += (float)$row['total'];
            }
        }
        $stmt->close();

        $sqlAnulado = "SELECT MONTH(f.fec_anulacion) AS mes, SUM(f.tot_fac) AS total
                         FROM factura f
                        WHERE f.est_registro = 'A'
                          AND f.est_pago = 'A'
                          AND f.fec_anulacion IS NOT NULL
                          AND YEAR(f.fec_anulacion) = ?
                     GROUP BY mes";
        $stmt = $this->con_bd->prepare($sqlAnulado);
        $stmt->bind_param('i', $anio);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $base[(int)$row['mes']] -= (float)$row['total'];
            }
        }
        $stmt->close();

        return array_values(array_map(static function ($value) {
            return round($value, 2);
        }, $base));
    }

    public function distribucionRubrosMensual(int $anio, int $mes): array
    {
        [$anio, $mes] = $this->normalizarMes($anio, $mes);

        $sqlBase = "SELECT ti.id_tip,
                           ti.nom_tip,
                           SUM(di.mon_imp) AS total
                      FROM factura f
                      INNER JOIN detalle_impuesto di ON di.fky_fac = f.id_fac AND di.est_registro = 'A'
                      INNER JOIN tipo_impuesto ti ON ti.id_tip = di.fky_tip_imp
                     WHERE %s
                  GROUP BY ti.id_tip, ti.nom_tip";

        $condicionPagado = "f.est_registro = 'A'
                            AND f.est_pago = 'P'
                            AND YEAR(f.fec_fac) = ?
                            AND MONTH(f.fec_fac) = ?";
        $stmt = $this->con_bd->prepare(sprintf($sqlBase, $condicionPagado));
        $stmt->bind_param('ii', $anio, $mes);
        $stmt->execute();
        $res = $stmt->get_result();
        $pagados = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $condicionAnulado = "f.est_registro = 'A'
                             AND f.est_pago = 'A'
                             AND f.fec_anulacion IS NOT NULL
                             AND YEAR(f.fec_anulacion) = ?
                             AND MONTH(f.fec_anulacion) = ?";
        $stmt = $this->con_bd->prepare(sprintf($sqlBase, $condicionAnulado));
        $stmt->bind_param('ii', $anio, $mes);
        $stmt->execute();
        $res = $stmt->get_result();
        $anulados = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        $map = [];
        foreach ($pagados as $row) {
            $id = $row['id_tip'];
            $map[$id] = [
                'id_tip' => (int)$row['id_tip'],
                'nombre' => $row['nom_tip'],
                'total' => (float)$row['total'],
            ];
        }
        foreach ($anulados as $row) {
            $id = $row['id_tip'];
            if (!isset($map[$id])) {
                $map[$id] = [
                    'id_tip' => (int)$row['id_tip'],
                    'nombre' => $row['nom_tip'],
                    'total' => 0.0,
                ];
            }
            $map[$id]['total'] -= (float)$row['total'];
        }

        $totales = array_map(static function ($item) {
            $item['total'] = round($item['total'], 2);
            return $item;
        }, array_values($map));

        usort($totales, static function ($a, $b) {
            return strcmp($a['nombre'], $b['nombre']);
        });

        $totalGeneral = array_reduce($totales, static function ($carry, $item) {
            return $carry + $item['total'];
        }, 0.0);

        return [
            'anio' => $anio,
            'mes' => $mes,
            'rubros' => $totales,
            'total' => round($totalGeneral, 2),
        ];
    }

    public function indicadoresDashboard(int $anio, int $diasAviso = 30): array
    {
        $anio = $anio > 0 ? $anio : (int)date('Y');
        $ingresosMensuales = $this->ingresosMensuales($anio);
        $ingresosAnuales = array_sum($ingresosMensuales);

        $sqlAnuladoAnio = "SELECT SUM(f.tot_fac) AS total
                             FROM factura f
                            WHERE f.est_registro = 'A'
                              AND f.est_pago = 'A'
                              AND f.fec_anulacion IS NOT NULL
                              AND YEAR(f.fec_anulacion) = ?";
        $stmt = $this->con_bd->prepare($sqlAnuladoAnio);
        $stmt->bind_param('i', $anio);
        $stmt->execute();
        $res = $stmt->get_result();
        $totalAnulado = 0.0;
        if ($res) {
            $row = $res->fetch_assoc();
            if ($row && $row['total'] !== null) {
                $totalAnulado = (float)$row['total'];
            }
        }
        $stmt->close();

        $sqlPendientes = "SELECT COUNT(*) AS cantidad FROM factura WHERE est_registro = 'A' AND est_pago = 'E'";
        $res = $this->con_bd->query($sqlPendientes);
        $pendientes = ($res && ($row = $res->fetch_assoc())) ? (int)$row['cantidad'] : 0;

        $limite = max(1, $diasAviso);
        $sqlVencLic = "SELECT COUNT(*) AS cantidad
                         FROM licencia
                        WHERE est_registro = 'A'
                          AND vig_lic BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)";
        $stmt = $this->con_bd->prepare($sqlVencLic);
        $stmt->bind_param('i', $limite);
        $stmt->execute();
        $res = $stmt->get_result();
        $licencias = ($res && ($row = $res->fetch_assoc())) ? (int)$row['cantidad'] : 0;
        $stmt->close();

        $sqlVencSolMun = "SELECT COUNT(*) AS cantidad
                            FROM solvencia_municipal
                           WHERE est_registro = 'A'
                             AND vig_sol BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)";
        $stmt = $this->con_bd->prepare($sqlVencSolMun);
        $stmt->bind_param('i', $limite);
        $stmt->execute();
        $res = $stmt->get_result();
        $solMun = ($res && ($row = $res->fetch_assoc())) ? (int)$row['cantidad'] : 0;
        $stmt->close();

        $sqlVencSolCom = "SELECT COUNT(*) AS cantidad
                            FROM solvencia_comercial
                           WHERE est_registro = 'A'
                             AND vig_sol_com BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)";
        $stmt = $this->con_bd->prepare($sqlVencSolCom);
        $stmt->bind_param('i', $limite);
        $stmt->execute();
        $res = $stmt->get_result();
        $solCom = ($res && ($row = $res->fetch_assoc())) ? (int)$row['cantidad'] : 0;
        $stmt->close();

        return [
            'anio' => $anio,
            'ingresosAnuales' => round($ingresosAnuales, 2),
            'ingresosMensuales' => $ingresosMensuales,
            'anuladoAnual' => round($totalAnulado, 2),
            'facturasPendientes' => $pendientes,
            'vencimientos' => [
                'licencias' => $licencias,
                'solvenciasMunicipales' => $solMun,
                'solvenciasComerciales' => $solCom,
                'total' => $licencias + $solMun + $solCom,
            ],
        ];
    }
}
?>
