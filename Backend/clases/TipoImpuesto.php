<?php
require_once 'Utilidad.php';

class TipoImpuesto extends Utilidad
{
    public $id_tip;
    public $nom_tip;
    public $des_tip;
    public $est_registro;

    /**
     * Devuelve los tipos de impuesto activos ordenados alfabeticamente.
     */
    public function consultar_activos()
    {
        $sql = "SELECT *
                  FROM tipo_impuesto
                 WHERE est_registro = 'A'
                 ORDER BY id_tip ASC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista todos los tipos de impuesto sin filtrar por estado.
     */
    public function consultar_todos()
    {
        $sql = "SELECT * FROM tipo_impuesto ORDER BY id_tip ASC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Inserta un tipo de impuesto y devuelve true si se almaceno correctamente.
     */
    public function registrar(array $datos)
    {
        $sql = "INSERT INTO tipo_impuesto (nom_tip, des_tip, est_registro) VALUES (?, ?, 'A')";
        $stmt = $this->con_bd->prepare($sql);
        $descripcion = $datos['des_tip'] ?? null;
        $stmt->bind_param('ss', $datos['nom_tip'], $descripcion);
        return $stmt->execute();
    }

    /**
     * Actualiza nombre y descripcion del tipo de impuesto.
     */
    public function modificar(array $datos)
    {
        $sql = "UPDATE tipo_impuesto SET nom_tip = ?, des_tip = ? WHERE id_tip = ?";
        $stmt = $this->con_bd->prepare($sql);
        $descripcion = $datos['des_tip'] ?? null;
        $stmt->bind_param('ssi', $datos['nom_tip'], $descripcion, $datos['id_tip']);
        return $stmt->execute();
    }

    /**
     * Cambia el estatus logico del tipo de impuesto.
     */
    public function cambiar_estado(int $id, string $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE tipo_impuesto SET est_registro = ? WHERE id_tip = ?");
        $stmt->bind_param('si', $nuevo_estado, $id);
        return $stmt->execute();
    }

    /**
     * Recupera un tipo de impuesto por su identificador.
     */
    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM tipo_impuesto WHERE id_tip = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Verifica si existe otro registro con el mismo nombre.
     */
    public function nombre_existe(string $nombre, ?int $excluir_id = null)
    {
        $sql = "SELECT id_tip FROM tipo_impuesto WHERE nom_tip = ?";
        if ($excluir_id !== null) {
            $sql .= " AND id_tip <> ?";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param('si', $nombre, $excluir_id);
        } else {
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param('s', $nombre);
        }
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}
?>
