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
    <script src="assets/js/common.js"></script>
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/cruds.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
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
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white"
            >
            <button 
                onclick="abrirModalProducto()" 
                class="px-6 py-2 bg-gradient-to-r from-purple-600 to-sky-600 hover:from-purple-700 hover:to-sky-700 text-white font-semibold rounded-lg transition"
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 flex justify-between items-center">
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nombre del Producto *
                    </label>
                    <input 
                        type="text" 
                        id="productoNombre" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Descripción
                    </label>
                    <textarea 
                        id="productoDescripcion" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Peso (gramos) *
                        </label>
                        <input 
                            type="number" 
                            id="productoPeso" 
                            step="0.01"
                            min="0.1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                            onchange="recalcularCosto()"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tiempo (minutos) *
                        </label>
                        <input 
                            type="number" 
                            id="productoTiempo" 
                            step="1"
                            min="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required
                            onchange="recalcularCosto()"
                        >
                    </div>
                </div>

                <!-- CALCULADOR COSTO EN VIVO -->
                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                    <h3 class="font-semibold text-purple-900 dark:text-purple-300 mb-3">
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
                        <div class="border-t border-purple-200 dark:border-purple-800 pt-2 mt-2">
                            <div class="flex justify-between font-semibold text-purple-900 dark:text-purple-300">
                                <span>COSTO TOTAL:</span>
                                <span class="font-mono" id="costoTotal">$0.00</span>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <span>Ganancia (50%):</span>
                            <span class="font-mono text-green-600 dark:text-green-400 font-semibold" id="costoGanancia">$0.00</span>
                        </div>
                        <div class="border-t border-purple-200 dark:border-purple-800 pt-2 mt-2">
                            <div class="flex justify-between font-bold text-lg text-sky-600 dark:text-sky-400">
                                <span>PRECIO VENTA:</span>
                                <span class="font-mono" id="costoVenta">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Imagen (URL)
                    </label>
                    <input 
                        type="text" 
                        id="productoImagen" 
                        placeholder="https://ejemplo.com/imagen.jpg"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                </div>

                <div class="flex gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        type="submit" 
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-600 to-sky-600 hover:from-purple-700 hover:to-sky-700 text-white font-semibold rounded-lg transition"
                    >
                        💾 Guardar
                    </button>
                    <button 
                        type="button"
                        onclick="cerrarModalProducto()"
                        class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/productos.js"></script>
    <script>
        // Simple dark mode toggle (sin cargar app.js)
        document.addEventListener('DOMContentLoaded', () => {
            const key = 'tienda3d-dark-mode';
            const toggle = document.getElementById('dark-mode-toggle-simple');
            
            if (toggle) {
                toggle.addEventListener('click', () => {
                    const html = document.documentElement;
                    html.classList.toggle('dark');
                    const isDark = html.classList.contains('dark');
                    localStorage.setItem(key, isDark.toString());
                });
            }
        });
    </script>
</body>
</html>
