<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Producto.php';

$database = new Database();
$db = $database->getConnection();

// Verificar conexión a la base de datos
if($db === null) {
    http_response_code(500);
    echo json_encode(array("mensaje" => "Error de conexión a la base de datos."));
    exit();
}

$producto = new Producto($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            // Leer un producto
            $producto->id = $_GET['id'];
            $producto->leerUno();
            
            if($producto->nombre != null) {
                $producto_arr = array(
                    "id" => $producto->id,
                    "nombre" => $producto->nombre,
                    "precio" => $producto->precio
                );
                http_response_code(200);
                echo json_encode($producto_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("mensaje" => "Producto no encontrado."));
            }
        } else {
            // Leer todos los productos
            $stmt = $producto->leer();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $productos_arr = array();
                $productos_arr["registros"] = array();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $producto_item = array(
                        "id" => $id,
                        "nombre" => $nombre,
                        "precio" => $precio
                    );
                    array_push($productos_arr["registros"], $producto_item);
                }
                
                http_response_code(200);
                echo json_encode($productos_arr);
            } else {
                http_response_code(200);
                echo json_encode(array("registros" => array()));
            }
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        // Validación mejorada
        if($data === null) {
            http_response_code(400);
            echo json_encode(array("mensaje" => "JSON inválido."));
            break;
        }
        
        if(!empty($data->nombre) && isset($data->precio) && is_numeric($data->precio)) {
            $producto->nombre = $data->nombre;
            $producto->precio = $data->precio;
            
            if($producto->crear()) {
                http_response_code(201);
                echo json_encode(array("mensaje" => "Producto creado."));
            } else {
                http_response_code(503);
                echo json_encode(array("mensaje" => "No se pudo crear el producto."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("mensaje" => "Datos incompletos o inválidos."));
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        // Validación mejorada
        if($data === null) {
            http_response_code(400);
            echo json_encode(array("mensaje" => "JSON inválido."));
            break;
        }
        
        if(!empty($data->id) && !empty($data->nombre) && isset($data->precio) && is_numeric($data->precio)) {
            $producto->id = $data->id;
            $producto->nombre = $data->nombre;
            $producto->precio = $data->precio;
            
            if($producto->actualizar()) {
                http_response_code(200);
                echo json_encode(array("mensaje" => "Producto actualizado."));
            } else {
                http_response_code(503);
                echo json_encode(array("mensaje" => "No se pudo actualizar el producto."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("mensaje" => "Datos incompletos o inválidos."));
        }
        break;
        
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        
        // Validación mejorada
        if($data === null) {
            http_response_code(400);
            echo json_encode(array("mensaje" => "JSON inválido."));
            break;
        }
        
        if(!empty($data->id)) {
            $producto->id = $data->id;
            
            if($producto->eliminar()) {
                http_response_code(200);
                echo json_encode(array("mensaje" => "Producto eliminado."));
            } else {
                http_response_code(503);
                echo json_encode(array("mensaje" => "No se pudo eliminar el producto."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("mensaje" => "ID del producto requerido."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("mensaje" => "Método no permitido."));
        break;
}
?>