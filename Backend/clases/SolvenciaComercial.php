<?php
require_once 'Utilidad.php';

class SolvenciaComercial extends Utilidad
{
    public $id_sol_com;
    public $num_sol_com;
    public $fky_pat;
    public $fec_exp_sol_com;
    public $vig_sol_com;
    public $est_sol_com;
    public $est_registro;

    /**
     * Registra una solvencia comercial.
     */
    public function registrar(array $datos)
    {
        $sql = "INSERT INTO solvencia_comercial
            (num_sol_com, fky_pat, fec_exp_sol_com, vig_sol_com, est_sol_com, est_registro)
            VALUES (?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($sql);
        $estado = $datos['est_sol_com'] ?? 'S';
        $stmt->bind_param('sisss', $datos['num_sol_com'], $datos['fky_pat'], $datos['fec_exp_sol_com'], $datos['vig_sol_com'], $estado);
        return $stmt->execute();
    }

    /**
     * Actualiza los datos de una solvencia comercial.
     */
    public function modificar(array $datos)
    {
        $sql = "UPDATE solvencia_comercial
                   SET num_sol_com = ?, fky_pat = ?, fec_exp_sol_com = ?, vig_sol_com = ?, est_sol_com = ?
                 WHERE id_sol_com = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param('sisssi', $datos['num_sol_com'], $datos['fky_pat'], $datos['fec_exp_sol_com'], $datos['vig_sol_com'], $datos['est_sol_com'], $datos['id_sol_com']);
        return $stmt->execute();
    }

    /**
     * Cambia el estado del proceso (S, P, V).
     */
    public function actualizar_estado(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE solvencia_comercial SET est_sol_com = ? WHERE id_sol_com = ?");
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }

    /**
     * Cambia el estado logico del registro.
     */
    public function cambiar_estado_registro(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE solvencia_comercial SET est_registro = ? WHERE id_sol_com = ?");
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }

    /**
     * Lista solvencias comerciales activas.
     */
    public function consultar_activas()
    {
        $sql = "SELECT sc.*, p.num_pat, p.nom_pat
                  FROM solvencia_comercial sc
                  INNER JOIN patente p ON sc.fky_pat = p.id_pat
                 WHERE sc.est_registro = 'A'
                 ORDER BY sc.fec_exp_sol_com DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista todas las solvencias comerciales.
     */
    public function consultar_todas()
    {
        $sql = "SELECT sc.*, p.num_pat, p.nom_pat
                  FROM solvencia_comercial sc
                  INNER JOIN patente p ON sc.fky_pat = p.id_pat
                 ORDER BY sc.fec_exp_sol_com DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Recupera una solvencia comercial por id.
     */
    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM solvencia_comercial WHERE id_sol_com = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Verifica si existe una solvencia comercial con el mismo numero.
     */
    public function numero_existe(string $numero, ?int $excluir_id = null)
    {
        $sql = "SELECT id_sol_com FROM solvencia_comercial WHERE num_sol_com = ?";
        if ($excluir_id !== null) {
            $sql .= " AND id_sol_com <> ?";
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

    /**
     * Lista solvencias por patente.
     */
    public function consultar_por_patente(int $patenteId)
    {
        $stmt = $this->con_bd->prepare(
            "SELECT * FROM solvencia_comercial WHERE fky_pat = ? ORDER BY fec_exp_sol_com DESC"
        );
        $stmt->bind_param('i', $patenteId);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
