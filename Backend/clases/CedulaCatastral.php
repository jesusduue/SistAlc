<?php
require_once 'Utilidad.php';

class CedulaCatastral extends Utilidad
{
    public $id_ced;
    public $num_ced;
    public $num_exp;
    public $fky_con;
    public $fky_inm;
    public $fky_lin;
    public $fky_doc;
    public $val_ced;
    public $fec_exp_ced;
    public $vig_ced;
    public $sol_ced;
    public $fky_usu;
    public $est_ced;
    public $est_registro;

    /**
     * Registra una cedula catastral.
     */
    public function registrar(array $datos)
    {
        $sql = "INSERT INTO cedula_catastral
            (num_ced, num_exp, fky_con, fky_inm, fky_lin, fky_doc, val_ced, fec_exp_ced, vig_ced, sol_ced, fky_usu, est_ced, est_registro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($sql);
        $estado = $datos['est_ced'] ?? 'S';
        $stmt->bind_param(
            'ssiiiidsssis',
            $datos['num_ced'],
            $datos['num_exp'],
            $datos['fky_con'],
            $datos['fky_inm'],
            $datos['fky_lin'],
            $datos['fky_doc'],
            $datos['val_ced'],
            $datos['fec_exp_ced'],
            $datos['vig_ced'],
            $datos['sol_ced'],
            $datos['fky_usu'],
            $estado
        );
        return $stmt->execute();
    }

    /**
     * Actualiza una cedula catastral existente.
     */
    public function modificar(array $datos)
    {
        $sql = "UPDATE cedula_catastral
                   SET num_ced = ?, num_exp = ?, fky_con = ?, fky_inm = ?, fky_lin = ?, fky_doc = ?,
                       val_ced = ?, fec_exp_ced = ?, vig_ced = ?, sol_ced = ?, fky_usu = ?, est_ced = ?
                 WHERE id_ced = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param(
            'ssiiiidsssisi',
            $datos['num_ced'],
            $datos['num_exp'],
            $datos['fky_con'],
            $datos['fky_inm'],
            $datos['fky_lin'],
            $datos['fky_doc'],
            $datos['val_ced'],
            $datos['fec_exp_ced'],
            $datos['vig_ced'],
            $datos['sol_ced'],
            $datos['fky_usu'],
            $datos['est_ced'],
            $datos['id_ced']
        );
        return $stmt->execute();
    }

    /**
     * Cambia el estado de proceso de la cedula (S, P, V).
     */
    public function actualizar_estado(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE cedula_catastral SET est_ced = ? WHERE id_ced = ?");
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }

    /**
     * Cambia el estado logico del registro.
     */
    public function cambiar_estado_registro(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE cedula_catastral SET est_registro = ? WHERE id_ced = ?");
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }

    /**
     * Recupera una cedula con sus relaciones principales.
     */
    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare(
            "SELECT cc.*, c.nom_con, i.dir_inm, i.tip_inm, dl.tip_doc
               FROM cedula_catastral cc
               INNER JOIN contribuyente c ON cc.fky_con = c.id_con
               INNER JOIN inmueble i ON cc.fky_inm = i.id_inm
               INNER JOIN documentos_legales dl ON cc.fky_doc = dl.id_doc
              WHERE cc.id_ced = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Lista cedulas activas.
     */
    public function consultar_activas()
    {
        $sql = "SELECT cc.*, c.nom_con, i.dir_inm
                  FROM cedula_catastral cc
                  INNER JOIN contribuyente c ON cc.fky_con = c.id_con
                  INNER JOIN inmueble i ON cc.fky_inm = i.id_inm
                 WHERE cc.est_registro = 'A'
                 ORDER BY cc.fec_exp_ced DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista todas las cedulas.
     */
    public function consultar_todas()
    {
        $sql = "SELECT cc.*, c.nom_con, i.dir_inm
                  FROM cedula_catastral cc
                  INNER JOIN contribuyente c ON cc.fky_con = c.id_con
                  INNER JOIN inmueble i ON cc.fky_inm = i.id_inm
                 ORDER BY cc.fec_exp_ced DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Recupera cedulas asociadas a un contribuyente.
     */
    public function consultar_por_contribuyente(int $idCon)
    {
        $stmt = $this->con_bd->prepare(
            "SELECT * FROM cedula_catastral WHERE fky_con = ? ORDER BY fec_exp_ced DESC"
        );
        $stmt->bind_param('i', $idCon);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Valida si existe una cedula con el mismo numero.
     */
    public function numero_existe(string $numero, ?int $excluir_id = null)
    {
        $sql = "SELECT id_ced FROM cedula_catastral WHERE num_ced = ?";
        if ($excluir_id !== null) {
            $sql .= " AND id_ced <> ?";
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

