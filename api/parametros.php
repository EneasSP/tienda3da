<?php
/**
 * API REST para Gestión de Parámetros del Sistema
 * PrintCraft - Sistema de Cotización y Gestión de Impresión 3D
 * 
 * Endpoints disponibles:
 * - GET    /api/parametros.php           - Listar todos los parámetros
 * - GET    /api/parametros.php?clave=X   - Obtener un parámetro específico
 * - POST   /api/parametros.php           - Crear o actualizar un parámetro
 * - PUT    /api/parametros.php?clave=X    - Actualizar un parámetro existente
 * - DELETE /api/parametros.php?clave=X   - Eliminar un parámetro
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

// Obtener clave si está presente en la URL
$clave = isset($_GET['clave']) ? trim($_GET['clave']) : null;

try {
    $pdo = getDBConnection();
    
    switch ($method) {
        case 'GET':
            obtenerParametros($pdo, $clave);
            break;
            
        case 'POST':
            crearActualizarParametro($pdo);
            break;
            
        case 'PUT':
            actualizarParametro($pdo, $clave);
            break;
            
        case 'DELETE':
            eliminarParametro($pdo, $clave);
            break;
            
        default:
            jsonResponse(false, null, 'Método no permitido', 405);
    }
    
} catch (PDOException $e) {
    error_log("Error en API parámetros: " . $e->getMessage());
    jsonResponse(false, null, 'Error interno del servidor', 500);
}

/**
 * Obtiene la lista de parámetros o un parámetro específico
 * 
 * @param PDO    $pdo   Conexión PDO
 * @param string $clave Clave del parámetro (opcional)
 */
function obtenerParametros($pdo, $clave) {
    if ($clave !== null) {
        $stmt = $pdo->prepare("SELECT * FROM parametros WHERE clave = :clave");
        $stmt->execute(['clave' => $clave]);
        $parametro = $stmt->fetch();
        
        if (!$parametro) {
            jsonResponse(false, null, 'Parámetro no encontrado', 404);
        }
        
        jsonResponse(true, $parametro, 'Parámetro obtenido correctamente');
    }
    
    // Obtener todos los parámetros
    $stmt = $pdo->query("SELECT * FROM parametros ORDER BY clave ASC");
    $parametros = $stmt->fetchAll();
    
    // Indexar por clave para facilidad de uso
    $parametrosIndexados = [];
    foreach ($parametros as $param) {
        $parametrosIndexados[$param['clave']] = $param;
    }
    
    jsonResponse(true, $parametrosIndexados, 'Parámetros obtenidos correctamente');
}

/**
 * Crea o actualiza un parámetro (upsert)
 * 
 * @param PDO $pdo Conexión PDO
 */
function crearActualizarParametro($pdo) {
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validaciones básicas
    if (!isset($input['clave']) || empty(trim($input['clave']))) {
        jsonResponse(false, null, 'La clave es requerida', 400);
    }
    
    if (!isset($input['valor']) || !is_numeric($input['valor'])) {
        jsonResponse(false, null, 'El valor debe ser numérico', 400);
    }
    
    // Escapar datos
    $clave = htmlspecialchars(strip_tags(trim($input['clave'])), ENT_QUOTES, 'UTF-8');
    $valor = (float)$input['valor'];
    $descripcion = isset($input['descripcion']) ? htmlspecialchars(strip_tags(trim($input['descripcion'])), ENT_QUOTES, 'UTF-8') : '';
    
    // Verificar si existe
    $stmtCheck = $pdo->prepare("SELECT id FROM parametros WHERE clave = :clave");
    $stmtCheck->execute(['clave' => $clave]);
    $existe = $stmtCheck->fetch();
    
    if ($existe) {
        // Actualizar
        $stmt = $pdo->prepare("
            UPDATE parametros 
            SET valor = :valor, descripcion = :descripcion 
            WHERE clave = :clave
        ");
        $stmt->execute([
            'clave' => $clave,
            'valor' => $valor,
            'descripcion' => $descripcion
        ]);
        
        jsonResponse(true, [
            'clave' => $clave,
            'valor' => $valor,
            'descripcion' => $descripcion
        ], 'Parámetro actualizado correctamente');
    } else {
        // Crear
        $stmt = $pdo->prepare("
            INSERT INTO parametros (clave, valor, descripcion)
            VALUES (:clave, :valor, :descripcion)
        ");
        $stmt->execute([
            'clave' => $clave,
            'valor' => $valor,
            'descripcion' => $descripcion
        ]);
        
        jsonResponse(true, [
            'clave' => $clave,
            'valor' => $valor,
            'descripcion' => $descripcion
        ], 'Parámetro creado correctamente', 201);
    }
}

/**
 * Actualiza un parámetro existente
 * 
 * @param PDO    $pdo   Conexión PDO
 * @param string $clave Clave del parámetro
 */
function actualizarParametro($pdo, $clave) {
    if ($clave === null) {
        jsonResponse(false, null, 'La clave del parámetro es requerida', 400);
    }
    
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Verificar que el parámetro existe
    $stmtCheck = $pdo->prepare("SELECT * FROM parametros WHERE clave = :clave");
    $stmtCheck->execute(['clave' => $clave]);
    
    if (!$stmtCheck->fetch()) {
        jsonResponse(false, null, 'Parámetro no encontrado', 404);
    }
    
    // Construir consulta de actualización dinámicamente
    $camposPermitidos = ['valor', 'descripcion'];
    $updates = [];
    $params = ['clave' => $clave];
    
    foreach ($camposPermitidos as $campo) {
        if (isset($input[$campo])) {
            $updates[] = "$campo = :$campo";
            
            if ($campo === 'valor') {
                if (!is_numeric($input[$campo])) {
                    jsonResponse(false, null, 'El valor debe ser numérico', 400);
                }
                $params[$campo] = (float)$input[$campo];
            } else {
                $params[$campo] = htmlspecialchars(strip_tags(trim($input[$campo])), ENT_QUOTES, 'UTF-8');
            }
        }
    }
    
    if (empty($updates)) {
        jsonResponse(false, null, 'No se proporcionaron campos para actualizar', 400);
    }
    
    $sql = "UPDATE parametros SET " . implode(', ', $updates) . " WHERE clave = :clave";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Obtener parámetro actualizado
    $stmtGet = $pdo->prepare("SELECT * FROM parametros WHERE clave = :clave");
    $stmtGet->execute(['clave' => $clave]);
    $parametro = $stmtGet->fetch();
    
    jsonResponse(true, $parametro, 'Parámetro actualizado correctamente');
}

/**
 * Elimina un parámetro
 * 
 * @param PDO    $pdo   Conexión PDO
 * @param string $clave Clave del parámetro
 */
function eliminarParametro($pdo, $clave) {
    if ($clave === null) {
        jsonResponse(false, null, 'La clave del parámetro es requerida', 400);
    }
    
    // Verificar que el parámetro existe
    $stmtCheck = $pdo->prepare("SELECT * FROM parametros WHERE clave = :clave");
    $stmtCheck->execute(['clave' => $clave]);
    
    if (!$stmtCheck->fetch()) {
        jsonResponse(false, null, 'Parámetro no encontrado', 404);
    }
    
    // Eliminar el parámetro
    $stmtDelete = $pdo->prepare("DELETE FROM parametros WHERE clave = :clave");
    $stmtDelete->execute(['clave' => $clave]);
    
    jsonResponse(true, null, 'Parámetro eliminado correctamente');
}