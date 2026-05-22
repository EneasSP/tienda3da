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
                            50: '#f1f5f9',
                            100: '#e2e8f0',
                            200: '#cbd5e1',
                            300: '#94a3b8',
                            400: '#64748b',
                            500: '#475569',
                            600: '#334155',
                            700: '#1e2530',
                            800: '#0f131a',
                            900: '#0a0c10',
                        },
                        secondary: {
                            50: '#fdfbf7',
                            100: '#f5ede0',
                            200: '#ebdcc5',
                            300: '#e2c9a6',
                            400: '#d8b787',
                            500: '#c5a880',
                            600: '#a88c64',
                            700: '#8b714b',
                            800: '#6f5734',
                            900: '#543f20',
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
    
    <div class="container mx-auto p-4 md:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                🏭 Gestión de Productos
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
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
                ➕ Nuevo Producto
            </button>
        </div>

        <!-- GRID DE PRODUCTOS -->
        <div id="productosContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="text-center py-12">
                <p class="text-gray-500">Cargando productos...</p>
            </div>
        </div>
    </div>

    <!-- MODAL PRODUCTO -->
    <div id="modalProducto" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-[var(--color-bg-card)] border-b border-[var(--color-border)] p-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white" id="modalProductoTitulo">
                    Nuevo Producto
                </h2>
                <button onclick="cerrarModalProducto()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    ✕
                </button>
            </div>

            <form id="formProducto" class="p-6 space-y-4" onsubmit="guardarProducto(event)">
                <input type="hidden" id="productoId">

                <div>
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

                <div>
                    <label class="form-label">
                        Descripción
                    </label>
                    <textarea 
                        id="productoDescripcion" 
                        rows="3"
                        class="form-input"
                    ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
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

                    <div>
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
                <div class="bg-secondary-50/30 dark:bg-secondary-900/10 border border-secondary-200/60 dark:border-secondary-900/40 rounded-lg p-4">
                    <h3 class="font-semibold text-secondary-900 dark:text-secondary-300 mb-3">
                        💰 Cálculo de Costo
                    </h3>

                    <div id="costoDesglose" class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <div class="flex justify-between">
                            <span>Costo Filamento (PLA):</span>
                            <span class="font-mono" id="costoPLA">$0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Costo Energía (Luz):</span>
                            <span class="font-mono" id="costoLuz">$0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Costo Máquina (Horas):</span>
                            <span class="font-mono" id="costoMaquina">$0.00</span>
                        </div>
                        <div class="border-t border-secondary-200/60 dark:border-secondary-900/40 pt-2 mt-2">
                            <div class="flex justify-between font-semibold text-secondary-900 dark:text-secondary-300">
                                <span>COSTO TOTAL:</span>
                                <span class="font-mono" id="costoTotal">$0.00</span>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <span>Ganancia (50%):</span>
                            <span class="font-mono text-green-600 dark:text-green-400 font-semibold" id="costoGanancia">$0.00</span>
                        </div>
                        <div class="border-t border-secondary-200/60 dark:border-secondary-900/40 pt-2 mt-2">
                            <div class="flex justify-between font-bold text-lg text-amber-600 dark:text-amber-400">
                                <span>PRECIO VENTA:</span>
                                <span class="font-mono" id="costoVenta">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
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

                <div class="flex gap-4 pt-4 border-t border-[var(--color-border)]">
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
