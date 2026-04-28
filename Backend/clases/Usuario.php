<?php
require_once 'Utilidad.php';
require_once 'auditoria.php';

class Usuario extends Utilidad
{
    public $id_usu;
    public $nom_usu;
    public $cla_usu;
    public $fky_rol;
    public $est_registro;
    public $ultimo_error = '';

    public function login($nombreUsuario, $clave)
    {
        $this->que_bd = "SELECT u.id_usu, u.nom_usu, u.cla_usu, u.fky_rol, u.est_registro, r.nom_rol
                         FROM usuario u
                         INNER JOIN rol r ON u.fky_rol = r.id_rol
                         WHERE u.nom_usu = ? AND u.est_registro = 'A'";

        $stmt = $this->con_bd->prepare($this->que_bd);

        if (!$stmt) {
            error_log("Error al preparar la sentencia: " . $this->con_bd->error);
            return false;
        }

        $stmt->bind_param("s", $nombreUsuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $datos_usuario = $resultado->fetch_assoc();

            if (password_verify($clave, $datos_usuario['cla_usu'])) {
                $_SESSION['id_usu'] = $datos_usuario['id_usu'];
                $_SESSION['nom_usu'] = $datos_usuario['nom_usu'];
                $_SESSION['fky_rol'] = $datos_usuario['fky_rol'];
                $_SESSION['nom_rol'] = $datos_usuario['nom_rol'];

                $stmt->close();
                return true;
            }
        }

        $stmt->close();
        return false;
    }

    public function iniciarSesion()
    {
        if (!empty($_POST['nom_usu']) && !empty($_POST['cla_usu'])) {
            $nombreUsuario = $_POST['nom_usu'];
            $clave = $_POST['cla_usu'];
            $login_exitoso = $this->login($nombreUsuario, $clave);

            if ($login_exitoso) {
                $auditoria = new Auditoria();
                $auditoria->registrarActividad(
                    $_SESSION['id_usu'] ?? 0,
                    $_SESSION['nom_usu'] ?? 'desconocido',
                    'Sistema',
                    'Login',
                    'Inicio de sesion exitoso'
                );
                header('Location: ../../Frontend/Vistas/dashboard.php');
                exit();
            }

            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Usuario o contrasena incorrectos, o el usuario esta inactivo.'];
            header('Location: ../../index.php');
            exit();
        }

        $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Usuario o contrasena incorrectos, o el usuario esta inactivo.'];
        header('Location: ../../index.php');
        exit();
    }

    public function cerrarSesion()
    {
        session_start();
        $_SESSION = array();

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

        session_destroy();

        session_start();
        $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Ha cerrado sesion exitosamente.'];
        header('Location: ../../index.php');
        exit;
    }

    public function consultar_todos_con_rol()
    {
        $this->que_bd = "SELECT u.id_usu, u.nom_usu, u.fky_rol, u.est_registro, r.nom_rol,
                     CASE u.est_registro WHEN 'A' THEN 'Activo' ELSE 'Inactivo' END AS estado_descripcion
                     FROM usuario u
                     INNER JOIN rol r ON u.fky_rol = r.id_rol
                     ORDER BY u.id_usu DESC";

        $resultado = $this->con_bd->query($this->que_bd);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function consultar_perfil(int $id)
    {
        $sql = "SELECT u.id_usu,
                       u.nom_usu,
                       u.fky_rol,
                       u.est_registro,
                       r.nom_rol,
                       CASE u.est_registro WHEN 'A' THEN 'Activo' ELSE 'Inactivo' END AS estado_descripcion
                  FROM usuario u
                 INNER JOIN rol r ON u.fky_rol = r.id_rol
                 WHERE u.id_usu = ?";
        $stmt = $this->con_bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_assoc() : null;
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
        $this->que_bd = "INSERT INTO usuario (nom_usu, cla_usu, fky_rol, est_registro) VALUES (?, ?, ?, ?)";
        $stmt = $this->con_bd->prepare($this->que_bd);
        $clave_hasheada = password_hash($datos['cla_usu'], PASSWORD_DEFAULT);
        $stmt->bind_param("ssis", $datos['nom_usu'], $clave_hasheada, $datos['fky_rol'], $datos['est_registro']);
        return $stmt->execute();
    }

    public function modificar($datos)
    {
        if (!empty($datos['cla_usu'])) {
            $this->que_bd = "UPDATE usuario SET nom_usu = ?, cla_usu = ?, fky_rol = ?, est_registro = ? WHERE id_usu = ?";
            $clave_hasheada = password_hash($datos['cla_usu'], PASSWORD_DEFAULT);
            $stmt = $this->con_bd->prepare($this->que_bd);
            $stmt->bind_param("ssisi", $datos['nom_usu'], $clave_hasheada, $datos['fky_rol'], $datos['est_registro'], $datos['id_usu']);
        } else {
            $this->que_bd = "UPDATE usuario SET nom_usu = ?, fky_rol = ?, est_registro = ? WHERE id_usu = ?";
            $stmt = $this->con_bd->prepare($this->que_bd);
            $stmt->bind_param("sisi", $datos['nom_usu'], $datos['fky_rol'], $datos['est_registro'], $datos['id_usu']);
        }
        return $stmt->execute();
    }

    public function cambiar_clave($id, $claveActual, $claveNueva)
    {
        $this->ultimo_error = '';

        $stmt = $this->con_bd->prepare("SELECT cla_usu, est_registro FROM usuario WHERE id_usu = ?");
        if (!$stmt) {
            $this->ultimo_error = 'error_consulta';
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();

        if (!$usuario) {
            $this->ultimo_error = 'usuario_no_encontrado';
            return false;
        }

        if (($usuario['est_registro'] ?? 'I') !== 'A') {
            $this->ultimo_error = 'usuario_inactivo';
            return false;
        }

        if (!password_verify($claveActual, $usuario['cla_usu'])) {
            $this->ultimo_error = 'clave_actual_incorrecta';
            return false;
        }

        if (strlen($claveNueva) < 8) {
            $this->ultimo_error = 'clave_nueva_corta';
            return false;
        }

        if (password_verify($claveNueva, $usuario['cla_usu'])) {
            $this->ultimo_error = 'clave_igual_actual';
            return false;
        }

        $nuevaClaveHash = password_hash($claveNueva, PASSWORD_DEFAULT);
        $stmt = $this->con_bd->prepare("UPDATE usuario SET cla_usu = ? WHERE id_usu = ?");
        if (!$stmt) {
            $this->ultimo_error = 'error_actualizacion';
            return false;
        }

        $stmt->bind_param("si", $nuevaClaveHash, $id);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            $this->ultimo_error = 'error_actualizacion';
        }

        return $ok;
    }

    public function cambiar_estado($id, $nuevo_estado)
    {
        $stmt = $this->con_bd->prepare("UPDATE usuario SET est_registro = ? WHERE id_usu = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $stmt = $this->con_bd->prepare("DELETE FROM usuario WHERE id_usu = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
