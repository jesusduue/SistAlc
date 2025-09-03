<?php
require_once 'Utilidad.php';

class Contribuyente extends Utilidad
{
    // Propiedades según la tabla contribuyente
    public $id_con;
    public $nom_con;
    public $rif_con;
    public $tel_con;
    public $ema_con;
    public $dir_con;
    public $fky_usu;
    public $est_con;

    /**
     * Consulta todos los contribuyentes activos.
     */
    public function consultar_activos()
    {
        $sql = "SELECT c.*,
                       CASE c.est_con WHEN 'A' THEN 'Activo' ELSE 'Inactivo' END AS estado_descripcion
                  FROM contribuyente c
                 WHERE c.est_con = 'A'
                 ORDER BY c.id_con ASC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Registra un nuevo contribuyente.
     */
    public function registrar($datos)
    {
        $this->que_bd = "INSERT INTO contribuyente 
            (nom_con, rif_con, tel_con, ema_con, dir_con, fky_usu, est_con) 
            VALUES (?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $stmt->bind_param(
            "sssssi",
            $datos['nom_con'],
            $datos['rif_con'],
            $datos['tel_con'],
            $datos['ema_con'],
            $datos['dir_con'],
            $datos['fky_usu']
        );
       $ok = $stmt->execute();
       if ($ok) {
           $this->id_con = $stmt->insert_id; // ID del nuevo contribuyente para auditoria
       }
       $stmt->close();
       return $ok;
    }

    /**
     * Modifica un contribuyente existente.
     */
    public function modificar($datos)
    {
        $this->que_bd = "UPDATE contribuyente 
                         SET nom_con = ?, rif_con = ?, tel_con = ?, ema_con = ?, dir_con = ? 
                         WHERE id_con = ?";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $stmt->bind_param(
            "sssssi",
            $datos['nom_con'],
            $datos['rif_con'],
            $datos['tel_con'],
            $datos['ema_con'],
            $datos['dir_con'],
            $datos['id_con']
        );
        return $stmt->execute();
    }

    /**
     * Cambia el estado (A/I) de un contribuyente.
     */
    public function cambiar_estado($id, $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE contribuyente SET est_con = ? WHERE id_con = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    /**
     * Consulta los datos de un contribuyente por ID.
     */
    public function consultar_por_id($id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM contribuyente WHERE id_con = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    /**
     * Consulta todos los contribuyentes (activos e inactivos).
     */
    public function consultar_todos()
    {
        $this->que_bd = "SELECT * FROM contribuyente WHERE est_con = 'A' ORDER BY nom_con ASC";
        $resultado = $this->con_bd->query($this->que_bd);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Verifica si un RIF ya existe (para validación).
     */
    public function rif_existe($rif)
    {
        $stmt = $this->con_bd->prepare("SELECT id_con FROM contribuyente WHERE rif_con = ?");
        $stmt->bind_param("s", $rif);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    /* elimina de forma logica un contribuyente */
    public function eliminar($id)
    {
        /* return $this->cambiar_estado($id, 'I'); */
        $stmt = $this->con_bd->prepare("DELETE FROM contribuyente WHERE id_con = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();

    }

    /* buscar por nombre,rif,email o telefono (para buscador en tiempo real)
     */
    public function buscar($termino)
    {
        $this->que_bd = "SELECT * FROM contribuyente 
                         WHERE (nom_con LIKE ? OR 
                                rif_con LIKE ? OR 
                                ema_con LIKE ? OR 
                                tel_con LIKE ? OR
                                dir_con LIKE ?) 
                         AND est_con = 'A' 
                         ORDER BY nom_con ASC";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $like_termino = '%' . $termino . '%';
        $stmt->bind_param("sssss", $like_termino, $like_termino, $like_termino, $like_termino, $like_termino);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
