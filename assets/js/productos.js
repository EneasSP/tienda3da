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
        const response = await fetch('api/parametros.php');
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
        const response = await fetch('api/productos.php');
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
        <div class="product-card overflow-hidden">
            ${prod.ruta_imagen ? `
                <img src="${prod.ruta_imagen}" alt="${prod.nombre}" class="product-image">
            ` : `
                <div class="product-placeholder">
                    <span>🏭</span>
                </div>
            `}
            
            <div class="p-4">
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1">${prod.nombre}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">${prod.descripcion || 'Sin descripción'}</p>
                
                <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                    <div class="bg-slate-100 dark:bg-slate-800/40 p-2 rounded">
                        <div class="text-xs text-gray-600 dark:text-gray-400">Peso</div>
                        <div class="font-mono font-bold text-slate-800 dark:text-slate-200">${prod.peso_gramos}g</div>
                    </div>
                    <div class="bg-slate-100 dark:bg-slate-800/40 p-2 rounded">
                        <div class="text-xs text-gray-600 dark:text-gray-400">Tiempo</div>
                        <div class="font-mono font-bold text-slate-800 dark:text-slate-200">${prod.tiempo_minutos}m</div>
                    </div>
                </div>
                
                <div class="flex gap-4 border-t border-slate-100/10 dark:border-slate-800 pt-3 mt-3">
                    <button onclick="editarProducto(${prod.id})" class="action-link flex-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        <span>Editar</span>
                    </button>
                    <button onclick="eliminarProducto(${prod.id})" class="action-link danger flex-1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        <span>Eliminar</span>
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
    const url = id ? `api/productos.php?id=${id}` : 'api/productos.php';
    
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
        const response = await fetch(`api/productos.php?id=${id}`, {
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
        const response = await fetch(`api/costo.php?peso=${peso}&tiempo=${tiempo}`);
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
