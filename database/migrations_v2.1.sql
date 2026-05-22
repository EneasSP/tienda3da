-- ========================================
-- MEJORAS BD tienda3d v2.1
-- Campos, tablas y procedimientos nuevos
-- ========================================

-- 1. AGREGAR CAMPO 'activo' a tabla 'productos'
ALTER TABLE `productos` ADD COLUMN `activo` TINYINT(1) DEFAULT 1 COMMENT 'Soft delete: 1=activo, 0=inactivo';
ALTER TABLE `productos` ADD INDEX `idx_activo` (`activo`);

-- 2. AGREGAR CAMPO 'activo' a tabla 'clientes'
ALTER TABLE `clientes` ADD COLUMN `activo` TINYINT(1) DEFAULT 1 COMMENT 'Soft delete: 1=activo, 0=inactivo';
ALTER TABLE `clientes` ADD INDEX `idx_activo` (`activo`);

-- 3. CREAR TABLA 'imagenes_productos' (para múltiples imágenes)
CREATE TABLE IF NOT EXISTS `imagenes_productos` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `producto_id` int(10) UNSIGNED NOT NULL COMMENT 'FK a productos',
  `ruta_imagen` varchar(255) NOT NULL COMMENT 'Ruta de la imagen',
  `es_principal` TINYINT(1) DEFAULT 0 COMMENT '1 si es la imagen principal',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_producto` (`producto_id`),
  KEY `idx_principal` (`es_principal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Imágenes múltiples de productos';

-- 4. CREAR PROCEDIMIENTO 'sp_calcular_costo_producto'
DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_calcular_costo_producto`$$
CREATE PROCEDURE `sp_calcular_costo_producto` (
    IN `p_peso_gramos` DECIMAL(10,2),
    IN `p_tiempo_minutos` INT,
    OUT `p_costo_unitario` DECIMAL(12,4),
    OUT `p_ganancia` DECIMAL(12,4),
    OUT `p_precio_venta` DECIMAL(12,4)
)
BEGIN
    DECLARE v_precio_pla_kg DECIMAL(10,4);
    DECLARE v_costo_luz_kwh DECIMAL(10,4);
    DECLARE v_hora_maquina DECIMAL(10,4);
    DECLARE v_ganancia_pct DECIMAL(10,4);
    DECLARE v_peso_kg DECIMAL(10,4);
    DECLARE v_tiempo_horas DECIMAL(10,4);
    DECLARE v_costo_pla DECIMAL(12,4);
    DECLARE v_costo_luz DECIMAL(12,4);
    DECLARE v_costo_maquina DECIMAL(12,4);
    
    -- Obtener parámetros configurables
    SELECT COALESCE(valor, 0) INTO v_precio_pla_kg FROM parametros WHERE clave = 'precio_pla_kg' LIMIT 1;
    SELECT COALESCE(valor, 0) INTO v_costo_luz_kwh FROM parametros WHERE clave = 'costo_luz_kwh' LIMIT 1;
    SELECT COALESCE(valor, 0) INTO v_hora_maquina FROM parametros WHERE clave = 'hora_maquina' LIMIT 1;
    SELECT COALESCE(valor, 50) INTO v_ganancia_pct FROM parametros WHERE clave = 'ganancia_porcentaje' LIMIT 1;
    
    -- Conversiones
    SET v_peso_kg = p_peso_gramos / 1000;
    SET v_tiempo_horas = p_tiempo_minutos / 60;
    
    -- Cálculos
    SET v_costo_pla = v_peso_kg * v_precio_pla_kg;
    SET v_costo_luz = v_tiempo_horas * (v_costo_luz_kwh * 3 / 60); -- 3kW máquina
    SET v_costo_maquina = v_tiempo_horas * v_hora_maquina;
    
    SET p_costo_unitario = v_costo_pla + v_costo_luz + v_costo_maquina;
    SET p_ganancia = p_costo_unitario * (v_ganancia_pct / 100);
    SET p_precio_venta = p_costo_unitario + p_ganancia;
END$$
DELIMITER ;

-- 5. CREAR VISTA 'v_productos_con_costo' (productos con cálculo de costo)
DROP VIEW IF EXISTS `v_productos_con_costo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`u736179347_db_cotiza3d`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_productos_con_costo` AS 
SELECT 
    p.id,
    p.nombre,
    p.descripcion,
    p.peso_gramos,
    p.tiempo_minutos,
    p.ruta_imagen,
    p.activo,
    ROUND((p.peso_gramos / 1000) * 45, 4) AS costo_pla,
    ROUND((p.tiempo_minutos / 60) * (15 * 3 / 60), 4) AS costo_luz,
    ROUND((p.tiempo_minutos / 60) * 150, 4) AS costo_maquina,
    ROUND(
        (p.peso_gramos / 1000) * 45 + 
        (p.tiempo_minutos / 60) * (15 * 3 / 60) + 
        (p.tiempo_minutos / 60) * 150,
        4
    ) AS costo_total,
    ROUND(
        ((p.peso_gramos / 1000) * 45 + 
         (p.tiempo_minutos / 60) * (15 * 3 / 60) + 
         (p.tiempo_minutos / 60) * 150) * 0.5,
        4
    ) AS ganancia_estimada,
    ROUND(
        ((p.peso_gramos / 1000) * 45 + 
         (p.tiempo_minutos / 60) * (15 * 3 / 60) + 
         (p.tiempo_minutos / 60) * 150) * 1.5,
        4
    ) AS precio_venta
FROM productos p
WHERE p.activo = 1;

-- 6. CREAR VISTA 'v_clientes_activos'
DROP VIEW IF EXISTS `v_clientes_activos`;
CREATE ALGORITHM=UNDEFINED DEFINER=`u736179347_db_cotiza3d`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_clientes_activos` AS 
SELECT 
    c.id,
    c.nombre,
    c.email,
    c.telefono,
    c.empresa,
    COUNT(DISTINCT p.id) AS total_pedidos,
    COALESCE(SUM(p.total), 0) AS total_gastado,
    c.fecha_creacion,
    c.fecha_actualizacion
FROM clientes c
LEFT JOIN pedidos p ON c.id = p.cliente_id AND p.estado IN ('aprobado', 'finalizado')
WHERE c.activo = 1
GROUP BY c.id, c.nombre, c.email, c.telefono, c.empresa, c.fecha_creacion, c.fecha_actualizacion;

-- 7. ACTUALIZAR ÍNDICES para búsqueda rápida
ALTER TABLE `productos` ADD FULLTEXT INDEX `ft_nombre_descripcion` (`nombre`, `descripcion`);
ALTER TABLE `clientes` ADD FULLTEXT INDEX `ft_nombre_empresa` (`nombre`, `empresa`);

-- 8. CREAR PROCEDIMIENTO para obtener parámetros
DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_get_parametros`$$
CREATE PROCEDURE `sp_get_parametros` ()
BEGIN
    SELECT 
        clave,
        valor,
        descripcion
    FROM parametros
    ORDER BY id;
END$$
DELIMITER ;

-- DONE ✅
COMMIT;
