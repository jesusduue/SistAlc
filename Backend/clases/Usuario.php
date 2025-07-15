<?php
require_once 'Utilidad.php';

/**
 * Clase Usuario
 * Modela la tabla 'usuario' de la base de datos y gestiona sus operaciones.
 */
class Usuario extends Utilidad
{
    // Propiedades que coinciden con las columnas de la tabla 'usuario'
    public $id_usu;
    public $nom_usu;
    public $cla_usu;
    public $fky_rol;
    public $est_usu;

    /**
     * Valida las credenciales de un usuario contra la base de datos.
     * Utiliza sentencias preparadas para prevenir inyección SQL.
     * @param string $nombreUsuario El nombre de usuario.
     * @param string $clave La contraseña sin encriptar.
     * @return bool True si el login es exitoso, False en caso contrario.
     */
    public function login($nombreUsuario, $clave)
    {
        // Consulta SQL con marcadores de posición (?) para seguridad
        $this->que_bd = "SELECT u.*, r.nom_rol 
                         FROM usuario u
                         JOIN rol r ON u.fky_rol = r.id_rol
                         WHERE u.nom_usu = ? AND u.est_usu = 'A'";

        // Preparar la sentencia
        $stmt = $this->con_bd->prepare($this->que_bd);

        if (!$stmt) {
            error_log("Error al preparar la sentencia: " . $this->con_bd->error);
            return false;
        }

        // Vincular el parámetro (nombre de usuario)
        $stmt->bind_param("s", $nombreUsuario);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $resultado = $stmt->get_result();

        // Verificar si se encontró un usuario
        if ($resultado->num_rows == 1) {
            $datos_usuario = $resultado->fetch_assoc();

            // Verificar la contraseña usando password_verify()
            // Esto asume que la contraseña en la BD está hasheada con password_hash()
            if (password_verify($clave, $datos_usuario['cla_usu'])) {
                // La contraseña es correcta. Crear la sesión.
                $_SESSION['id_usu'] = $datos_usuario['id_usu'];
                $_SESSION['nom_usu'] = $datos_usuario['nom_usu'];
                $_SESSION['fky_rol'] = $datos_usuario['fky_rol'];
                $_SESSION['nom_rol'] = $datos_usuario['nom_rol']; // Guardamos también el nombre del rol
                
                $stmt->close();
                return true; // Login exitoso
            }
        }

        // Si el usuario no existe, la clave es incorrecta o está inactivo
        $stmt->close();
        return false;
    }
}
?>
