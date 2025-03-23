const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

async function loadCentrosCostos() {
    const tbody = document.querySelector('#centrosCostosTable tbody');
    if (!tbody) {
        console.log('Tabla #centrosCostosTable no encontrada, omitiendo carga de centros de costos');
        return;
    }

    const estadoFiltro = document.querySelector('#estadoFiltro')?.value || 'ACTIVO';
    const url = `index.php?controller=centrocosto&action=list${estadoFiltro ? '&estado=' + estadoFiltro : ''}`;

    try {
        const response = await fetch(url, {
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
        const centrosCostos = await response.json();
        tbody.innerHTML = '';
        if (centrosCostos.length > 0) {
            centrosCostos.forEach(centro => {
                const rowClass = centro.estado === 'INACTIVO' ? 'class="inactive-row"' : '';
                tbody.innerHTML += `
                    <tr ${rowClass}>
                        <td data-label="ID">${centro.id}</td>
                        <td data-label="Nombre">${centro.nombre}</td>
                        <td data-label="Descripción">${centro.descripcion || 'Sin descripción'}</td>
                        <td data-label="Estado">${centro.estado || 'Sin estado'}</td>
                        <td data-label="Acciones">
                            <button class="edit-btn" onclick="showEditForm(${centro.id}); window.history.pushState({}, '', 'index.php?controller=centrocosto&action=update&id=${centro.id}')">Editar</button>
                            <button class="delete-btn" onclick="deleteCentroCosto(${centro.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5">No hay centros de costos registrados.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar centros de costos:', error.message);
        alert('No se pudo cargar la lista de centros de costos: ' + error.message + '. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function createCentroCosto(data) {
    const response = await fetch('index.php?controller=centrocosto&action=create', {
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

async function updateCentroCosto(id, data) {
    const response = await fetch(`index.php?controller=centrocosto&action=update&id=${id}`, {
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

async function deleteCentroCosto(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este centro de costos?')) return;

    try {
        const response = await fetch(`index.php?controller=centrocosto&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al eliminar centro de costos');
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válida: ${text}`);
            }
        }
        const result = await response.json();
        alert(result.message || 'Centro de costos eliminado');
        loadCentrosCostos();
    } catch (error) {
        console.error('Error al eliminar centro de costos:', error);
        alert('No se puede eliminar el centro de costos porque está asociado a otras entidades');
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
        const response = await fetch('index.php?controller=centrocosto&action=create', {
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
        const response = await fetch(`index.php?controller=centrocosto&action=update&id=${id}`, {
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
    const form = document.querySelector('#modalForm #centroCostoFormInner');
    if (!form || form.tagName !== 'FORM') {
        console.error('No se encontró un elemento <form> dentro de #centroCostoFormInner');
        alert('No se pudo inicializar el formulario. Intenta de nuevo.');
        return;
    }

    const fields = {
        nombre: { required: true },
        descripcion: { required: false },
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
                const action = id ? updateCentroCosto(id, formData) : createCentroCosto(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadCentrosCostos();
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

if (document.querySelector('#centrosCostosTable')) {
    loadCentrosCostos();
}