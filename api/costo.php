<?php
/**
 * API REST - Calculador de Costo
 * tienda3d v2.1
 * 
 * Calcula el costo total, ganancia y precio de venta de un producto
 * basado en peso (gramos) y tiempo de impresión (minutos)
 * 
 * GET /api/costo.php?peso=100&tiempo=60
 * Response: { costo_pla, costo_luz, costo_maquina, costo_total, ganancia, precio_venta }
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../includes/db.php';

try {
    $pdo = getDBConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $peso = isset($_GET['peso']) ? floatval($_GET['peso']) : 0;
        $tiempo = isset($_GET['tiempo']) ? intval($_GET['tiempo']) : 0;
        
        if ($peso <= 0 || $tiempo <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Peso (gramos) y tiempo (minutos) deben ser mayores a 0'
            ]);
            exit;
        }
        
        calcularCosto($pdo, $peso, $tiempo);
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function calcularCosto($pdo, $pesoGramos, $tiempoMinutos) {
    // Obtener parámetros de la BD
    $params = obtenerParametros($pdo);
    
    // Conversiones
    $pesoKg = $pesoGramos / 1000;
    $tiempoHoras = $tiempoMinutos / 60;
    
    // Cálculos desglosados
    $costoPLA = $pesoKg * $params['precio_pla_kg'];
    $costoLuz = $tiempoHoras * ($params['costo_luz_kwh'] * 3 / 60); // Máquina 3kW
    $costoMaquina = $tiempoHoras * $params['hora_maquina'];
    
    $costoTotal = $costoPLA + $costoLuz + $costoMaquina;
    $ganancia = $costoTotal * ($params['ganancia_porcentaje'] / 100);
    $precioVenta = $costoTotal + $ganancia;
    
    echo json_encode([
        'success' => true,
        'datos_entrada' => [
            'peso_gramos' => $pesoGramos,
            'peso_kg' => round($pesoKg, 4),
            'tiempo_minutos' => $tiempoMinutos,
            'tiempo_horas' => round($tiempoHoras, 4)
        ],
        'desglose_costo' => [
            'costo_pla' => round($costoPLA, 4),
            'costo_luz' => round($costoLuz, 4),
            'costo_maquina' => round($costoMaquina, 4)
        ],
        'parametros_usados' => [
            'precio_pla_kg' => $params['precio_pla_kg'],
            'costo_luz_kwh' => $params['costo_luz_kwh'],
            'potencia_maquina_kw' => 3,
            'hora_maquina' => $params['hora_maquina'],
            'ganancia_porcentaje' => $params['ganancia_porcentaje']
        ],
        'resultado' => [
            'costo_total' => round($costoTotal, 4),
            'ganancia_estimada' => round($ganancia, 4),
            'precio_venta' => round($precioVenta, 4)
        ]
    ]);
}

function obtenerParametros($pdo) {
    $stmt = $pdo->query("SELECT clave, valor FROM parametros");
    $params = [
        'precio_pla_kg' => 45,
        'costo_luz_kwh' => 15,
        'hora_maquina' => 150,
        'ganancia_porcentaje' => 50
    ];
    
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $params[$row['clave']] = floatval($row['valor']);
    }
    
    return $params;
}
?>
