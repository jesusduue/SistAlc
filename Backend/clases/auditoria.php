<?php
/**
 * Clase Auditoria
 * - Centraliza el registro de eventos/acciones de usuario en la tabla log_actividad.
 * - Requiere que exista la tabla log_actividad (DDL que te pasé antes).
 */
require_once 'Utilidad.php';

class Auditoria extends Utilidad
{
    // Propiedades según la tabla log_actividad
    public $id_log;
    public $fky_usu;
    public $nom_usu;
    public $modulo;
    public $accion;
    public $detalle;
    public $entidad_tipo;
    public $entidad_id;
    public $metadata;
    public $ip;
    public $user_agent;
    public $fecha;

    /**
     * Registra una actividad en log_actividad.
     *
     * @param int         $fky_usu       ID del usuario (FK a usuario.id_usu).
     * @param string      $nom_usu       Nombre visible del usuario (cacheado).
     * @param string      $modulo        Módulo donde ocurre la acción (Usuarios, Facturación, etc.).
     * @param string      $accion        Acción realizada (Creación, Actualización, Anulación, etc.).
     * @param string      $detalle       Descripción corta visible en dashboard.
     * @param string|null $entidad_tipo  Tipo de entidad afectada (ej: 'factura', 'contribuyente').
     * @param int|null    $entidad_id    ID de la entidad afectada.
     * @param array|null  $metadata      Datos extra (se serializa a JSON). Ej: ['antes'=>[], 'despues'=>[]]
     *
     * @return bool True si insertó, False en error.
     */
    public function registrarActividad(
        int $fky_usu,
        string $nom_usu,
        string $modulo,
        string $accion,
        string $detalle,
        ?string $entidad_tipo = null,
        ?int $entidad_id = null,
        ?array $metadata = null
    ) {
        $metadataJson = $metadata !== null
            ? json_encode($metadata, JSON_UNESCAPED_UNICODE)
            : null;

        $ip = $this->getClientIP();
        $ua = $this->getUserAgent();

        $sql = "INSERT INTO log_actividad
                    (fky_usu, nom_usu, modulo, accion, detalle, entidad_tipo, entidad_id, metadata, ip, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->con_bd->prepare($sql);
        if (!$stmt) {
            error_log('Auditoria::registrarActividad prepare() falló: ' . $this->con_bd->error);
            return false;
        }
        $stmt->bind_param(
            "isssssisss",
            $fky_usu,
            $nom_usu,
            $modulo,
            $accion,
            $detalle,
            $entidad_tipo,
            $entidad_id,
            $metadataJson,
            $ip,
            $ua
        );
        $ok = $stmt->execute();
        if (!$ok) {
            error_log('Auditoria::registrarActividad execute() falló: ' . $stmt->error);
        }
        $stmt->close();
        return $ok;
    }

    /**
     * Consulta todas las actividades registradas.
     */
    public function consultar_todas()
    {
        $this->que_bd = "SELECT * FROM log_actividad ORDER BY fecha DESC";
        $resultado = $this->con_bd->query($this->que_bd);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Consulta actividades por usuario.
     */
    public function consultar_por_usuario($id_usuario)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM log_actividad WHERE fky_usu = ? ORDER BY fecha DESC");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene la IP del cliente (considera X-Forwarded-For si hay proxy).
     */
    private function getClientIP()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return null;
    }

    /**
     * Obtiene el User-Agent del navegador/cliente.
     */
    private function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }


    //
}
