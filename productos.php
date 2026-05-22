<?php
/**
 * PÁGINA - Gestión de Productos
 * tienda3d v2.1
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - tienda3d</title>
    
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
    
    <!-- JS Común (Modo oscuro y navegación) -->
    <script src="assets/js/common.js"></script>
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/cruds.css">
</head>
<body class="min-h-screen">
    <?php include 'includes/navbar.php'; ?>
    
    <main class="main-container">
        <div class="mb-8">
            <h1 class="page-title">
                🏭 Gestión de Productos
            </h1>
            <p class="body-text">
                Crea, edita y gestiona los productos de impresión 3D
            </p>
        </div>

        <!-- TOOLBAR -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <input 
                type="text" 
                id="searchProducto" 
                placeholder="Buscar producto..." 
                class="flex-1 form-input"
            >
            <button 
                onclick="abrirModalProducto()" 
                class="btn btn-primary"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Nuevo Producto</span>
            </button>
        </div>

        <!-- GRID DE PRODUCTOS -->
        <div id="productosContainer" class="grid-productos">
            <div class="text-center py-12">
                <p class="text-gray-500">Cargando productos...</p>
            </div>
        </div>
    </main>

    <!-- MODAL PRODUCTO -->
    <div id="modalProducto" class="modal-backdrop hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalProductoTitulo">
                    Nuevo Producto
                </h2>
                <button onclick="cerrarModalProducto()" class="modal-close">
                    ✕
                </button>
            </div>

            <form id="formProducto" onsubmit="guardarProducto(event)">
                <div class="modal-body">
                    <input type="hidden" id="productoId">

                    <div class="form-group">
                        <label class="form-label">
                            Nombre del Producto *
                        </label>
                        <input 
                            type="text" 
                            id="productoNombre" 
                            class="form-input"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Descripción
                        </label>
                        <textarea 
                            id="productoDescripcion" 
                            rows="3"
                            class="form-textarea"
                        ></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                Peso (gramos) *
                            </label>
                            <input 
                                type="number" 
                                id="productoPeso" 
                                step="0.01"
                                min="0.1"
                                class="form-input"
                                required
                                onchange="recalcularCosto()"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Tiempo (minutos) *
                            </label>
                            <input 
                                type="number" 
                                id="productoTiempo" 
                                step="1"
                                min="1"
                                class="form-input"
                                required
                                onchange="recalcularCosto()"
                            >
                        </div>
                    </div>

                    <!-- CALCULADOR COSTO EN VIVO -->
                    <div class="costo-box">
                        <h3 class="font-semibold mb-3" style="color: var(--color-text)">
                            💰 Cálculo de Costo
                        </h3>

                        <div id="costoDesglose">
                            <div class="costo-item">
                                <span class="costo-item-label">Costo Filamento (PLA):</span>
                                <span class="costo-item-valor" id="costoPLA">$0.00</span>
                            </div>
                            <div class="costo-item">
                                <span class="costo-item-label">Costo Energía (Luz):</span>
                                <span class="costo-item-valor" id="costoLuz">$0.00</span>
                            </div>
                            <div class="costo-item">
                                <span class="costo-item-label">Costo Máquina (Horas):</span>
                                <span class="costo-item-valor" id="costoMaquina">$0.00</span>
                            </div>
                            <div class="costo-total">
                                <span>COSTO TOTAL:</span>
                                <span id="costoTotal">$0.00</span>
                            </div>
                            <div class="costo-item">
                                <span class="costo-item-label">Ganancia Estimada:</span>
                                <span class="costo-item-valor" style="color: var(--color-success)" id="costoGanancia">$0.00</span>
                            </div>
                            <div class="costo-precio">
                                <span>PRECIO VENTA:</span>
                                <span id="costoVenta">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Imagen (URL)
                        </label>
                        <input 
                            type="text" 
                            id="productoImagen" 
                            placeholder="https://ejemplo.com/imagen.jpg"
                            class="form-input"
                        >
                    </div>
                </div>

                <div class="modal-footer">
                    <button 
                        type="submit" 
                        class="flex-1 btn btn-primary"
                    >
                        💾 Guardar
                    </button>
                    <button 
                        type="button"
                        onclick="cerrarModalProducto()"
                        class="flex-1 btn btn-secondary"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/productos.js"></script>
</body>
</html>
