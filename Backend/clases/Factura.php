<?php
require_once 'Utilidad.php';
require_once 'DetalleImpuesto.php';

class Factura extends Utilidad
{
    public $id_fac;
    public $num_fac;
    public $fec_fac;
    public $fky_usu;
    public $fky_con;
    public $des_fac;
    public $tot_fac;
    public $est_pago;
    public $est_registro;

    /**
     * Devuelve las facturas activas ordenadas de la mas reciente a la mas antigua.
     */
    public function consultar_activas()
    {
        $sql = "SELECT f.*, c.nom_con, u.nom_usu
                  FROM factura f
                  INNER JOIN contribuyente c ON f.fky_con = c.id_con
                  INNER JOIN usuario u ON f.fky_usu = u.id_usu
                 WHERE f.est_registro = 'A'
                 ORDER BY f.fec_fac DESC, f.id_fac DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Recupera todas las facturas sin aplicar filtros de estado.
     */
    public function consultar_todas()
    {
        $sql = "SELECT f.*, c.nom_con, u.nom_usu
                  FROM factura f
                  INNER JOIN contribuyente c ON f.fky_con = c.id_con
                  INNER JOIN usuario u ON f.fky_usu = u.id_usu
                 ORDER BY f.fec_fac DESC, f.id_fac DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Registra una factura y guarda el identificador generado.
     */
    public function registrar(array $datos)
    {
        $sql = "INSERT INTO factura (num_fac, fec_fac, fky_usu, fky_con, des_fac, tot_fac, est_pago, est_registro)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($sql);
        $fecha = $datos['fec_fac'] ?? date('Y-m-d H:i:s');
        $estado = $datos['est_pago'] ?? 'E';
        $stmt->bind_param(
            'ssiisds',
            $datos['num_fac'],
            $fecha,
            $datos['fky_usu'],
            $datos['fky_con'],
            $datos['des_fac'],
            $datos['tot_fac'],
            $estado
        );
        $ok = $stmt->execute();
        if ($ok) {
            $this->id_fac = $stmt->insert_id;
        }
        $stmt->close();
        return $ok;
    }

    /**
     * Registra una factura y sus detalles dentro de una transaccion segura.
     */
    public function registrar_con_detalles(array $facturaDatos, array $detalles)
    {
        $detalleModel = new DetalleImpuesto();
        $this->con_bd->begin_transaction();
        try {
            if (!$this->registrar($facturaDatos)) {
                throw new Exception('No se pudo registrar la factura');
            }
            $facturaId = $this->id_fac;
            if (!empty($detalles)) {
                foreach ($detalles as $detalle) {
                    $detalle['fky_fac'] = $facturaId;
                    if (!$detalleModel->registrar($detalle, $this->con_bd)) {
                        throw new Exception('No se pudo registrar el detalle de impuesto');
                    }
                }
            }
            $this->con_bd->commit();
            return true;
        } catch (Throwable $e) {
            $this->con_bd->rollback();
            error_log('Factura::registrar_con_detalles error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza los datos principales de la factura.
     */
    public function modificar(array $datos)
    {
        $sql = "UPDATE factura
                   SET num_fac = ?, fec_fac = ?, fky_usu = ?, fky_con = ?, des_fac = ?, tot_fac = ?, est_pago = ?
                 WHERE id_fac = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param(
            'ssiisdsi',
            $datos['num_fac'],
            $datos['fec_fac'],
            $datos['fky_usu'],
            $datos['fky_con'],
            $datos['des_fac'],
            $datos['tot_fac'],
            $datos['est_pago'],
            $datos['id_fac']
        );
        return $stmt->execute();
    }

    /**
     * Cambia el estado logico de la factura.
     */
    public function cambiar_estado_registro(int $id, string $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE factura SET est_registro = ? WHERE id_fac = ?");
        $stmt->bind_param('si', $nuevo_estado, $id);
        return $stmt->execute();
    }

    /**
     * Actualiza el estado de pago de la factura.
     */
    public function actualizar_estado_pago(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE factura SET est_pago = ? WHERE id_fac = ?");
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }

    /**
     * Recupera una factura por su identificador.
     */
    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM factura WHERE id_fac = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Lista facturas asociadas a un contribuyente.
     */
    public function consultar_por_contribuyente(int $idContribuyente)
    {
        $stmt = $this->con_bd->prepare(
            "SELECT * FROM factura WHERE fky_con = ? ORDER BY fec_fac DESC, id_fac DESC"
        );
        $stmt->bind_param('i', $idContribuyente);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Verifica si el numero de factura se encuentra registrado.
     */
    public function numero_existe(string $numero, ?int $excluir_id = null)
    {
        $sql = "SELECT id_fac FROM factura WHERE num_fac = ?";
        if ($excluir_id !== null) {
            $sql .= " AND id_fac <> ?";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param('si', $numero, $excluir_id);
        } else {
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param('s', $numero);
        }
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}
?>

