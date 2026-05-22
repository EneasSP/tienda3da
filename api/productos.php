<?php
/**
 * API REST para Gestión de Productos
 * PrintCraft - Sistema de Cotización y Gestión de Impresión 3D
 * 
 * Endpoints disponibles:
 * - GET    /api/productos.php          - Listar todos los productos
 * - GET    /api/productos.php?id=X      - Obtener un producto específico
 * - POST   /api/productos.php           - Crear un nuevo producto
 * - PUT    /api/productos.php?id=X      - Actualizar un producto existente
 * - DELETE /api/productos.php?id=X      - Eliminar un producto
 * 
 * @author PrintCraft Development Team
 * @version 1.0.0
 */

// Encabezados CORS y Content-Type
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar solicitud OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once __DIR__ . '/../includes/db.php';

// Obtener método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Obtener ID si está presente en la URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Obtener término de búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $pdo = getDBConnection();
    
    switch ($method) {
        case 'GET':
            obtenerProductos($pdo, $id, $search);
            break;
            
        case 'POST':
            crearProducto($pdo);
            break;
            
        case 'PUT':
            actualizarProducto($pdo, $id);
            break;
            
        case 'DELETE':
            eliminarProducto($pdo, $id);
            break;
            
        default:
            jsonResponse(false, null, 'Método no permitido', 405);
    }
    
} catch (PDOException $e) {
    error_log("Error en API productos: " . $e->getMessage());
    jsonResponse(false, null, 'Error interno del servidor', 500);
}

/**
 * Obtiene la lista de productos o un producto específico
 * 
 * @param PDO    $pdo    Conexión PDO
 * @param int    $id     ID del producto (opcional)
 * @param string $search Término de búsqueda
 */
function obtenerProductos($pdo, $id, $search) {
    // Si hay ID específico, obtener solo ese producto
    if ($id !== null) {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $producto = $stmt->fetch();
        
        if (!$producto) {
            jsonResponse(false, null, 'Producto no encontrado', 404);
        }
        
        jsonResponse(true, $producto, 'Producto obtenido correctamente');
    }
    
    // Construir consulta con filtros
    $sql = "SELECT * FROM productos WHERE 1=1";
    $params = [];
    
    if ($search !== '') {
        $sql .= " AND (nombre LIKE :search OR descripcion LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    $sql .= " ORDER BY nombre ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll();
    
    jsonResponse(true, $productos, 'Productos obtenidos correctamente');
}

/**
 * Crea un nuevo producto
 * 
 * @param PDO $pdo Conexión PDO
 */
function crearProducto($pdo) {
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validaciones básicas
    if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
        jsonResponse(false, null, 'El nombre es requerido', 400);
    }
    
    if (!isset($input['peso_gramos']) || !is_numeric($input['peso_gramos']) || $input['peso_gramos'] <= 0) {
        jsonResponse(false, null, 'El peso en gramos debe ser un número positivo', 400);
    }
    
    if (!isset($input['tiempo_minutos']) || !is_numeric($input['tiempo_minutos']) || $input['tiempo_minutos'] <= 0) {
        jsonResponse(false, null, 'El tiempo en minutos debe ser un número positivo', 400);
    }
    
    // Escapar datos
    $nombre = htmlspecialchars(strip_tags(trim($input['nombre'])), ENT_QUOTES, 'UTF-8');
    $descripcion = isset($input['descripcion']) ? htmlspecialchars(strip_tags(trim($input['descripcion'])), ENT_QUOTES, 'UTF-8') : '';
    $peso_gramos = (float)$input['peso_gramos'];
    $tiempo_minutos = (int)$input['tiempo_minutos'];
    $ruta_imagen = isset($input['ruta_imagen']) ? htmlspecialchars(strip_tags(trim($input['ruta_imagen'])), ENT_QUOTES, 'UTF-8') : '';
    
    // Insertar producto
    $stmt = $pdo->prepare("
        INSERT INTO productos (nombre, descripcion, peso_gramos, tiempo_minutos, ruta_imagen)
        VALUES (:nombre, :descripcion, :peso_gramos, :tiempo_minutos, :ruta_imagen)
    ");
    $stmt->execute([
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'peso_gramos' => $peso_gramos,
        'tiempo_minutos' => $tiempo_minutos,
        'ruta_imagen' => $ruta_imagen
    ]);
    
    $productoId = $pdo->lastInsertId();
    
    jsonResponse(true, [
        'id' => $productoId,
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'peso_gramos' => $peso_gramos,
        'tiempo_minutos' => $tiempo_minutos,
        'ruta_imagen' => $ruta_imagen
    ], 'Producto creado correctamente', 201);
}

/**
 * Actualiza un producto existente
 * 
 * @param PDO $pdo Conexión PDO
 * @param int $id ID del producto
 */
function actualizarProducto($pdo, $id) {
    if ($id === null) {
        jsonResponse(false, null, 'ID de producto es requerido', 400);
    }
    
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Verificar que el producto existe
    $stmtCheck = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    
    if (!$stmtCheck->fetch()) {
        jsonResponse(false, null, 'Producto no encontrado', 404);
    }
    
    // Construir consulta de actualización dinámicamente
    $camposPermitidos = ['nombre', 'descripcion', 'peso_gramos', 'tiempo_minutos', 'ruta_imagen'];
    $updates = [];
    $params = ['id' => $id];
    
    foreach ($camposPermitidos as $campo) {
        if (isset($input[$campo])) {
            $updates[] = "$campo = :$campo";
            
            // Validaciones específicas
            if ($campo === 'peso_gramos') {
                if (!is_numeric($input[$campo]) || $input[$campo] <= 0) {
                    jsonResponse(false, null, 'El peso en gramos debe ser un número positivo', 400);
                }
                $params[$campo] = (float)$input[$campo];
            } elseif ($campo === 'tiempo_minutos') {
                if (!is_numeric($input[$campo]) || $input[$campo] <= 0) {
                    jsonResponse(false, null, 'El tiempo en minutos debe ser un número positivo', 400);
                }
                $params[$campo] = (int)$input[$campo];
            } else {
                $params[$campo] = htmlspecialchars(strip_tags(trim($input[$campo])), ENT_QUOTES, 'UTF-8');
            }
        }
    }
    
    if (empty($updates)) {
        jsonResponse(false, null, 'No se proporcionaron campos para actualizar', 400);
    }
    
    $sql = "UPDATE productos SET " . implode(', ', $updates) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Obtener producto actualizado
    $stmtGet = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
    $stmtGet->execute(['id' => $id]);
    $producto = $stmtGet->fetch();
    
    jsonResponse(true, $producto, 'Producto actualizado correctamente');
}

/**
 * Elimina un producto
 * 
 * @param PDO $pdo Conexión PDO
 * @param int $id ID del producto
 */
function eliminarProducto($pdo, $id) {
    if ($id === null) {
        jsonResponse(false, null, 'ID de producto es requerido', 400);
    }
    
    // Verificar que el producto existe
    $stmtCheck = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    
    if (!$stmtCheck->fetch()) {
        jsonResponse(false, null, 'Producto no encontrado', 404);
    }
    
    // Verificar si tiene detalles de pedido asociados
    $stmtDetalles = $pdo->prepare("SELECT COUNT(*) FROM detalles_pedido WHERE producto_id = :producto_id");
    $stmtDetalles->execute(['producto_id' => $id]);
    $cantidadDetalles = (int)$stmtDetalles->fetch()[0];
    
    if ($cantidadDetalles > 0) {
        jsonResponse(false, null, "No se puede eliminar el producto porque está en $cantidadDetalles detalle(s) de pedido", 400);
    }
    
    // Eliminar el producto
    $stmtDelete = $pdo->prepare("DELETE FROM productos WHERE id = :id");
    $stmtDelete->execute(['id' => $id]);
    
    jsonResponse(true, null, 'Producto eliminado correctamente');
}