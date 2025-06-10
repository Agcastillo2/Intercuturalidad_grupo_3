<?php
require_once 'ControllerLibro.php';
require_once 'Libro.php';

$controller = new ControllerLibro();

// Manejar acciones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        $controller->crearLibro($_POST['nombre'], $_POST['precio']);
    } elseif (isset($_POST['actualizar'])) {
        $controller->actualizarLibro($_POST['id'], $_POST['nombre'], $_POST['precio']);
    } elseif (isset($_POST['eliminar'])) {
        $controller->eliminarLibro($_POST['id']);
    }
}

// Obtener libros para mostrar
$libros = $controller->obtenerLibros();

// Si se va a editar, obtener el libro
$libroEditar = null;
if (isset($_GET['editar'])) {
    $libroEditar = $controller->obtenerLibroPorId($_GET['editar']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Libros</title>
</head>
<body>
    <h1>Libros</h1>

    <!-- Formulario para crear o editar -->
    <form method="post">
        <input type="hidden" name="id" value="<?php echo $libroEditar ? $libroEditar->getId() : ''; ?>">
        <input type="text" name="nombre" placeholder="Nombre" required value="<?php echo $libroEditar ? $libroEditar->getNombre() : ''; ?>">
        <input type="number" step="0.01" name="precio" placeholder="Precio" required value="<?php echo $libroEditar ? $libroEditar->getPrecio() : ''; ?>">
        <?php if ($libroEditar): ?>
            <button type="submit" name="actualizar">Actualizar</button>
        <?php else: ?>
            <button type="submit" name="crear">Crear</button>
        <?php endif; ?>
    </form>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($libros as $libro): ?>
        <tr>
            <td><?php echo $libro->getId(); ?></td>
            <td><?php echo $libro->getNombre(); ?></td>
            <td><?php echo $libro->getPrecio(); ?></td>
            <td>
                <a href="?editar=<?php echo $libro->getId(); ?>">Editar</a>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $libro->getId(); ?>">
                    <button type="submit" name="eliminar" onclick="return confirm('¿Eliminar este libro?');">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
