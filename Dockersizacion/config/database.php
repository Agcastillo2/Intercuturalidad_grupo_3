<?php
class Database {
    private $host = "mysql"; // Cambiar a "mysql" (nombre del servicio Docker)
    private $db_name = "tienda";
    private $username = "user";
    private $password = "pass";
    private $port = "3306"; // Cambiar a 3306 (puerto interno del contenedor)
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>