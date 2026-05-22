/**
 * JavaScript - Gestión de Productos
 * tienda3d v2.1
 */

let productosData = [];
let parametrosGlobales = {};

document.addEventListener('DOMContentLoaded', () => {
    cargarParametros();
    cargarProductos();
    
    document.getElementById('searchProducto').addEventListener('input', filtrarProductos);
});

/**
 * Cargar parámetros de costo desde la BD
 */
async function cargarParametros() {
    try {
        const response = await fetch('../api/parametros.php');
        const data = await response.json();
        
        if (data.success) {
            data.data.forEach(param => {
                parametrosGlobales[param.clave] = parseFloat(param.valor);
            });
        }
    } catch (error) {
        console.error('Error cargando parámetros:', error);
        // Usar valores por defecto
        parametrosGlobales = {
            'precio_pla_kg': 45,
            'costo_luz_kwh': 15,
            'hora_maquina': 150,
            'ganancia_porcentaje': 50
        };
    }
}

/**
 * Cargar listado de productos
 */
async function cargarProductos() {
    try {
        const response = await fetch('../api/productos.php');
        const data = await response.json();
        
        if (data.success) {
            productosData = data.data || [];
            renderizarProductos(productosData);
        }
    } catch (error) {
        console.error('Error cargando productos:', error);
        mostrarError('Error al cargar productos');
    }
}

/**
 * Renderizar grid de productos
 */
function renderizarProductos(productos) {
    const container = document.getElementById('productosContainer');
    
    if (productos.length === 0) {
        container.innerHTML = `
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">📦 No hay productos. ¡Crea uno nuevo!</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = productos.map(prod => `
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition border-l-4 border-purple-500 overflow-hidden">
            ${prod.ruta_imagen ? `
                <img src="${prod.ruta_imagen}" alt="${prod.nombre}" class="w-full h-48 object-cover">
            ` : `
                <div class="w-full h-48 bg-gradient-to-br from-purple-200 to-sky-200 dark:from-purple-900 dark:to-sky-900 flex items-center justify-center">
                    <span class="text-5xl">🏭</span>
                </div>
            `}
            
            <div class="p-4">
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1">${prod.nombre}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">${prod.descripcion || 'Sin descripción'}</p>
                
                <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                    <div class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded">
                        <div class="text-xs text-gray-600 dark:text-gray-400">Peso</div>
                        <div class="font-mono font-bold text-purple-700 dark:text-purple-300">${prod.peso_gramos}g</div>
                    </div>
                    <div class="bg-sky-100 dark:bg-sky-900/30 p-2 rounded">
                        <div class="text-xs text-gray-600 dark:text-gray-400">Tiempo</div>
                        <div class="font-mono font-bold text-sky-700 dark:text-sky-300">${prod.tiempo_minutos}m</div>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="editarProducto(${prod.id})" class="flex-1 px-3 py-2 bg-purple-500 hover:bg-purple-600 text-white text-sm rounded transition">
                        ✏️ Editar
                    </button>
                    <button onclick="eliminarProducto(${prod.id})" class="flex-1 px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded transition">
                        🗑️ Eliminar
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Filtrar productos por búsqueda
 */
function filtrarProductos() {
    const search = document.getElementById('searchProducto').value.toLowerCase();
    const filtrados = productosData.filter(p => 
        p.nombre.toLowerCase().includes(search) || 
        (p.descripcion && p.descripcion.toLowerCase().includes(search))
    );
    renderizarProductos(filtrados);
}

/**
 * Abrir modal para nuevo producto
 */
function abrirModalProducto() {
    document.getElementById('productoId').value = '';
    document.getElementById('modalProductoTitulo').textContent = 'Nuevo Producto';
    document.getElementById('formProducto').reset();
    document.getElementById('modalProducto').classList.remove('hidden');
    recalcularCosto();
}

/**
 * Cerrar modal
 */
function cerrarModalProducto() {
    document.getElementById('modalProducto').classList.add('hidden');
}

/**
 * Editar producto
 */
async function editarProducto(id) {
    const prod = productosData.find(p => p.id === id);
    if (!prod) return;
    
    document.getElementById('productoId').value = prod.id;
    document.getElementById('productoNombre').value = prod.nombre;
    document.getElementById('productoDescripcion').value = prod.descripcion || '';
    document.getElementById('productoPeso').value = prod.peso_gramos;
    document.getElementById('productoTiempo').value = prod.tiempo_minutos;
    document.getElementById('productoImagen').value = prod.ruta_imagen || '';
    
    document.getElementById('modalProductoTitulo').textContent = 'Editar Producto';
    document.getElementById('modalProducto').classList.remove('hidden');
    recalcularCosto();
}

/**
 * Guardar producto (crear o actualizar)
 */
async function guardarProducto(event) {
    event.preventDefault();
    
    const id = document.getElementById('productoId').value;
    const metodo = id ? 'PUT' : 'POST';
    const url = id ? `../api/productos.php?id=${id}` : '../api/productos.php';
    
    const data = {
        nombre: document.getElementById('productoNombre').value,
        descripcion: document.getElementById('productoDescripcion').value,
        peso_gramos: parseFloat(document.getElementById('productoPeso').value),
        tiempo_minutos: parseInt(document.getElementById('productoTiempo').value),
        ruta_imagen: document.getElementById('productoImagen').value
    };
    
    try {
        const response = await fetch(url, {
            method: metodo,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarExito(id ? 'Producto actualizado' : 'Producto creado');
            cerrarModalProducto();
            cargarProductos();
        } else {
            mostrarError(result.message || 'Error al guardar');
        }
    } catch (error) {
        mostrarError('Error al guardar: ' + error.message);
    }
}

/**
 * Eliminar producto
 */
async function eliminarProducto(id) {
    if (!confirm('¿Confirmas que deseas eliminar este producto?')) return;
    
    try {
        const response = await fetch(`../api/productos.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarExito('Producto eliminado');
            cargarProductos();
        } else {
            mostrarError(result.message || 'Error al eliminar');
        }
    } catch (error) {
        mostrarError('Error al eliminar: ' + error.message);
    }
}

/**
 * Recalcular costo en vivo
 */
async function recalcularCosto() {
    const peso = parseFloat(document.getElementById('productoPeso').value) || 0;
    const tiempo = parseInt(document.getElementById('productoTiempo').value) || 0;
    
    if (peso <= 0 || tiempo <= 0) {
        document.getElementById('costoPLA').textContent = '$0.00';
        document.getElementById('costoLuz').textContent = '$0.00';
        document.getElementById('costoMaquina').textContent = '$0.00';
        document.getElementById('costoTotal').textContent = '$0.00';
        document.getElementById('costoGanancia').textContent = '$0.00';
        document.getElementById('costoVenta').textContent = '$0.00';
        return;
    }
    
    try {
        const response = await fetch(`../api/costo.php?peso=${peso}&tiempo=${tiempo}`);
        const data = await response.json();
        
        if (data.success) {
            const r = data.resultado;
            const d = data.desglose_costo;
            
            document.getElementById('costoPLA').textContent = `$${d.costo_pla.toFixed(2)}`;
            document.getElementById('costoLuz').textContent = `$${d.costo_luz.toFixed(2)}`;
            document.getElementById('costoMaquina').textContent = `$${d.costo_maquina.toFixed(2)}`;
            document.getElementById('costoTotal').textContent = `$${r.costo_total.toFixed(2)}`;
            document.getElementById('costoGanancia').textContent = `$${r.ganancia_estimada.toFixed(2)}`;
            document.getElementById('costoVenta').textContent = `$${r.precio_venta.toFixed(2)}`;
        }
    } catch (error) {
        console.error('Error calculando costo:', error);
    }
}

/**
 * Mostrar notificaciones
 */
function mostrarExito(mensaje) {
    console.log('✅', mensaje);
    const div = document.createElement('div');
    div.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    div.textContent = mensaje;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

function mostrarError(mensaje) {
    console.error('❌', mensaje);
    const div = document.createElement('div');
    div.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    div.textContent = mensaje;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}
