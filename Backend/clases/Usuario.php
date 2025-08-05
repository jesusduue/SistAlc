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
        $this->que_bd = "SELECT u.id_usu, u.nom_usu, u.cla_usu, u.fky_rol, u.est_usu, r.nom_rol 
                         FROM usuario u
                         INNER JOIN rol r ON u.fky_rol = r.id_rol
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

    /**
     * Maneja las peticiones HTTP relacionadas con los usuarios.
     * Maneja la lógica del inicio de sesión.
     */
    public function iniciarSesion()
    {
        // Validar que los campos no estén vacíos
        if (!empty($_POST['nom_usu']) && !empty($_POST['cla_usu'])) {

            $nombreUsuario = $_POST['nom_usu'];
            $clave = $_POST['cla_usu'];

            // Intentar iniciar sesión
            // Llamar al método login de la clase Usuario
            $login_exitoso = $this->login($nombreUsuario, $clave);
            // Si el login es exitoso, redirigir al dashboard

            if ($login_exitoso) {
                // Redirigir al panel principal si el login es correcto
                header('Location: ../../Frontend/Vistas/dashboard.php');
                exit();
            } else {
                // Redirigir al index con un mensaje de error si falla
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Usuario o contraseña incorrectos, o el usuario está inactivo.'];
                header('Location: ../../index.php');
                exit();
            }
        } else {
            // Redirigir si los datos están incompletos
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Usuario o contraseña incorrectos, o el usuario está inactivo.'];
            header('Location: ../../index.php');
            exit();
        }
    }
    /* cerrar sesion */
    public function cerrarSesion()
    {
        session_start();
        // Elimina todas las variables de sesión.
        $_SESSION = array();

        // Si se desea eliminar completamente la sesión, también se debe borrar la cookie de sesión.
        // Nota: Esto destruirá la sesión, no solo los datos almacenados en ella.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Finalmente, destruye la sesión.
        session_destroy();

        // Redirige a la página de inicio de sesión con un mensaje
        session_start(); // Inicia una nueva sesión solo para pasar un mensaje
        $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Ha cerrado sesión exitosamente.'];
        header('Location: ../../index.php');
        exit;
    }

    public function consultar_todos_con_rol()
    {
        $this->que_bd = "SELECT u.id_usu, u.nom_usu, u.fky_rol, u.est_usu, r.nom_rol,
                     CASE u.est_usu WHEN 'A' THEN 'Activo' ELSE 'Inactivo' END AS estado_descripcion
                     FROM usuario u
                     INNER JOIN rol r ON u.fky_rol = r.id_rol
                     ORDER BY u.id_usu DESC";

        $resultado = $this->con_bd->query($this->que_bd);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function consultar_por_id($id)
    {
        $stmt = $this->con_bd->prepare("SELECT * FROM usuario WHERE id_usu = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function registrar($datos)
    {
        $this->que_bd = "INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_usu) VALUES (?, ?, ?, ?)";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $clave_hasheada = password_hash($datos['cla_usu'], PASSWORD_DEFAULT);
        $stmt->bind_param("ssis", $datos['nom_usu'], $clave_hasheada, $datos['fky_rol'], $datos['est_usu']);
        return $stmt->execute();
    }

    public function modificar($datos)
    {
        if (!empty($datos['cla_usu'])) {
            $this->que_bd = "UPDATE usuario SET nom_usu = ?, cla_usu = ?, fky_rol = ?, est_usu = ? WHERE id_usu = ?";
            $clave_hasheada = password_hash($datos['cla_usu'], PASSWORD_DEFAULT);
            $stmt = $this->con_bd->prepare($this->que_bd);
            $stmt->bind_param("ssisi", $datos['nom_usu'], $clave_hasheada, $datos['fky_rol'], $datos['est_usu'], $datos['id_usu']);
        } else {
            $this->que_bd = "UPDATE usuario SET nom_usu = ?, fky_rol = ?, est_usu = ? WHERE id_usu = ?";
            $stmt = $this->con_bd->prepare($this->que_bd);
            $stmt->bind_param("sisi", $datos['nom_usu'], $datos['fky_rol'], $datos['est_usu'], $datos['id_usu']);
        }
        return $stmt->execute();
    }

    public function cambiar_estado($id, $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE usuario SET est_usu = ? WHERE id_usu = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }
}//fin archivo php
