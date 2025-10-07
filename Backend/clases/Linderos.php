<?php
require_once 'Utilidad.php';

class Linderos extends Utilidad
{
    public $id_lin;
    public $lin_norte;
    public $lin_sur;
    public $lin_este;
    public $lin_oeste;
    public $mt2_norte;
    public $mt2_sur;
    public $mt2_este;
    public $mt2_oeste;
    public $mt2_total;
    public $est_registro;

    /**
     * Registra un juego de linderos y devuelve true si la insercion fue exitosa.
     */
    public function registrar(array $datos)
    {
        $sql = "INSERT INTO linderos
            (lin_norte, lin_sur, lin_este, lin_oeste, mt2_norte, mt2_sur, mt2_este, mt2_oeste, mt2_total, est_registro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param(
            'ssssddddd',
            $datos['lin_norte'],
            $datos['lin_sur'],
            $datos['lin_este'],
            $datos['lin_oeste'],
            $datos['mt2_norte'],
            $datos['mt2_sur'],
            $datos['mt2_este'],
            $datos['mt2_oeste'],
            $datos['mt2_total']
        );
        return $stmt->execute();
    }

    /**
     * Actualiza los linderos existentes.
     */
    public function modificar(array $datos)
    {
        $sql = "UPDATE linderos
                   SET lin_norte = ?, lin_sur = ?, lin_este = ?, lin_oeste = ?,
                       mt2_norte = ?, mt2_sur = ?, mt2_este = ?, mt2_oeste = ?, mt2_total = ?
                 WHERE id_lin = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param(
            'ssssdddddi',
            $datos['lin_norte'],
            $datos['lin_sur'],
            $datos['lin_este'],
            $datos['lin_oeste'],
            $datos['mt2_norte'],
            $datos['mt2_sur'],
            $datos['mt2_este'],
            $datos['mt2_oeste'],
            $datos['mt2_total'],
            $datos['id_lin']
        );
        return $stmt->execute();
    }

    /**
     * Recupera todos los linderos activos.
     */
    public function consultar_activos()
    {
        $sql = "SELECT * FROM linderos WHERE est_registro = 'A' ORDER BY id_lin DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista los linderos sin filtro de estado.
     */
    public function consultar_todos()
    {
        $sql = "SELECT * FROM linderos ORDER BY id_lin DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Recupera un registro de linderos por id.
     */
    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM linderos WHERE id_lin = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Cambia el estado logico del registro de linderos.
     */
    public function cambiar_estado(int $id, string $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE linderos SET est_registro = ? WHERE id_lin = ?");
        $stmt->bind_param('si', $nuevo_estado, $id);
        return $stmt->execute();
    }
}
?>

