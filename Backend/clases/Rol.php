<?php
require_once 'Utilidad.php';

/**
 * Clase Rol
 * Gestiona las operaciones relacionadas con la tabla 'rol' en la base de datos.
 */
class Rol extends Utilidad
{
    public $id_rol;
    public $nom_rol;
    public $est_rol;

    /**
     * Consulta todos los roles activos en la base de datos.
     * @return array Lista de roles como array asociativo.
     */
    public function consultar_activos()
    {
        $this->que_bd = "SELECT * FROM rol WHERE est_rol = 'A' ORDER BY nom_rol ASC";
        $resultado = $this->con_bd->query($this->que_bd);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Consulta todos los roles (activos e inactivos).
     * @return array Lista completa de roles.
     */
    public function consultar_todos()
    {
        $this->que_bd = "SELECT * FROM rol ORDER BY nom_rol ASC";
        $resultado = $this->con_bd->query($this->que_bd);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Consulta un rol específico por ID.
     * @param int $id
     * @return array|null
     */
    public function consultar_por_id($id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM rol WHERE id_rol = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    /**
     * Registra un nuevo rol.
     */
    public function registrar($nombre)
    {
        $this->que_bd = "INSERT INTO rol (nom_rol, est_rol) VALUES (?, 'A')";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $stmt->bind_param("s", $nombre);
        return $stmt->execute();
    }

    /**
     * Cambia el estado del rol.
     */
    public function cambiar_estado($id, $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE rol SET est_rol = ? WHERE id_rol = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }
}
?>
