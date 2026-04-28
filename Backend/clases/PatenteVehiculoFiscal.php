<?php
require_once 'Utilidad.php';

class PatenteVehiculoFiscal extends Utilidad
{
    public $id_pat_vf;
    public $num_pat_vf;
    public $fky_veh_fis;
    public $fec_emi_pat;
    public $fec_ven_pat;
    public $mon_pat;
    public $est_pat;
    public $est_registro;
    public $fky_usu;

    public function consultar_activas()
    {
        $sql = "SELECT p.id_pat_vf,
                       p.num_pat_vf,
                       p.fky_veh_fis,
                       p.fec_emi_pat,
                       p.fec_ven_pat,
                       p.mon_pat,
                       LOWER(p.est_pat) AS est_pat,
                       LOWER(p.est_registro) AS est_registro,
                       p.fky_usu,
                       v.pla_veh,
                       v.mar_veh,
                       v.mod_veh,
                       v.`año_veh` AS ano_veh,
                       c.nom_con,
                       c.rif_con,
                       CASE LOWER(p.est_pat)
                           WHEN 'e' THEN 'Emitida'
                           WHEN 'v' THEN 'Vencida'
                           WHEN 'a' THEN 'Anulada'
                           ELSE 'Sin definir'
                       END AS estado_patente,
                       CASE LOWER(p.est_registro)
                           WHEN 'a' THEN 'Activo'
                           ELSE 'Inactivo'
                       END AS estado_registro
                  FROM patente_vehiculo_fiscal p
                 INNER JOIN vehiculo_fiscal v ON v.id_veh_fis = p.fky_veh_fis
                 INNER JOIN contribuyente c ON c.id_con = v.fky_con
                 WHERE LOWER(p.est_registro) = 'a'
              ORDER BY p.fec_ven_pat DESC, p.id_pat_vf DESC";
        $resultado = $this->con_bd->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function consultar_todas()
    {
        $sql = "SELECT p.id_pat_vf,
                       p.num_pat_vf,
                       p.fky_veh_fis,
                       p.fec_emi_pat,
                       p.fec_ven_pat,
                       p.mon_pat,
                       LOWER(p.est_pat) AS est_pat,
                       LOWER(p.est_registro) AS est_registro,
                       p.fky_usu,
                       v.pla_veh,
                       v.mar_veh,
                       v.mod_veh,
                       v.`año_veh` AS ano_veh,
                       c.nom_con,
                       c.rif_con,
                       CASE LOWER(p.est_pat)
                           WHEN 'e' THEN 'Emitida'
                           WHEN 'v' THEN 'Vencida'
                           WHEN 'a' THEN 'Anulada'
                           ELSE 'Sin definir'
                       END AS estado_patente,
                       CASE LOWER(p.est_registro)
                           WHEN 'a' THEN 'Activo'
                           ELSE 'Inactivo'
                       END AS estado_registro
                  FROM patente_vehiculo_fiscal p
                 INNER JOIN vehiculo_fiscal v ON v.id_veh_fis = p.fky_veh_fis
                 INNER JOIN contribuyente c ON c.id_con = v.fky_con
              ORDER BY p.fec_ven_pat DESC, p.id_pat_vf DESC";
        $resultado = $this->con_bd->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function consultar_por_id(int $id)
    {
        $sql = "SELECT p.id_pat_vf,
                       p.num_pat_vf,
                       p.fky_veh_fis,
                       p.fec_emi_pat,
                       p.fec_ven_pat,
                       p.mon_pat,
                       LOWER(p.est_pat) AS est_pat,
                       LOWER(p.est_registro) AS est_registro,
                       p.fky_usu
                  FROM patente_vehiculo_fiscal p
                 WHERE p.id_pat_vf = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    public function registrar(array $datos)
    {
        $sql = "INSERT INTO patente_vehiculo_fiscal
                (num_pat_vf, fky_veh_fis, fec_emi_pat, fec_ven_pat, mon_pat, est_pat, est_registro, fky_usu)
                VALUES (?, ?, ?, ?, ?, ?, 'a', ?)";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param(
            "sissdsi",
            $datos['num_pat_vf'],
            $datos['fky_veh_fis'],
            $datos['fec_emi_pat'],
            $datos['fec_ven_pat'],
            $datos['mon_pat'],
            $datos['est_pat'],
            $datos['fky_usu']
        );
        $ok = $stmt->execute();
        if ($ok) {
            $this->id_pat_vf = $stmt->insert_id;
        }
        $stmt->close();
        return $ok;
    }

    public function modificar(array $datos)
    {
        $sql = "UPDATE patente_vehiculo_fiscal
                   SET num_pat_vf = ?,
                       fky_veh_fis = ?,
                       fec_emi_pat = ?,
                       fec_ven_pat = ?,
                       mon_pat = ?,
                       est_pat = ?
                 WHERE id_pat_vf = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param(
            "sissdsi",
            $datos['num_pat_vf'],
            $datos['fky_veh_fis'],
            $datos['fec_emi_pat'],
            $datos['fec_ven_pat'],
            $datos['mon_pat'],
            $datos['est_pat'],
            $datos['id_pat_vf']
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function cambiar_estado(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE patente_vehiculo_fiscal SET est_registro = ? WHERE id_pat_vf = ?");
        $stmt->bind_param("si", $estado, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizar_estado_patente(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE patente_vehiculo_fiscal SET est_pat = ? WHERE id_pat_vf = ?");
        $stmt->bind_param("si", $estado, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function eliminar(int $id)
    {
        $stmt = $this->con_bd->prepare("DELETE FROM patente_vehiculo_fiscal WHERE id_pat_vf = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function numero_existe(string $numero, ?int $excluir = null)
    {
        $sql = "SELECT id_pat_vf FROM patente_vehiculo_fiscal WHERE num_pat_vf = ?";
        if ($excluir !== null) {
            $sql .= " AND id_pat_vf <> ?";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("si", $numero, $excluir);
        } else {
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("s", $numero);
        }
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function buscar($termino)
    {
        $sql = "SELECT p.id_pat_vf,
                       p.num_pat_vf,
                       p.fky_veh_fis,
                       p.fec_emi_pat,
                       p.fec_ven_pat,
                       p.mon_pat,
                       LOWER(p.est_pat) AS est_pat,
                       LOWER(p.est_registro) AS est_registro,
                       v.pla_veh,
                       v.mar_veh,
                       v.mod_veh,
                       c.nom_con,
                       c.rif_con
                  FROM patente_vehiculo_fiscal p
                 INNER JOIN vehiculo_fiscal v ON v.id_veh_fis = p.fky_veh_fis
                 INNER JOIN contribuyente c ON c.id_con = v.fky_con
                 WHERE LOWER(p.est_registro) = 'a'
                   AND (
                        p.num_pat_vf LIKE ? OR
                        v.pla_veh LIKE ? OR
                        v.mar_veh LIKE ? OR
                        v.mod_veh LIKE ? OR
                        c.nom_con LIKE ? OR
                        c.rif_con LIKE ?
                   )
              ORDER BY p.fec_ven_pat DESC, p.id_pat_vf DESC";
        $stmt = $this->con_bd->prepare($sql);
        $like = '%' . $termino . '%';
        $stmt->bind_param("ssssss", $like, $like, $like, $like, $like, $like);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
