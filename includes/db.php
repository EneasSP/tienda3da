<?php
/**
 * Archivo de conexión a la base de datos MySQL
 * tienda3d - Sistema de Cotización y Gestión de Impresión 3D
 */

// Configuración de la base de datos para Hostinger
define('DB_HOST', 'localhost');
define('DB_NAME', 'u736179347_db_cotiza3d');
define('DB_USER', 'u736179347_db_cotiza3d');
define('DB_PASS', 'R_GP-4n4');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    try {
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
        
    } catch (PDOException $e) {
        // MODO DEBUG: Enviamos el error real a la pantalla
        http_response_code(503);
        echo json_encode([
            'success' => false,
            'error'   => 'Error de conexión',
            'message' => $e->getMessage() // ¡Acá está el cambio!
        ]);
        exit;
    }
}

function jsonResponse($success, $data = null, $message = '', $code = 200) {
    http_response_code($code);
    
    $response = [
        'success' => $success,
        'timestamp' => date('c')
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    if ($message) {
        $response['message'] = $message;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

function verifyRole($allowedRoles) {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, null, 'No autenticado', 401);
    }
    
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowedRoles)) {
        jsonResponse(false, null, 'Acceso denegado', 403);
    }
    
    return true;
}

function escapeString($pdo, $value) {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}
?>