# 🚀 INICIO RÁPIDO - tienda3d v2.1

## 3 PASOS PARA EMPEZAR

### 1️⃣ EJECUTAR MIGRACIONES BD (5 minutos)

**Opción A: phpMyAdmin**
```
1. Abre phpMyAdmin
2. Selecciona tu base de datos (tienda3d)
3. Click en "SQL"
4. Abre archivo: database/INSTRUCCIONES_MIGRACION_v2.1.sql
5. Copia TODO el contenido
6. Pega en la ventana SQL
7. Click "Ejecutar"
8. ✅ Listo! Verás "Consulta ejecutada exitosamente"
```

**Opción B: Línea de comandos (Terminal/CMD)**
```bash
cd C:\Users\eneas\OneDrive\Documentos\PlatformIO\Projects\tienda3d

# Ejecutar SQL
mysql -u tu_usuario -p tu_base_datos < database/INSTRUCCIONES_MIGRACION_v2.1.sql

# Ingresar contraseña cuando pida
# ✅ Listo!
```

**Verificar que funcionó:**
```sql
-- Ejecutar en phpMyAdmin:
SELECT * FROM productos LIMIT 1;

-- Debe mostrar columna 'activo' al final
-- Si ves 'activo': ✅ Funcionó!
```

---

### 2️⃣ ABRIR EN NAVEGADOR

```
http://localhost:8000/clientes.php      👥 Gestión de Clientes
http://localhost:8000/productos.php     🏭 Gestión de Productos
```

---

### 3️⃣ PROBAR EL CALCULADOR DE COSTO

**En la página de Productos:**

1. Click "Nuevo Producto"
2. Ingresa:
   - Nombre: `Soporte Celular`
   - Peso: `35`
   - Tiempo: `45`
3. Mira abajo: **💰 Cálculo de Costo**

**Deberías ver:**
```
Costo Filamento: $1.58
Costo Energía: $0.56
Costo Máquina: $112.50
─────────────────────
COSTO TOTAL: $114.64
Ganancia (50%): $57.32
─────────────────────
PRECIO VENTA: $171.96
```

**Ahora cambia el peso a 50:**
- El precio se actualiza AUTOMÁTICAMENTE
- ✅ ¡El calculador funciona!

---

## 🎯 FUNCIONES PRINCIPALES

### 👥 Gestión de Clientes

```
http://localhost:8000/clientes.php

Crear cliente:
  1. Click "Nuevo Cliente"
  2. Ingresa: Nombre, Email, Teléfono, Empresa
  3. Click "Guardar"
  4. ✅ Cliente creado

Buscar cliente:
  - Escribe en barra de búsqueda
  - Se filtra en tiempo real

Editar cliente:
  - Click en botón "Editar"
  - Modifica datos
  - Click "Guardar"

Eliminar cliente:
  - Click en "Eliminar"
  - Confirma
  - ✅ Cliente eliminado (soft delete)
```

---

### 🏭 Gestión de Productos

```
http://localhost:8000/productos.php

Crear producto:
  1. Click "Nuevo Producto"
  2. Ingresa: Nombre, Descripción, Peso, Tiempo
  3. (Opcional) URL de imagen
  4. VER CALCULADOR → Actualizarse automáticamente
  5. Click "Guardar"
  6. ✅ Producto creado

Buscar producto:
  - Escribe en barra
  - Se filtra en tiempo real

Editar producto:
  - Click en "Editar"
  - Cambiar datos
  - VER CALCULADOR actualizar
  - Click "Guardar"

Eliminar producto:
  - Click en "Eliminar"
  - Confirma
  - ✅ Producto eliminado

⭐ CALCULADOR DE COSTO:
  - Cuando cambias peso o tiempo
  - Los precios se actualizan EN VIVO
  - Muestra desglose completo
```

---

## 💰 FÓRMULA DE COSTO (Quick Reference)

```
ENTRADA: peso_gramos + tiempo_minutos

CÁLCULOS:
  Filamento = (peso ÷ 1000) × $45/kg
  Energía   = (tiempo ÷ 60) × $0.75/hora
  Máquina   = (tiempo ÷ 60) × $150/hora
  
COSTO TOTAL = Suma anterior

GANANCIA = COSTO TOTAL × 50%

PRECIO = COSTO TOTAL + GANANCIA
```

---

## 📱 EN MÓVIL

```
< 768px de ancho:

1. Hamburger menu aparece (3 líneas)
2. Click = Abre menú
3. Click de nuevo = Cierra menú
4. Menú incluye: Clientes, Productos, etc
5. Interfaz se adapta automáticamente
6. Botones grandes (touch-friendly)
```

---

## 🌙 DARK MODE

```
En esquina superior derecha:
  Click botón → Cambia a dark mode
  Click de nuevo → Vuelve a light mode

Se guarda automáticamente (localStorage)
Próxima vez que entres → Mantiene el modo
```

---

## ⚙️ CAMBIAR PARÁMETROS DE COSTO

Si quieres que los precios cambien (ej: PLA más caro):

```sql
-- Abrir phpMyAdmin
-- Click en tabla 'parametros'

-- CAMBIAR PRECIO PLA:
UPDATE parametros SET valor = 50 WHERE clave = 'precio_pla_kg';
-- Ahora PLA cuesta 50 ARS/kg en lugar de 45

-- CAMBIAR GANANCIA A 60%:
UPDATE parametros SET valor = 60 WHERE clave = 'ganancia_porcentaje';
-- Ahora ganancias son 60% en lugar de 50%

-- CAMBIAR COSTO MÁQUINA A 200 ARS/hora:
UPDATE parametros SET valor = 200 WHERE clave = 'hora_maquina';
-- Ahora máquina cuesta 200 por hora
```

Después: Recarga la página y el calculador usa los nuevos valores.

---

## 🔧 API DE COSTO (Para integrar)

```javascript
// Obtener cálculo de costo
fetch('/api/costo.php?peso=100&tiempo=60')
    .then(r => r.json())
    .then(data => {
        console.log('Costo:', data.resultado.costo_total);
        console.log('Precio:', data.resultado.precio_venta);
    });
```

**Retorna:**
```json
{
  "resultado": {
    "costo_total": 155.25,
    "ganancia_estimada": 77.63,
    "precio_venta": 232.88
  },
  "desglose_costo": {
    "costo_pla": 4.5,
    "costo_luz": 0.75,
    "costo_maquina": 150
  }
}
```

---

## ✅ VERIFICACIÓN RÁPIDA

**¿Funciona todo?**

```
1. Clientes página carga:        http://localhost:8000/clientes.php ✅
2. Productos página carga:        http://localhost:8000/productos.php ✅
3. Crear cliente funciona:        (probá en la web) ✅
4. Crear producto funciona:       (probá en la web) ✅
5. Calculador actualiza:          (cambia peso/tiempo) ✅
6. Búsqueda funciona:             (escribe en barra) ✅
7. Dark mode funciona:            (toggle esquina superior) ✅
8. Móvil funciona:                (DevTools responsive) ✅
```

Si TODO está ✅ → **¡Listo para producción!**

---

## 📚 DOCUMENTACIÓN

```
Quiero entender...                              Leer este archivo

La fórmula de costo completa                    → GUIA_COSTEO_COMPLETA.md
Cómo usar cada componente                       → IMPLEMENTACION_v2.1.md
Visión general del proyecto                     → RESUMEN_EJECUTIVO_v2.1.txt
Detalles de BD (migrations)                     → INSTRUCCIONES_MIGRACION_v2.1.sql
Checklist de testing                            → CHECKLIST_v2.1.md
Este inicio rápido                              → Este archivo (INICIO_RAPIDO.md)
```

---

## 🆘 TROUBLESHOOTING

### "No cargan los estilos"
```
1. Reload página (Ctrl+Shift+R para hard refresh)
2. Verifica en console (F12) si hay errores CSS
3. Comprueba que assets/css/cruds.css existe
```

### "No aparecen los productos"
```
1. Verifica BD: SELECT * FROM productos WHERE activo = 1;
2. Comprueba que api/productos.php existe
3. Revisa console (F12) para errores
```

### "Calculador no actualiza"
```
1. Asegúrate de que peso > 0 y tiempo > 0
2. Revisa console (F12)
3. Verifica que /api/costo.php existe
```

### "Email rechazado como duplicado"
```
Este es el COMPORTAMIENTO CORRECTO
El sistema valida que cada cliente tenga email único
Usa un email diferente
```

### "Erro al ejecutar SQL"
```
1. Asegúrate de tener backup de BD
2. Revisa que estés en BD correcta
3. Ejecuta línea por línea (no todo junto)
4. Comprueba sintaxis SQL
```

---

## 📞 CONTACTO / AYUDA

Si algo no funciona:

1. Abre DevTools: **F12**
2. Revisa "Console" para errores
3. Copia el error
4. Busca en IMPLEMENTACION_v2.1.md

---

## 🎉 ¡LISTO!

**Versión**: 2.1.0  
**Fecha**: 20 de Mayo de 2026  
**Estado**: ✅ Funcional

**Próximo paso:** Ve a clientes.php o productos.php y ¡empieza a usar!

```
👥 http://localhost:8000/clientes.php
🏭 http://localhost:8000/productos.php
```

¡Disfrutá tienda3d v2.1! 🚀
