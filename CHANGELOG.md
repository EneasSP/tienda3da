# tienda3d - Sistema de Gestión de Impresión 3D

## Versiones y Cambios

### v2.0.0 - Modernización UI/UX (Actual)
**Cambios principales:**
- ✅ Rebranding: PrintCraft → tienda3d
- ✅ Interfaz moderna con gradientes púrpura y azul
- ✅ 100% Responsiva con mobile-first design
- ✅ Hamburger menu animado en dispositivos móviles (<768px)
- ✅ Animaciones CSS puras (sin dependencias externas)
- ✅ Colores actualizados: Purple (#9333ea) + Sky Blue (#0ea5e9)
- ✅ Navbar mejorado con efectos hover
- ✅ Tarjetas con bordes coloreados y sombras suaves
- ✅ Botones con gradientes y micro-interacciones
- ✅ Loading spinner mejorado
- ✅ Modal con animaciones suaves

**Archivos modificados:**
- `index.php` - Estructura HTML + hamburger menu
- `assets/css/custom.css` - Estilos modernos y responsive
- `assets/css/animations.css` - Animaciones CSS puras (NUEVO)
- `assets/js/app.js` - Lógica hamburger menu + actualización de comentarios
- `includes/db.php` - Actualización de comentarios de branding

**Breakpoints Responsive:**
- Desktop: 1024px+
- Tablet: 768px - 1023px  
- Mobile: 480px - 767px
- Mobile Pequeño: < 480px

---

### v1.0.0 - Release Inicial
**Características:**
- Dashboard principal con gestión de pedidos
- CRUD completo (Pedidos, Productos, Clientes)
- Cálculo automático de costos y márgenes
- Dark mode nativo
- API REST con endpoints documentados
- Validación de datos robusta
- Interfaz básica con Tailwind CSS

---

## Estructura del Proyecto

```
tienda3d/
├── index.php                 # Dashboard principal (HTML5)
├── api/
│   ├── pedidos.php          # CRUD Pedidos
│   ├── productos.php        # CRUD Productos
│   ├── clientes.php         # CRUD Clientes
│   ├── parametros.php       # Configuración del sistema
│   └── auth.php             # Autenticación
├── includes/
│   └── db.php               # Conexión MySQL + funciones globales
├── assets/
│   ├── css/
│   │   ├── custom.css       # Estilos principales (v2.0)
│   │   └── animations.css   # Animaciones CSS puras (NUEVO)
│   └── js/
│       ├── app.js           # Lógica principal + hamburger menu
│       └── mobile.js        # (Reservado para lógica mobile-specific)
└── database/
    └── schema.sql           # Estructura de base de datos
```

---

## Instalación y Configuración

### Requisitos
- PHP 7.4+
- MySQL 5.7+
- Navegador moderno (Chrome, Firefox, Safari, Edge)

### Pasos de instalación

1. **Clonar/descargar el repositorio**
```bash
cd /ruta/del/proyecto/tienda3d
```

2. **Configurar la base de datos**
```bash
mysql -u usuario -p < database/schema.sql
```

3. **Actualizar credenciales en `includes/db.php`**
```php
define('DB_HOST', 'tu-host');
define('DB_NAME', 'tu-base-datos');
define('DB_USER', 'tu-usuario');
define('DB_PASS', 'tu-contraseña');
```

4. **Iniciar servidor local**
```bash
php -S localhost:8000
```

5. **Abrir en navegador**
```
http://localhost:8000
```

---

## Características Principales

### Dashboard (v2.0)
- **Métricas en tiempo real:**
  - Total Facturado
  - Costo de Producción
  - Ganancia Neta
  
- **Gestión de Pedidos:**
  - Búsqueda por cliente/empresa
  - Filtros por estado (Activos/Finalizados/Todos)
  - Modal para crear/editar
  - Cálculo automático de costos

### Catálogo
- Listado de productos con especificaciones
- Peso (gramos) y tiempo de impresión
- Búsqueda y filtrado

### Configuración
- Parámetros ajustables de costos:
  - Precio PLA por kg
  - Costo de luz por kWh
  - Costo de hora máquina
  - Porcentaje de ganancia

---

## Responsive Design (v2.0)

### Móvil (<768px)
- ✅ Hamburger menu con animación de cruz
- ✅ Menú desplegable con transiciones suaves
- ✅ Botones touch-friendly (44px mínimo)
- ✅ Layout en columna única
- ✅ Tablas con scroll horizontal

### Tablet (768px - 1023px)
- ✅ Navegación híbrida
- ✅ Grid de 2 columnas en métricas
- ✅ Sidebar colapsable

### Desktop (1024px+)
- ✅ Navegación horizontal completa
- ✅ Grid de 3 columnas en métricas
- ✅ Componentes en tamaño completo

---

## Paleta de Colores (v2.0)

### Modo Claro
- **Primary:** #9333ea (Purple)
- **Secondary:** #0ea5e9 (Sky Blue)
- **Success:** #22c55e (Green)
- **Warning:** #f59e0b (Amber)
- **Error:** #ef4444 (Red)
- **Background:** #f8fafc (Light Slate)

### Modo Oscuro
- **Primary:** #a855f7 (Purple Light)
- **Secondary:** #38bdf8 (Sky Blue Light)
- **Background:** #0f172a (Dark Navy)
- **Card:** #1e293b (Dark Slate)

---

## API Endpoints

### Pedidos
```
GET    /api/pedidos.php              # Listar pedidos
GET    /api/pedidos.php?id=X         # Obtener pedido
POST   /api/pedidos.php              # Crear pedido
PUT    /api/pedidos.php?id=X         # Actualizar pedido
DELETE /api/pedidos.php?id=X         # Eliminar pedido
```

### Productos
```
GET    /api/productos.php            # Listar productos
GET    /api/productos.php?id=X       # Obtener producto
POST   /api/productos.php            # Crear producto
PUT    /api/productos.php?id=X       # Actualizar producto
DELETE /api/productos.php?id=X       # Eliminar producto
```

### Clientes
```
GET    /api/clientes.php             # Listar clientes
GET    /api/clientes.php?id=X        # Obtener cliente
POST   /api/clientes.php             # Crear cliente
PUT    /api/clientes.php?id=X        # Actualizar cliente
DELETE /api/clientes.php?id=X        # Eliminar cliente
```

---

## Animaciones CSS Puras (v2.0)

Sin dependencias externas:
- `fadeIn` - Fade simple
- `slideUpFadeIn` - Slide hacia arriba con fade
- `slideDownFadeIn` - Slide hacia abajo con fade
- `scaleIn` - Zoom in
- `pulse` - Pulso continuo
- `spin` - Rotación continua
- `hamburgerTop/Bottom` - Animaciones del menú

---

## Seguridad

⚠️ **NOTA IMPORTANTE:**
- Credenciales de BD en `includes/db.php` deben ser protegidas
- CORS actualmente permite origen `*`
- En producción, restringir CORS a dominios específicos
- Implementar autenticación y autorización

---

## Roadmap Futuro

- [ ] Sistema de autenticación completo
- [ ] Exportar reportes a PDF
- [ ] Gráficos de rendimiento
- [ ] Integración de pagos
- [ ] Aplicación móvil nativa
- [ ] API GraphQL
- [ ] Testing automático

---

## Licencia

Proyecto propietario - tienda3d

---

**Última actualización:** 20 de Mayo de 2026
**Versión actual:** 2.0.0
