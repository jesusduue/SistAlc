<?php
// Iniciar la sesión si no está iniciada. Es crucial para manejar variables de sesión en todo el sistema.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Clase Utilidad
 * Gestiona la conexión a la base de datos y provee métodos auxiliares.
 * Es la clase padre de las demás clases del modelo.
 */
class Utilidad
{
    private $nombre_servidor;   // Nombre del servidor (e.g., "localhost")
    private $usuario_servidor;  // Nombre de usuario de la BD (e.g., "root")
    private $clave_servidor;    // Clave del usuario de la BD
    private $nombre_bd;         // Nombre de la base de datos
    protected $con_bd;          // Objeto de conexión mysqli (protegido para acceso en clases hijas)
    public $que_bd;             // Almacena la consulta SQL a ejecutar
    public $resultado;          // Almacena el resultado de una consulta
    public $puntero;            // Puntero para extraer datos de un resultado

    /**
     * Constructor de la clase.
     * Inicializa las propiedades de conexión y establece la conexión con la base de datos.
     */
    public function __construct()
    {
        $this->nombre_servidor = "localhost";
        $this->usuario_servidor = "root";
        $this->clave_servidor = "";
        $this->nombre_bd = "alcaldia"; // **MODIFICACIÓN: Se asigna el nombre de la BD directamente.**

        // Intentar conectar a la base de datos
        try {
            $this->con_bd = new mysqli($this->nombre_servidor, $this->usuario_servidor, $this->clave_servidor, $this->nombre_bd);
            $this->con_bd->set_charset("utf8"); // Buena práctica para manejar acentos y caracteres especiales

            // Verificar si hay errores en la conexión
            if ($this->con_bd->connect_error) {
                throw new Exception("Error de conexión: " . $this->con_bd->connect_error);
            }
        } catch (Exception $e) {
            // Si la conexión falla, se detiene la ejecución y se muestra un error crítico.
            error_log("Error Crítico de Base de Datos: " . $e->getMessage());
            die("Error Crítico de Base de Datos. Por favor, contacte al administrador.");
        }
    }

    /**
     * Ejecuta una consulta SQL simple (no preparada).
     * @return bool|mysqli_result El resultado de la consulta.
     */
    public function ejecutar()
    {
        $this->resultado = $this->con_bd->query($this->que_bd);
        if ($this->resultado) {
            $this->puntero = $this->resultado;
        } else {
            // Registrar el error para depuración
            error_log("Error en la consulta SQL: " . $this->con_bd->error . " | SQL: " . $this->que_bd);
        }
        return $this->resultado;
    }

    /**
     * Extrae una fila de datos del resultado de una consulta.
     * @return array|null|false Un array asociativo con los datos de la fila, o null si no hay más filas.
     */
    public function extraer_dato()
    {
        // Solo intenta extraer si el puntero es un objeto de resultado válido
        if ($this->puntero instanceof mysqli_result) {
            return $this->puntero->fetch_assoc();
        }
        return null;
    }

    /**
     * Asigna valores de $_REQUEST a las propiedades de la clase hija.
     * ¡Usar con precaución! Asegurarse de que los nombres de los inputs coincidan con las propiedades.
     */
    public function asignar_valor()
    {
        foreach ($_REQUEST as $atributo => $valor) {
            // Asigna solo si la propiedad existe en la clase que hereda
            if (property_exists($this, $atributo)) {
                // Se recomienda sanitizar el valor antes de asignarlo
                $this->$atributo = htmlspecialchars(strip_tags($valor));
            }
        }
    }

    /**
     * Muestra un mensaje de alerta (JavaScript) y redirige.
     * @param bool $tipo True para éxito, False para error.
     * @param string $mensaje_exito Mensaje a mostrar en caso de éxito.
     * @param string $mensaje_error Mensaje a mostrar en caso de error.
     * @param string $url_redireccion URL a la que se redirigirá en caso de éxito.
     */
    public function mensaje_registro($tipo, $mensaje_exito, $mensaje_error, $url_redireccion)
    {
        if ($tipo) {
            echo "<script>
                    alert('" . addslashes($mensaje_exito) . "');
                    window.location.href='" . $url_redireccion . "';
                  </script>";
        } else {
            $error_db = $this->con_bd->error;
            error_log("Error de registro: " . $error_db); // Log del error para el desarrollador
            echo "<script>
                    alert('" . addslashes($mensaje_error) . "');
                    window.history.back();
                  </script>";
        }
        exit; // Detener la ejecución del script después de la redirección
    }

    /**
     * Cierra la conexión a la base de datos al destruir el objeto.
     */
    public function __destruct()
    {
        if ($this->con_bd) {
            $this->con_bd->close();
        }
    }
}
?>
