/**
 * tienda3d Dashboard - JavaScript Principal
 * Sistema de Cotización y Gestión de Impresión 3D
 * 
 * @author tienda3d Development Team
 * @version 2.0.0
 */

/* =================================================================
   CONFIGURACIÓN GLOBAL
   ================================================================= */

const CONFIG = {
    API_BASE: 'api',
    DARK_MODE_KEY: 'tienda3d-dark-mode',
    SESSION_TIMEOUT: 86400000 // 24 horas en milisegundos
};

/* =================================================================
   ESTADO DE LA APLICACIÓN
   ================================================================= */

const AppState = {
    currentTab: 'pedidos',
    pedidos: [],
    clientes: [],
    productos: [],
    parametros: {},
    metricas: {},
    filtroEstado: 'activos',
    busqueda: '',
    pedidoEditando: null
};

/* =================================================================
   UTILIDADES
   ================================================================= */

/**
 * Realiza una petición a la API
 * @param {string} endpoint - Endpoint de la API
 * @param {Object} options - Opciones de fetch
 * @returns {Promise<Object>} Respuesta JSON
 */
async function apiRequest(endpoint, options = {}) {
    const url = `${CONFIG.API_BASE}/${endpoint}`;
    
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    const config = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...options.headers
        }
    };
    
    try {
        const response = await fetch(url, config);
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Error en la solicitud');
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

/**
 * Formatea un número como moneda
 * @param {number} amount - Cantidad
 * @returns {string} Monto formateado
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS'
    }).format(amount);
}

/**
 * Formatea una fecha
 * @param {string} dateString - Cadena de fecha ISO
 * @returns {string} Fecha formateada
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-AR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

/* =================================================================
   GESTIÓN DE TABS
   ================================================================= */

/**
 * Cambia la pestaña activa
 * @param {string} tabId - ID de la pestaña
 */
function switchTab(tabId) {
    // Actualizar estado
    AppState.currentTab = tabId;
    
    // Actualizar botones de navegación
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.tab === tabId);
    });
    
    // Actualizar contenido de secciones
    document.querySelectorAll('.tab-section').forEach(section => {
        section.classList.toggle('hidden', section.id !== `${tabId}-section`);
    });
    
    // Cargar datos si es necesario
    if (tabId === 'pedidos') {
        cargarPedidos();
    } else if (tabId === 'catalogo') {
        cargarProductos();
    } else if (tabId === 'configuracion') {
        cargarParametros();
    }
}

/* =================================================================
   GESTIÓN DE PEDIDOS
   ================================================================= */

/**
 * Carga la lista de pedidos
 */
async function cargarPedidos() {
    try {
        const finalizados = AppState.filtroEstado === 'finalizados' ? 'true' : 'false';
        const search = AppState.busqueda ? `&search=${encodeURIComponent(AppState.busqueda)}` : '';
        const estado = AppState.filtroEstado !== 'todos' ? `&estado=${AppState.filtroEstado}` : '';
        
        const response = await apiRequest(`pedidos.php?finalizados=${finalizados}${search}${estado}`);
        
        if (response.success) {
            AppState.pedidos = response.data.pedidos;
            AppState.metricas = response.data.metricas;
            renderizarMetricas();
            renderizarPedidos();
        }
    } catch (error) {
        showToast('Error al cargar pedidos: ' + error.message, 'error');
    }
}

/**
 * Renderiza las métricas en tarjetas
 */
function renderizarMetricas() {
    const container = document.getElementById('metricas-container');
    if (!container) return;
    
    const { total_facturado, costo_produccion, ganancia_neta } = AppState.metricas;
    
    container.innerHTML = `
        <div class="metric-card">
            <div class="metric-header">
                <h3 class="metric-title">Total Facturado</h3>
                <div class="metric-icon total">💰</div>
            </div>
            <p class="metric-value">${formatCurrency(total_facturado)}</p>
            <p class="metric-change positive">📈 Ingresos acumulados</p>
        </div>
        
        <div class="metric-card">
            <div class="metric-header">
                <h3 class="metric-title">Costo Producción</h3>
                <div class="metric-icon cost">🏭</div>
            </div>
            <p class="metric-value">${formatCurrency(costo_produccion)}</p>
            <p class="metric-change negative">📉 Gastos de operación</p>
        </div>
        
        <div class="metric-card">
            <div class="metric-header">
                <h3 class="metric-title">Ganancia Neta</h3>
                <div class="metric-icon profit">📊</div>
            </div>
            <p class="metric-value">${formatCurrency(ganancia_neta)}</p>
            <p class="metric-change positive">✅ Margen de beneficio</p>
        </div>
    `;
}

/**
 * Renderiza la tabla de pedidos
 */
function renderizarPedidos() {
    const tbody = document.getElementById('pedidos-tbody');
    if (!tbody) return;
    
    if (AppState.pedidos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <h3 class="empty-title">No hay pedidos</h3>
                        <p class="empty-description">Comienza creando tu primer pedido</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = AppState.pedidos.map(pedido => `
        <tr data-id="${pedido.id}">
            <td><strong>#${pedido.id}</strong></td>
            <td>${escapeHtml(pedido.cliente_nombre || 'Sin cliente')}</td>
            <td>${escapeHtml(pedido.cliente_empresa || '-')}</td>
            <td>${formatDate(pedido.fecha_pedido)}</td>
            <td><strong>${formatCurrency(pedido.total)}</strong></td>
            <td><span class="badge badge-${pedido.estado}">${pedido.estado}</span></td>
            <td>
                <div class="flex gap-2">
                    <button class="action-link" onclick="verPedido(${pedido.id})" title="Ver detalles">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <span>Detalles</span>
                    </button>
                    <button class="action-link" onclick="editarEstadoPedido(${pedido.id})" title="Cambiar estado">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        <span>Estado</span>
                    </button>
                    <button class="action-link danger" onclick="eliminarPedido(${pedido.id})" title="Eliminar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        <span>Eliminar</span>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Busca pedidos condebounce
 */
let searchTimeout;
function buscarPedidos(query) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        AppState.busqueda = query;
        cargarPedidos();
    }, 300);
}

/**
 * Filtra pedidos por estado
 * @param {string} filtro - Estado a filtrar
 */
function filtrarPedidos(filtro) {
    AppState.filtroEstado = filtro;
    
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.filtro === filtro);
    });
    
    cargarPedidos();
}

/**
 * Abre el modal para crear/editar pedido
 */
async function abrirModalPedido(pedidoId = null) {
    const modal = document.getElementById('pedido-modal');
    const form = document.getElementById('pedido-form');
    const title = document.getElementById('modal-title');
    
    // Limpiar formulario
    form.reset();
    document.getElementById('pedido-id').value = '';
    document.getElementById('detalles-container').innerHTML = '';
    
    // Cargar selects necesarios
    await cargarSelectClientes();
    await cargarSelectProductos();
    
    if (pedidoId) {
        title.textContent = 'Editar Pedido';
        AppState.pedidoEditando = pedidoId;
        // Cargar datos del pedido
        try {
            const response = await apiRequest(`pedidos.php?id=${pedidoId}`);
            if (response.success) {
                const pedido = response.data;
                document.getElementById('cliente-select').value = pedido.cliente_id;
                // Cargar detalles existentes...
            }
        } catch (error) {
            showToast('Error al cargar pedido', 'error');
            return;
        }
    } else {
        title.textContent = 'Nuevo Pedido';
        AppState.pedidoEditando = null;
    }
    
    modal.classList.add('active');
}

/**
 * Cierra el modal de pedido
 */
function cerrarModalPedido() {
    const modal = document.getElementById('pedido-modal');
    modal.classList.remove('active');
    AppState.pedidoEditando = null;
}

/**
 * Carga clientes en el select
 */
async function cargarSelectClientes() {
    try {
        const response = await apiRequest('clientes.php');
        if (response.success) {
            AppState.clientes = response.data;
            const select = document.getElementById('cliente-select');
            select.innerHTML = `
                <option value="">Seleccionar cliente...</option>
                ${response.data.map(c => `
                    <option value="${c.id}">${escapeHtml(c.nombre)} ${c.empresa ? `(${escapeHtml(c.empresa)})` : ''}</option>
                `).join('')}
            `;
        }
    } catch (error) {
        showToast('Error al cargar clientes', 'error');
    }
}

/**
 * Carga productos en el select
 */
async function cargarSelectProductos() {
    try {
        const response = await apiRequest('productos.php');
        if (response.success) {
            AppState.productos = response.data;
            const select = document.getElementById('producto-select');
            select.innerHTML = `
                <option value="">Seleccionar producto...</option>
                ${response.data.map(p => `
                    <option value="${p.id}" data-peso="${p.peso_gramos}" data-tiempo="${p.tiempo_minutos}">
                        ${escapeHtml(p.nombre)} - ${p.peso_gramos}g - ${p.tiempo_minutos}min
                    </option>
                `).join('')}
            `;
        }
    } catch (error) {
        showToast('Error al cargar productos', 'error');
    }
}

/**
 * Agrega un producto al pedido
 */
function agregarProductoPedido() {
    const select = document.getElementById('producto-select');
    const cantidad = document.getElementById('cantidad-input');
    const container = document.getElementById('detalles-container');
    
    if (!select.value) {
        showToast('Selecciona un producto', 'warning');
        return;
    }
    
    if (!cantidad.value || cantidad.value < 1) {
        showToast('Ingresa una cantidad válida', 'warning');
        return;
    }
    
    const producto = AppState.productos.find(p => p.id == select.value);
    const cantidadNum = parseInt(cantidad.value);
    
    const itemHtml = `
        <div class="detalle-item" data-producto-id="${producto.id}">
            <div class="detalle-info">
                <strong>${escapeHtml(producto.nombre)}</strong>
                <span>Cantidad: ${cantidadNum}</span>
                <span>Peso: ${producto.peso_gramos * cantidadNum}g</span>
                <span>Tiempo: ${producto.tiempo_minutos * cantidadNum}min</span>
            </div>
            <button type="button" class="btn btn-danger" onclick="eliminarDetallePedido(this)">✕</button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    
    // Limpiar inputs
    select.value = '';
    cantidad.value = '1';
    
    // Recalcular costos
    calcularCostosPedido();
}

/**
 * Elimina un producto del pedido
 * @param {HTMLElement} button - Botón de eliminar
 */
function eliminarDetallePedido(button) {
    button.closest('.detalle-item').remove();
    calcularCostosPedido();
}

/**
 * Calcula los costos del pedido
 */
async function calcularCostosPedido() {
    const detalles = document.querySelectorAll('.detalle-item');
    const summaryContainer = document.getElementById('cost-summary');
    
    if (detalles.length === 0) {
        if (summaryContainer) summaryContainer.innerHTML = '';
        return;
    }
    
    // Obtener parámetros si no existen
    if (Object.keys(AppState.parametros).length === 0) {
        try {
            const response = await apiRequest('parametros.php');
            if (response.success) {
                AppState.parametros = response.data;
            }
        } catch (error) {
            console.error('Error al obtener parámetros');
        }
    }
    
    const params = AppState.parametros;
    const precioPlaKg = parseFloat(params.precio_pla_kg?.valor ?? 45);
    const costoLuzKwh = parseFloat(params.costo_luz_kwh?.valor ?? 15);
    const horaMaquina = parseFloat(params.hora_maquina?.valor ?? 150);
    const gananciaPorcentaje = parseFloat(params.ganancia_porcentaje?.valor ?? 50);
    
    let totalCostoMaterial = 0;
    let totalCostoEnergia = 0;
    let totalCostoMaquina = 0;
    let totalCostoProduccion = 0;
    let subtotal = 0;
    
    detalles.forEach(detalle => {
        const productoId = detalle.dataset.productoId;
        const producto = AppState.productos.find(p => p.id == productoId);
        
        // Extraer cantidad de forma segura
        const cantidadText = detalle.querySelector('span:nth-child(2)').textContent;
        const cantidad = parseInt(cantidadText.replace(/[^\d]/g, '')) || 1;
        
        const pesoTotalKg = (producto.peso_gramos / 1000) * cantidad;
        const tiempoTotalHoras = (producto.tiempo_minutos / 60) * cantidad;
        
        const costoPLA = pesoTotalKg * precioPlaKg;
        const costoLuz = tiempoTotalHoras * (costoLuzKwh * 3 / 60);
        const costoMaq = tiempoTotalHoras * horaMaquina;
        
        const costoProdItem = costoPLA + costoLuz + costoMaq;
        const precioVentaItem = costoProdItem * (1 + (gananciaPorcentaje / 100));
        
        totalCostoMaterial += costoPLA;
        totalCostoEnergia += costoLuz;
        totalCostoMaquina += costoMaq;
        totalCostoProduccion += costoProdItem;
        subtotal += precioVentaItem;
    });
    
    if (summaryContainer) {
        summaryContainer.innerHTML = `
            <div class="costo-box mt-4">
                <div class="costo-item">
                    <span class="costo-item-label">Costo Material (PLA):</span>
                    <span class="costo-item-valor">${formatCurrency(totalCostoMaterial)}</span>
                </div>
                <div class="costo-item">
                    <span class="costo-item-label">Costo Energía:</span>
                    <span class="costo-item-valor">${formatCurrency(totalCostoEnergia)}</span>
                </div>
                <div class="costo-item">
                    <span class="costo-item-label">Costo Amortización Máquina:</span>
                    <span class="costo-item-valor">${formatCurrency(totalCostoMaquina)}</span>
                </div>
                <div class="costo-item">
                    <span class="costo-item-label">Costo de Producción Total:</span>
                    <span class="costo-item-valor">${formatCurrency(totalCostoProduccion)}</span>
                </div>
                <div class="costo-item border-b-0 pb-0">
                    <span class="costo-item-label">Ganancia (${gananciaPorcentaje}%):</span>
                    <span class="costo-item-valor">${formatCurrency(subtotal - totalCostoProduccion)}</span>
                </div>
                <div class="costo-precio text-lg font-bold">
                    <span>Total Pedido:</span>
                    <span>${formatCurrency(subtotal)}</span>
                </div>
            </div>
        `;
    }
}

/**
 * Guarda el pedido (crear o actualizar)
 */
async function guardarPedido() {
    const clienteId = document.getElementById('cliente-select').value;
    const detalles = document.querySelectorAll('.detalle-item');
    
    if (!clienteId) {
        showToast('Selecciona un cliente', 'warning');
        return;
    }
    
    if (detalles.length === 0) {
        showToast('Agrega al menos un producto', 'warning');
        return;
    }
    
    const productos = [];
    detalles.forEach(detalle => {
        productos.push({
            producto_id: detalle.dataset.productoId,
            cantidad: parseInt(detalle.querySelector('span:nth-child(2)').textContent.replace('Cantidad: ', ''))
        });
    });
    
    try {
        const response = await apiRequest('pedidos.php', {
            method: 'POST',
            body: JSON.stringify({
                cliente_id: clienteId,
                productos: productos
            })
        });
        
        if (response.success) {
            showToast('Pedido guardado correctamente', 'success');
            cerrarModalPedido();
            cargarPedidos();
        }
    } catch (error) {
        showToast('Error al guardar pedido: ' + error.message, 'error');
    }
}

/**
 * Ver detalles de un pedido
 * @param {number} pedidoId - ID del pedido
 */
async function verPedido(pedidoId) {
    try {
        const response = await apiRequest(`pedidos.php?id=${pedidoId}`);
        if (response.success) {
            const pedido = response.data;
            // Mostrar en modal o expandir en línea
            alert(`Pedido #${pedido.id}\nCliente: ${pedido.cliente_nombre}\nTotal: ${formatCurrency(pedido.total)}\nEstado: ${pedido.estado}`);
        }
    } catch (error) {
        showToast('Error al obtener detalles del pedido', 'error');
    }
}

/**
 * Edita el estado de un pedido
 * @param {number} pedidoId - ID del pedido
 */
async function editarEstadoPedido(pedidoId) {
    const nuevoEstado = prompt('Ingresa el nuevo estado (pendiente, aprobado, rechazado, finalizado):');
    
    if (!nuevoEstado || !['pendiente', 'aprobado', 'rechazado', 'finalizado'].includes(nuevoEstado)) {
        if (nuevoEstado !== null) {
            showToast('Estado no válido', 'warning');
        }
        return;
    }
    
    try {
        const response = await apiRequest('pedidos.php', {
            method: 'PUT',
            body: JSON.stringify({ estado: nuevoEstado })
        });
        
        if (response.success) {
            showToast('Estado actualizado correctamente', 'success');
            cargarPedidos();
        }
    } catch (error) {
        showToast('Error al actualizar estado', 'error');
    }
}

/**
 * Elimina un pedido
 * @param {number} pedidoId - ID del pedido
 */
async function eliminarPedido(pedidoId) {
    if (!confirm('¿Estás seguro de eliminar este pedido?')) {
        return;
    }
    
    try {
        const response = await apiRequest(`pedidos.php?id=${pedidoId}`, {
            method: 'DELETE'
        });
        
        if (response.success) {
            showToast('Pedido eliminado correctamente', 'success');
            cargarPedidos();
        }
    } catch (error) {
        showToast('Error al eliminar pedido', 'error');
    }
}

/* =================================================================
   GESTIÓN DE PRODUCTOS (CATÁLOGO)
   ================================================================= */

/**
 * Carga la lista de productos
 */
async function cargarProductos() {
    try {
        // Cargar parámetros primero si no están cargados
        if (Object.keys(AppState.parametros).length === 0) {
            const paramsResponse = await apiRequest('parametros.php');
            if (paramsResponse.success) {
                AppState.parametros = paramsResponse.data;
            }
        }
        
        const response = await apiRequest('productos.php');
        if (response.success) {
            AppState.productos = response.data;
            renderizarProductos();
        }
    } catch (error) {
        showToast('Error al cargar productos', 'error');
    }
}

/**
 * Renderiza el catálogo de productos
 */
function renderizarProductos() {
    const container = document.getElementById('catalog-grid');
    if (!container) return;
    
    if (AppState.productos.length === 0) {
        container.innerHTML = `
            <div class="empty-state col-span-full py-12 text-center">
                <div class="empty-icon text-5xl mb-3">📦</div>
                <h3 class="empty-title text-xl font-semibold mb-1 text-gray-800 dark:text-gray-100">No hay productos</h3>
                <p class="empty-description text-gray-500 dark:text-gray-400">El catálogo está vacío actualmente.</p>
            </div>
        `;
        return;
    }
    
    const params = AppState.parametros;
    const precioPlaKg = parseFloat(params.precio_pla_kg?.valor ?? 45);
    const costoLuzKwh = parseFloat(params.costo_luz_kwh?.valor ?? 15);
    const horaMaquina = parseFloat(params.hora_maquina?.valor ?? 150);
    const gananciaPorcentaje = parseFloat(params.ganancia_porcentaje?.valor ?? 50);
    
    container.innerHTML = AppState.productos.map(producto => {
        const pesoKg = producto.peso_gramos / 1000;
        const tiempoHoras = producto.tiempo_minutos / 60;
        
        const costoPLA = pesoKg * precioPlaKg;
        const costoLuz = tiempoHoras * (costoLuzKwh * 3 / 60);
        const costoMaq = tiempoHoras * horaMaquina;
        
        const costoUnitario = costoPLA + costoLuz + costoMaq;
        const precioVenta = costoUnitario * (1 + (gananciaPorcentaje / 100));
        
        // Lógica de imagen: mostrar imagen o un gradiente premium de marcador de posición
        let imageHtml = '';
        if (producto.ruta_imagen && producto.ruta_imagen.trim() !== '') {
            imageHtml = `<img src="${escapeHtml(producto.ruta_imagen)}" alt="${escapeHtml(producto.nombre)}" class="product-image">`;
        } else {
            imageHtml = `<div class="product-placeholder text-4xl">🖨️</div>`;
        }
        
        return `
            <div class="product-card flex flex-col h-full">
                ${imageHtml}
                <div class="product-content p-5 flex flex-col flex-1">
                    <h4 class="product-name font-bold text-lg mb-2 text-slate-900 dark:text-slate-100">${escapeHtml(producto.nombre)}</h4>
                    <p class="product-description text-sm text-slate-600 dark:text-slate-400 mb-4 line-clamp-3 flex-1">${escapeHtml(producto.descripcion || 'Sin descripción')}</p>
                    <div class="product-meta grid grid-cols-2 gap-2 text-xs font-medium text-slate-500 dark:text-slate-450 border-t border-b border-slate-100/10 dark:border-slate-800 py-3 mb-4">
                        <div class="flex items-center gap-1.5 justify-center py-1 bg-slate-500/5 dark:bg-white/5 rounded-md">
                            <span>⚖️</span> <span>${producto.peso_gramos} g</span>
                        </div>
                        <div class="flex items-center gap-1.5 justify-center py-1 bg-slate-500/5 dark:bg-white/5 rounded-md">
                            <span>⏱️</span> <span>${producto.tiempo_minutos} min</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-auto pt-2">
                        <span class="text-xs uppercase tracking-wider font-bold text-slate-500 dark:text-slate-400">Precio Sugerido</span>
                        <span class="text-xl font-extrabold text-slate-900 dark:text-amber-400">${formatCurrency(precioVenta)}</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

/* =================================================================
   GESTIÓN DE CONFIGURACIÓN
   ================================================================= */

/**
 * Carga los parámetros del sistema
 */
async function cargarParametros() {
    try {
        const response = await apiRequest('parametros.php');
        if (response.success) {
            AppState.parametros = response.data;
            renderizarParametros();
        }
    } catch (error) {
        showToast('Error al cargar parámetros', 'error');
    }
}

/**
 * Renderiza el formulario de configuración
 */
function renderizarParametros() {
    const container = document.getElementById('settings-container');
    if (!container) return;
    
    const params = AppState.parametros;
    
    container.innerHTML = `
        <div class="settings-section">
            <h3 class="settings-title">💰 Costos de Producción</h3>
            <div class="settings-grid">
                <div class="setting-item">
                    <label for="param-precio-pla">Precio PLA por kg (ARS)</label>
                    <input type="number" id="param-precio-pla" class="form-input" 
                           value="${params.precio_pla_kg?.valor || 45}" step="0.01">
                </div>
                <div class="setting-item">
                    <label for="param-costo-luz">Costo de luz por kWh (ARS)</label>
                    <input type="number" id="param-costo-luz" class="form-input" 
                           value="${params.costo_luz_kwh?.valor || 15}" step="0.01">
                </div>
                <div class="setting-item">
                    <label for="param-hora-maquina">Costo hora máquina (ARS)</label>
                    <input type="number" id="param-hora-maquina" class="form-input" 
                           value="${params.hora_maquina?.valor || 150}" step="0.01">
                </div>
                <div class="setting-item">
                    <label for="param-ganancia">Porcentaje de ganancia (%)</label>
                    <input type="number" id="param-ganancia" class="form-input" 
                           value="${params.ganancia_porcentaje?.valor || 50}" step="1">
                </div>
            </div>
            <button class="btn btn-primary mt-4" onclick="guardarParametros()">
                💾 Guardar Cambios
            </button>
        </div>
    `;
}

/**
 * Guarda los parámetros del sistema
 */
async function guardarParametros() {
    const parametros = [
        { clave: 'precio_pla_kg', valor: parseFloat(document.getElementById('param-precio-pla').value) },
        { clave: 'costo_luz_kwh', valor: parseFloat(document.getElementById('param-costo-luz').value) },
        { clave: 'hora_maquina', valor: parseFloat(document.getElementById('param-hora-maquina').value) },
        { clave: 'ganancia_porcentaje', valor: parseFloat(document.getElementById('param-ganancia').value) }
    ];
    
    try {
        for (const param of parametros) {
            await apiRequest('parametros.php', {
                method: 'POST',
                body: JSON.stringify(param)
            });
        }
        
        showToast('Parámetros guardados correctamente', 'success');
        cargarParametros();
    } catch (error) {
        showToast('Error al guardar parámetros', 'error');
    }
}

/* =================================================================
   INICIALIZACIÓN
   ================================================================= */

/**
 * Inicializa la aplicación cuando el DOM está listo
 */

/* Set active nav-tab based on current URL */
function setActiveNavFromLocation() {
    const path = window.location.pathname.split('/').pop();
    let tab = 'pedidos';
    if (path === '' || path === 'index.php') tab = 'pedidos';
    else if (path.includes('clientes.php') || path.includes('clientes')) tab = 'clientes';
    else if (path.includes('productos.php') || path.includes('productos')) tab = 'productos';
    // Toggle active on nav tabs
    document.querySelectorAll('.nav-tab').forEach(t => {
        const name = t.dataset.tab || (t.getAttribute('href') || '').split('/').pop().replace('.php','');
        t.classList.toggle('active', name === tab);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Configurar filtros de estado
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', () => filtrarPedidos(tab.dataset.filtro));
    });
    
    // Configurar buscador
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => buscarPedidos(e.target.value));
    }
    
    // Configurar botón de nuevo pedido
    const nuevoPedidoBtn = document.getElementById('nuevo-pedido-btn');
    if (nuevoPedidoBtn) {
        nuevoPedidoBtn.addEventListener('click', () => abrirModalPedido());
    }
    
    // Configurar botón de cerrar modal
    const closeModalBtn = document.getElementById('close-modal-btn');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', cerrarModalPedido);
    }
    
    // Cerrar modal al hacer clic fuera
    const modalOverlay = document.getElementById('pedido-modal');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                cerrarModalPedido();
            }
        });
    }
    
    // Configurar botón de agregar producto
    const agregarProductoBtn = document.getElementById('agregar-producto-btn');
    if (agregarProductoBtn) {
        agregarProductoBtn.addEventListener('click', agregarProductoPedido);
    }
    
    // Configurar botón de guardar pedido
    const guardarPedidoBtn = document.getElementById('guardar-pedido-btn');
    if (guardarPedidoBtn) {
        guardarPedidoBtn.addEventListener('click', guardarPedido);
    }
    
    // Cargar datos iniciales según la pestaña activa en la URL
    const initialTab = typeof getTabFromUrl === 'function' ? getTabFromUrl() : 'pedidos';
    switchTab(initialTab);
});