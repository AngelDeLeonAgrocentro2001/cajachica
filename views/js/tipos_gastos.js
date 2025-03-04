const tipoGastoForm = document.querySelector('#tipoGastoForm');

document.addEventListener('DOMContentLoaded', () => {
    loadTiposGastos();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    if (id && tipoGastoForm) {
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
                        <td>${tipo.id}</td>
                        <td>${tipo.name}</td>
                        <td>${tipo.description}</td>
                        <td>${tipo.estado}</td>
                        <td>
                            <button onclick="showEditForm(${tipo.id})">Editar</button>
                            <button onclick="deleteTipoGasto(${tipo.id})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5">No hay tipos de gastos registrados.</td></tr>';
        }
        return tipos;
    } catch (error) {
        console.error('Error al cargar tipos de gastos:', error);
        alert('No se pudo cargar la lista de tipos de gastos. Por favor, inicia sesión nuevamente.');
        window.location.href = 'index.php?controller=login&action=login';
    }
}

async function checkNombreExists(nombre, excludeId = null) {
    const tipos = await loadTiposGastos();
    const excludeIdNum = excludeId ? Number(excludeId) : null;
    return tipos.some(tipo => tipo.name === nombre && (excludeIdNum === null || tipo.id !== excludeIdNum));
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
        const errorData = await response.json();
        throw new Error(errorData.error || 'Error al crear tipo de gasto');
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
            throw new Error(errorData.error || 'Error al actualizar tipo de gasto');
        } catch (parseError) {
            throw new Error(`Respuesta no es JSON válida: ${text}`);
        }
    }
    return response.json();
}

async function deleteTipoGasto(id) {
    const response = await fetch(`index.php?controller=tipogasto&action=delete&id=${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || 'Error al eliminar tipo de gasto');
    }
    if (response.ok) loadTiposGastos();
}

async function showCreateForm() {
    if (!tipoGastoForm) {
        console.error('El elemento #tipoGastoForm no se encontró en el DOM');
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
        console.log('HTML devuelto (create):', html);
        tipoGastoForm.innerHTML = html;
        tipoGastoForm.style.display = 'block';
        const form = tipoGastoForm.querySelector('#tipoGastoFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="tipoGastoFormInner" dentro de #tipoGastoForm');
            tipoGastoForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addValidations();
    } catch (error) {
        console.error('Error al cargar el formulario (create):', error);
        if (tipoGastoForm) {
            tipoGastoForm.innerHTML = `<div class="error">${error.message}</div>`;
            tipoGastoForm.style.display = 'block';
        } else {
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    }
}

async function showEditForm(id) {
    if (!tipoGastoForm) {
        console.error('El elemento #tipoGastoForm no se encontró en el DOM');
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
        console.log('HTML devuelto (update):', html);
        tipoGastoForm.innerHTML = html;
        tipoGastoForm.style.display = 'block';
        const form = tipoGastoForm.querySelector('#tipoGastoFormInner');
        if (!form) {
            console.error('No se encontró un elemento <form> con id="tipoGastoFormInner" dentro de #tipoGastoForm');
            tipoGastoForm.innerHTML = '<div class="error">Error al cargar el formulario. Intenta de nuevo.</div>';
            return;
        }
        addValidations();
    } catch (error) {
        console.error('Error al cargar el formulario (update):', error);
        if (tipoGastoForm) {
            tipoGastoForm.innerHTML = `<div class="error">${error.message}</div>`;
            tipoGastoForm.style.display = 'block';
        } else {
            alert('Error: No se encontró el contenedor del formulario. Intenta de nuevo.');
        }
    }
}

function cancelForm() {
    const formContainer = document.getElementById('tipoGastoForm');
    if (formContainer) {
        const urlParams = new URLSearchParams(window.location.search);
        const idFromUrl = urlParams.get('id');
        formContainer.style.display = 'none';
        formContainer.innerHTML = '';
        if (idFromUrl) {
            window.history.pushState({}, '', `index.php?controller=tipogasto&action=update&id=${idFromUrl}`);
        } else {
            window.history.pushState({}, '', 'index.php?controller=tipogasto&action=list');
        }
    }
}

function addValidations() {
    const form = document.getElementById('tipoGastoFormInner');
    if (!form || form.tagName !== 'FORM') {
        console.error('No se encontró un elemento <form> con ID #tipoGastoFormInner. Verifica el HTML cargado:', document.getElementById('tipoGastoForm')?.innerHTML || 'No se encontró #tipoGastoForm');
        alert('No se pudo inicializar el formulario. Intenta de nuevo.');
        return;
    }

    const fields = {
        name: { required: true, minLength: 2 },
        description: { required: true, minLength: 2 },
        estado: { required: true }
    };

    let formMode = 'create';
    const urlParams = new URLSearchParams(window.location.search);
    const idFromUrl = urlParams.get('id');
    const formIdInput = form.querySelector('input[name="id"]');
    const formId = formIdInput ? formIdInput.value : null;

    if (idFromUrl || formId) {
        formMode = 'update';
    }

    console.log('formMode (determined):', formMode);
    console.log('idFromUrl:', idFromUrl);
    console.log('formId:', formId);

    form.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('input', validateField);
    });

    async function validateField(e) {
        const fieldName = e.target.name;
        const value = e.target.value;
        const errorElement = form.querySelector(`.error[data-field="${fieldName}"]`);
        if (!errorElement) return true;

        errorElement.style.display = 'none';
        e.target.classList.remove('invalid');

        if (fields[fieldName]) {
            if (fields[fieldName].required && !value) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} es obligatorio.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fields[fieldName].minLength && value && value.length < fields[fieldName].minLength) {
                errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} debe tener al menos ${fields[fieldName].minLength} caracteres.`;
                errorElement.style.display = 'block';
                e.target.classList.add('invalid');
                return false;
            }
            if (fieldName === 'name') {
                const nombreExists = await checkNombreExists(value, idFromUrl || formId);
                if (nombreExists) {
                    errorElement.textContent = `El nombre "${value}" ya está registrado. Por favor, usa un nombre diferente.`;
                    errorElement.style.display = 'block';
                    e.target.classList.add('invalid');
                    return false;
                }
            }
            return true;
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
            const formIdInput = form.querySelector('input[name="id"]');
            const formId = formIdInput ? formIdInput.value : null;

            console.log('formMode (submit):', formMode);
            console.log('formId (submit):', formId);

            try {
                let result;
                if (formMode === 'update' && (idFromUrl || formId)) {
                    const idToUse = idFromUrl || formId;
                    result = await updateTipoGasto(idToUse, formData);
                } else {
                    result = await createTipoGasto(formData);
                }

                if (result.message) {
                    const successElement = form.querySelector('.success');
                    successElement.textContent = result.message;
                    successElement.style.display = 'block';
                    setTimeout(() => {
                        window.location.href = 'index.php?controller=tipogasto&action=list';
                    }, 1000);
                } else if (result.error) {
                    const errorElement = form.querySelector('.error');
                    errorElement.textContent = result.error;
                    errorElement.style.display = 'block';
                }
            } catch (error) {
                console.error('Error al enviar formulario:', error);
                const errorElement = form.querySelector('.error');
                errorElement.textContent = error.message || 'Error al procesar la solicitud. Intenta de nuevo.';
                errorElement.style.display = 'block';
            }
        }
    });
}