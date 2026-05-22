/**
/**
 * JavaScript Común - tienda3d
 * Manejo global de navegación, menú hamburguesa y sincronización de Modo Oscuro
 */

const COMMON_CONFIG = {
    DARK_MODE_KEY: 'tienda3d-dark-mode'
};

// Inicializar Dark Mode lo antes posible para evitar parpadeos
function initGlobalDarkMode() {
    const stored = localStorage.getItem(COMMON_CONFIG.DARK_MODE_KEY);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = stored === 'true' || (!stored && prefersDark);
    
    if (isDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    
    // Sincronizar el estado visual del botón una vez que el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('dark-mode-toggle');
        if (toggleBtn) {
            toggleBtn.innerHTML = isDark ? '☀️' : '🌙';
            toggleBtn.classList.toggle('active', isDark);
        }
    });
}

// Ejecutar inmediatamente
initGlobalDarkMode();

// Alternar Modo Oscuro
function toggleGlobalDarkMode() {
    const html = document.documentElement;
    html.classList.toggle('dark');
    const isDark = html.classList.contains('dark');
    
    localStorage.setItem(COMMON_CONFIG.DARK_MODE_KEY, isDark.toString());
    
    const toggleBtn = document.getElementById('dark-mode-toggle');
    if (toggleBtn) {
        toggleBtn.innerHTML = isDark ? '☀️' : '🌙';
        toggleBtn.classList.toggle('active', isDark);
    }
}

// Obtener pestaña activa desde la URL
function getTabFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('tab') || 'pedidos';
}

// Actualizar clases de tabs activos
function updateActiveNavbarItem() {
    const path = window.location.pathname.split('/').pop();
    let activeTab = '';

    if (path === 'clientes.php' || path.includes('clientes')) {
        activeTab = 'clientes';
    } else if (path === 'productos.php' || path.includes('productos')) {
        activeTab = 'productos';
    } else if (path === '' || path === 'index.php' || !path.includes('.php')) {
        activeTab = getTabFromUrl();
    }

    document.querySelectorAll('.nav-tab').forEach(tab => {
        const tabData = tab.dataset.tab;
        tab.classList.toggle('active', tabData === activeTab);
    });
}

// Configuración general de eventos de UI
document.addEventListener('DOMContentLoaded', () => {
    // Configurar hamburger menu
    const hamburgerMenu = document.getElementById('hamburger-menu');
    const navTabs = document.getElementById('nav-tabs');
    
    if (hamburgerMenu && navTabs) {
        hamburgerMenu.addEventListener('click', () => {
            hamburgerMenu.classList.toggle('active');
            navTabs.classList.toggle('active');
        });
        
        // Cerrar menú al hacer clic en un tab
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                hamburgerMenu.classList.remove('active');
                navTabs.classList.remove('active');
            });
        });
    }
    
    // Configurar botón de modo oscuro
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', toggleGlobalDarkMode);
    }
    
    // Interceptar navegación por pestañas en index.php
    const path = window.location.pathname.split('/').pop();
    const isIndex = path === '' || path === 'index.php' || !path.includes('.php');
    
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', (e) => {
            const href = tab.getAttribute('href');
            if (isIndex && href && href.includes('tab=')) {
                e.preventDefault();
                const tabId = href.split('tab=')[1];
                
                // Actualizar URL sin recargar
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabId;
                window.history.pushState({ path: newUrl }, '', newUrl);
                
                // Llamar a switchTab de app.js si existe
                if (typeof switchTab === 'function') {
                    switchTab(tabId);
                }
            }
        });
    });
    
    // Inicializar estado del menú
    updateActiveNavbarItem();
});

// Escuchar evento de popstate para cambios en la URL (atrás/adelante)
window.addEventListener('popstate', () => {
    updateActiveNavbarItem();
    const path = window.location.pathname.split('/').pop();
    const isIndex = path === '' || path === 'index.php' || !path.includes('.php');
    if (isIndex && typeof switchTab === 'function') {
        switchTab(getTabFromUrl());
    }
});

/**
 * Escapa HTML para prevenir XSS
 * @param {string} text - Texto a escapar
 * @returns {string} Texto escapado
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Muestra una notificación toast
 * @param {string} message - Mensaje
 * @param {string} type - Tipo (success, error, warning)
 * @param {string} title - Título (opcional)
 */
function showToast(message, type = 'info', title = '') {
    const container = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    const titles = {
        success: 'Éxito',
        error: 'Error',
        warning: 'Advertencia',
        info: 'Información'
    };
    
    toast.innerHTML = `
        <span class="toast-icon">${icons[type]}</span>
        <div class="toast-content">
            <div class="toast-title">${title || titles[type]}</div>
            <div class="toast-message">${escapeHtml(message)}</div>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

/**
 * Crea el contenedor de toasts si no existe
 * @returns {HTMLElement}
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

