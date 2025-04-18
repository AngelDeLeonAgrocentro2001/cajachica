const modal = document.querySelector('#modal');
const modalForm = document.querySelector('#modalForm');

async function loadCuentasContables() {
    console.log('Iniciando loadCuentasContables');
    const tbody = document.querySelector('#cuentasContablesTable tbody');
    if (!tbody) {
        console.log('Tabla #cuentasContablesTable no encontrada, omitiendo carga de cuentas contables');
        return;
    }

    const estadoFiltro = document.querySelector('#estadoFiltro')?.value || '';
    const url = `index.php?controller=cuentacontable&action=list${estadoFiltro ? '&estado=' + estadoFiltro : ''}`;
    console.log('URL de la solicitud:', url);

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        console.log('Respuesta del servidor (status):', response.status);
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(`Error HTTP: ${response.status} - ${errorData.error || 'Error desconocido'}`);
            } catch (parseError) {
                throw new Error(`Error HTTP: ${response.status} - Respuesta no es JSON válida: ${text}`);
            }
        }
        const cuentasContables = await response.json();
        console.log('Datos recibidos:', cuentasContables);
        tbody.innerHTML = '';
        if (cuentasContables.length > 0) {
            cuentasContables.forEach(cuenta => {
                const rowClass = cuenta.estado === 'INACTIVO' ? 'class="inactive-row"' : '';
                tbody.innerHTML += `
                    <tr ${rowClass}>
                        <td data-label="ID">${cuenta.id}</td>
                        <td data-label="Nombre">${cuenta.nombre}</td>
                        <td data-label="Descripción">${cuenta.descripcion || 'Sin descripción'}</td>
                        <td data-label="Estado">${cuenta.estado || 'Sin estado'}</td>
                        <td data-label="Acciones">
                            <button class="edit-btn" onclick="showEditForm(${cuenta.id}); window.history.pushState({}, '', 'index.php?controller=cuentacontable&action=update&id=${cuenta.id}')">Editar</button>
                            <button class="delete-btn" onclick="deleteCuentaContable(${cuenta.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5">No hay cuentas contables registradas.</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar cuentas contables:', error.message);
        tbody.innerHTML = `<tr><td colspan="5">Error al cargar cuentas contables: ${error.message}</td></tr>`;
    }
}

async function createCuentaContable(data) {
    const response = await fetch('index.php?controller=cuentacontable&action=create', {
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

async function updateCuentaContable(id, data) {
    const response = await fetch(`index.php?controller=cuentacontable&action=update&id=${id}`, {
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

async function deleteCuentaContable(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta cuenta contable?')) return;

    try {
        const response = await fetch(`index.php?controller=cuentacontable&action=delete&id=${id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            const text = await response.text();
            try {
                const errorData = JSON.parse(text);
                throw new Error(errorData.error || 'Error al eliminar cuenta contable');
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válida: ${text}`);
            }
        }
        const result = await response.json();
        alert(result.message || 'Cuenta contable eliminada');
        loadCuentasContables();
    } catch (error) {
        console.error('Error al eliminar cuenta contable:', error);
        alert('No se puede eliminar la cuenta contable porque está asociada a otras entidades');
    }
}

function closeModal() {
    if (modal) {
        modal.classList.remove('active');
        modalForm.innerHTML = '';
        window.history.pushState({}, '', 'index.php?controller=cuentacontable&action=list');
    }
}

async function showCreateForm() {
    if (!modal || !modalForm) {
        console.error('Modal o modalForm no encontrados en el DOM');
        alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        return;
    }

    try {
        const response = await fetch('index.php?controller=cuentacontable&action=createForm', {
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
        const response = await fetch(`index.php?controller=cuentacontable&action=updateForm&id=${id}`, {
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
    const form = document.querySelector('#modalForm #cuentaContableFormInner');
    if (!form || form.tagName !== 'FORM') {
        console.error('No se encontró un elemento <form> dentro de #cuentaContableFormInner');
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
                const action = id ? updateCuentaContable(id, formData) : createCuentaContable(formData);
                const result = await action;
                alert(result.message || 'Operación exitosa');
                closeModal();
                loadCuentasContables();
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

// Cargar las cuentas contables al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded disparado');
    loadCuentasContables();

    const estadoFiltro = document.querySelector('#estadoFiltro');
    if (estadoFiltro) {
        estadoFiltro.addEventListener('change', loadCuentasContables);
    }
});