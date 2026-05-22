# 💾 Guía de Backup - tienda3d

## Crear Backups de Seguridad

### Opción 1: Usando Windows (Automático)

#### Desde Explorador de Archivos
```
1. Abrir: C:\Users\eneas\OneDrive\Documentos\PlatformIO\Projects
2. Click derecho en carpeta "tienda3d"
3. Seleccionar "Enviar a" → "Carpeta comprimida"
4. Se crea: tienda3d.zip
5. Renombrar a: tienda3d_ver_2.0.zip
```

#### Desde Command Prompt (CMD)
```bash
cd C:\Users\eneas\OneDrive\Documentos\PlatformIO\Projects
# Windows 10+:
tar -a -c -f tienda3d_ver_2.0.zip tienda3d\

# O usar PowerShell:
Compress-Archive -Path "tienda3d" -DestinationPath "tienda3d_ver_2.0.zip" -Force
```

### Opción 2: Usando 7-Zip (Recomendado)
```
1. Descargar 7-Zip desde: https://www.7-zip.org/
2. Click derecho en "tienda3d"
3. Seleccionar "7-Zip" → "Add to archive"
4. Nombre: tienda3d_ver_2.0.zip
5. Formato: zip
6. Click "OK"
```

### Opción 3: Usando Script Git
```bash
cd tienda3d
git archive --format zip --output ../tienda3d_ver_2.0.zip HEAD
```

---

## Estructura del Backup

Cuando comprimas la carpeta, se incluirá:

```
tienda3d_ver_2.0.zip
│
├── index.php
├── CHANGELOG.md
├── ACTUALIZACION_v2.0.md
├── RESUMEN_v2.0.txt
├── TESTING_GUIDE.md
├── ESTRUCTURA_PROYECTO.md
├── migrate-v2.0.sh
│
├── api/
│   ├── pedidos.php
│   ├── productos.php
│   ├── clientes.php
│   ├── parametros.php
│   └── auth.php
│
├── includes/
│   └── db.php
│
├── assets/
│   ├── css/
│   │   ├── custom.css
│   │   ├── animations.css
│   │   └── custom_new.css (puede eliminarse)
│   │
│   └── js/
│       ├── app.js
│       └── mobile.js
│
├── database/
│   └── schema.sql
│
└── .git/
    └── (si tienes Git)
```

---

## Dónde Guardar los Backups

### Opción A: Carpeta Local
```
C:\Users\eneas\OneDrive\Documentos\PlatformIO\Projects\

Backups Existentes:
├── tienda3d_ver_1.0.zip  (si lo creaste)
└── tienda3d_ver_2.0.zip  (nuevo)
```

### Opción B: OneDrive (En la nube)
```
Ya está sincronizado automáticamente en:
C:\Users\eneas\OneDrive\

Ventajas:
✓ Backup en la nube automático
✓ Sincronizado en múltiples dispositivos
✓ Recuperable si el PC se daña
✓ Historial de versiones
```

### Opción C: Unidad Externa
```
1. Conectar USB
2. Copiar tienda3d_ver_2.0.zip a la USB
3. Guardar en lugar seguro
```

### Opción D: GitHub/GitLab
```
git init
git add .
git commit -m "v2.0: Modernización de UI"
git remote add origin https://github.com/usuario/tienda3d.git
git push -u origin main
```

---

## Cronograma de Backups Recomendado

```
ANTES DE CAMBIOS GRANDES:
├── v1.0.zip → Antes de cambios estéticos ✓ Hecho
└── v2.0.zip → Después de completar cambios (AHORA)

DURANTE DESARROLLO:
├── v2.1.zip → Si agregas más features
├── v2.2.zip → Cambios significativos
└── etc.

ANTES DE PRODUCCIÓN:
├── v2.0-final.zip → Pre-production testing
└── v2.0-prod.zip → Deploy a servidor

MANTENER:
├── Último 3 versiones localmente
├── Histórico en la nube (OneDrive/GitHub)
└── Copia física en USB (opcional)
```

---

## Cómo Restaurar desde Backup

### Si algo falla:

#### 1. Usando Explorador
```
1. Hacer click derecho en tienda3d_ver_2.0.zip
2. Seleccionar "Extraer todo..."
3. Elegir ubicación
4. Click "Extraer"
5. Reemplazar archivos si es necesario
```

#### 2. Usando 7-Zip
```
1. Hacer click derecho en .zip
2. Seleccionar "7-Zip" → "Extract to folder"
3. Esperar a que termine
```

#### 3. Desde Command Prompt
```bash
# Windows:
tar -x -f tienda3d_ver_2.0.zip

# O:
Expand-Archive -Path "tienda3d_ver_2.0.zip" -DestinationPath "tienda3d_restaurado"
```

---

## Verificar Integridad del Backup

```bash
# Probar que el ZIP es válido:
tar -t -f tienda3d_ver_2.0.zip

# Ver tamaño del backup:
Get-Item "tienda3d_ver_2.0.zip" | Select-Object Length

# Contar archivos:
(Get-Content (Expand-Archive -Path "tienda3d_ver_2.0.zip" -PassThru) | Measure-Object).Count
```

---

## Contenido del Backup v2.0

Archivos principales a incluir:

```
CÓDIGO FUENTE:
✓ index.php                 (540 líneas)
✓ assets/css/custom.css     (1200+ líneas)
✓ assets/css/animations.css (200 líneas) - NUEVO
✓ assets/js/app.js          (900+ líneas)
✓ includes/db.php           (80 líneas)
✓ api/pedidos.php           (430 líneas)
✓ api/productos.php         (260 líneas)
✓ api/clientes.php          (267 líneas)
✓ api/parametros.php
✓ api/auth.php
✓ database/schema.sql

DOCUMENTACIÓN:
✓ CHANGELOG.md
✓ ACTUALIZACION_v2.0.md
✓ RESUMEN_v2.0.txt
✓ TESTING_GUIDE.md
✓ ESTRUCTURA_PROYECTO.md
✓ migrate-v2.0.sh

CONFIGURACIÓN:
✓ .git/ (si tienes repo)
✓ .gitignore (si existe)
```

---

## Archivo custom_new.css

⚠️ NOTA: En el backup v2.0 está el archivo `custom_new.css` que se puede:

**Opción 1: Eliminar antes de hacer backup**
```bash
# Eliminar archivo duplicado
del assets\css\custom_new.css

# Luego hacer backup
```

**Opción 2: Incluir en backup (no ocupa mucho)**
```
Es solo una copia temporal, no afecta al proyecto
Tamaño: ~20KB
```

---

## Tamaño Esperado

```
Tamaño SIN comprimir: ~3-4 MB
Tamaño COMPRIMIDO (.zip): ~500-800 KB

Desglose:
├── Código PHP: ~2MB
├── CSS: ~50KB
├── JavaScript: ~100KB
├── Assets: ~800KB
└── Documentación: ~500KB
```

---

## Checklist de Backup

```
ANTES DE COMPRIMIR:
☐ Todos los archivos modificados guardados
☐ Sin archivos temporales
☐ custom_new.css disponible para eliminar
☐ .git actualizado (si aplica)

CREAR BACKUP:
☐ Comprimir carpeta tienda3d
☐ Renombrar a tienda3d_ver_2.0.zip
☐ Guardar en lugar seguro

VERIFICAR BACKUP:
☐ Probar que el ZIP se abre
☐ Verificar archivos principales están
☐ Comprobar tamaño
☐ Test de extracción en carpeta temporal

DOCUMENTAR:
☐ Anotar fecha del backup
☐ Anotar qué versión es
☐ Describir contenido
☐ Guardar en lugar conocido
```

---

## Recomendaciones Finales

```
✅ HAZLO AHORA:
1. Crear tienda3d_ver_2.0.zip
2. Guardar en OneDrive (automático)
3. Hacer copia en USB
4. Documentar en archivo de control

✅ FUTURO:
1. Backup automático semanal
2. Histórico en GitHub
3. Testing de restauración mensual
4. Mantener solo 3 últimas versiones localmente

✅ SEGURIDAD:
1. Nunca perder v1.0.zip
2. Guardar v2.0.zip en 2 lugares
3. Documentar qué contiene cada backup
4. Probar restauración antes de necesitarlo
```

---

## Comandos Útiles

```bash
# Crear backup:
Compress-Archive -Path "C:\ruta\tienda3d" -DestinationPath "tienda3d_ver_2.0.zip"

# Extraer backup:
Expand-Archive -Path "tienda3d_ver_2.0.zip" -DestinationPath "tienda3d_restaurado"

# Ver contenido sin extraer:
tar -t -f tienda3d_ver_2.0.zip

# Listar archivos del proyecto:
Get-ChildItem -Path "tienda3d" -Recurse | Where-Object {$_.Length} | Select-Object FullName, Length

# Tamaño total:
Get-ChildItem -Path "tienda3d" -Recurse | Measure-Object -Property Length -Sum
```

---

## Archivo de Control (Recomendado crear)

Crear un archivo `BACKUPS.txt`:

```
TIENDA3D - REGISTRO DE BACKUPS
==============================

VERSIÓN 1.0 - Initial Release
- Archivo: tienda3d_ver_1.0.zip
- Fecha: 20/05/2026
- Ubicación: C:\Users\eneas\OneDrive\Documentos\PlatformIO\Projects
- Tamaño: ~500KB
- Notas: Versión original con PrintCraft
- Estado: ✓ Restaurable

VERSIÓN 2.0 - UI Modernizada
- Archivo: tienda3d_ver_2.0.zip
- Fecha: 20/05/2026
- Ubicación: C:\Users\eneas\OneDrive\Documentos\PlatformIO\Projects
- Ubicación 2 (OneDrive): C:\Users\eneas\OneDrive\
- Tamaño: ~600KB
- Notas: Rebranding a tienda3d + Mobile-first + Hamburger menu
- Estado: ✓ Testing completado

PRÓXIMA VERSIÓN 2.1
- Características planeadas:
  - [ ] Más animaciones
  - [ ] Optimización móvil
  - [ ] PWA features
  - [ ] etc.
```

---

**Última actualización:** 20 de Mayo de 2026
**Versión:** v2.0
