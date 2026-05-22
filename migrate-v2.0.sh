#!/bin/bash
# Script de migración v1.0 → v2.0
# tienda3d - Modernización UI

echo "🔄 Iniciando migración a v2.0..."

# Crear backup de seguridad
if [ ! -f "tienda3d_ver_1.0.zip" ]; then
    echo "📦 Creando backup v1.0..."
    zip -r tienda3d_ver_1.0.zip . -q
    echo "✅ Backup creado: tienda3d_ver_1.0.zip"
fi

# Reemplazar CSS
echo "🎨 Actualizando estilos CSS..."
mv assets/css/custom_new.css assets/css/custom.css
echo "✅ CSS modernizado"

# Limpiar archivos temporales
rm -f assets/css/custom_new.css

echo "🚀 ¡Migración completada!"
echo "📝 Cambios aplicados:"
echo "  ✓ Branding: tienda3d"
echo "  ✓ UI moderna con gradientes"
echo "  ✓ Hamburger menu responsivo"
echo "  ✓ Animaciones CSS puras"
echo "  ✓ Mobile-first design"
