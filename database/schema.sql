-- ================================================================
-- PrintCraft - Schema de Base de Datos
-- Sistema de Cotización y Gestión de Impresión 3D
-- 
-- MySQL 8.0+ / MariaDB 10.4+
-- 
-- @author PrintCraft Development Team
-- @version 1.0.0
-- ================================================================

-- ================================================================
-- TABLA: usuarios
-- Almacena los usuarios del sistema (administradores, vendedores)
-- ================================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre completo del usuario',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'Correo electrónico único',
    password VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt de la contraseña',
    rol ENUM('admin', 'vendedor', 'usuario') DEFAULT 'usuario' COMMENT 'Rol del usuario en el sistema',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de usuarios del sistema';

-- ================================================================
-- TABLA: clientes
-- Almacena la información de los clientes
-- ================================================================
CREATE TABLE IF NOT EXISTS clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre del cliente',
    email VARCHAR(255) NOT NULL COMMENT 'Correo electrónico',
    telefono VARCHAR(20) DEFAULT '' COMMENT 'Número de teléfono',
    empresa VARCHAR(100) DEFAULT '' COMMENT 'Nombre de la empresa',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_email (email),
    INDEX idx_empresa (empresa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de clientes';

-- ================================================================
-- TABLA: parametros
-- Almacena los parámetros de configuración del sistema
-- ================================================================
CREATE TABLE IF NOT EXISTS parametros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) NOT NULL UNIQUE COMMENT 'Identificador único del parámetro',
    valor DECIMAL(10,4) NOT NULL DEFAULT 0 COMMENT 'Valor numérico del parámetro',
    descripcion VARCHAR(255) DEFAULT '' COMMENT 'Descripción del parámetro',
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Parámetros de configuración del sistema';

-- ================================================================
-- TABLA: productos
-- Almacena el catálogo de productos de impresión 3D
-- ================================================================
CREATE TABLE IF NOT EXISTS productos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre del producto',
    descripcion TEXT DEFAULT '' COMMENT 'Descripción detallada',
    peso_gramos DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Peso en gramos de una unidad',
    tiempo_minutos INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Tiempo de impresión en minutos',
    ruta_imagen VARCHAR(255) DEFAULT '' COMMENT 'Ruta de la imagen del producto',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Catálogo de productos de impresión 3D';

-- ================================================================
-- TABLA: pedidos
-- Almacena los pedidos realizados por los clientes
-- ================================================================
CREATE TABLE IF NOT EXISTS pedidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT UNSIGNED NOT NULL COMMENT 'FK a clientes',
    usuario_id INT UNSIGNED DEFAULT NULL COMMENT 'FK a usuarios (quién lo creó)',
    fecha_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del pedido',
    total DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'Total del pedido',
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'finalizado') DEFAULT 'pendiente' COMMENT 'Estado del pedido',
    notas TEXT DEFAULT '' COMMENT 'Notas u observaciones',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_cliente (cliente_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Pedidos realizados por clientes';

-- ================================================================
-- TABLA: detalles_pedido
-- Almacena los productos de cada pedido (muchos a muchos)
-- ================================================================
CREATE TABLE IF NOT EXISTS detalles_pedido (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL COMMENT 'FK a pedidos',
    producto_id INT UNSIGNED NOT NULL COMMENT 'FK a productos',
    cantidad INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Cantidad de unidades',
    costo_unitario DECIMAL(10,4) NOT NULL DEFAULT 0 COMMENT 'Costo por unidad al momento',
    precio_unitario DECIMAL(10,4) NOT NULL DEFAULT 0 COMMENT 'Precio de venta por unidad',
    tiempo_minutos INT UNSIGNED DEFAULT 0 COMMENT 'Tiempo total de impresión',
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_pedido (pedido_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Detalles de productos en cada pedido';

-- ================================================================
-- DATOS INICIALES: Usuarios
-- ================================================================
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'admin@printcraft.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X.VQ5JQnQJN0gC6j2', 'admin'),
('Vendedor', 'ventas@printcraft.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X.VQ5JQnQJN0gC6j2', 'vendedor');

-- ================================================================
-- DATOS INICIALES: Parámetros
-- ================================================================
INSERT INTO parametros (clave, valor, descripcion) VALUES
('precio_pla_kg', 45.0000, 'Precio del filamento PLA por kilogramo en ARS'),
('costo_luz_kwh', 15.0000, 'Costo del kilovatio hora de energía eléctrica en ARS'),
('hora_maquina', 150.0000, 'Costo por hora de uso de la máquina de impresión 3D'),
('ganancia_porcentaje', 50.0000, 'Porcentaje de ganancia sobre el costo de producción');

-- ================================================================
-- DATOS INICIALES: Productos de ejemplo
-- ================================================================
INSERT INTO productos (nombre, descripcion, peso_gramos, tiempo_minutos, ruta_imagen) VALUES
('Tornillo M3x20', 'Tornillo métrico M3x20mm para impresiones técnicas', 2.50, 15, ''),
('Tornillo M4x30', 'Tornillo métrico M4x30mm para impresiones técnicas', 4.20, 22, ''),
('Soporte Celular Genérico', 'Soporte universal para smartphone - modelo básico', 35.00, 45, ''),
('Caja Organizadora 100x60x40', 'Caja organizadora pequeña con tapa', 55.00, 65, ''),
('Caja Organizadora 150x100x50', 'Caja organizadora mediana con divisiones', 95.00, 90, ''),
('Marco Foto 10x15', 'Marco para foto de 10x15cm con soporte de mesa', 42.00, 55, ''),
('Maceta Pequeña', 'Maceta para planta pequeña con drenaje', 65.00, 50, ''),
('Maceta Mediana', 'Maceta mediana para planta mediana', 120.00, 75, ''),
('Llave Francesa 7mm', 'Llave inglesa pequeña ajustable', 28.00, 35, ''),
('Protector Auricular', 'Protector de auriculares - gancho para uso prolongado', 18.00, 25, '');

-- ================================================================
-- DATOS INICIALES: Clientes de ejemplo
-- ================================================================
INSERT INTO clientes (nombre, email, telefono, empresa) VALUES
('Juan Pérez', 'juan.perez@email.com', '+54 11 1234-5678', 'TechLabs Argentina'),
('María González', 'maria.gonzalez@email.com', '+54 11 8765-4321', 'Diseño 3D Studio'),
('Carlos Rodríguez', 'carlos.rodriguez@email.com', '+54 11 5555-1234', 'Impresiones Rápidas SA'),
('Ana Martínez', 'ana.martinez@email.com', '+54 11 9876-5432', 'FabLab Buenos Aires'),
('Luis Fernández', 'luis.fernandez@email.com', '+54 11 2468-1357', 'MakerSpace Central');

-- ================================================================
-- DATOS INICIALES: Pedidos de ejemplo
-- ================================================================
INSERT INTO pedidos (cliente_id, usuario_id, fecha_pedido, total, estado, notas) VALUES
(1, 1, DATE_SUB(NOW(), INTERVAL 5 DAY), 450.00, 'finalizado', 'Pedido entregado en tiempo'),
(2, 1, DATE_SUB(NOW(), INTERVAL 3 DAY), 1250.00, 'aprobado', 'Confirmado por el cliente'),
(3, 2, DATE_SUB(NOW(), INTERVAL 2 DAY), 890.00, 'pendiente', 'Esperando aprobación de presupuesto'),
(4, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 2100.00, 'aprobado', 'Pedido urgente'),
(5, 2, NOW(), 675.00, 'pendiente', 'Primer pedido del cliente');

-- ================================================================
-- DATOS INICIALES: Detalles de pedido de ejemplo
-- ================================================================
INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, costo_unitario, precio_unitario, tiempo_minutos) VALUES
-- Pedido 1
(1, 1, 10, 0.1125, 0.17, 150),
(1, 3, 2, 1.5750, 2.36, 90),
-- Pedido 2
(2, 4, 5, 2.4750, 3.71, 325),
(2, 5, 3, 4.2750, 6.41, 270),
(2, 6, 4, 1.8900, 2.84, 220),
-- Pedido 3
(3, 7, 8, 2.9250, 4.39, 400),
(3, 8, 2, 5.4000, 8.10, 150),
-- Pedido 4
(4, 2, 20, 0.1890, 0.28, 440),
(4, 9, 6, 1.2600, 1.89, 210),
(4, 10, 15, 0.8100, 1.22, 375),
-- Pedido 5
(5, 1, 15, 0.1125, 0.17, 225),
(5, 4, 3, 2.4750, 3.71, 195);

-- ================================================================
-- VISTAS ÚTILES PARA REPORTES
-- ================================================================

-- Vista: Resumen de pedidos por cliente
CREATE OR REPLACE VIEW v_pedidos_por_cliente AS
SELECT 
    c.id,
    c.nombre AS cliente,
    c.empresa,
    COUNT(p.id) AS total_pedidos,
    COALESCE(SUM(p.total), 0) AS total_facturado,
    AVG(p.total) AS promedio_pedido
FROM clientes c
LEFT JOIN pedidos p ON c.id = p.cliente_id AND p.estado IN ('aprobado', 'finalizado')
GROUP BY c.id, c.nombre, c.empresa;

-- Vista: Productos más vendidos
CREATE OR REPLACE VIEW v_productos_populares AS
SELECT 
    pr.id,
    pr.nombre,
    pr.peso_gramos,
    SUM(dp.cantidad) AS unidades_vendidas,
    SUM(dp.cantidad * dp.precio_unitario) AS revenue_total
FROM productos pr
LEFT JOIN detalles_pedido dp ON pr.id = dp.producto_id
LEFT JOIN pedidos p ON dp.pedido_id = p.id AND p.estado IN ('aprobado', 'finalizado')
GROUP BY pr.id, pr.nombre, pr.peso_gramos
ORDER BY unidades_vendidas DESC;

-- Vista: Resumen financiero
CREATE OR REPLACE VIEW v_resumen_financiero AS
SELECT 
    COUNT(DISTINCT p.id) AS total_pedidos,
    COUNT(DISTINCT p.cliente_id) AS clientes_ativos,
    SUM(p.total) AS revenue_total,
    SUM(p.total) * 0.3 AS costo_estimado,
    SUM(p.total) * 0.7 AS ganancia_estimada
FROM pedidos p
WHERE p.estado IN ('aprobado', 'finalizado');

-- ================================================================
-- PROCEDIMIENTOS ALMACENADOS ÚTILES
-- ================================================================

DELIMITER //

-- Procedimiento: Actualizar estado de pedido
CREATE PROCEDURE sp_actualizar_estado_pedido(
    IN p_pedido_id INT,
    IN p_nuevo_estado ENUM('pendiente', 'aprobado', 'rechazado', 'finalizado')
)
BEGIN
    UPDATE pedidos 
    SET estado = p_nuevo_estado,
        fecha_actualizacion = CURRENT_TIMESTAMP
    WHERE id = p_pedido_id;
END //

-- Procedimiento: Calcular costo de producción de un pedido
CREATE PROCEDURE sp_calcular_costo_pedido(
    IN p_pedido_id INT,
    OUT p_costo_total DECIMAL(12,4),
    OUT p_precio_total DECIMAL(12,4)
)
BEGIN
    SELECT 
        COALESCE(SUM(dp.costo_unitario * dp.cantidad), 0),
        COALESCE(SUM(dp.precio_unitario * dp.cantidad), 0)
    INTO p_costo_total, p_precio_total
    FROM detalles_pedido dp
    WHERE dp.pedido_id = p_pedido_id;
END //

DELIMITER ;