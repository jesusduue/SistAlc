<?php
require_once 'Utilidad.php';

class Rol extends Utilidad
{
    public $id_rol;
    public $nom_rol;
    public $est_registro;

    public function consultar_activos()
    {
        $sql = "SELECT id_rol, nom_rol, est_registro
                  FROM rol
                 WHERE est_registro = 'A'
                 ORDER BY nom_rol ASC";
        $resultado = $this->con_bd->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function consultar_todos()
    {
        $sql = "SELECT r.id_rol,
                       r.nom_rol,
                       r.est_registro,
                       COUNT(u.id_usu) AS total_usuarios,
                       SUM(CASE WHEN u.est_registro = 'A' THEN 1 ELSE 0 END) AS usuarios_activos
                  FROM rol r
             LEFT JOIN usuario u ON u.fky_rol = r.id_rol
              GROUP BY r.id_rol, r.nom_rol, r.est_registro
              ORDER BY r.nom_rol ASC";
        $resultado = $this->con_bd->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM rol WHERE id_rol = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
    }

    public function registrar(array $datos)
    {
        $sql = "INSERT INTO rol (nom_rol, est_registro) VALUES (?, ?)";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param('ss', $datos['nom_rol'], $datos['est_registro']);
        return $stmt->execute();
    }

    public function modificar(array $datos)
    {
        $sql = "UPDATE rol
                   SET nom_rol = ?, est_registro = ?
                 WHERE id_rol = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param('ssi', $datos['nom_rol'], $datos['est_registro'], $datos['id_rol']);
        return $stmt->execute();
    }

    public function cambiar_estado(int $id, string $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE rol SET est_registro = ? WHERE id_rol = ?");
        $stmt->bind_param('si', $nuevo_estado, $id);
        return $stmt->execute();
    }

    public function nombre_existe(string $nombre, ?int $excluir_id = null)
    {
        $sql = "SELECT id_rol FROM rol WHERE nom_rol = ?";

        if ($excluir_id !== null) {
            $sql .= " AND id_rol <> ?";
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

    public function contar_usuarios_asociados(int $id_rol, ?string $estado = null)
    {
        $sql = "SELECT COUNT(*) AS total
                  FROM usuario
                 WHERE fky_rol = ?";

        if ($estado !== null) {
            $sql .= " AND est_registro = ?";
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param('is', $id_rol, $estado);
        } else {
            $stmt = $this->con_bd->prepare($sql);
            $stmt->bind_param('i', $id_rol);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado ? $resultado->fetch_assoc() : ['total' => 0];

        return (int)($fila['total'] ?? 0);
    }
}
?>
