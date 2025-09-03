<?php
/**
 * Clase Dashboard
 * - Depende de tu clase Utilidad (maneja conexión $this->con_bd y $this->que_bd)
 * - Expone métodos para traer datos del dashboard (actividad reciente, KPIs, etc.)
 */
require_once 'Utilidad.php';

class Dashboard extends Utilidad
{
    /**
     * Obtiene las actividades recientes desde la tabla log_actividad.
     * @param int $limite Cantidad máxima de filas a traer.
     * @return array Arreglo de actividades con mapeo de badge listo para la vista.
     */
    public function actividadesRecientes(int $limite = 20): array
    {
        // Consulta optimizada (usa índice por fecha si lo creaste)
        $sql = "SELECT la.fec_log       AS fecha,
                       la.nom_usu       AS usuario,
                       la.accion        AS accion,
                       la.modulo        AS modulo,
                       la.detalle       AS detalle
                  FROM log_actividad la
                 WHERE la.est_log = 'A'
                 ORDER BY la.fec_log DESC
                 LIMIT ?";

        $stmt = $this->con_bd->prepare($sql);
        if (!$stmt) {
            // Si falla el prepare, devolvemos arreglo vacío (y logueamos si quieres)
            error_log('Dashboard::actividadesRecientes prepare() falló: ' . $this->con_bd->error);
            return [];
        }

        // Bind del límite como entero
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($r = $res->fetch_assoc()) {
            // Mapeo simple de acción a badge para el front
            $r['badge'] = $this->badgePorAccion($r['accion']);
            // Formato opcional de fecha (si prefieres ya formateado)
            // $r['fecha'] = date('Y-m-d H:i', strtotime($r['fecha']));
            $rows[] = $r;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Mapea el nombre de la acción a una clase de Bootstrap Badge.
     * Mantén este método centralizado para consistencia visual.
     */
    private function badgePorAccion(string $accion): string
    {
        // Normalizamos por si llega con mayúsculas/minúsculas variadas
        $a = mb_strtolower($accion, 'UTF-8');

        return match ($a) {
            'registrar', 'activación', 'activacion', 'pago' => 'success',
            'modificar', 'modificacion', 'login', 'logout'         => 'primary',
            'eliminación', 'eliminacion', 'anulación', 'anulacion'      => 'danger',
            'inactivación', 'cambio de estado', 'vencimiento'               => 'secondary',
            default                                                     => 'secondary',
        };
    }
}
