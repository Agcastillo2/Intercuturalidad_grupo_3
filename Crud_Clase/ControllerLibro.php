<?php
// ControllerLibro.php

require_once 'Libro.php';

class ControllerLibro
{
    private $conn;

    public function __construct($host = 'localhost', $user = 'root', $pass = '', $db = 'tienda')
    {
        $this->conn = new mysqli($host, $user, $pass, $db);
        if ($this->conn->connect_error) {
            die('Error de conexión: ' . $this->conn->connect_error);
        }
    }

    // Crear un nuevo libro
    public function crearLibro($nombre, $precio)
    {
        $stmt = $this->conn->prepare("INSERT INTO producto (nombre, precio) VALUES (?, ?)");
        $stmt->bind_param("sd", $nombre, $precio);
        $stmt->execute();
        $stmt->close();
    }

    // Obtener todos los libros
    public function obtenerLibros()
    {
        $result = $this->conn->query("SELECT * FROM producto");
        $libros = [];
        while ($row = $result->fetch_assoc()) {
            $libros[] = new Libro($row['id'], $row['nombre'], $row['precio']);
        }
        return $libros;
    }

    // Obtener un libro por ID
    public function obtenerLibroPorId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM producto WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $libro = null;
        if ($row = $result->fetch_assoc()) {
            $libro = new Libro($row['id'], $row['nombre'], $row['precio']);
        }
        $stmt->close();
        return $libro;
    }

    // Actualizar un libro
    public function actualizarLibro($id, $nombre, $precio)
    {
        $stmt = $this->conn->prepare("UPDATE producto SET nombre = ?, precio = ? WHERE id = ?");
        $stmt->bind_param("sdi", $nombre, $precio, $id);
        $stmt->execute();
        $stmt->close();
    }

    // Eliminar un libro
    public function eliminarLibro($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM producto WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}
?>
