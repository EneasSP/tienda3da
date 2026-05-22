<?php
/**
 * API REST para Gestión de Pedidos
 * PrintCraft - Sistema de Cotización y Gestión de Impresión 3D
 * 
 * Endpoints disponibles:
 * - GET    /api/pedidos.php          - Listar todos los pedidos (con filtros)
 * - GET    /api/pedidos.php?id=X     - Obtener un pedido específico
 * - POST   /api/pedidos.php          - Crear un nuevo pedido
 * - PUT    /api/pedidos.php?id=X     - Actualizar un pedido existente
 * - DELETE /api/pedidos.php?id=X     - Eliminar un pedido
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

// Manejar búsqueda por texto y filtros
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';
$mostrar_finalizados = isset($_GET['finalizados']) && $_GET['finalizados'] === 'true';

try {
    $pdo = getDBConnection();
    
    switch ($method) {
        case 'GET':
            obtenerPedidos($pdo, $id, $search, $estado, $mostrar_finalizados);
            break;
            
        case 'POST':
            crearPedido($pdo);
            break;
            
        case 'PUT':
            actualizarPedido($pdo, $id);
            break;
            
        case 'DELETE':
            eliminarPedido($pdo, $id);
            break;
            
        default:
            jsonResponse(false, null, 'Método no permitido', 405);
    }
    
} catch (PDOException $e) {
    error_log("Error en API pedidos: " . $e->getMessage());
    jsonResponse(false, null, 'Error interno del servidor', 500);
}

/**
 * Obtiene la lista de pedidos con filtros opcionales
 * 
 * @param PDO    $pdo                 Conexión PDO
 * @param int    $id                  ID del pedido (opcional)
 * @param string $search              Término de búsqueda
 * @param string $estado              Filtrar por estado
 * @param bool   $mostrar_finalizados Mostrar pedidos finalizados
 */
function obtenerPedidos($pdo, $id, $search, $estado, $mostrar_finalizados) {
    // Si hay ID específico, obtener solo ese pedido
    if ($id !== null) {
        $stmt = $pdo->prepare("
            SELECT p.*, 
                   c.nombre AS cliente_nombre, 
                   c.empresa AS cliente_empresa,
                   u.nombre AS usuario_nombre
            FROM pedidos p
            LEFT JOIN clientes c ON p.cliente_id = c.id
            LEFT JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $pedido = $stmt->fetch();
        
        if (!$pedido) {
            jsonResponse(false, null, 'Pedido no encontrado', 404);
        }
        
        // Obtener detalles del pedido
        $detallesStmt = $pdo->prepare("
            SELECT dp.*, pr.nombre AS producto_nombre, pr.peso_gramos, pr.tiempo_minutos
            FROM detalles_pedido dp
            LEFT JOIN productos pr ON dp.producto_id = pr.id
            WHERE dp.pedido_id = :pedido_id
        ");
        $detallesStmt->execute(['pedido_id' => $id]);
        $pedido['detalles'] = $detallesStmt->fetchAll();
        
        jsonResponse(true, $pedido, 'Pedido obtenido correctamente');
    }
    
    // Construir consulta SQL con filtros
    $sql = "
        SELECT p.*, 
               c.nombre AS cliente_nombre, 
               c.empresa AS cliente_empresa,
               u.nombre AS usuario_nombre
        FROM pedidos p
        LEFT JOIN clientes c ON p.cliente_id = c.id
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Filtro de búsqueda
    if ($search !== '') {
        $sql .= " AND (c.nombre LIKE :search OR c.empresa LIKE :search OR p.id LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    // Filtro de estado
    if ($estado !== '') {
        $sql .= " AND p.estado = :estado";
        $params['estado'] = $estado;
    } elseif (!$mostrar_finalizados) {
        // Por defecto, excluir finalizados
        $sql .= " AND p.estado != 'finalizado'";
    }
    
    // Ordenar por fecha descendente
    $sql .= " ORDER BY p.fecha_pedido DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll();
    
    // Calcular métricas adicionales
    $metricas = calcularMetricas($pdo, $mostrar_finalizados);
    
    jsonResponse(true, [
        'pedidos' => $pedidos,
        'metricas' => $metricas,
        'total' => count($pedidos)
    ], 'Pedidos obtenidos correctamente');
}

/**
 * Calcula las métricas de negocio
 * 
 * @param PDO  $pdo                 Conexión PDO
 * @param bool $mostrar_finalizados Incluir pedidos finalizados
 * @return array Métricas calculadas
 */
function calcularMetricas($pdo, $mostrar_finalizados) {
    // Total facturado (solo pedidos aprobados o finalizados)
    $stmtTotal = $pdo->query("
        SELECT COALESCE(SUM(total), 0) AS total_facturado
        FROM pedidos
        WHERE estado IN ('aprobado', 'finalizado')
    ");
    $totalFacturado = (float)$stmtTotal->fetch()['total_facturado'];
    
    // Costo de producción (suma de costos de productos en pedidos)
    $stmtCosto = $pdo->query("
        SELECT COALESCE(SUM(dp.cantidad * dp.costo_unitario), 0) AS costo_produccion
        FROM detalles_pedido dp
        LEFT JOIN pedidos p ON dp.pedido_id = p.id
        WHERE p.estado IN ('aprobado', 'finalizado')
    ");
    $costoProduccion = (float)$stmtCosto->fetch()['costo_produccion'];
    
    // Ganancia neta
    $stmtGanancia = $pdo->query("
        SELECT COALESCE(SUM(dp.cantidad * (dp.precio_unitario - dp.costo_unitario)), 0) AS ganancia_neta
        FROM detalles_pedido dp
        LEFT JOIN pedidos p ON dp.pedido_id = p.id
        WHERE p.estado IN ('aprobado', 'finalizado')
    ");
    $gananciaNeta = (float)$stmtGanancia->fetch()['ganancia_neta'];
    
    return [
        'total_facturado' => $totalFacturado,
        'costo_produccion' => $costoProduccion,
        'ganancia_neta' => $gananciaNeta
    ];
}

/**
 * Crea un nuevo pedido con cálculo automático de costos
 * 
 * @param PDO $pdo Conexión PDO
 */
function crearPedido($pdo) {
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validaciones básicas
    if (!isset($input['cliente_id']) || empty($input['cliente_id'])) {
        jsonResponse(false, null, 'El cliente es requerido', 400);
    }
    
    if (!isset($input['productos']) || empty($input['productos'])) {
        jsonResponse(false, null, 'Al menos un producto es requerido', 400);
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    try {
        // Obtener parámetros del sistema
        $parametros = obtenerParametros($pdo);
        
        // Calcular costo total del pedido
        $total = 0;
        $costoTotal = 0;
        $detalles = [];
        
        foreach ($input['productos'] as $producto) {
            // Obtener datos del producto
            $stmtProd = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
            $stmtProd->execute(['id' => $producto['producto_id']]);
            $prod = $stmtProd->fetch();
            
            if (!$prod) {
                throw new Exception("Producto con ID {$producto['producto_id']} no encontrado");
            }
            
            $cantidad = (int)$producto['cantidad'];
            
            // Calculamos costos unitarios (para 1 unidad)
            $pesoKgUnitario = $prod['peso_gramos'] / 1000;
            $tiempoHorasUnitario = $prod['tiempo_minutos'] / 60;
            
            $costoPLA = $pesoKgUnitario * $parametros['precio_pla_kg'];
            $costoLuz = $tiempoHorasUnitario * ($parametros['costo_luz_kwh'] * 3 / 60);
            $costoMaquina = $tiempoHorasUnitario * $parametros['hora_maquina'];
            
            $costoUnitario = $costoPLA + $costoLuz + $costoMaquina;
            $precioUnitario = $costoUnitario * (1 + ($parametros['ganancia_porcentaje'] / 100));
            
            $detalles[] = [
                'producto_id' => $prod['id'],
                'cantidad' => $cantidad,
                'peso_gramos' => $prod['peso_gramos'],
                'tiempo_minutos' => $prod['tiempo_minutos'],
                'costo_unitario' => $costoUnitario,
                'precio_unitario' => $precioUnitario
            ];
            
            $total += $precioUnitario * $cantidad;
            $costoTotal += $costoUnitario * $cantidad;
        }
        
        // Determinar usuario (si hay sesión activa)
        $usuarioId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        // Insertar pedido
        $stmtPedido = $pdo->prepare("
            INSERT INTO pedidos (cliente_id, usuario_id, fecha_pedido, total, estado)
            VALUES (:cliente_id, :usuario_id, NOW(), :total, 'pendiente')
        ");
        $stmtPedido->execute([
            'cliente_id' => $input['cliente_id'],
            'usuario_id' => $usuarioId,
            'total' => $total
        ]);
        
        $pedidoId = $pdo->lastInsertId();
        
        // Insertar detalles del pedido
        $stmtDetalle = $pdo->prepare("
            INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, costo_unitario, precio_unitario, tiempo_minutos)
            VALUES (:pedido_id, :producto_id, :cantidad, :costo_unitario, :precio_unitario, :tiempo_minutos)
        ");
        
        foreach ($detalles as $detalle) {
            $stmtDetalle->execute([
                'pedido_id' => $pedidoId,
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'costo_unitario' => $detalle['costo_unitario'],
                'precio_unitario' => $detalle['precio_unitario'],
                'tiempo_minutos' => $detalle['tiempo_minutos'] * $detalle['cantidad']
            ]);
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        jsonResponse(true, [
            'id' => $pedidoId,
            'total' => $total,
            'costo_total' => $costoTotal,
            'estado' => 'pendiente'
        ], 'Pedido creado correctamente', 201);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Obtiene los parámetros del sistema
 * 
 * @param PDO $pdo Conexión PDO
 * @return array Parámetros indexados por clave
 */
function obtenerParametros($pdo) {
    $stmt = $pdo->query("SELECT clave, valor FROM parametros");
    $rows = $stmt->fetchAll();
    
    $parametros = [];
    foreach ($rows as $row) {
        $parametros[$row['clave']] = (float)$row['valor'];
    }
    
    // Valores por defecto si no existen
    $parametros['precio_pla_kg'] = $parametros['precio_pla_kg'] ?? 45.00;
    $parametros['costo_luz_kwh'] = $parametros['costo_luz_kwh'] ?? 15.00;
    $parametros['hora_maquina'] = $parametros['hora_maquina'] ?? 150.00;
    $parametros['ganancia_porcentaje'] = $parametros['ganancia_porcentaje'] ?? 50.00;
    
    return $parametros;
}

/**
 * Actualiza un pedido existente
 * 
 * @param PDO $pdo Conexión PDO
 * @param int $id ID del pedido
 */
function actualizarPedido($pdo, $id) {
    if ($id === null) {
        jsonResponse(false, null, 'ID de pedido es requerido', 400);
    }
    
    // Leer datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Verificar que el pedido existe
    $stmtCheck = $pdo->prepare("SELECT * FROM pedidos WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    
    if (!$stmtCheck->fetch()) {
        jsonResponse(false, null, 'Pedido no encontrado', 404);
    }
    
    // Construir consulta de actualización dinámicamente
    $camposPermitidos = ['cliente_id', 'estado', 'total'];
    $updates = [];
    $params = ['id' => $id];
    
    foreach ($camposPermitidos as $campo) {
        if (isset($input[$campo])) {
            $updates[] = "$campo = :$campo";
            $params[$campo] = $input[$campo];
        }
    }
    
    if (empty($updates)) {
        jsonResponse(false, null, 'No se proporcionaron campos para actualizar', 400);
    }
    
    $sql = "UPDATE pedidos SET " . implode(', ', $updates) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    jsonResponse(true, null, 'Pedido actualizado correctamente');
}

/**
 * Elimina un pedido
 * 
 * @param PDO $pdo Conexión PDO
 * @param int $id ID del pedido
 */
function eliminarPedido($pdo, $id) {
    if ($id === null) {
        jsonResponse(false, null, 'ID de pedido es requerido', 400);
    }
    
    // Verificar que el pedido existe
    $stmtCheck = $pdo->prepare("SELECT * FROM pedidos WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    
    if (!$stmtCheck->fetch()) {
        jsonResponse(false, null, 'Pedido no encontrado', 404);
    }
    
    // Eliminar detalles primero (por integridad referencial)
    $stmtDeleteDetalles = $pdo->prepare("DELETE FROM detalles_pedido WHERE pedido_id = :pedido_id");
    $stmtDeleteDetalles->execute(['pedido_id' => $id]);
    
    // Eliminar el pedido
    $stmtDelete = $pdo->prepare("DELETE FROM pedidos WHERE id = :id");
    $stmtDelete->execute(['id' => $id]);
    
    jsonResponse(true, null, 'Pedido eliminado correctamente');
}