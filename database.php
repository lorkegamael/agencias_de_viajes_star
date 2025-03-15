<?php
require_once 'config.php';

class DatabaseConnection {
    private $conexion;
    private $dbname;

    public function __construct($dbname) {
        $this->dbname = $dbname;
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . CHARSET
            ];

            $this->conexion = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . $this->dbname . ";charset=" . CHARSET,
                DB_USER,
                DB_PASS,
                $options
            );
        } catch(PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
        }
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function cerrarConexion() {
        $this->conexion = null;
    }

    public function ejecutarConsulta($sql, $params = []) {
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Error en consulta: " . $e->getMessage());
            throw new Exception("Error al ejecutar la consulta");
        }
    }
}
?>
