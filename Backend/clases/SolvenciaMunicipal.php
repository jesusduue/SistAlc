<?php
require_once 'Utilidad.php';

class SolvenciaMunicipal extends Utilidad
{
    public $id_sol;
    public $num_sol;
    public $fky_con;
    public $fky_ced;
    public $sol_sol;
    public $fec_exp_sol;
    public $vig_sol;
    public $fky_usu;
    public $est_sol;
    public $est_registro;

    /**
     * Registra una solvencia municipal.
     */
    public function registrar(array $datos)
    {
        $sql = "INSERT INTO solvencia_municipal
            (num_sol, fky_con, fky_ced, sol_sol, fec_exp_sol, vig_sol, fky_usu, est_sol, est_registro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($sql);
        $estado = $datos['est_sol'] ?? 'S';
        $stmt->bind_param(
            'siisssis',
            $datos['num_sol'],
            $datos['fky_con'],
            $datos['fky_ced'],
            $datos['sol_sol'],
            $datos['fec_exp_sol'],
            $datos['vig_sol'],
            $datos['fky_usu'],
            $estado
        );
        return $stmt->execute();
    }

    /**
     * Actualiza una solvencia municipal.
     */
    public function modificar(array $datos)
    {
        $sql = "UPDATE solvencia_municipal
                   SET num_sol = ?, fky_con = ?, fky_ced = ?, sol_sol = ?, fec_exp_sol = ?, vig_sol = ?, fky_usu = ?, est_sol = ?
                 WHERE id_sol = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param(
            'siisssisi',
            $datos['num_sol'],
            $datos['fky_con'],
            $datos['fky_ced'],
            $datos['sol_sol'],
            $datos['fec_exp_sol'],
            $datos['vig_sol'],
            $datos['fky_usu'],
            $datos['est_sol'],
            $datos['id_sol']
        );
        return $stmt->execute();
    }

    /**
     * Cambia el estado del proceso (S, P, V).
     */
    public function actualizar_estado(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE solvencia_municipal SET est_sol = ? WHERE id_sol = ?");
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }

    /**
     * Cambia el estado logico del registro.
     */
    public function cambiar_estado_registro(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE solvencia_municipal SET est_registro = ? WHERE id_sol = ?");
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }

    /**
     * Lista las solvencias activas con datos del contribuyente.
     */
    public function consultar_activas()
    {
        $sql = "SELECT sm.*, c.nom_con, cc.num_ced
                  FROM solvencia_municipal sm
                  INNER JOIN contribuyente c ON sm.fky_con = c.id_con
                  INNER JOIN cedula_catastral cc ON sm.fky_ced = cc.id_ced
                 WHERE sm.est_registro = 'A'
                 ORDER BY sm.fec_exp_sol DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista todas las solvencias sin filtrar.
     */
    public function consultar_todas()
    {
        $sql = "SELECT sm.*, c.nom_con, cc.num_ced
                  FROM solvencia_municipal sm
                  INNER JOIN contribuyente c ON sm.fky_con = c.id_con
                  INNER JOIN cedula_catastral cc ON sm.fky_ced = cc.id_ced
                 ORDER BY sm.fec_exp_sol DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Recupera una solvencia segun su id.
     */
    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM solvencia_municipal WHERE id_sol = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Lista solvencias asociadas a un contribuyente.
     */
    public function consultar_por_contribuyente(int $idCon)
    {
        $stmt = $this->con_bd->prepare(
            "SELECT * FROM solvencia_municipal WHERE fky_con = ? ORDER BY fec_exp_sol DESC"
        );
        $stmt->bind_param('i', $idCon);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Valida si existe el numero de solvencia.
     */
    public function numero_existe(string $numero, ?int $excluir_id = null)
    {
        $sql = "SELECT id_sol FROM solvencia_municipal WHERE num_sol = ?";
        if ($excluir_id !== null) {
            $sql .= " AND id_sol <> ?";
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
