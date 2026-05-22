<!-- Barra de navegación superior unificada -->
<nav class="navbar">
    <div class="navbar-content">
        <!-- Logo -->
        <a href="index.php" class="logo">
            <div class="logo-icon">
                <svg class="w-6 h-6 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    <path d="M12 8l-4-2.3M12 8l4-2.3"></path>
                    <circle cx="12" cy="8" r="1.5" fill="currentColor"></circle>
                </svg>
            </div>
            <span>tienda3d</span>
        </a>
        
        <!-- Hamburger Menu (Mobile) -->
        <button id="hamburger-menu" class="hamburger-menu" aria-label="Menú de navegación">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        
        <!-- Tabs de navegación -->
        <div class="nav-tabs" id="nav-tabs">
            <a href="index.php?tab=pedidos" class="nav-tab" data-tab="pedidos">📋 Pedidos</a>
            <a href="index.php?tab=catalogo" class="nav-tab" data-tab="catalogo">📦 Catálogo</a>
            <a href="clientes.php" class="nav-tab" data-tab="clientes">👥 Clientes</a>
            <a href="productos.php" class="nav-tab" data-tab="productos">🏭 Productos</a>
            <a href="index.php?tab=configuracion" class="nav-tab" data-tab="configuracion">⚙️ Configuración</a>
        </div>
        
        <!-- Controles -->
        <div class="navbar-controls">
            <!-- Toggle Dark Mode -->
            <button id="dark-mode-toggle" class="dark-mode-toggle" title="Cambiar modo oscuro">🌙</button>
        </div>
    </div>
</nav>
