# 📁 Estructura del Proyecto tienda3d v2.0

## Árbol de Directorios

```
tienda3d/
│
├── 📄 index.php                      # Dashboard principal (HTML5 + Hamburger menu)
├── 📄 CHANGELOG.md                   # Historial de versiones
├── 📄 ACTUALIZACION_v2.0.md         # Detalles de cambios v2.0
├── 📄 RESUMEN_v2.0.txt              # Resumen visual en ASCII
├── 📄 TESTING_GUIDE.md              # Guía de testing
├── 📄 migrate-v2.0.sh               # Script de migración
│
├── 📁 api/                          # API REST (PHP)
│   ├── 📄 pedidos.php               # CRUD Pedidos (GET/POST/PUT/DELETE)
│   ├── 📄 productos.php             # CRUD Productos
│   ├── 📄 clientes.php              # CRUD Clientes
│   ├── 📄 parametros.php            # Configuración del sistema
│   └── 📄 auth.php                  # Autenticación
│
├── 📁 includes/                     # Includes y utilidades
│   └── 📄 db.php                    # Conexión MySQL + funciones globales
│
├── 📁 assets/                       # Recursos estáticos
│   ├── 📁 css/
│   │   ├── 📄 custom.css            # ⭐ Estilos principales (v2.0 modernizado)
│   │   └── 📄 animations.css        # ⭐ Animaciones CSS puras (NUEVO v2.0)
│   │
│   └── 📁 js/
│       ├── 📄 app.js                # ⭐ JavaScript principal (con hamburger menu)
│       └── 📄 mobile.js             # (Reservado para lógica mobile específica)
│
├── 📁 database/                     # Scripts SQL
│   └── 📄 schema.sql                # Estructura de base de datos
│
├── 📁 backups/                      # (Crear manualmente si es necesario)
│   ├── tienda3d_ver_1.0.zip        # Backup v1.0 (RECOMENDADO crear)
│   └── tienda3d_ver_2.0.zip        # Backup v2.0 (DESPUÉS de testing)
│
└── 📁 .git/                         # Repositorio Git (si aplica)
    └── ...
```

---

## 📊 Detalles de Archivos Clave

### `index.php` (540 líneas aprox)
```
SECCIONES:
├── Head
│   ├── Meta tags
│   ├── Tailwind CSS
│   ├── Google Fonts
│   ├── Custom CSS (v2.0)
│   └── Animations CSS (NUEVO)
│
├── Body
│   ├── Navbar (MEJORADA v2.0)
│   │   ├── Logo con gradiente
│   │   ├── 🍔 Hamburger Menu (NUEVO)
│   │   ├── Tabs navegación
│   │   └── Dark mode toggle
│   │
│   ├── Main container
│   │   ├── Sección Pedidos
│   │   ├── Sección Catálogo
│   │   └── Sección Configuración
│   │
│   └── Modal Nuevo Pedido
│
└── Scripts
    └── app.js (con hamburger menu)
```

### `assets/css/custom.css` (1200+ líneas)
```
SECCIONES:
├── Variables CSS
│   ├── Colores Light Mode (Purple + Sky Blue)
│   └── Colores Dark Mode
│
├── Estilos Base
│   ├── HTML, Body, *
│   └── Tipografía
│
├── Navbar (MODERNIZADA v2.0)
│   ├── Logo con gradient text
│   ├── 🍔 Hamburger Menu CSS
│   ├── Tabs navegación
│   └── Dark mode toggle
│
├── Main Container
│   ├── Layout principal
│   └── Spacing
│
├── Componentes
│   ├── Métricas/Cards
│   ├── Botones
│   ├── Formularios
│   ├── Modal
│   ├── Tabla
│   └── Catálogo
│
├── Responsive (v2.0)
│   ├── @media (768px) - Tablet
│   └── @media (480px) - Mobile
│
└── Utilidades
    ├── Clases helper
    └── Estados
```

### `assets/css/animations.css` (200 líneas)
```
NUEVO EN v2.0 - Animaciones CSS puras:

KEYFRAMES:
├── fadeIn
├── slideUpFadeIn
├── slideDownFadeIn
├── slideLeftFadeIn
├── slideRightFadeIn
├── scaleIn
├── pulse
├── spin
├── shimmer
├── hamburgerTop
├── hamburgerMiddle
└── hamburgerBottom

APLICADAS A:
├── Tab sections
├── Cards
├── Botones
├── Modales
├── Loading spinner
└── 🍔 Hamburger menu
```

### `assets/js/app.js` (900+ líneas)
```
SECCIONES:
├── Configuración Global
│   ├── CONFIG object
│   └── AppState
│
├── Utilidades
│   ├── apiRequest()
│   ├── formatCurrency()
│   ├── formatDate()
│   ├── showToast()
│   └── ...
│
├── ⭐ NUEVO v2.0: Hamburger Menu
│   ├── Toggle hamburger
│   ├── Abrir/cerrar menú
│   └── Cerrar al seleccionar tab
│
├── Gestión de Pedidos
│   ├── cargarPedidos()
│   ├── crearPedido()
│   ├── editarPedido()
│   └── eliminarPedido()
│
├── Gestión de Productos
│   ├── cargarProductos()
│   └── renderizarProductos()
│
├── Dark Mode
│   ├── initDarkMode()
│   └── toggleDarkMode()
│
├── Inicialización
│   └── DOMContentLoaded event
│
└── Funciones auxiliares
```

### `includes/db.php` (80 líneas)
```
FUNCIONES:
├── getDBConnection()        # Conexión MySQL
├── jsonResponse()           # Respuesta estándar
├── verifyRole()             # Verificación de permisos
├── escapeString()           # Sanitización

CONSTANTES:
├── DB_HOST
├── DB_NAME
├── DB_USER
├── DB_PASS
└── DB_CHARSET
```

### `api/pedidos.php` (430 líneas)
```
ENDPOINTS:
├── GET  /api/pedidos.php         → obtenerPedidos()
├── GET  /api/pedidos.php?id=X    → obtenerPedido()
├── POST /api/pedidos.php         → crearPedido()
├── PUT  /api/pedidos.php?id=X    → actualizarPedido()
└── DELETE /api/pedidos.php?id=X  → eliminarPedido()

FUNCIONES:
├── obtenerPedidos()         # Con filtros y búsqueda
├── calcularMetricas()       # Total, costo, ganancia
├── crearPedido()            # Con cálculo de costos
└── ...
```

---

## 🎨 Paleta de Colores v2.0

### CSS Variables (`:root`)

**LIGHT MODE:**
```css
--color-primary: #9333ea;          /* Purple moderno */
--color-primary-dark: #7e22ce;     /* Purple oscuro */
--color-secondary: #0ea5e9;        /* Sky Blue */
--color-secondary-dark: #0284c7;   /* Blue oscuro */
--color-success: #22c55e;
--color-warning: #f59e0b;
--color-error: #ef4444;
--color-bg: #f8fafc;
--color-bg-card: #ffffff;
--color-text: #1e293b;
```

**DARK MODE:**
```css
--color-primary: #a855f7;          /* Purple más claro */
--color-secondary: #38bdf8;        /* Sky Blue más claro */
--color-bg: #0f172a;
--color-bg-card: #1e293b;
--color-text: #f1f5f9;
```

---

## 📱 Breakpoints Responsive

```css
DESKTOP:  1024px+     /* Navbar completa */
TABLET:   768-1023px  /* Transición */
MOBILE:   480-767px   /* Hamburger menu */
MINI:     < 480px     /* Fullscreen */

@media (max-width: 768px)  → Hamburger activo
@media (max-width: 480px)  → Mobile mini
```

---

## 🍔 Hamburger Menu - Código

### HTML (`index.php`)
```html
<button id="hamburger-menu" class="hamburger-menu">
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
</button>

<div class="nav-tabs" id="nav-tabs">
    <!-- Tabs aquí -->
</div>
```

### CSS (`animations.css`)
```css
.hamburger-menu.active .hamburger-line:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
}
.hamburger-menu.active .hamburger-line:nth-child(2) {
    opacity: 0;
}
.hamburger-menu.active .hamburger-line:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
}
```

### JavaScript (`app.js`)
```javascript
const hamburgerMenu = document.getElementById('hamburger-menu');
const navTabs = document.getElementById('nav-tabs');

hamburgerMenu.addEventListener('click', () => {
    hamburgerMenu.classList.toggle('active');
    navTabs.classList.toggle('active');
});
```

---

## 📋 Checklist de Archivos

### Modificados en v2.0
- [x] `index.php` - Logo, hamburger menu
- [x] `assets/css/custom.css` - Colores, responsive
- [x] `assets/js/app.js` - Hamburger logic
- [x] `includes/db.php` - Comentarios

### Creados en v2.0
- [x] `assets/css/animations.css` - Animaciones CSS
- [x] `CHANGELOG.md` - Documentación
- [x] `ACTUALIZACION_v2.0.md` - Detalles cambios
- [x] `RESUMEN_v2.0.txt` - Resumen ASCII
- [x] `TESTING_GUIDE.md` - Guía testing

### No Modificados (compatibles)
- [x] `api/pedidos.php` - API funciona igual
- [x] `api/productos.php` - API funciona igual
- [x] `api/clientes.php` - API funciona igual
- [x] `database/schema.sql` - BD sin cambios

---

## 🚀 Cómo Usar

### Instalación
```bash
1. Clonar: git clone ...
2. Configurar db.php
3. Importar schema.sql
4. Abrir index.php en navegador
```

### Desarrollo Local
```bash
php -S localhost:8000
# Abrir http://localhost:8000
```

### Testing Responsive
```
DevTools: F12 o Ctrl+Shift+I
Responsive: Ctrl+Shift+M
Probar en: 320px, 480px, 768px, 1024px, 1440px
```

### Deploy
```bash
1. Crear backup v2.0.zip
2. Subir a servidor
3. Configurar credenciales BD
4. Testing en producción
```

---

## 📊 Estadísticas

```
Líneas de código:
├── index.php:          540 líneas
├── custom.css:        1200+ líneas
├── animations.css:     200 líneas
├── app.js:            900+ líneas
└── Total Frontend:    2840+ líneas

Líneas de API:
├── api/pedidos.php:    430 líneas
├── api/productos.php:  260 líneas
├── api/clientes.php:   267 líneas
└── Total API:          957 líneas

TOTAL PROYECTO:        ~4000 líneas
```

---

**Última actualización:** 20 de Mayo de 2026  
**Versión:** 2.0.0  
**Estado:** Completada
