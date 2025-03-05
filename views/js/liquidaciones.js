const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

document.addEventListener('DOMContentLoaded', () => {
    loadLiquidaciones();

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

async function loadLiquidaciones() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const mode = urlParams.get('mode') || '';
        const response = await fetch(`index.php?controller=liquidacion&action=list${mode ? '&mode=' + mode : ''}`, {
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
        const tbody = document.querySelector('#liquidacionesTable tbody');
        tbody.innerHTML = '';
        if (data.length > 0) {
            data.forEach(liquidacion => {
                const mode = urlParams.get('mode') || '';
                const actions = mode === 'autorizar' || mode === 'revisar'
                    ? `<button class="edit-btn" onclick="autorizarLiquidacion(${liquidacion.id}, '${mode}')">${mode === 'autorizar' ? 'Autorizar' : 'Revisar'}</button>`
                    : `
                        <button class="edit-btn" onclick="showEditForm(${liquidacion.id}); window.history.pushState({}, '', 'index.php?controller=liquidacion&action=update&id=${liquidacion.id}')">Editar</button>
                        <button class="delete-btn" onclick="deleteLiquidacion(${liquidacion.id})">Eliminar</button>
                    `;
                tbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${liquidacion.id}</td>
                        <td data-label="Caja Chica">${liquidacion.nombre_caja_chica || 'N/A'}</td>
                        <td data-label="Fecha Creación">${liquidacion.fecha_creacion}</td>
                        <td data-label="Monto Total">${parseFloat(liquidacion.monto_total).toFixed(2)}</td>
                        <td data-label="Estado">${liquidacion.estado}</td>
                        <td data-label="Acciones">${actions}</td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6">No hay liquidaciones registradas.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar liquidaciones:', error);
        alert('No se pudo cargar la lista de liquidaciones. Por favor, inicia sesión nuevamente.');
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
        const response = await fetch('index.php?controller=liquidacion&action=create', {
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
        const response = await fetch(`index.php?controller=liquidacion&action=update&id=${id}`, {
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

async function createLiquidacion(data) {
    const response = await fetch('index.php?controller=liquidacion&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    const result = await response.json();
    if (!response.ok) {
        throw new Error(result.error || 'Error al crear liquidación');
    }
    return result;
}

async function updateLiquidacion(id, data) {
    const response = await fetch(`index.php?controller=liquidacion&action=update&id=${id}`, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    const result = await response.json();
    if (!response.ok) {
        throw new Error(result.error || 'Error al actualizar liquidación');
    }
    return result;
}

async function deleteLiquidacion(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta liquidación?')) return;

    try {
        const response = await fetch(`index.php?controller=liquidacion&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Error al eliminar liquidación');
        }
        const result = await response.json();
        alert(result.message || 'Liquidación eliminada');
        loadLiquidaciones();
    } catch (error) {
        console.error('Error al eliminar liquidación:', error);
        alert(error.message || 'Error al eliminar liquidación. Intenta de nuevo.');
    }
}

async function autorizarLiquidacion(id, mode) {
    window.location.href = `index.php?controller=liquidacion&action=autorizar&id=${id}&mode=${mode}`;
}

function addFormValidations(id = null) {
    const form = document.querySelector('#modalForm #liquidacionFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="liquidacionFormInner" dentro de #modalForm');
        return;
    }

    const fields = {
        id_caja_chica: { required: true },
        fecha_creacion: { required: true },
        monto_total: { required: true, type: 'number', min: 0 },
        estado: { required: true }
    };

    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value;
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
            if (fields[fieldName].type === 'number' && isNaN(value)) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe ser un número.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].min && value < fields[fieldName].min) {
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
            Array.from(form.querySelectorAll('input, select')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            try {
                const action = id ? updateLiquidacion(id, formData) : createLiquidacion(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadLiquidaciones();
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