<?php
/**
 * API REST para Autenticación de Usuarios
 * PrintCraft - Sistema de Cotización y Gestión de Impresión 3D
 * 
 * Endpoints disponibles:
 * - POST /api/auth.php?action=login    - Iniciar sesión
 * - POST /api/auth.php?action=logout  - Cerrar sesión
 * - GET  /api/auth.php?action=session  - Verificar sesión activa
 * - POST /api/auth.php?action=register - Registrar nuevo usuario (solo admin)
 * 
 * @author PrintCraft Development Team
 * @version 1.0.0
 */

// Encabezados CORS y Content-Type
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar solicitud OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once __DIR__ . '/../includes/db.php';

// Obtener acción de la solicitud
$action = isset($_GET['action']) ? trim($_GET['action']) : '';

// Iniciar sesión para manejo de sesiones PHP
session_start();

try {
    $pdo = getDBConnection();
    
    switch ($action) {
        case 'login':
            login($pdo);
            break;
            
        case 'logout':
            logout();
            break;
            
        case 'session':
            verificarSesion();
            break;
            
        case 'register':
            registrarUsuario($pdo);
            break;
            
        default:
            jsonResponse(false, null, 'Acción no válida', 400);
    }
    
} catch (PDOException $e) {
    error_log("Error en API auth: " . $e->getMessage());
    jsonResponse(false, null, 'Error interno del servidor', 500);
}

/**
 * Inicia sesión de usuario
 * 
 * @param PDO $pdo Conexión PDO
 */
function login($pdo) {
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validaciones básicas
    if (!isset($input['email']) || empty(trim($input['email']))) {
        jsonResponse(false, null, 'El email es requerido', 400);
    }
    
    if (!isset($input['password']) || empty($input['password'])) {
        jsonResponse(false, null, 'La contraseña es requerida', 400);
    }
    
    // Escapar datos
    $email = htmlspecialchars(strip_tags(trim($input['email'])), ENT_QUOTES, 'UTF-8');
    
    // Buscar usuario por email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        jsonResponse(false, null, 'Credenciales incorrectas', 401);
    }
    
    // Verificar contraseña
    if (!password_verify($input['password'], $usuario['password'])) {
        jsonResponse(false, null, 'Credenciales incorrectas', 401);
    }
    
    // Regenerar ID de sesión para prevenir session fixation
    session_regenerate_id(true);
    
    // Almacenar datos en sesión
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['user_nombre'] = $usuario['nombre'];
    $_SESSION['user_email'] = $usuario['email'];
    $_SESSION['user_role'] = $usuario['rol'];
    $_SESSION['session_time'] = time();
    
    jsonResponse(true, [
        'id' => $usuario['id'],
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'rol' => $usuario['rol']
    ], 'Sesión iniciada correctamente');
}

/**
 * Cierra la sesión del usuario
 */
function logout() {
    // Limpiar todas las variables de sesión
    $_SESSION = [];
    
    // Destruir la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Destruir la sesión
    session_destroy();
    
    jsonResponse(true, null, 'Sesión cerrada correctamente');
}

/**
 * Verifica si hay una sesión activa
 */
function verificarSesion() {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, null, 'No hay sesión activa', 401);
    }
    
    // Verificar si la sesión ha expirado (24 horas)
    $tiempoLimite = 86400; // 24 horas en segundos
    if (isset($_SESSION['session_time']) && (time() - $_SESSION['session_time']) > $tiempoLimite) {
        logout();
        jsonResponse(false, null, 'Sesión expirada', 401);
    }
    
    jsonResponse(true, [
        'id' => $_SESSION['user_id'],
        'nombre' => $_SESSION['user_nombre'],
        'email' => $_SESSION['user_email'],
        'rol' => $_SESSION['user_role']
    ], 'Sesión activa');
}

/**
 * Registra un nuevo usuario (solo administradores)
 * 
 * @param PDO $pdo Conexión PDO
 */
function registrarUsuario($pdo) {
    // Verificar que hay una sesión activa de admin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        jsonResponse(false, null, 'Acceso denegado. Se requiere rol de administrador', 403);
    }
    
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validaciones básicas
    if (!isset($input['nombre']) || empty(trim($input['nombre']))) {
        jsonResponse(false, null, 'El nombre es requerido', 400);
    }
    
    if (!isset($input['email']) || empty(trim($input['email']))) {
        jsonResponse(false, null, 'El email es requerido', 400);
    }
    
    if (!isset($input['password']) || empty($input['password'])) {
        jsonResponse(false, null, 'La contraseña es requerida', 400);
    }
    
    if (strlen($input['password']) < 8) {
        jsonResponse(false, null, 'La contraseña debe tener al menos 8 caracteres', 400);
    }
    
    // Validar formato de email
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, null, 'El formato del email no es válido', 400);
    }
    
    // Escapar datos
    $nombre = htmlspecialchars(strip_tags(trim($input['nombre'])), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(strip_tags(trim($input['email'])), ENT_QUOTES, 'UTF-8');
    $rol = isset($input['rol']) && in_array($input['rol'], ['admin', 'vendedor', 'usuario']) 
        ? $input['rol'] 
        : 'usuario';
    
    // Verificar si el email ya existe
    $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmtCheck->execute(['email' => $email]);
    
    if ($stmtCheck->fetch()) {
        jsonResponse(false, null, 'El email ya está registrado', 409);
    }
    
    // Hash de la contraseña usando bcrypt
    $passwordHash = password_hash($input['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Insertar usuario
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nombre, email, password, rol)
        VALUES (:nombre, :email, :password, :rol)
    ");
    $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'password' => $passwordHash,
        'rol' => $rol
    ]);
    
    $usuarioId = $pdo->lastInsertId();
    
    jsonResponse(true, [
        'id' => $usuarioId,
        'nombre' => $nombre,
        'email' => $email,
        'rol' => $rol
    ], 'Usuario registrado correctamente', 201);
}