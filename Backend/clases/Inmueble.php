<?php
require_once 'Utilidad.php';

class Inmueble extends Utilidad
{
    public $id_inm;
    public $dir_inm;
    public $tip_inm;
    public $area_inm;
    public $est_registro;

    /**
     * Registra un inmueble.
     */
    public function registrar(array $datos)
    {
        $sql = "INSERT INTO inmueble (dir_inm, tip_inm, area_inm, est_registro) VALUES (?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param('sss', $datos['dir_inm'], $datos['tip_inm'], $datos['area_inm']);
        return $stmt->execute();
    }

    /**
     * Actualiza datos del inmueble.
     */
    public function modificar(array $datos)
    {
        $sql = "UPDATE inmueble SET dir_inm = ?, tip_inm = ?, area_inm = ? WHERE id_inm = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param('sssi', $datos['dir_inm'], $datos['tip_inm'], $datos['area_inm'], $datos['id_inm']);
        return $stmt->execute();
    }

    /**
     * Devuelve los inmuebles activos.
     */
    public function consultar_activos()
    {
        $sql = "SELECT * FROM inmueble WHERE est_registro = 'A' ORDER BY id_inm DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista todos los inmuebles registrados.
     */
    public function consultar_todos()
    {
        $sql = "SELECT * FROM inmueble ORDER BY id_inm DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Recupera un inmueble por su identificador.
     */
    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM inmueble WHERE id_inm = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Cambia el estado logico del inmueble.
     */
    public function cambiar_estado(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE inmueble SET est_registro = ? WHERE id_inm = ?");
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }
}
?>
