<?php
/**
 * PÁGINA - Gestión de Clientes
 * tienda3d v2.1
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - tienda3d</title>
    
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
                👥 Gestión de Clientes
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Administra la cartera de clientes
            </p>
        </div>

        <!-- TOOLBAR -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
            <input 
                type="text" 
                id="searchCliente" 
                placeholder="Buscar cliente..." 
                class="flex-1 form-input"
            >
            <button 
                onclick="abrirModalCliente()" 
                class="btn btn-primary"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span>Nuevo Cliente</span>
            </button>
        </div>

        <!-- TABLA DE CLIENTES -->
        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-lg shadow-md overflow-hidden">
            <table class="tabla-responsive">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Empresa</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="clientesTabla">
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500">Cargando clientes...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL CLIENTE -->
    <div id="modalCliente" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-lg shadow-xl max-w-2xl w-full">
            <div class="border-b border-[var(--color-border)] p-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white" id="modalClienteTitulo">
                    Nuevo Cliente
                </h2>
                <button onclick="cerrarModalCliente()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    ✕
                </button>
            </div>

            <form id="formCliente" class="p-6 space-y-4" onsubmit="guardarCliente(event)">
                <input type="hidden" id="clienteId">

                <div>
                    <label class="form-label">
                        Nombre Completo *
                    </label>
                    <input 
                        type="text" 
                        id="clienteNombre" 
                        class="form-input"
                        required
                    >
                </div>

                <div>
                    <label class="form-label">
                        Email *
                    </label>
                    <input 
                        type="email" 
                        id="clienteEmail" 
                        class="form-input"
                        required
                    >
                </div>

                <div>
                    <label class="form-label">
                        Teléfono
                    </label>
                    <input 
                        type="tel" 
                        id="clienteTelefono" 
                        class="form-input"
                    >
                </div>

                <div>
                    <label class="form-label">
                        Empresa
                    </label>
                    <input 
                        type="text" 
                        id="clienteEmpresa" 
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
                        onclick="cerrarModalCliente()"
                        class="flex-1 btn btn-secondary"
                    >
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/clientes.js"></script>
</body>
</html>
