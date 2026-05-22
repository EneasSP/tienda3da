<?php
/**
 * API REST para Gestión de Clientes
 * PrintCraft - Sistema de Cotización y Gestión de Impresión 3D
 * 
 * Endpoints disponibles:
 * - GET    /api/clientes.php          - Listar todos los clientes
 * - GET    /api/clientes.php?id=X      - Obtener un cliente específico
 * - POST   /api/clientes.php           - Crear un nuevo cliente
 * - PUT    /api/clientes.php?id=X      - Actualizar un cliente existente
 * - DELETE /api/clientes.php?id=X      - Eliminar un cliente
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
            obtenerClientes($pdo, $id, $search);
            break;
            
        case 'POST':
            crearCliente($pdo);
            break;
            
        case 'PUT':
            actualizarCliente($pdo, $id);
            break;
            
        case 'DELETE':
            eliminarCliente($pdo, $id);
            break;
            
        default:
            jsonResponse(false, null, 'Método no permitido', 405);
    }
    
} catch (PDOException $e) {
    error_log("Error en API clientes: " . $e->getMessage());
    jsonResponse(false, null, 'Error interno del servidor', 500);
}

/**
 * Obtiene la lista de clientes o un cliente específico
 * 
 * @param PDO    $pdo    Conexión PDO
 * @param int    $id     ID del cliente (opcional)
 * @param string $search Término de búsqueda
 */
function obtenerClientes($pdo, $id, $search) {
    // Si hay ID específico, obtener solo ese cliente
    if ($id !== null) {
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $cliente = $stmt->fetch();
        
        if (!$cliente) {
            jsonResponse(false, null, 'Cliente no encontrado', 404);
        }
        
        jsonResponse(true, $cliente, 'Cliente obtenido correctamente');
    }
    
    // Construir consulta con filtros
    $sql = "SELECT * FROM clientes WHERE 1=1";
    $params = [];
    
    if ($search !== '') {
        $sql .= " AND (nombre LIKE :search OR email LIKE :search OR empresa LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    $sql .= " ORDER BY nombre ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clientes = $stmt->fetchAll();
    
    jsonResponse(true, $clientes, 'Clientes obtenidos correctamente');
}

/**
 * Crea un nuevo cliente
 * 
 * @param PDO $pdo Conexión PDO
 */
function crearCliente($pdo) {
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validaciones básicas
    if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
        jsonResponse(false, null, 'El nombre es requerido', 400);
    }
    
    if (!isset($input['email']) || empty(trim($input['email']))) {
        jsonResponse(false, null, 'El email es requerido', 400);
    }
    
    // Validar formato de email
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, null, 'El formato del email no es válido', 400);
    }
    
    // Escapar datos
    $nombre = htmlspecialchars(strip_tags(trim($input['nombre'])), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(strip_tags(trim($input['email'])), ENT_QUOTES, 'UTF-8');
    $telefono = isset($input['telefono']) ? htmlspecialchars(strip_tags(trim($input['telefono'])), ENT_QUOTES, 'UTF-8') : '';
    $empresa = isset($input['empresa']) ? htmlspecialchars(strip_tags(trim($input['empresa'])), ENT_QUOTES, 'UTF-8') : '';
    
    // Verificar si el email ya existe
    $stmtCheck = $pdo->prepare("SELECT id FROM clientes WHERE email = :email");
    $stmtCheck->execute(['email' => $email]);
    
    if ($stmtCheck->fetch()) {
        jsonResponse(false, null, 'El email ya está registrado', 409);
    }
    
    // Insertar cliente
    $stmt = $pdo->prepare("
        INSERT INTO clientes (nombre, email, telefono, empresa)
        VALUES (:nombre, :email, :telefono, :empresa)
    ");
    $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'telefono' => $telefono,
        'empresa' => $empresa
    ]);
    
    $clienteId = $pdo->lastInsertId();
    
    jsonResponse(true, [
        'id' => $clienteId,
        'nombre' => $nombre,
        'email' => $email,
        'telefono' => $telefono,
        'empresa' => $empresa
    ], 'Cliente creado correctamente', 201);
}

/**
 * Actualiza un cliente existente
 * 
 * @param PDO $pdo Conexión PDO
 * @param int $id ID del cliente
 */
function actualizarCliente($pdo, $id) {
    if ($id === null) {
        jsonResponse(false, null, 'ID de cliente es requerido', 400);
    }
    
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Verificar que el cliente existe
    $stmtCheck = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    
    if (!$stmtCheck->fetch()) {
        jsonResponse(false, null, 'Cliente no encontrado', 404);
    }
    
    // Construir consulta de actualización dinámicamente
    $camposPermitidos = ['nombre', 'email', 'telefono', 'empresa'];
    $updates = [];
    $params = ['id' => $id];
    
    foreach ($camposPermitidos as $campo) {
        if (isset($input[$campo])) {
            $updates[] = "$campo = :$campo";
            $params[$campo] = htmlspecialchars(strip_tags(trim($input[$campo])), ENT_QUOTES, 'UTF-8');
        }
    }
    
    if (empty($updates)) {
        jsonResponse(false, null, 'No se proporcionaron campos para actualizar', 400);
    }
    
    // Validar email si se proporciona
    if (isset($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, null, 'El formato del email no es válido', 400);
    }
    
    // Verificar email duplicado
    if (isset($input['email'])) {
        $stmtEmail = $pdo->prepare("SELECT id FROM clientes WHERE email = :email AND id != :id");
        $stmtEmail->execute(['email' => $input['email'], 'id' => $id]);
        
        if ($stmtEmail->fetch()) {
            jsonResponse(false, null, 'El email ya está registrado', 409);
        }
    }
    
    $sql = "UPDATE clientes SET " . implode(', ', $updates) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Obtener cliente actualizado
    $stmtGet = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
    $stmtGet->execute(['id' => $id]);
    $cliente = $stmtGet->fetch();
    
    jsonResponse(true, $cliente, 'Cliente actualizado correctamente');
}

/**
 * Elimina un cliente
 * 
 * @param PDO $pdo Conexión PDO
 * @param int $id ID del cliente
 */
function eliminarCliente($pdo, $id) {
    if ($id === null) {
        jsonResponse(false, null, 'ID de cliente es requerido', 400);
    }
    
    // Verificar que el cliente existe
    $stmtCheck = $pdo->prepare("SELECT * FROM clientes WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    
    if (!$stmtCheck->fetch()) {
        jsonResponse(false, null, 'Cliente no encontrado', 404);
    }
    
    // Verificar si tiene pedidos asociados
    $stmtPedidos = $pdo->prepare("SELECT COUNT(*) FROM pedidos WHERE cliente_id = :cliente_id");
    $stmtPedidos->execute(['cliente_id' => $id]);
    $cantidadPedidos = (int)$stmtPedidos->fetch()[0];
    
    if ($cantidadPedidos > 0) {
        jsonResponse(false, null, "No se puede eliminar el cliente porque tiene $cantidadPedidos pedido(s) asociado(s)", 400);
    }
    
    // Eliminar el cliente
    $stmtDelete = $pdo->prepare("DELETE FROM clientes WHERE id = :id");
    $stmtDelete->execute(['id' => $id]);
    
    jsonResponse(true, null, 'Cliente eliminado correctamente');
}