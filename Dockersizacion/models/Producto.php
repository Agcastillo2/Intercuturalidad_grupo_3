<?php
class Producto {
    private $conn;
    private $table_name = "producto";

    public $id;
    public $nombre;
    public $precio;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear producto
    function crear() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre=:nombre, precio=:precio";
        $stmt = $this->conn->prepare($query);
        
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":precio", $this->precio);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todos los productos
    function leer() {
        $query = "SELECT id, nombre, precio FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer un producto - CORREGIDO
    function leerUno() {
        $query = "SELECT nombre, precio FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Validación agregada para evitar errores
        if($row) {
            $this->nombre = $row['nombre'];
            $this->precio = $row['precio'];
        } else {
            $this->nombre = null;
            $this->precio = null;
        }
    }

    // Actualizar producto
    function actualizar() {
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, precio = :precio WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar producto
    function eliminar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>