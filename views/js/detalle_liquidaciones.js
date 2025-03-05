const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

document.addEventListener('DOMContentLoaded', () => {
    loadDetalles();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && modal) {
        showEditForm(id);
    }
});

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalForm.innerHTML = '';
    }
}

async function loadDetalles() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const mode = urlParams.get('mode') || '';
        const response = await fetch(`index.php?controller=detalleliquidacion&action=list${mode ? '&mode=' + mode : ''}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            if (response.status === 401) {
                throw new Error(errorData.error || 'No autorizado');
            }
            throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
        }
        const data = await response.json();
        const detalles = data;
        const tbody = document.querySelector('#detallesTable tbody');
        tbody.innerHTML = '';
        if (detalles.length > 0) {
            detalles.forEach(detalle => {
                const mode = urlParams.get('mode') || '';
                const archivos = detalle.rutas_archivos && typeof detalle.rutas_archivos === 'string' ? JSON.parse(detalle.rutas_archivos) : [];
                const archivosLinks = Array.isArray(archivos) ? archivos.map(ruta => `<a href="../${ruta}" target="_blank">Ver</a>`).join(' ') : 'N/A';
                const actions = mode === 'revisar'
                    ? `<button class="revisar-btn" onclick="revisarDetalle(${detalle.id})">Revisar</button>`
                    : `
                        <button class="edit-btn" onclick="showEditForm(${detalle.id}); window.history.pushState({}, '', 'index.php?controller=detalleliquidacion&action=update&id=${detalle.id}')">Editar</button>
                        <button class="delete-btn" onclick="deleteDetalle(${detalle.id})">Eliminar</button>
                        ${detalle.estado === 'PENDIENTE' ? `<button class="revisar-btn" onclick="revisarDetalle(${detalle.id})">Revisar</button>` : ''}
                    `;
                tbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${detalle.id}</td>
                        <td data-label="Liquidación">${detalle.liquidacion || 'N/A'}</td>
                        <td data-label="Factura">${detalle.no_factura}</td>
                        <td data-label="Proveedor">${detalle.nombre_proveedor || 'N/A'}</td>
                        <td data-label="Fecha">${detalle.fecha}</td>
                        <td data-label="Bien/Servicio">${detalle.bien_servicio}</td>
                        <td data-label="Tipo de Gasto">${detalle.t_gasto}</td>
                        <td data-label="Precio Unitario">${parseFloat(detalle.p_unitario).toFixed(2)}</td>
                        <td data-label="Total Factura">${parseFloat(detalle.total_factura).toFixed(2)}</td>
                        <td data-label="Estado">${detalle.estado}</td>
                        <td data-label="Archivos">${archivosLinks}</td>
                        <td data-label="Acciones">${actions}</td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="12">No hay detalles de liquidaciones registrados.</td></tr>';
        }
        return detalles;
    } catch (error) {
        console.error('Error al cargar detalles:', error);
        alert('No se pudo cargar la lista de detalles de liquidaciones. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function showCreateForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=detalleliquidacion&action=create', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
        modalForm.innerHTML = html;
        modal.classList.add('active');
        addFormValidations();
    } catch (error) {
        console.error('Error al cargar el formulario:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function showEditForm(id) {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch(`index.php?controller=detalleliquidacion&action=update&id=${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }
        const html = await response.text();
        if (!html.includes('<form')) {
            throw new Error('El servidor no devolvió un formulario válido');
        }
        modalForm.innerHTML = html;
        modal.classList.add('active');
        addFormValidations(id);
    } catch (error) {
        console.error('Error al cargar el formulario:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

async function createDetalle(data) {
    const dataObj = {};
    for (let [key, value] of data.entries()) {
        if (key !== 'archivos[]') {
            dataObj[key] = value;
        }
    }
    console.log('Datos enviados al servidor:', dataObj);
    console.log('Archivos enviados:', data.getAll('archivos[]'));

    const response = await fetch('index.php?controller=detalleliquidacion&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    const text = await response.text();
    console.log('Respuesta del servidor:', text);

    try {
        const result = JSON.parse(text);
        if (!response.ok) {
            throw new Error(result.error || 'Error al crear detalle');
        }
        return result;
    } catch (parseError) {
        throw new Error('Error al procesar la respuesta del servidor: ' + text);
    }
}

async function updateDetalle(id, data) {
    const dataObj = {};
    for (let [key, value] of data.entries()) {
        if (key !== 'archivos[]') {
            dataObj[key] = value;
        }
    }
    console.log('Datos enviados al servidor:', dataObj);
    console.log('Archivos enviados:', data.getAll('archivos[]'));

    const response = await fetch(`index.php?controller=detalleliquidacion&action=update&id=${id}`, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    const text = await response.text();
    console.log('Respuesta del servidor:', text);

    try {
        const result = JSON.parse(text);
        if (!response.ok) {
            throw new Error(result.error || 'Error al actualizar detalle');
        }
        return result;
    } catch (parseError) {
        throw new Error('Error al procesar la respuesta del servidor: ' + text);
    }
}

async function deleteDetalle(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este detalle de liquidación?')) return;

    try {
        const response = await fetch(`index.php?controller=detalleliquidacion&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al eliminar detalle');
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válida: ${text}`);
            }
        }
        const result = await response.json();
        alert(result.message || 'Detalle de liquidación eliminado');
        loadDetalles();
    } catch (error) {
        console.error('Error al eliminar detalle:', error);
        alert(error.message || 'Error al eliminar detalle. Intenta de nuevo.');
    }
}

async function revisarDetalle(id) {
    if (!confirm('¿Estás seguro de que deseas enviar este detalle a revisión contable?')) return;

    try {
        const response = await fetch(`index.php?controller=detalleliquidacion&action=revisar&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Error al enviar a revisión');
        }
        const result = await response.json();
        alert(result.message || 'Detalle enviado a revisión');
        loadDetalles();
    } catch (error) {
        console.error('Error al enviar a revisión:', error);
        alert(error.message || 'Error al enviar a revisión. Intenta de nuevo.');
    }
}

function addFormValidations(id = null) {
    const form = document.querySelector('#modalForm #detalleFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="detalleFormInner" dentro de #modalForm');
        return;
    }

    const fields = {
        id_liquidacion: { required: true },
        no_factura: { required: true },
        nombre_proveedor: { required: true },
        fecha: { required: true },
        bien_servicio: { required: true },
        t_gasto: { required: true },
        p_unitario: { required: true, type: 'number', min: 0 },
        total_factura: { required: true, type: 'number', min: 0 },
        estado: { required: true }
    };

    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value.trim();
        const errorElement = form.querySelector(`.error[data-field="${fieldName}"]`) || document.createElement('div');
        errorElement.className = 'error';
        errorElement.setAttribute('data-field', fieldName);
        if (!form.contains(errorElement)) {
            e.target.parentNode.appendChild(errorElement);
        }

        errorElement.style.display = 'none';
        e.target.classList.remove('invalid');

        if (fields[fieldName]) {
            if (fields[fieldName].required && !value) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} es obligatorio.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].type === 'number' && value && isNaN(value)) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe ser un número.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].min && value && parseFloat(value) < fields[fieldName].min) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe ser mayor o igual a ${fields[fieldName].min}.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
        }
        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        const validations = await Promise.all(
            Array.from(form.querySelectorAll('input, select, textarea')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            try {
                const action = id ? updateDetalle(id, formData) : createDetalle(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadDetalles();
            } catch (error) {
                console.error('Error al enviar formulario:', error);
                const errorElement = form.querySelector('.error:not([data-field])') || document.createElement('div');
                errorElement.className = 'error';
                errorElement.textContent = error.message || 'Error al enviar el formulario. Intenta de nuevo.';
                errorElement.style.display = 'block';
                if (!form.contains(errorElement)) {
                    form.appendChild(errorElement);
                }
            }
        }
    });
}