<?php
require_once 'Utilidad.php';

class Licencia extends Utilidad
{
    // Columnas expuestas por la tabla licencia
    public $id_lic;
    public $num_lic;
    public $fky_pat;
    public $fec_exp_lic;
    public $vig_lic;
    public $est_lic;
    public $est_registro;

    /**
     * Lista las licencias activas junto a los datos mas relevantes de la patente.
     */
    public function consultar_activas()
    {
        $sql = "SELECT l.*, p.num_pat, p.nom_pat,
                       CASE l.est_lic
                           WHEN 'S' THEN 'Solicitada'
                           WHEN 'P' THEN 'Pagada'
                           WHEN 'V' THEN 'Vencida'
                           ELSE 'Desconocido'
                       END AS estado_tramite,
                       CASE l.est_registro WHEN 'A' THEN 'Activo' ELSE 'Inactivo' END AS estado_registro
                  FROM licencia l
                  INNER JOIN patente p ON l.fky_pat = p.id_pat
                 WHERE l.est_registro = 'A'
                 ORDER BY l.vig_lic DESC, l.id_lic ASC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Registra una licencia para una patente determinada.
     */
    public function registrar($datos)
    {
        $this->que_bd = "INSERT INTO licencia
            (num_lic, fky_pat, fec_exp_lic, vig_lic, est_lic, est_registro)
            VALUES (?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $estado = $datos['est_lic'] ?? 'S';
        $stmt->bind_param(
            "sisss",
            $datos['num_lic'],
            $datos['fky_pat'],
            $datos['fec_exp_lic'],
            $datos['vig_lic'],
            $estado
        );
        $ok = $stmt->execute();
        if ($ok) {
            $this->id_lic = $stmt->insert_id;
        }
        $stmt->close();
        return $ok;
    }

    /**
     * Actualiza los datos de una licencia existente.
     */
    public function modificar($datos)
    {
        $this->que_bd = "UPDATE licencia
                         SET num_lic = ?, fky_pat = ?, fec_exp_lic = ?, vig_lic = ?, est_lic = ?
                       WHERE id_lic = ?";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $stmt->bind_param(
            "sisssi",
            $datos['num_lic'],
            $datos['fky_pat'],
            $datos['fec_exp_lic'],
            $datos['vig_lic'],
            $datos['est_lic'],
            $datos['id_lic']
        );
        return $stmt->execute();
    }

    /**
     * Cambia el estado logico (A/I) del registro.
     */
    public function cambiar_estado_registro($id, $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE licencia SET est_registro = ? WHERE id_lic = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    /**
     * Actualiza el estado del tramite (S, P, V).
     */
    public function actualizar_estado_licencia($id, $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE licencia SET est_lic = ? WHERE id_lic = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    /**
     * Devuelve una licencia segun su identificador.
     */
    public function consultar_por_id($id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM licencia WHERE id_lic = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    /**
     * Lista licencias asociadas a una patente en orden de vigencia.
     */
    public function consultar_por_patente($idPatente)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM licencia WHERE fky_pat = ? ORDER BY vig_lic DESC");
        $stmt->bind_param("i", $idPatente);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista todas las licencias registradas, sin filtrar por estado.
     */
    public function consultar_todas()
    {
        $this->que_bd = "SELECT l.*, p.num_pat, p.nom_pat
                           FROM licencia l
                           INNER JOIN patente p ON l.fky_pat = p.id_pat
                       ORDER BY l.vig_lic DESC, l.id_lic ASC";
        $resultado = $this->con_bd->query($this->que_bd);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Verifica si ya existe una licencia con el mismo numero.
     */
    public function numero_existe($numero)
    {
        $stmt = $this->con_bd->prepare("SELECT id_lic FROM licencia WHERE num_lic = ? LIMIT 1");
        $stmt->bind_param("s", $numero);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    /**
     * Determina si la patente tiene una licencia activa (registro A y estado S/P).
     */
    public function existe_activa_para_patente($idPatente)
    {
        $stmt = $this->con_bd->prepare(
            "SELECT id_lic
               FROM licencia
              WHERE fky_pat = ?
                AND est_registro = 'A'
                AND est_lic IN ('S', 'P')
              LIMIT 1"
        );
        $stmt->bind_param("i", $idPatente);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    /**
     * Elimina fisicamente una licencia.
     */
    public function eliminar($id)
    {
        $stmt = $this->con_bd->prepare("DELETE FROM licencia WHERE id_lic = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Busca licencias activas segun numero, patente, vigencia o estado.
     */
    public function buscar($termino)
    {
        $this->que_bd = "SELECT l.*, p.num_pat, p.nom_pat
                           FROM licencia l
                           INNER JOIN patente p ON l.fky_pat = p.id_pat
                          WHERE l.est_registro = 'A'
                            AND (
                                l.num_lic LIKE ? OR
                                p.num_pat LIKE ? OR
                                p.nom_pat LIKE ? OR
                                DATE_FORMAT(l.vig_lic, '%Y-%m-%d') LIKE ? OR
                                l.est_lic LIKE ?
                            )
                       ORDER BY l.vig_lic DESC, p.num_pat ASC";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $like = '%' . $termino . '%';
        $stmt->bind_param("sssss", $like, $like, $like, $like, $like);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
