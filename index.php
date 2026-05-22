<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="tienda3d - Sistema de Cotización y Gestión de Impresión 3D">
    <title>tienda3d - Dashboard de Impresión 3D</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Configuración de Tailwind para Dark Mode -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: 'var(--color-primary-50)',
                            100: 'var(--color-primary-100)',
                            200: 'var(--color-primary-200)',
                            300: 'var(--color-primary-300)',
                            400: 'var(--color-primary-400)',
                            500: 'var(--color-primary)',
                            600: 'var(--color-primary-600)',
                            700: 'var(--color-primary-700)',
                            800: 'var(--color-primary-800)',
                            900: 'var(--color-primary-900)',
                            DEFAULT: 'var(--color-primary)',
                        },
                        secondary: {
                            50: 'var(--color-secondary-50)',
                            100: 'var(--color-secondary-100)',
                            200: 'var(--color-secondary-200)',
                            300: 'var(--color-secondary-300)',
                            400: 'var(--color-secondary-400)',
                            500: 'var(--color-secondary)',
                            600: 'var(--color-secondary-600)',
                            700: 'var(--color-secondary-700)',
                            800: 'var(--color-secondary-800)',
                            900: 'var(--color-secondary-900)',
                            DEFAULT: 'var(--color-secondary)',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/cruds.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    
    <!-- JS Común (Modo oscuro y navegación) -->
    <script src="assets/js/common.js"></script>
</head>
<body class="min-h-screen">
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Contenedor principal -->
    <main class="main-container">
        
        <!-- ============================================================
             SECCIÓN: PEDIDOS
             ============================================================ -->
        <section id="pedidos-section" class="tab-section">
            
            <!-- Tarjetas de métricas -->
            <div id="metricas-container" class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-header">
                        <h3 class="metric-title">Total Facturado</h3>
                        <div class="metric-icon total">💰</div>
                    </div>
                    <p class="metric-value">$0.00</p>
                    <p class="metric-change positive">📈 Cargando...</p>
                </div>
                
                <div class="metric-card">
                    <div class="metric-header">
                        <h3 class="metric-title">Costo Producción</h3>
                        <div class="metric-icon cost">🏭</div>
                    </div>
                    <p class="metric-value">$0.00</p>
                    <p class="metric-change negative">📉 Cargando...</p>
                </div>
                
                <div class="metric-card">
                    <div class="metric-header">
                        <h3 class="metric-title">Ganancia Neta</h3>
                        <div class="metric-icon profit">📊</div>
                    </div>
                    <p class="metric-value">$0.00</p>
                    <p class="metric-change positive">✅ Cargando...</p>
                </div>
            </div>
            
            <!-- Sección de pedidos -->
            <div class="pedidos-section">
                <div class="section-header">
                    <h2 class="section-title"> Gestión de Pedidos</h2>
                    
                    <div class="controls-bar">
                        <!-- Buscador -->
                        <div class="search-box">
                            <span class="search-icon">🔍</span>
                            <input type="text" 
                                   id="search-input" 
                                   class="search-input" 
                                   placeholder="Buscar por cliente o empresa...">
                        </div>
                        
                        <!-- Filtros de estado -->
                        <div class="filter-tabs">
                            <button class="filter-tab active" data-filtro="activos">Activos</button>
                            <button class="filter-tab" data-filtro="finalizados">Finalizados</button>
                            <button class="filter-tab" data-filtro="todos">Todos</button>
                        </div>
                        
                        <!-- Botón nuevo pedido -->
                        <button id="nuevo-pedido-btn" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            <span>Nuevo Pedido</span>
                        </button>
                    </div>
                </div>
                
                <!-- Tabla de pedidos -->
                <div class="pedidos-table-container">
                    <table class="pedidos-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Empresa</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="pedidos-tbody">
                            <tr>
                                <td colspan="7" class="text-center py-8">
                                    <div class="loading-spinner mx-auto"></div>
                                    <p class="mt-2 text-sm text-gray-500">Cargando pedidos...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        
        <!-- ============================================================
             SECCIÓN: CATÁLOGO
             ============================================================ -->
        <section id="catalogo-section" class="tab-section hidden">
            <div class="pedidos-section">
                <div class="section-header">
                    <h2 class="section-title">📦 Catálogo de Productos</h2>
                </div>
                
                <div id="catalog-grid" class="grid-productos p-6">
                    <div class="text-center py-8 col-span-full">
                        <div class="loading-spinner mx-auto"></div>
                        <p class="mt-2 text-sm text-gray-500">Cargando productos...</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- ============================================================
             SECCIÓN: CONFIGURACIÓN
             ============================================================ -->
        <section id="configuracion-section" class="tab-section hidden">
            <div id="settings-container">
                <div class="settings-section">
                    <h3 class="settings-title">💰 Costos de Producción</h3>
                    <div class="settings-grid">
                        <div class="setting-item">
                            <label for="param-precio-pla">Precio PLA por kg (ARS)</label>
                            <input type="number" id="param-precio-pla" class="form-input" value="45" step="0.01">
                        </div>
                        <div class="setting-item">
                            <label for="param-costo-luz">Costo de luz por kWh (ARS)</label>
                            <input type="number" id="param-costo-luz" class="form-input" value="15" step="0.01">
                        </div>
                        <div class="setting-item">
                            <label for="param-hora-maquina">Costo hora máquina (ARS)</label>
                            <input type="number" id="param-hora-maquina" class="form-input" value="150" step="0.01">
                        </div>
                        <div class="setting-item">
                            <label for="param-ganancia">Porcentaje de ganancia (%)</label>
                            <input type="number" id="param-ganancia" class="form-input" value="50" step="1">
                        </div>
                    </div>
                    <button class="btn btn-primary mt-4" onclick="guardarParametros()">
                        💾 Guardar Cambios
                    </button>
                </div>
            </div>
        </section>
        
    </main>
    
    <!-- ============================================================
         MODAL: NUEVO PEDIDO
         ============================================================ -->
    <div id="pedido-modal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modal-title" class="modal-title">Nuevo Pedido</h3>
                <button id="close-modal-btn" class="modal-close">✕</button>
            </div>
            
            <div class="modal-body">
                <form id="pedido-form">
                    <input type="hidden" id="pedido-id" value="">
                    
                    <!-- Selector de cliente -->
                    <div class="form-group">
                        <label for="cliente-select" class="form-label">Cliente</label>
                        <select id="cliente-select" class="form-select" required>
                            <option value="">Seleccionar cliente...</option>
                        </select>
                    </div>
                    
                    <!-- Selector de producto -->
                    <div class="form-group">
                        <label for="producto-select" class="form-label">Agregar Producto</label>
                        <div class="flex gap-2">
                            <select id="producto-select" class="form-select flex-1">
                                <option value="">Seleccionar producto...</option>
                            </select>
                            <input type="number" id="cantidad-input" class="form-input w-24" value="1" min="1" placeholder="Cant.">
                            <button type="button" id="agregar-producto-btn" class="btn btn-secondary" title="Agregar producto">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Lista de productos agregados -->
                    <div id="detalles-container" class="form-group">
                        <!-- Detalles se agregan dinámicamente -->
                    </div>
                    
                    <!-- Resumen de costos -->
                    <div id="cost-summary">
                        <!-- Costos calculados -->
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModalPedido()">Cancelar</button>
                <button type="button" id="guardar-pedido-btn" class="btn btn-success">💾 Guardar Pedido</button>
            </div>
        </div>
    </div>
    
    <!-- Scripts de la aplicación -->
    <script src="assets/js/app.js"></script>
</body>
</html>