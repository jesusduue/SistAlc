<!-- clase patente  -->
<?php
require_once 'Utilidad.php';

class Patente extends Utilidad
{
    // Propiedades segun la tabla patente
    public $id_pat;
    public $fec_pat;
    public $num_pat;
    public $rif_pat;
    public $nom_pat;
    public $fky_con;
    public $dir_pat;
    public $rub_pat;
    public $fky_usu;
    public $est_pat;
    public $est_registro;

    /**
     * Consulta todas las patentes activas (est_registro = 'A').
     */
    public function consultar_activas()
    {
        $sql = "SELECT p.*,
                       CASE p.est_pat
                           WHEN 'S' THEN 'Solicitada'
                           WHEN 'P' THEN 'Pagada'
                           WHEN 'V' THEN 'Vencida'
                           ELSE 'Desconocido'
                       END AS estado_patente,
                       CASE p.est_registro
                           WHEN 'A' THEN 'Activo'
                           ELSE 'Inactivo'
                       END AS estado_registro
                  FROM patente p
                 WHERE p.est_registro = 'A'
                 ORDER BY p.fec_pat DESC, p.id_pat ASC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Registra una nueva patente.
     * La fecha se fija con la fecha actual del servidor.
     */
    public function registrar($datos)
    {
        $this->que_bd = "INSERT INTO patente
            (fec_pat, num_pat, rif_pat, nom_pat, fky_con, dir_pat, rub_pat, fky_usu, est_pat, est_registro)
            VALUES (CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $estado = isset($datos['est_pat']) ? $datos['est_pat'] : 'S';
        $stmt->bind_param(
            "sssissis",
            $datos['num_pat'],
            $datos['rif_pat'],
            $datos['nom_pat'],
            $datos['fky_con'],
            $datos['dir_pat'],
            $datos['rub_pat'],
            $datos['fky_usu'],
            $estado
        );
        $ok = $stmt->execute();
        if ($ok) {
            $this->id_pat = $stmt->insert_id;
        }
        $stmt->close();
        return $ok;
    }

    /**
     * Modifica una patente existente (sin tocar fec_pat).
     */
    public function modificar($datos)
    {
        $this->que_bd = "UPDATE patente
                         SET num_pat = ?, rif_pat = ?, nom_pat = ?, fky_con = ?, dir_pat = ?, rub_pat = ?, est_pat = ?
                         WHERE id_pat = ?";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $stmt->bind_param(
            "sssisssi",
            $datos['num_pat'],
            $datos['rif_pat'],
            $datos['nom_pat'],
            $datos['fky_con'],
            $datos['dir_pat'],
            $datos['rub_pat'],
            $datos['est_pat'],
            $datos['id_pat']
        );
        return $stmt->execute();
    }

    /**
     * Cambia el estado de tabla (A/I) de una patente par borrado logico.
     */
    public function cambiar_estado_tabla($id, $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE patente SET est_registro = ? WHERE id_pat = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    /**
     * Actualiza el estado del trámite de una patente (S/P/V).
     */
    public function actualizar_estado_patente($id, $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE patente SET est_pat = ? WHERE id_pat = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    /**
     * Consulta los datos de una patente por ID.
     */
    public function consultar_por_id($id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM patente WHERE id_pat = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    /**
     * Consulta todos los registros (activos e inactivos).
     */
    public function consultar_todas()
    {
        $this->que_bd = "SELECT * FROM patente ORDER BY fec_pat DESC, num_pat ASC";
        $resultado = $this->con_bd->query($this->que_bd);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Verifica si un número de patente ya existe.
     */
    public function numero_existe($numero)
    {
        $stmt = $this->con_bd->prepare("SELECT id_pat FROM patente WHERE num_pat = ?");
        $stmt->bind_param("s", $numero);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    /**
     * Elimina físicamente una patente (usar con precaución).
     */
    public function eliminar($id)
    {
        $stmt = $this->con_bd->prepare("DELETE FROM patente WHERE id_pat = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Busca patentes activas por número, rif, nombre, dirección o rubro.
     */
    public function buscar($termino)
    {
        $this->que_bd = "SELECT * FROM patente
                         WHERE est_registro = 'A' AND
                               (num_pat LIKE ? OR
                                rif_pat LIKE ? OR
                                nom_pat LIKE ? OR
                                dir_pat LIKE ? OR
                                rub_pat LIKE ?)
                         ORDER BY fec_pat DESC, num_pat ASC";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $like = '%' . $termino . '%';
        $stmt->bind_param("sssss", $like, $like, $like, $like, $like);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>

