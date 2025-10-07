<?php
require_once 'Utilidad.php';

class DetalleImpuesto extends Utilidad
{
    public $id_det_imp;
    public $fky_fac;
    public $fky_tip_imp;
    public $mon_imp;
    public $fky_ced;
    public $fky_sol_mun;
    public $fky_lic;
    public $fky_sol_com;
    public $est_registro;

    /**
     * Inserta un detalle de impuesto. Permite recibir una conexion externa para reutilizar transacciones.
     */
    public function registrar(array $datos, ?mysqli $conexionExterna = null)
    {
        $conexion = $conexionExterna ?? $this->con_bd;
        $sql = "INSERT INTO detalle_impuesto
                    (fky_fac, fky_tip_imp, mon_imp, fky_ced, fky_sol_mun, fky_lic, fky_sol_com, est_registro)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $conexion->prepare($sql);
        $fky_ced = $datos['fky_ced'] ?? null;
        $fky_sol_mun = $datos['fky_sol_mun'] ?? null;
        $fky_lic = $datos['fky_lic'] ?? null;
        $fky_sol_com = $datos['fky_sol_com'] ?? null;
        $stmt->bind_param(
            'iidiiii',
            $datos['fky_fac'],
            $datos['fky_tip_imp'],
            $datos['mon_imp'],
            $fky_ced,
            $fky_sol_mun,
            $fky_lic,
            $fky_sol_com
        );
        return $stmt->execute();
    }

    /**
     * Inserta un conjunto de detalles en lote dentro de la misma transaccion.
     */
    public function registrar_lote(int $facturaId, array $detalles)
    {
        if (empty($detalles)) {
            return true;
        }
        $this->con_bd->begin_transaction();
        try {
            foreach ($detalles as $detalle) {
                $detalle['fky_fac'] = $facturaId;
                if (!$this->registrar($detalle, $this->con_bd)) {
                    throw new Exception('Error al insertar detalle');
                }
            }
            $this->con_bd->commit();
            return true;
        } catch (Throwable $e) {
            $this->con_bd->rollback();
            error_log('DetalleImpuesto::registrar_lote error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los detalles asociados a una factura.
     */
    public function listar_por_factura(int $facturaId)
    {
        $stmt = $this->con_bd->prepare(
            "SELECT di.*, ti.nom_tip
               FROM detalle_impuesto di
               INNER JOIN tipo_impuesto ti ON di.fky_tip_imp = ti.id_tip
              WHERE di.fky_fac = ? AND di.est_registro = 'A'"
        );
        $stmt->bind_param('i', $facturaId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Elimina (logicamente) los detalles de una factura.
     */
    public function desactivar_por_factura(int $facturaId)
    {
        $stmt = $this->con_bd->prepare("UPDATE detalle_impuesto SET est_registro = 'I' WHERE fky_fac = ?");
        $stmt->bind_param('i', $facturaId);
        return $stmt->execute();
    }

    /**
     * Borra de forma fisica los detalles de una factura.
     */
    public function eliminar_por_factura(int $facturaId)
    {
        $stmt = $this->con_bd->prepare("DELETE FROM detalle_impuesto WHERE fky_fac = ?");
        $stmt->bind_param('i', $facturaId);
        return $stmt->execute();
    }
}
?>
