/**
 * JavaScript - Gestión de Clientes
 * tienda3d v2.1
 */

let clientesData = [];

document.addEventListener('DOMContentLoaded', () => {
    cargarClientes();
    document.getElementById('searchCliente').addEventListener('input', filtrarClientes);
});

/**
 * Cargar listado de clientes
 */
async function cargarClientes() {
    try {
        const response = await fetch('api/clientes.php');
        const data = await response.json();
        
        if (data.success) {
            clientesData = data.data || [];
            renderizarClientes(clientesData);
        }
    } catch (error) {
        console.error('Error cargando clientes:', error);
        mostrarError('Error al cargar clientes');
    }
}

/**
 * Renderizar tabla de clientes
 */
function renderizarClientes(clientes) {
    const tabla = document.getElementById('clientesTabla');
    
    if (clientes.length === 0) {
        tabla.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-8 text-gray-500">
                    👥 No hay clientes. ¡Crea uno nuevo!
                </td>
            </tr>
        `;
        return;
    }
    
    tabla.innerHTML = clientes.map(cliente => `
        <tr class="animate-fadeInUp">
            <td class="font-semibold text-gray-900 dark:text-white">${cliente.nombre}</td>
            <td>
                <a href="mailto:${cliente.email}" class="text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:underline transition">
                    ${cliente.email}
                </a>
            </td>
            <td>
                ${cliente.telefono ? `<a href="tel:${cliente.telefono}" class="text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:underline transition">${cliente.telefono}</a>` : '<span class="text-gray-400">—</span>'}
            </td>
            <td>${cliente.empresa || '<span class="text-gray-400">—</span>'}</td>
            <td>
                <div class="flex gap-2">
                    <button 
                        onclick="editarCliente(${cliente.id})"
                        class="action-link"
                        title="Editar cliente"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        <span>Editar</span>
                    </button>
                    <button 
                        onclick="eliminarCliente(${cliente.id})"
                        class="action-link danger"
                        title="Eliminar cliente"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        <span>Eliminar</span>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Filtrar clientes por búsqueda
 */
function filtrarClientes() {
    const search = document.getElementById('searchCliente').value.toLowerCase();
    const filtrados = clientesData.filter(c => 
        c.nombre.toLowerCase().includes(search) || 
        c.email.toLowerCase().includes(search) ||
        (c.empresa && c.empresa.toLowerCase().includes(search))
    );
    renderizarClientes(filtrados);
}

/**
 * Abrir modal para nuevo cliente
 */
function abrirModalCliente() {
    document.getElementById('clienteId').value = '';
    document.getElementById('modalClienteTitulo').textContent = 'Nuevo Cliente';
    document.getElementById('formCliente').reset();
    document.getElementById('modalCliente').classList.remove('hidden');
}

/**
 * Cerrar modal
 */
function cerrarModalCliente() {
    document.getElementById('modalCliente').classList.add('hidden');
}

/**
 * Editar cliente
 */
function editarCliente(id) {
    const cliente = clientesData.find(c => c.id === id);
    if (!cliente) return;
    
    document.getElementById('clienteId').value = cliente.id;
    document.getElementById('clienteNombre').value = cliente.nombre;
    document.getElementById('clienteEmail').value = cliente.email;
    document.getElementById('clienteTelefono').value = cliente.telefono || '';
    document.getElementById('clienteEmpresa').value = cliente.empresa || '';
    
    document.getElementById('modalClienteTitulo').textContent = 'Editar Cliente';
    document.getElementById('modalCliente').classList.remove('hidden');
}

/**
 * Guardar cliente (crear o actualizar)
 */
async function guardarCliente(event) {
    event.preventDefault();
    
    const id = document.getElementById('clienteId').value;
    const metodo = id ? 'PUT' : 'POST';
    const url = id ? `api/clientes.php?id=${id}` : 'api/clientes.php';
    
    const data = {
        nombre: document.getElementById('clienteNombre').value,
        email: document.getElementById('clienteEmail').value,
        telefono: document.getElementById('clienteTelefono').value,
        empresa: document.getElementById('clienteEmpresa').value
    };
    
    try {
        const response = await fetch(url, {
            method: metodo,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarExito(id ? 'Cliente actualizado' : 'Cliente creado');
            cerrarModalCliente();
            cargarClientes();
        } else {
            mostrarError(result.message || 'Error al guardar');
        }
    } catch (error) {
        mostrarError('Error al guardar: ' + error.message);
    }
}

/**
 * Eliminar cliente
 */
async function eliminarCliente(id) {
    if (!confirm('¿Confirmas que deseas eliminar este cliente?')) return;
    
    try {
        const response = await fetch(`api/clientes.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarExito('Cliente eliminado');
            cargarClientes();
        } else {
            mostrarError(result.message || 'Error al eliminar');
        }
    } catch (error) {
        mostrarError('Error al eliminar: ' + error.message);
    }
}

/**
 * Mostrar notificaciones
 */
function mostrarExito(mensaje) {
    showToast(mensaje, 'success');
}

function mostrarError(mensaje) {
    showToast(mensaje, 'error');
}
