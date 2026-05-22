# 💰 GUÍA COMPLETA: SISTEMA DE COSTEO tienda3d v2.1

## ¿QUÉ ES EL SISTEMA DE COSTEO?

Es un calculador automático que determina **cuánto cuesta producir un producto** y **a qué precio debes venderlo** para obtener ganancia.

---

## 📊 LA FÓRMULA

### Paso 1: COSTO DE FILAMENTO PLA

```
Costo PLA = (peso_gramos ÷ 1000) × precio_pla_kg
```

**Explicación:**
- Conviertes gramos a kilogramos (dividir por 1000)
- Multiplicas por el precio del PLA por kilogramo (por defecto 45 ARS/kg)

**Ejemplo:** Producto de 100 gramos
```
Costo PLA = (100 ÷ 1000) × 45
          = 0.1 kg × 45
          = $4.50
```

---

### Paso 2: COSTO DE ENERGÍA (LUZ)

```
Costo Energía = (tiempo_minutos ÷ 60) × (costo_luz_kwh × potencia_kw ÷ 60)
```

**Explicación:**
- Conviertes minutos a horas (dividir por 60)
- La máquina consume 3kW (kilovatios)
- La electricidad cuesta 15 ARS/kWh (por defecto)
- Divides por 60 porque los minutos se dividen en una hora

**Fórmula simplificada:**
```
Costo Energía = (tiempo_minutos ÷ 60) × (15 × 3 ÷ 60)
              = (tiempo_minutos ÷ 60) × 0.75
```

**Ejemplo:** Impresión de 60 minutos (1 hora)
```
Costo Energía = (60 ÷ 60) × (15 × 3 ÷ 60)
              = 1 × 0.75
              = $0.75
```

---

### Paso 3: COSTO DE MÁQUINA (AMORTIZACIÓN)

```
Costo Máquina = (tiempo_minutos ÷ 60) × hora_maquina
```

**Explicación:**
- Conviertes minutos a horas (dividir por 60)
- Multiplicas por el costo por hora de máquina (por defecto 150 ARS/hora)
- Esto cubre: amortización, mantenimiento, depreciación

**Ejemplo:** Impresión de 60 minutos
```
Costo Máquina = (60 ÷ 60) × 150
              = 1 × 150
              = $150.00
```

---

### Paso 4: COSTO TOTAL

```
COSTO TOTAL = Costo PLA + Costo Energía + Costo Máquina
```

**Ejemplo con el producto anterior (100g, 60min):**
```
COSTO TOTAL = 4.50 + 0.75 + 150.00
            = $155.25
```

---

### Paso 5: GANANCIA (MARGEN DE BENEFICIO)

```
Ganancia = COSTO TOTAL × (ganancia_porcentaje ÷ 100)
```

**Explicación:**
- Por defecto: 50% de ganancia
- Esto significa que ganas la mitad de lo que cuesta producir

**Ejemplo con costo de $155.25:**
```
Ganancia = 155.25 × (50 ÷ 100)
         = 155.25 × 0.5
         = $77.63
```

---

### Paso 6: PRECIO DE VENTA (PRECIO AL CLIENTE)

```
PRECIO VENTA = COSTO TOTAL + Ganancia
```

**Ejemplo:**
```
PRECIO VENTA = 155.25 + 77.63
             = $232.88
```

---

## 📱 CÓMO USAR EL CALCULADOR EN LA WEB

### Dentro del Modal de Producto

```
┌─────────────────────────────────────┐
│ Nombre: Soporte para Celular       │
│ Peso: 35 gramos    ← CAMBIAS ESTO  │
│ Tiempo: 45 minutos ← CAMBIAS ESTO  │
├─────────────────────────────────────┤
│ 💰 Cálculo de Costo:                │
├─────────────────────────────────────┤
│ Costo Filamento: $1.58              │
│ Costo Energía:   $0.56              │
│ Costo Máquina:   $112.50            │
│ ─────────────────────────────────── │
│ COSTO TOTAL:     $114.64            │
│                                     │
│ Ganancia (50%):  $57.32             │
│ ─────────────────────────────────── │
│ PRECIO VENTA:    $171.96            │
└─────────────────────────────────────┘
```

**El calculador AUTOMÁTICAMENTE:**
- Recalcula cuando cambias peso o tiempo
- Muestra cada componente del costo
- Sugiere el precio de venta

---

## 🔢 PARÁMETROS CONFIGURABLES

Todos estos valores están en la **base de datos** y pueden cambiarse:

| Parámetro | Valor Actual | Descripción | Clave BD |
|-----------|-------------|------------|----------|
| Precio PLA | 45 ARS/kg | Costo del filamento por kg | `precio_pla_kg` |
| Costo Luz | 15 ARS/kWh | Costo de electricidad | `costo_luz_kwh` |
| Costo Máquina | 150 ARS/hora | Costo por hora de máquina | `hora_maquina` |
| Ganancia | 50% | Margen de beneficio | `ganancia_porcentaje` |
| Potencia | 3kW | Consumo de la impresora | (fijo en código) |

### Cambiar Parámetros

```sql
-- Cambiar precio del PLA a 50 ARS/kg
UPDATE parametros SET valor = 50 WHERE clave = 'precio_pla_kg';

-- Cambiar ganancia a 60%
UPDATE parametros SET valor = 60 WHERE clave = 'ganancia_porcentaje';

-- Cambiar costo hora máquina a 200 ARS
UPDATE parametros SET valor = 200 WHERE clave = 'hora_maquina';
```

---

## 📈 EJEMPLOS PRÁCTICOS

### Ejemplo 1: Pequeño (Tornillo M3x20)
```
Peso: 2.50g
Tiempo: 15 minutos

Costo PLA:    (2.50 ÷ 1000) × 45       = $0.11
Costo Energía: (15 ÷ 60) × 0.75        = $0.19
Costo Máquina: (15 ÷ 60) × 150         = $37.50
─────────────────────────────────────────────────
COSTO TOTAL:                            = $37.80
Ganancia (50%):                         = $18.90
PRECIO VENTA:                           = $56.70
```

### Ejemplo 2: Mediano (Soporte Celular)
```
Peso: 35g
Tiempo: 45 minutos

Costo PLA:    (35 ÷ 1000) × 45         = $1.58
Costo Energía: (45 ÷ 60) × 0.75        = $0.56
Costo Máquina: (45 ÷ 60) × 150         = $112.50
─────────────────────────────────────────────────
COSTO TOTAL:                            = $114.64
Ganancia (50%):                         = $57.32
PRECIO VENTA:                           = $171.96
```

### Ejemplo 3: Grande (Maceta Mediana)
```
Peso: 120g
Tiempo: 75 minutos

Costo PLA:    (120 ÷ 1000) × 45        = $5.40
Costo Energía: (75 ÷ 60) × 0.75        = $0.94
Costo Máquina: (75 ÷ 60) × 150         = $187.50
─────────────────────────────────────────────────
COSTO TOTAL:                            = $193.84
Ganancia (50%):                         = $96.92
PRECIO VENTA:                           = $290.76
```

---

## 🎯 ¿POR QUÉ ESTOS COSTOS?

### 💲 Filamento (PLA)
- El PLA cuesta ~45 ARS por kg
- Es el material que "se gasta" en cada impresión
- Más peso = Más material = Más costo

### ⚡ Energía
- La máquina consume 3kW mientras imprime
- La electricidad cuesta ~15 ARS por kWh
- Más tiempo = Más energía = Más costo
- Pero es un costo PEQUEÑO (solo 0.75 ARS por hora)

### 🔧 Máquina
- Es el costo MAYOR (150 ARS/hora)
- Incluye:
  - Amortización de la máquina (se desgasta con el tiempo)
  - Mantenimiento y reparaciones
  - Depreciación
  - Mano de obra del operario
- Más tiempo de impresión = Más costo de máquina

### 💰 Ganancia
- 50% de margen es estándar en industria
- Cubre gastos generales (local, servicios, etc)
- Permite obtener beneficio

---

## 🔗 CÓMO ACCEDER A LA API DE COSTO

Si quieres integrar el cálculo en otra aplicación:

```javascript
// Fetch al API
fetch('/api/costo.php?peso=100&tiempo=60')
    .then(response => response.json())
    .then(data => {
        console.log('Costo Total:', data.resultado.costo_total);
        console.log('Precio Venta:', data.resultado.precio_venta);
        
        // Desglose completo
        console.log('Desglose:', data.desglose_costo);
    });
```

**Respuesta:**
```json
{
  "success": true,
  "datos_entrada": {
    "peso_gramos": 100,
    "peso_kg": 0.1,
    "tiempo_minutos": 60,
    "tiempo_horas": 1
  },
  "desglose_costo": {
    "costo_pla": 4.5,
    "costo_luz": 0.75,
    "costo_maquina": 150
  },
  "parametros_usados": {
    "precio_pla_kg": 45,
    "costo_luz_kwh": 15,
    "potencia_maquina_kw": 3,
    "hora_maquina": 150,
    "ganancia_porcentaje": 50
  },
  "resultado": {
    "costo_total": 155.25,
    "ganancia_estimada": 77.625,
    "precio_venta": 232.875
  }
}
```

---

## 🛠️ AJUSTAR LA GANANCIA

Si quieres diferentes márgenes según el tipo de producto:

### Opción 1: Cambiar globalmente
```sql
UPDATE parametros SET valor = 60 WHERE clave = 'ganancia_porcentaje';
-- Ahora TODOS los productos tendrán 60% de ganancia
```

### Opción 2: Por tipo de producto (futuro)
```sql
ALTER TABLE productos ADD COLUMN ganancia_custom DECIMAL(5,2) NULL;
-- Luego usar este valor si existe, sino usar el global
```

### Márgenes típicos por industria:
- **Bajo**: 30-40% (productos populares, gran volumen)
- **Medio**: 50% (estándar, buen balance)
- **Alto**: 60-100% (productos especiales, bajo volumen)

---

## 📊 GRÁFICO DEL FLUJO

```
┌─────────────────────────────────────┐
│  ENTRADA: Peso + Tiempo             │
│  (lo que ingresa el usuario)         │
└────────────────┬────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  CÁLCULO DE COSTOS                  │
│  • Filamento: peso × precio         │
│  • Energía: tiempo × consumo        │
│  • Máquina: tiempo × costo/hora     │
└────────────────┬────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  COSTO TOTAL = suma de costos       │
└────────────────┬────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  GANANCIA = costo × 50%             │
└────────────────┬────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  PRECIO VENTA = costo + ganancia    │
│  (lo que cobras al cliente)          │
└─────────────────────────────────────┘
```

---

## ✅ CHECKLIST PARA ENTENDER

- [ ] ¿Entiendo que el costo tiene 3 componentes?
- [ ] ¿Sé por qué el costo de máquina es el mayor?
- [ ] ¿Puedo calcular manualmente un ejemplo?
- [ ] ¿Conozco los parámetros y puedo cambiarlos?
- [ ] ¿Entiendo que el precio incluye ganancia?
- [ ] ¿Sé cómo usar el calculador en web?
- [ ] ¿Podría integrar la API en otra app?

---

**Versión**: 2.1.0
**Última actualización**: 20 de Mayo de 2026
**Preguntas**: Ver IMPLEMENTACION_v2.1.md
