const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

document.addEventListener('DOMContentLoaded', () => {
    loadTiposGastos();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && modal) {
        showEditForm(id);
    }
});

async function loadTiposGastos() {
    try {
        const response = await fetch('index.php?controller=tipogasto&action=list', {
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
        const tipos = await response.json();
        const tbody = document.querySelector('#tiposGastosTable tbody');
        tbody.innerHTML = '';
        if (tipos.length > 0) {
            tipos.forEach(tipo => {
                tbody.innerHTML += `
                    <tr>
                        <td data-label="ID">${tipo.id}</td>
                        <td data-label="Nombre">${tipo.name}</td>
                        <td data-label="Descripción">${tipo.description || 'N/A'}</td>
                        <td data-label="Impuesto">${tipo.impuesto_nombre || 'N/A'}</td>
                        <td data-label="Cuenta Contable">${tipo.cuenta_contable_nombre || 'N/A'}</td>
                        <td data-label="Estado">${tipo.estado}</td>
                        <td data-label="Acciones">
                            <button class="edit-btn" onclick="showEditForm(${tipo.id}); window.history.pushState({}, '', 'index.php?controller=tipogasto&action=update&id=${tipo.id}')">Editar</button>
                            <button class="delete-btn" onclick="deleteTipoGasto(${tipo.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="7">No hay tipos de gastos registrados.</td></tr>';
        }
        return tipos;
    } catch (error) {
        console.error('Error al cargar tipos de gastos:', error);
        alert('No se pudo cargar la lista de tipos de gastos. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function checkNombreExists(nombre, excludeId = null) {
    try {
        const tipos = await loadTiposGastos();
        const excludeIdNum = excludeId ? Number(excludeId) : null;
        return tipos.some(tipo => tipo.name === nombre && (excludeIdNum === null || Number(tipo.id) !== excludeIdNum));
    } catch (error) {
        console.error('Error al verificar duplicados de nombre:', error);
        return false;
    }
}

async function createTipoGasto(data) {
    const response = await fetch('index.php?controller=tipogasto&action=create', {
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

async function updateTipoGasto(id, data) {
    const response = await fetch(`index.php?controller=tipogasto&action=update&id=${id}`, {
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

async function deleteTipoGasto(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este tipo de gasto?')) return;

    try {
        const response = await fetch(`index.php?controller=tipogasto&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al eliminar tipo de gasto');
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válida: ${text}`);
            }
        }
        const result = await response.json();
        alert(result.message || 'Tipo de gasto eliminado');
        loadTiposGastos();
    } catch (error) {
        console.error('Error al eliminar tipo de gasto:', error);
        alert(error.message || 'Error al eliminar tipo de gasto. Intenta de nuevo.');
    }
}

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalForm.innerHTML = '';
        window.history.pushState({}, '', 'index.php?controller=tipogasto&action=list');
    }
}

async function showCreateForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=tipogasto&action=create', {
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
        const response = await fetch(`index.php?controller=tipogasto&action=update&id=${id}`, {
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
    const form = document.querySelector('#modalForm #tipoGastoFormInner');
    if (!form) {
        console.error('No se encontró un elemento <form> con id="tipoGastoFormInner" dentro de #modalForm');
        return;
    }

    const fields = {
        name: { required: true, minLength: 2 },
        description: { required: true, minLength: 2 },
        estado: { required: true }
        // No se requiere validación para impuesto_id y cuenta_contable_id ya que son opcionales
    };

    form.querySelectorAll('input, textarea, select').forEach(field => {
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
            if (fields[fieldName].minLength && value.length < fields[fieldName].minLength) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ')} debe tener al menos ${fields[fieldName].minLength} caracteres.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fieldName === 'name') {
                const nombreExists = await checkNombreExists(value, id);
                if (nombreExists) {
                    errorElement.textContent = `El nombre "${value}" ya está registrado. Por favor, usa un nombre diferente.`;
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
            }
        }
        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        const validations = await Promise.all(
            Array.from(form.querySelectorAll('input, textarea, select')).map(field => validateField({ target: field }))
        );
        isValid = validations.every(valid => valid);

        if (isValid) {
            const formData = new FormData(form);
            const formId = formData.get('id') || id;

            try {
                const action = formId ? updateTipoGasto(formId, formData) : createTipoGasto(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadTiposGastos();
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