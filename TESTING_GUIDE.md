# 🧪 Guía de Testing - tienda3d v2.0

## 📱 Testing en Diferentes Dispositivos

### 1. Desktop - Chrome/Firefox/Safari
```
URL: http://localhost:8000
Tamaño: 1440px x 900px (Full HD)

VERIFICAR:
✓ Navbar horizontal completa
✓ Hamburger menu NO visible
✓ 3 tabs (Pedidos, Catálogo, Configuración) visibles
✓ Logo con gradiente Purple → Blue
✓ Toggle dark mode (esquina superior derecha)
✓ Cards de métricas en 3 columnas
✓ Tabla de pedidos completa con scroll
✓ Botones con gradientes
✓ Hover effects en cards y botones
```

### 2. Tablet - iPad
```
DevTools: Responsive Design Mode (Ctrl+Shift+M)
Tamaño: 768px x 1024px

VERIFICAR:
✓ Hamburger menu aparece (o desaparece según diseño)
✓ Grid de métricas en 2 columnas
✓ Menú responsivo
✓ Navegación funcional
✓ Tabla con scroll horizontal si es necesario
```

### 3. Mobile - iPhone 12
```
DevTools: Responsive Design Mode (Ctrl+Shift+M)
Tamaño: 390px x 844px

VERIFICAR HAMBURGER MENU:
1. Cargar página
2. Ver hamburger menu (3 líneas) visible en navbar
3. Hacer clic en hamburger
4. Verificar animación:
   - Línea superior rota 45° hacia arriba
   - Línea central desaparece
   - Línea inferior rota -45° hacia abajo
   - Forma una "X"
5. Menú desplegable aparece
6. Hacer clic en un tab (ej: "📦 Catálogo")
7. Verificar menú se cierra automáticamente
8. Hacer clic de nuevo en hamburger
9. Verificar que la "X" vuelve a ser 3 líneas
```

### 4. Mobile - Pantalla Pequeña
```
DevTools: Responsive Design Mode
Tamaño: 320px x 568px

VERIFICAR:
✓ Todo es legible (no recortado)
✓ Botones 44px mínimo
✓ Texto escalado correctamente
✓ Hamburger menu funciona
✓ Modales fullscreen
✓ Spacing/padding apropiado
✓ Sin scroll horizontal innecesario
```

---

## 🎨 Testing Visual

### Colores
```
LIGHT MODE:
- Primary (Purple):      #9333ea  ✓
- Secondary (Sky Blue):  #0ea5e9  ✓
- Success (Green):       #22c55e  ✓
- Error (Red):           #ef4444  ✓

DARK MODE:
- Primary (Purple Light): #a855f7 ✓
- Secondary (Sky Blue):   #38bdf8 ✓
- Background:             #0f172a ✓
```

### Gradientes
```
Logo y botones: Purple → Sky Blue
Verificar que los gradientes sean suaves
```

### Animaciones
```
1. Fade in al cargar página
2. Slide up al cambiar tab
3. Hover en botones (ripple effect)
4. Hamburger menu rotación suave
5. Loading spinner girando
```

---

## 🧩 Testing Funcional

### Cambio de Tabs
```
✓ Clic en "📋 Pedidos"  → Carga sección pedidos
✓ Clic en "📦 Catálogo" → Carga catálogo
✓ Clic en "⚙️ Configuración" → Carga settings
✓ Animación slide up en cada cambio
```

### Dark Mode
```
✓ Clic en toggle oscuro → Active dark mode
✓ Colores invertidos
✓ Guarda preferencia (localStorage)
✓ Se mantiene al recargar página
```

### Hamburger Menu (Mobile)
```
✓ Visible en < 768px
✓ Oculto en > 768px
✓ Animación suave
✓ Se abre/cierra correctamente
✓ Se cierra al seleccionar tab
✓ Z-index correcto (sobre navbar)
```

### Responsive
```
Resize navegador:
✓ En 768px → Hamburger aparece/desaparece
✓ En 480px → Layout cambia
✓ En 320px → Sin scroll horizontal
✓ Transiciones fluidas entre breakpoints
```

---

## 📋 Checklist de Testing

### Desktop (1440px)
- [ ] Navbar horizontal completa
- [ ] Hamburger menu oculto
- [ ] 3 tabs visibles
- [ ] Logo con gradiente
- [ ] Tablas sin scroll necesario
- [ ] Botones con hover effect
- [ ] Dark mode funciona
- [ ] Modales centradas

### Tablet (768px)
- [ ] Hamburger menu visible
- [ ] Grid de 2 columnas
- [ ] Menú desplegable funciona
- [ ] Tabla con scroll h
- [ ] Botones 44px
- [ ] Touch-friendly

### Mobile (390px - iPhone)
- [ ] Hamburger menu animado (X)
- [ ] Menú se abre/cierra
- [ ] Botones 44px
- [ ] Layout columna única
- [ ] Tabla scrollable
- [ ] No scroll h innecesario
- [ ] Modales fullscreen
- [ ] Legibilidad óptima

### Mobile Mini (320px)
- [ ] Todo legible
- [ ] Sin recortes
- [ ] Hamburger funciona
- [ ] Spacing correcto
- [ ] Sin errores visuales

---

## 🐛 Bugs Comunes a Verificar

```
1. ❌ Hamburger menu no anima
   → Verificar CSS en animations.css
   
2. ❌ Menú no se abre/cierra en móvil
   → Verificar JavaScript en app.js
   
3. ❌ Gradientes no se ven
   → Verificar colores en :root de custom.css
   
4. ❌ Texto recortado en móvil
   → Verificar breakpoint @media (max-width: 480px)
   
5. ❌ Botones no son 44px
   → Verificar padding/altura en mobile
   
6. ❌ Menú no se cierra al hacer clic
   → Verificar event listener en app.js
   
7. ❌ Animaciones no suaves
   → Verificar transiciones en custom.css
   
8. ❌ Logo distorsionado
   → Verificar aspect ratio del logo-icon
```

---

## 📊 Performance

```
VERIFICAR:
✓ Página carga en < 2 segundos
✓ Animaciones fluidas (60fps)
✓ Sin lag en mobile
✓ Responde rápido a clicks
✓ CSS no es demasiado pesado
✓ Sin JavaScript bloqueante
```

---

## 🌐 Navegadores Recomendados

```
DESKTOP:
✓ Chrome 90+
✓ Firefox 88+
✓ Safari 14+
✓ Edge 90+

MOBILE:
✓ Chrome móvil
✓ Firefox móvil
✓ Safari iOS 14+
✓ Samsung Internet
```

---

## 📝 Reporte de Testing

Si encuentras problemas:

```
1. Describe el problema
2. Screenshot o video
3. Dispositivo/Navegador/Tamaño
4. Pasos para reproducir
5. Resultado esperado vs actual
```

---

## ✅ Pasar a Producción

Cuando todo esté correcto:

```bash
1. Crear backup v2.0.zip
   zip -r tienda3d_ver_2.0.zip .

2. Enviar a producción
3. Notificar usuarios sobre cambios
4. Monitorear errors en consola
```

---

**Última actualización:** 20 de Mayo de 2026  
**Estado:** Testing
