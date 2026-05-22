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
                👥 Gestión de Clientes
            </h1>
            <p class="body-text">
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
    </main>

    <!-- MODAL CLIENTE -->
    <div id="modalCliente" class="modal-backdrop hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalClienteTitulo">
                    Nuevo Cliente
                </h2>
                <button onclick="cerrarModalCliente()" class="modal-close">
                    ✕
                </button>
            </div>

            <form id="formCliente" onsubmit="guardarCliente(event)">
                <div class="modal-body">
                    <input type="hidden" id="clienteId">

                    <div class="form-group">
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

                    <div class="form-group">
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

                    <div class="form-group">
                        <label class="form-label">
                            Teléfono
                        </label>
                        <input 
                            type="tel" 
                            id="clienteTelefono" 
                            class="form-input"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Empresa
                        </label>
                        <input 
                            type="text" 
                            id="clienteEmpresa" 
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
