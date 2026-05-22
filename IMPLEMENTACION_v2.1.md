# 📦 GUÍA DE IMPLEMENTACIÓN v2.1 - CRUDs + Sistema de Costeo

## ✅ Lo que se implementó

### 1. **APIs REST (Backend)**

#### `api/costo.php` ✨ NUEVO
- Calcula costo, ganancia y precio de venta automáticamente
- GET `/api/costo.php?peso=100&tiempo=60`
- Retorna desglose completo:
  - Costo Filamento (PLA)
  - Costo Energía (Luz)
  - Costo Máquina (Horas)
  - Ganancia estimada (50%)
  - Precio de venta final

#### `api/clientes.php` (Ya existía - Sin cambios)
- GET: Listar todos los clientes
- POST: Crear cliente
- PUT: Actualizar cliente
- DELETE: Eliminar cliente (soft delete)

#### `api/productos.php` (Ya existía - Sin cambios)
- GET: Listar todos los productos
- POST: Crear producto
- PUT: Actualizar producto
- DELETE: Eliminar producto

### 2. **Mejoras en Base de Datos**

Archivo: `database/migrations_v2.1.sql` ✨ NUEVO

#### Cambios:
- ✅ Agregar campo `activo` en tabla `productos`
- ✅ Agregar campo `activo` en tabla `clientes`
- ✅ Crear tabla `imagenes_productos` (para múltiples imágenes)
- ✅ Crear procedimiento `sp_calcular_costo_producto`
- ✅ Crear vista `v_productos_con_costo` (cálculo automático de precios)
- ✅ Crear vista `v_clientes_activos` (solo clientes activos)
- ✅ Agregar índices para búsqueda rápida

### 3. **Páginas Frontend**

#### `clientes.php` ✨ NUEVO
- Tabla de clientes con búsqueda
- Modal para crear/editar clientes
- Botones: Editar, Eliminar
- Validaciones en tiempo real
- Responsive (mobile + desktop)

#### `productos.php` ✨ NUEVO
- Grid de productos con imágenes
- Modal para crear/editar productos
- **Calculador de costo en vivo:**
  - Actualiza precio al cambiar peso/tiempo
  - Muestra desglose completo
  - Precio sugerido automático
- Botones: Editar, Eliminar
- Validaciones en tiempo real
- Responsive (mobile + desktop)

### 4. **JavaScript**

#### `assets/js/clientes.js` ✨ NUEVO
- Cargar/listar clientes
- Filtrar por búsqueda
- Crear cliente
- Editar cliente
- Eliminar cliente
- Notificaciones (éxito/error)

#### `assets/js/productos.js` ✨ NUEVO
- Cargar/listar productos
- Filtrar por búsqueda
- Crear producto con calculador de costo
- Editar producto
- Eliminar producto
- Cálculo automático de costo/precio
- Notificaciones (éxito/error)

### 5. **Estilos CSS**

#### `assets/css/cruds.css` ✨ NUEVO
- 300+ líneas de CSS responsivo
- Estilos para: botones, tablas, modales, formularios
- Componentes: badges, info boxes, calculador
- Animaciones suaves
- Dark mode soportado
- Mobile-first approach

### 6. **Actualización de Navegación**

#### `index.php` (Modificado)
- Agregados links a: 👥 Clientes y 🏭 Productos
- Los botones del navbar ahora incluyen estas nuevas secciones
- Mantiene la estructura de hamburger menu en móvil

---

## 🧮 FÓRMULA DE COSTEO

```
Costo Filamento PLA:    peso_kg × precio_pla_kg
Costo Energía:          tiempo_horas × (costo_luz_kwh × 3kW / 60)
Costo Máquina:          tiempo_horas × hora_maquina

COSTO TOTAL = Filamento + Energía + Máquina
GANANCIA = COSTO TOTAL × (ganancia_porcentaje / 100)
PRECIO VENTA = COSTO TOTAL + GANANCIA
```

### Parámetros Configurables (en BD):
- `precio_pla_kg`: 45 ARS/kg (precio filamento)
- `costo_luz_kwh`: 15 ARS/kWh (costo energía)
- `hora_maquina`: 150 ARS/hora (costo máquina)
- `ganancia_porcentaje`: 50% (margen de ganancia)

---

## 📁 ÁRBOL DE ARCHIVOS NUEVOS

```
tienda3d/
├── database/
│   └── migrations_v2.1.sql          ✨ Nuevas tablas y procedimientos
│
├── api/
│   └── costo.php                    ✨ Calculador de costo
│
├── clientes.php                     ✨ Página gestión de clientes
├── productos.php                    ✨ Página gestión de productos
│
├── assets/
│   ├── js/
│   │   ├── clientes.js              ✨ Lógica de clientes
│   │   └── productos.js             ✨ Lógica de productos + costo
│   │
│   └── css/
│       └── cruds.css                ✨ Estilos CRUDs
│
└── assets/images/productos/         ✨ Carpeta para imágenes
```

---

## 🚀 CÓMO USAR

### 1. Aplicar Migraciones BD
```sql
-- Ejecutar en phpMyAdmin o línea de comandos:
mysql -u usuario -p base_datos < database/migrations_v2.1.sql
```

### 2. Acceder a Clientes
```
http://localhost:8000/clientes.php
```
- Crear, buscar, editar, eliminar clientes
- Tabla con búsqueda en tiempo real
- Validación de email único

### 3. Acceder a Productos
```
http://localhost:8000/productos.php
```
- Crear, buscar, editar, eliminar productos
- Grid con imágenes
- **Calculador de costo automático**
- Al cambiar peso o tiempo, se actualiza:
  - Costo PLA
  - Costo Energía
  - Costo Máquina
  - Precio de venta

### 4. API de Costo (desde JavaScript)
```javascript
// Obtener cálculo de costo
fetch('/api/costo.php?peso=100&tiempo=60')
    .then(r => r.json())
    .then(data => {
        console.log('Costo:', data.resultado.costo_total);
        console.log('Precio:', data.resultado.precio_venta);
    });
```

---

## ✨ CARACTERÍSTICAS PRINCIPALES

### Clientes CRUD
- ✅ Listar todos los clientes activos
- ✅ Búsqueda en tiempo real (nombre, email, empresa)
- ✅ Crear cliente con validación de email
- ✅ Editar cliente
- ✅ Eliminar cliente (soft delete)
- ✅ Tabla responsiva en móvil
- ✅ Modal para crear/editar

### Productos CRUD
- ✅ Listar todos los productos
- ✅ Búsqueda en tiempo real (nombre, descripción)
- ✅ Crear producto
- ✅ Editar producto
- ✅ Eliminar producto (soft delete)
- ✅ Subida de imagen (URL)
- ✅ Grid responsive
- ✅ **Calculador de costo en vivo**

### Sistema de Costeo
- ✅ Cálculo automático basado en peso + tiempo
- ✅ Desglose transparente de costos
- ✅ Precio sugerido automático
- ✅ API REST para integración
- ✅ Parámetros configurables en BD
- ✅ Sensibilidad en vivo (cambio peso/tiempo)

---

## 🎨 DISEÑO Y UX

### Colores (continuidad v2.0)
- **Purple** (#9333ea): Primario
- **Sky Blue** (#0ea5e9): Secundario
- **Gradientes**: Purple → Sky Blue

### Componentes
- Tablas con hover effects
- Modales con transiciones suaves
- Botones con gradientes
- Badges para estados
- Notificaciones toast (éxito/error)
- Calculador con desglose visual

### Responsive
- ✅ Mobile: <480px
- ✅ Tablet: 480px-768px
- ✅ Desktop: >768px
- ✅ Hamburger menu en móvil
- ✅ Tabla scroll horizontal en móvil

### Dark Mode
- ✅ Todos los colores adaptados
- ✅ Contraste apropiado
- ✅ Persistencia (localStorage)

---

## 📊 VISTAS CREADAS

```sql
-- Vista: v_productos_con_costo
SELECT id, nombre, peso_gramos, tiempo_minutos,
       costo_pla, costo_luz, costo_maquina,
       costo_total, ganancia_estimada, precio_venta
FROM productos
WHERE activo = 1;

-- Vista: v_clientes_activos
SELECT c.id, c.nombre, c.email, c.telefono, c.empresa,
       COUNT(p.id) AS total_pedidos,
       SUM(p.total) AS total_gastado
FROM clientes c
LEFT JOIN pedidos p ON c.id = p.cliente_id
WHERE c.activo = 1
GROUP BY c.id;
```

---

## 🔒 SEGURIDAD

- ✅ Validación de email (cliente único)
- ✅ Sanitización de inputs (htmlspecialchars)
- ✅ Validación de números (peso > 0, tiempo > 0)
- ✅ Soft delete (no elimina físicamente)
- ✅ CORS headers en APIs
- ✅ Manejo de excepciones

---

## 🧪 TESTING RECOMENDADO

### Frontend
1. Crear cliente: Nombre, Email (único), Teléfono, Empresa
2. Buscar cliente: Usar barra de búsqueda
3. Editar cliente: Cambiar datos
4. Eliminar cliente: Confirmación
5. Crear producto: Nombre, Peso, Tiempo, Imagen
6. Calculador: Cambiar peso/tiempo, ver actualización de precio
7. Editar producto: Cambiar datos
8. Eliminar producto: Confirmación
9. Responsive: Probar en móvil (390px)
10. Dark mode: Toggle y verificar colores

### Backend (APIs)
1. GET /api/clientes.php - Lista completa
2. POST /api/clientes.php - Crear (validar email único)
3. GET /api/costo.php?peso=100&tiempo=60 - Cálculo
4. GET /api/productos.php - Lista completa

---

## 📝 PRÓXIMOS PASOS (v2.2)

- [ ] Dashboard con reportes (clientes, productos, ventas)
- [ ] Exportar a PDF/Excel
- [ ] Historial de cambios de precios
- [ ] Integración con facturación
- [ ] Gráficos de sensibilidad (costo vs peso/tiempo)
- [ ] Carga múltiple de imágenes
- [ ] API de backup automático

---

## 🆘 NOTAS IMPORTANTES

1. **Base de Datos**: Ejecutar `migrations_v2.1.sql` antes de usar
2. **Parámetros**: Ya están configurados en la BD con valores por defecto
3. **Imágenes**: Usar URLs externas o subir a `assets/images/productos/`
4. **Mobile**: Hamburger menu en <768px funciona automáticamente
5. **Dark Mode**: Se guarda en localStorage

---

**Versión**: 2.1.0
**Fecha**: 20 de Mayo de 2026
**Estado**: ✅ IMPLEMENTADO Y LISTO PARA USAR
