<?php
require_once 'Utilidad.php';

class Licencia extends Utilidad
{
    public $id_lic;
    public $num_lic;
    public $fky_pat;
    public $fky_fac;
    public $fec_exp_lic;
    public $vig_lic;
    public $est_lic;
    public $est_registro;

    public function consultar_activas()
    {
        $sql = "SELECT l.*, p.num_pat, p.nom_pat, f.num_fac,
                       CASE l.est_lic
                           WHEN 'S' THEN 'Solicitada'
                           WHEN 'P' THEN 'Pagada'
                           WHEN 'V' THEN 'Vencida'
                           ELSE 'Desconocido'
                       END AS estado_tramite,
                       CASE l.est_registro WHEN 'A' THEN 'Activo' ELSE 'Inactivo' END AS estado_registro
                  FROM licencia l
                  INNER JOIN patente p ON l.fky_pat = p.id_pat
                  LEFT JOIN factura f ON l.fky_fac = f.id_fac
                 WHERE l.est_registro = 'A'
                 ORDER BY l.vig_lic DESC, l.id_lic ASC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function registrar(array $datos)
    {
        $this->que_bd = "INSERT INTO licencia
            (num_lic, fky_pat, fky_fac, fec_exp_lic, vig_lic, est_lic, est_registro)
            VALUES (?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $estado = $datos['est_lic'] ?? 'S';
        $factura = $datos['fky_fac'] ?? null;
        $stmt->bind_param(
            "siisss",
            $datos['num_lic'],
            $datos['fky_pat'],
            $factura,
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

    public function modificar(array $datos)
    {
        $this->que_bd = "UPDATE licencia
                         SET num_lic = ?, fky_pat = ?, fky_fac = ?, fec_exp_lic = ?, vig_lic = ?, est_lic = ?
                       WHERE id_lic = ?";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $factura = $datos['fky_fac'] ?? null;
        $stmt->bind_param(
            "siisssi",
            $datos['num_lic'],
            $datos['fky_pat'],
            $factura,
            $datos['fec_exp_lic'],
            $datos['vig_lic'],
            $datos['est_lic'],
            $datos['id_lic']
        );
        return $stmt->execute();
    }

    public function cambiar_estado_registro(int $id, string $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE licencia SET est_registro = ? WHERE id_lic = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    public function actualizar_estado_licencia(int $id, string $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE licencia SET est_lic = ? WHERE id_lic = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM licencia WHERE id_lic = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    public function consultar_por_patente(int $idPatente)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM licencia WHERE fky_pat = ? ORDER BY vig_lic DESC");
        $stmt->bind_param("i", $idPatente);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function consultar_todas()
    {
        $this->que_bd = "SELECT l.*, p.num_pat, p.nom_pat, f.num_fac
                           FROM licencia l
                           INNER JOIN patente p ON l.fky_pat = p.id_pat
                           LEFT JOIN factura f ON l.fky_fac = f.id_fac
                       ORDER BY l.vig_lic DESC, l.id_lic ASC";
        $resultado = $this->con_bd->query($this->que_bd);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function numero_existe(string $numero, ?int $excluir_id = null)
    {
        $sql = "SELECT id_lic FROM licencia WHERE num_lic = ?";
        if ($excluir_id !== null) {
            $sql .= " AND id_lic <> ?";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("si", $numero, $excluir_id);
        } else {
            $sql .= " LIMIT 1";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("s", $numero);
        }
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function existe_activa_para_patente(int $idPatente, ?int $excluir_id = null)
    {
        $sql = "SELECT id_lic
                  FROM licencia
                 WHERE fky_pat = ?
                   AND est_registro = 'A'
                   AND est_lic IN ('S', 'P')";
        if ($excluir_id !== null) {
            $sql .= " AND id_lic <> ?";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("ii", $idPatente, $excluir_id);
        } else {
            $sql .= " LIMIT 1";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("i", $idPatente);
        }
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function factura_en_uso(int $facturaId, ?int $excluir_id = null): bool
    {
        $sql = "SELECT id_lic FROM licencia WHERE fky_fac = ?";
        if ($excluir_id !== null) {
            $sql .= " AND id_lic <> ?";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("ii", $facturaId, $excluir_id);
        } else {
            $sql .= " LIMIT 1";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("i", $facturaId);
        }
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function eliminar(int $id)
    {
        $stmt = $this->con_bd->prepare("DELETE FROM licencia WHERE id_lic = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function buscar($termino)
    {
        $this->que_bd = "SELECT l.*, p.num_pat, p.nom_pat, f.num_fac
                           FROM licencia l
                           INNER JOIN patente p ON l.fky_pat = p.id_pat
                           LEFT JOIN factura f ON l.fky_fac = f.id_fac
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

    public function consultar_detallada(int $id)
    {
        $sql = "SELECT l.*,
                       p.num_pat,
                       p.nom_pat,
                       p.rif_pat,
                       p.dir_pat,
                       c.nom_con,
                       c.rif_con,
                       c.dir_con,
                       f.num_fac,
                       f.fec_fac,
                       f.tot_fac
                  FROM licencia l
                  INNER JOIN patente p ON l.fky_pat = p.id_pat
                  INNER JOIN contribuyente c ON p.fky_con = c.id_con
                  LEFT JOIN factura f ON l.fky_fac = f.id_fac
                 WHERE l.id_lic = ?
                 LIMIT 1";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $datos = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $datos ?: null;
    }
}
?>
