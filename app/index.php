<?php
// index.php

// db.php (Integrado)
$servername = "db";
$username = "dark"; // Tu usuario de MySQL dentro del contenedor
$password = "Dark230900."; // Tu contraseña de MySQL dentro del contenedor
$dbname = "tienda"; // El nombre de la base de datos que creaste

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// crud.php (Integrado)

// Función para crear un nuevo producto
function createProduct($nombre, $precio) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO producto (nombre, precio) VALUES (?, ?)");
    $stmt->bind_param("sd", $nombre, $precio); // 's' para string, 'd' para double
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

// Función para leer todos los productos
function readProducts() {
    global $conn;
    $sql = "SELECT id, nombre, precio FROM producto ORDER BY id DESC";
    $result = $conn->query($sql);
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Función para leer un solo producto por ID
function readProductById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, nombre, precio FROM producto WHERE id = ?");
    $stmt->bind_param("i", $id); // 'i' para integer
    $stmt->execute();
    $result = $stmt->get_result();
    $product = null;
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
    return $product;
}

// Función para actualizar un producto existente
function updateProduct($id, $nombre, $precio) {
    global $conn;
    $stmt = $conn->prepare("UPDATE producto SET nombre = ?, precio = ? WHERE id = ?");
    $stmt->bind_param("sdi", $nombre, $precio, $id);
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

// Función para eliminar un producto
function deleteProduct($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM producto WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

// Lógica de index.php (Integrada)

$message = ''; // Para mostrar mensajes al usuario

// Manejar la creación de productos
if (isset($_POST['create_product'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    if (createProduct($nombre, $precio)) {
        $message = "Producto '{$nombre}' creado exitosamente.";
    } else {
        $message = "Error al crear el producto.";
    }
}

// Manejar la actualización de productos
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    if (updateProduct($id, $nombre, $precio)) {
        $message = "Producto con ID '{$id}' actualizado exitosamente.";
    } else {
        $message = "Error al actualizar el producto.";
    }
}

// Manejar la eliminación de productos
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    if (deleteProduct($id)) {
        $message = "Producto con ID '{$id}' eliminado exitosamente.";
    } else {
        $message = "Error al eliminar el producto.";
    }
}

// Obtener todos los productos para mostrarlos
$products = readProducts();

// Si se va a editar un producto, cargar sus datos en el formulario
$edit_product = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $edit_product = readProductById($_GET['id']);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Productos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; }
        .message { background-color: #dff0d8; color: #3c763d; padding: 10px; margin-bottom: 20px; border: 1px solid #d6e9c6; border-radius: 4px; }
        form { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; }
        form label { display: block; margin-bottom: 5px; font-weight: bold; }
        form input[type="text"], form input[type="number"] { width: calc(100% - 22px); padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        form input[type="submit"] { background-color: #5cb85c; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        form input[type="submit"]:hover { background-color: #4cae4c; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions a { text-decoration: none; padding: 5px 10px; margin-right: 5px; border-radius: 3px; }
        .actions .edit { background-color: #f0ad4e; color: white; }
        .actions .delete { background-color: #d9534f; color: white; }
        .actions a:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestión de Productos</h2>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <h3><?php echo $edit_product ? 'Editar Producto' : 'Crear Nuevo Producto'; ?></h3>
        <form method="POST" action="index.php">
            <?php if ($edit_product): ?>
                <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
            <?php endif; ?>
            <label for="nombre">Nombre del Producto:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $edit_product ? $edit_product['nombre'] : ''; ?>" required>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" value="<?php echo $edit_product ? $edit_product['precio'] : ''; ?>" required>

            <input type="submit" name="<?php echo $edit_product ? 'update_product' : 'create_product'; ?>" value="<?php echo $edit_product ? 'Actualizar Producto' : 'Crear Producto'; ?>">
            <?php if ($edit_product): ?>
                <a href="index.php" style="margin-left: 10px; text-decoration: none; color: #333;">Cancelar</a>
            <?php endif; ?>
        </form>

        <hr>

        <h3>Listado de Productos</h3>
        <?php if (empty($products)): ?>
            <p>No hay productos registrados.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo $product['nombre']; ?></td>
                            <td>$<?php echo number_format($product['precio'], 2); ?></td>
                            <td class="actions">
                                <a href="index.php?action=edit&id=<?php echo $product['id']; ?>" class="edit">Editar</a>
                                <a href="index.php?action=delete&id=<?php echo $product['id']; ?>" class="delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>