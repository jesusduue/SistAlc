<?php
require_once 'Utilidad.php';

class VehiculoFiscal extends Utilidad
{
    public $id_veh_fis;
    public $fky_con;
    public $pla_veh;
    public $mar_veh;
    public $mod_veh;
    public $ano_veh;
    public $col_veh;
    public $tip_veh;
    public $cap_car_veh;
    public $num_ser_veh;
    public $est_veh;
    public $est_registro;
    public $ultimo_error = '';

    public function consultar_activos()
    {
        $sql = "SELECT v.id_veh_fis,
                       v.fky_con,
                       v.pla_veh,
                       v.mar_veh,
                       v.mod_veh,
                       v.`año_veh` AS ano_veh,
                       v.col_veh,
                       v.tip_veh,
                       v.cap_car_veh,
                       v.num_ser_veh,
                       LOWER(v.est_veh) AS est_veh,
                       LOWER(v.est_registro) AS est_registro,
                       c.nom_con,
                       c.rif_con,
                       COUNT(p.id_pat_vf) AS total_patentes,
                       CASE LOWER(v.est_veh)
                           WHEN 'p' THEN 'Propio'
                           WHEN 't' THEN 'Traspaso'
                           WHEN 'e' THEN 'En venta'
                           ELSE 'Sin definir'
                       END AS estado_vehiculo,
                       CASE LOWER(v.est_registro)
                           WHEN 'a' THEN 'Activo'
                           ELSE 'Inactivo'
                       END AS estado_registro
                  FROM vehiculo_fiscal v
                 INNER JOIN contribuyente c ON c.id_con = v.fky_con
                  LEFT JOIN patente_vehiculo_fiscal p
                         ON p.fky_veh_fis = v.id_veh_fis
                        AND LOWER(p.est_registro) = 'a'
                 WHERE LOWER(v.est_registro) = 'a'
              GROUP BY v.id_veh_fis, v.fky_con, v.pla_veh, v.mar_veh, v.mod_veh, v.`año_veh`, v.col_veh,
                       v.tip_veh, v.cap_car_veh, v.num_ser_veh, v.est_veh, v.est_registro, c.nom_con, c.rif_con
              ORDER BY v.pla_veh ASC";
        $resultado = $this->con_bd->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function consultar_todos()
    {
        $sql = "SELECT v.id_veh_fis,
                       v.fky_con,
                       v.pla_veh,
                       v.mar_veh,
                       v.mod_veh,
                       v.`año_veh` AS ano_veh,
                       v.col_veh,
                       v.tip_veh,
                       v.cap_car_veh,
                       v.num_ser_veh,
                       LOWER(v.est_veh) AS est_veh,
                       LOWER(v.est_registro) AS est_registro,
                       c.nom_con,
                       c.rif_con,
                       COUNT(p.id_pat_vf) AS total_patentes,
                       CASE LOWER(v.est_veh)
                           WHEN 'p' THEN 'Propio'
                           WHEN 't' THEN 'Traspaso'
                           WHEN 'e' THEN 'En venta'
                           ELSE 'Sin definir'
                       END AS estado_vehiculo,
                       CASE LOWER(v.est_registro)
                           WHEN 'a' THEN 'Activo'
                           ELSE 'Inactivo'
                       END AS estado_registro
                  FROM vehiculo_fiscal v
                 INNER JOIN contribuyente c ON c.id_con = v.fky_con
                  LEFT JOIN patente_vehiculo_fiscal p ON p.fky_veh_fis = v.id_veh_fis
              GROUP BY v.id_veh_fis, v.fky_con, v.pla_veh, v.mar_veh, v.mod_veh, v.`año_veh`, v.col_veh,
                       v.tip_veh, v.cap_car_veh, v.num_ser_veh, v.est_veh, v.est_registro, c.nom_con, c.rif_con
              ORDER BY v.pla_veh ASC";
        $resultado = $this->con_bd->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function consultar_por_id(int $id)
    {
        $sql = "SELECT v.id_veh_fis,
                       v.fky_con,
                       v.pla_veh,
                       v.mar_veh,
                       v.mod_veh,
                       v.`año_veh` AS ano_veh,
                       v.col_veh,
                       v.tip_veh,
                       v.cap_car_veh,
                       v.num_ser_veh,
                       LOWER(v.est_veh) AS est_veh,
                       LOWER(v.est_registro) AS est_registro
                  FROM vehiculo_fiscal v
                 WHERE v.id_veh_fis = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    public function registrar(array $datos)
    {
        $sql = "INSERT INTO vehiculo_fiscal
                (fky_con, pla_veh, mar_veh, mod_veh, `año_veh`, col_veh, tip_veh, cap_car_veh, num_ser_veh, est_veh, est_registro)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'a')";
        $stmt = $this->con_bd->prepare($sql);
        $color = $datos['col_veh'] !== '' ? $datos['col_veh'] : null;
        $capacidad = $datos['cap_car_veh'] !== '' ? $datos['cap_car_veh'] : null;
        $serial = $datos['num_ser_veh'] !== '' ? $datos['num_ser_veh'] : null;
        $stmt->bind_param(
            "isssisssss",
            $datos['fky_con'],
            $datos['pla_veh'],
            $datos['mar_veh'],
            $datos['mod_veh'],
            $datos['ano_veh'],
            $color,
            $datos['tip_veh'],
            $capacidad,
            $serial,
            $datos['est_veh']
        );
        $ok = $stmt->execute();
        if ($ok) {
            $this->id_veh_fis = $stmt->insert_id;
        }
        $stmt->close();
        return $ok;
    }

    public function modificar(array $datos)
    {
        $sql = "UPDATE vehiculo_fiscal
                   SET fky_con = ?,
                       pla_veh = ?,
                       mar_veh = ?,
                       mod_veh = ?,
                       `año_veh` = ?,
                       col_veh = ?,
                       tip_veh = ?,
                       cap_car_veh = ?,
                       num_ser_veh = ?,
                       est_veh = ?
                 WHERE id_veh_fis = ?";
        $stmt = $this->con_bd->prepare($sql);
        $color = $datos['col_veh'] !== '' ? $datos['col_veh'] : null;
        $capacidad = $datos['cap_car_veh'] !== '' ? $datos['cap_car_veh'] : null;
        $serial = $datos['num_ser_veh'] !== '' ? $datos['num_ser_veh'] : null;
        $stmt->bind_param(
            "isssisssssi",
            $datos['fky_con'],
            $datos['pla_veh'],
            $datos['mar_veh'],
            $datos['mod_veh'],
            $datos['ano_veh'],
            $color,
            $datos['tip_veh'],
            $capacidad,
            $serial,
            $datos['est_veh'],
            $datos['id_veh_fis']
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function cambiar_estado(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE vehiculo_fiscal SET est_registro = ? WHERE id_veh_fis = ?");
        $stmt->bind_param("si", $estado, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function eliminar(int $id)
    {
        $stmt = $this->con_bd->prepare("DELETE FROM vehiculo_fiscal WHERE id_veh_fis = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function placa_existe(string $placa, ?int $excluir = null)
    {
        $sql = "SELECT id_veh_fis FROM vehiculo_fiscal WHERE UPPER(pla_veh) = UPPER(?)";
        if ($excluir !== null) {
            $sql .= " AND id_veh_fis <> ?";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("si", $placa, $excluir);
        } else {
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param("s", $placa);
        }
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function buscar($termino)
    {
        $sql = "SELECT v.id_veh_fis,
                       v.fky_con,
                       v.pla_veh,
                       v.mar_veh,
                       v.mod_veh,
                       v.`año_veh` AS ano_veh,
                       v.col_veh,
                       v.tip_veh,
                       v.cap_car_veh,
                       v.num_ser_veh,
                       LOWER(v.est_veh) AS est_veh,
                       LOWER(v.est_registro) AS est_registro,
                       c.nom_con,
                       c.rif_con
                  FROM vehiculo_fiscal v
                 INNER JOIN contribuyente c ON c.id_con = v.fky_con
                 WHERE LOWER(v.est_registro) = 'a'
                   AND (
                        v.pla_veh LIKE ? OR
                        v.mar_veh LIKE ? OR
                        v.mod_veh LIKE ? OR
                        v.tip_veh LIKE ? OR
                        c.nom_con LIKE ? OR
                        c.rif_con LIKE ?
                   )
              ORDER BY v.pla_veh ASC";
        $stmt = $this->con_bd->prepare($sql);
        $like = '%' . $termino . '%';
        $stmt->bind_param("ssssss", $like, $like, $like, $like, $like, $like);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
