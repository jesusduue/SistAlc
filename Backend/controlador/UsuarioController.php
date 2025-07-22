<?php
// Incluir la clase Usuario
require_once '../clases/Usuario.php';

// Verificar si se ha enviado una acción desde un formulario
if (isset($_POST['accion'])) {
    
    // Crear una instancia del controlador
    $controlador = new UsuarioController();

    // Determinar la acción a realizar
    switch ($_POST['accion']) {
        case 'login':
            $controlador->handleLogin();
            break;
        case 'logout':
            $controlador->handleLogout();
            break;
        // Aquí se pueden agregar más casos (registrar, actualizar, etc.)
        default:
            // Redirigir si la acción no es válida
            header('Location: ../../index.php');
            break;
    }
} else {
    // Si no se especifica una acción, redirigir al inicio
    header('Location: ../../index.php');
    exit();
}


/**
 * Clase UsuarioController
 * Maneja las peticiones HTTP relacionadas con los usuarios.
 */
class UsuarioController
{
    private $usuario;

    public function __construct()
    {
        // Se instancia la clase Usuario (que ya inicia la sesión y la conexión a BD)
        $this->usuario = new Usuario();
    }

    /**
     * Maneja la lógica del inicio de sesión.
     */
    public function handleLogin()
    {
        // Validar que los campos no estén vacíos
        if (!empty($_POST['nom_usu']) && !empty($_POST['cla_usu'])) {
            
            $nombreUsuario = $_POST['nom_usu'];
            $clave = $_POST['cla_usu'];

            // Intentar iniciar sesión
            $login_exitoso = $this->usuario->login($nombreUsuario, $clave);

            if ($login_exitoso) {
                // Redirigir al panel principal si el login es correcto
                header('Location: ../../Frontend/Vistas/dashboard.php');
                exit();
            } else {
                // Redirigir al index con un mensaje de error si falla
                header('Location: ../../index.php?error=1');
                exit();
            }

        } else {
            // Redirigir si los datos están incompletos
            header('Location: ../../index.php?error=1');
            exit();
        }
    }
    /* cerrar sesion */
    public function handleLogout()
    {
        session_destroy();
        // redirigir a la pagina de inicio con un mensaje de que se cerró la sesión exitosamente
        session_start();
        $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Ha cerrado sesión exitosamente.'];
        header('Location: ../../index.php');
        exit();
    }
}
?>
