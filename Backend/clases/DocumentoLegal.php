<?php
require_once 'Utilidad.php';

class DocumentoLegal extends Utilidad
{
    public $id_doc;
    public $tip_doc;
    public $num_doc;
    public $matr_doc;
    public $fol_doc;
    public $fec_doc;
    public $des_doc;
    public $est_registro;

    /**
     * Registra un documento legal.
     */
    public function registrar(array $datos)
    {
        $sql = "INSERT INTO documentos_legales
                (tip_doc, num_doc, matr_doc, fol_doc, fec_doc, des_doc, est_registro)
                VALUES (?, ?, ?, ?, ?, ?, 'A')";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param(
            'ssssss',
            $datos['tip_doc'],
            $datos['num_doc'],
            $datos['matr_doc'],
            $datos['fol_doc'],
            $datos['fec_doc'],
            $datos['des_doc']
        );
        return $stmt->execute();
    }

    /**
     * Actualiza un documento existente.
     */
    public function modificar(array $datos)
    {
        $sql = "UPDATE documentos_legales
                   SET tip_doc = ?, num_doc = ?, matr_doc = ?, fol_doc = ?, fec_doc = ?, des_doc = ?
                 WHERE id_doc = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param(
            'ssssssi',
            $datos['tip_doc'],
            $datos['num_doc'],
            $datos['matr_doc'],
            $datos['fol_doc'],
            $datos['fec_doc'],
            $datos['des_doc'],
            $datos['id_doc']
        );
        return $stmt->execute();
    }

    /**
     * Devuelve documentos segun estado activo.
     */
    public function consultar_activos()
    {
        $sql = "SELECT * FROM documentos_legales WHERE est_registro = 'A' ORDER BY id_doc DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lista todos los documentos.
     */
    public function consultar_todos()
    {
        $sql = "SELECT * FROM documentos_legales ORDER BY id_doc DESC";
        $res = $this->con_bd->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Recupera un documento por id.
     */
    public function consultar_por_id(int $id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM documentos_legales WHERE id_doc = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Cambia el estado logico del documento.
     */
    public function cambiar_estado(int $id, string $estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE documentos_legales SET est_registro = ? WHERE id_doc = ?");
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }
}
?>
