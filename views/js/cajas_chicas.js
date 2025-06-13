const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

async function loadCajasChicas() {
    const tbody = document.querySelector('#cajasChicasTable tbody');
    if (!tbody) {
        console.log('Tabla #cajasChicasTable no encontrada, omitiendo carga de cajas chicas');
        return;
    }

    try {
        const response = await fetch('index.php?controller=cajachica&action=list', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
            } catch (parseError) {
                throw new Error(`Error HTTP: ${response.status} - Respuesta no es JSON válida: ${text}`);
            }
        }
        const cajasChicas = await response.json();
        tbody.innerHTML = '';
        if (cajasChicas.length > 0) {
            cajasChicas.forEach(caja => {
                tbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${caja.id}</td>
                        <td data-label="Nombre">${caja.nombre}</td>
                        <td data-label="Monto Asignado">${parseFloat(caja.monto_asignado).toFixed(2)}</td>
                        <td data-label="Monto Disponible">${parseFloat(caja.monto_disponible).toFixed(2)}</td>
                        <td data-label="Centro de Costos">${caja.centro_costo || 'No asignado'}</td>
                        <td data-label="Estado">${caja.estado || 'Sin estado'}</td>
                        <td data-label="Acciones">
                            <button class="edit-btn" onclick="showEditForm(${caja.id}); window.history.pushState({}, '', 'index.php?controller=cajachica&action=update&id=${caja.id}')">Editar</button>
                            <button class="delete-btn" onclick="deleteCajaChica(${caja.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="8">No hay cajas chicas registradas.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar cajas chicas:', error.message);
        alert('No se pudo cargar la lista de cajas chicas: ' + error.message + '. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function createCajaChica(data) {
    const response = await fetch('index.php?controller=cajachica&action=create', {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const text = await response.text();
        try {
            const errorData = JSON.parse(text);
            throw new Error(errorData.error || text);
        } catch (parseError) {
            throw new Error(`Respuesta no es JSON válida: ${text}`);
        }
    }
    return response.json();
}

async function updateCajaChica(id, data) {
    const response = await fetch(`index.php?controller=cajachica&action=update&id=${id}`, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const text = await response.text();
        try {
            const errorData = JSON.parse(text);
            throw new Error(errorData.error || text);
        } catch (parseError) {
            throw new Error(`Respuesta no es JSON válida: ${text}`);
        }
    }
    return response.json();
}

async function deleteCajaChica(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta caja chica?')) return;

    try {
        const response = await fetch(`index.php?controller=cajachica&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al eliminar caja chica');
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válida: ${text}`);
            }
        }
        const result = await response.json();
        alert(result.message || 'Caja chica eliminada');
        loadCajasChicas();
    } catch (error) {
        console.error('Error al eliminar caja chica:');
        alert('No se puede eliminar la caja chica porque tiene liquidaciones asociadas');
    }
}

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalForm.innerHTML = '';
    }
}

async function showCreateForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=cajachica&action=create', {
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
        addValidations();
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
        const response = await fetch(`index.php?controller=cajachica&action=update&id=${id}`, {
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
        addValidations(id);
    } catch (error) {
        console.error('Error al cargar el formulario:', error);
        modalForm.innerHTML = `<div class="error">${error.message}</div>`;
        modal.classList.add('active');
    }
}

function addValidations(id = null) {
    const form = document.querySelector('#modalForm #cajaChicaFormInner');
    if (!form || form.tagName !== 'FORM') {
        console.error('No se encontró un elemento <form> dentro de #cajaChicaFormInner');
        alert('No se pudo inicializar el formulario. Intenta de nuevo.');
        return;
    }

    const fields = {
        nombre: { required: true },
        monto_asignado: { required: true, type: 'number', min: 0 },
        monto_disponible: { required: true, type: 'number', min: 0 },
        id_usuario_encargado: { required: true },
        id_supervisor: { required: true },
        id_contador: { required: true },
        id_centro_costo: { required: true },
        estado: { required: true }
    };

    form.querySelectorAll('input, select').forEach(field => {
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
            Array.from(form.querySelectorAll('input, select')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            try {
                const action = id ? updateCajaChica(id, formData) : createCajaChica(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadCajasChicas();
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

if (document.querySelector('#cajasChicasTable')) {
    loadCajasChicas();
}